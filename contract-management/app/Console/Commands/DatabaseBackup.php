<?php

namespace App\Console\Commands;

use App\Mail\BackupNotification;
use App\Models\ActivityLog;
use App\Models\Backup;
use App\Models\User;
use App\Services\BackupDeduplicator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup {--user-id=} {--backup-type=manual} {--force-skip} {--content-hash=}';

    protected $description = 'Backup the database';

    public function handle(BackupDeduplicator $deduplicator)
    {
        $backupType = $this->option('backup-type');
        $isScheduled = ($backupType === 'auto' || $backupType === 'scheduled');
        $userId = $this->option('user-id');
        $settings = $this->getSettings();

        $smartMode = $isScheduled && filter_var(
            $settings['smart_mode'] ?? config('backup.smart_mode', true),
            FILTER_VALIDATE_BOOLEAN
        );
        $forceSkip = $isScheduled && (bool) $this->option('force-skip');

        if ($isScheduled && (bool) ($settings['skip_next'] ?? false)) {
            $forceSkip = true;
            $settings['skip_next'] = false;
            $this->saveSettings($settings);
        }

        // Change detection only makes sense for scheduled runs; a manual
        // backup carries the fingerprint the controller computed just before
        // the dump so the next manual run can tell whether anything changed.
        $newHash = null;
        $lastHash = null;

        if ($isScheduled) {
            try {
                $newHash = $deduplicator->fingerprint()['hash'];
                $lastHash = $deduplicator->lastSuccessfulHash();
            } catch (Throwable $e) {
                $this->warn('Change detection unavailable: '.$e->getMessage());
                $smartMode = false;
            }
        } else {
            $newHash = $this->option('content-hash') ?: null;
        }

        $decision = $deduplicator->decide($smartMode, $forceSkip, $lastHash, $newHash);

        if ($decision['skip']) {
            return $this->skipBackup($deduplicator, $decision['reason'], $newHash);
        }

        return $this->runBackup($deduplicator, $backupType, $isScheduled, $userId, $newHash, $settings);
    }

    private function skipBackup(BackupDeduplicator $deduplicator, ?string $reason, ?string $contentHash): int
    {
        $skipReason = $reason === 'forced'
            ? 'Skipped on request from the Backups page'
            : 'Skipped because the database has not changed since the last backup';

        $deduplicator->recordSkip($contentHash, $skipReason);

        $description = $reason === 'forced'
            ? 'Scheduled backup skipped on request at '.now()->toDateTimeString()
            : 'Scheduled backup skipped (no database changes) at '.now()->toDateTimeString();

        ActivityLog::log(
            ActivityLog::TYPE_ADMIN_ACTION,
            null,
            'backup_skipped',
            null,
            json_encode([
                'filename' => null,
                'type' => 'scheduled',
                'reason' => $reason,
                'content_hash' => $contentHash,
            ]),
            ActivityLog::SEVERITY_INFO,
            $description
        );

        $this->info($description);

        return 0;
    }

    private function runBackup(
        BackupDeduplicator $deduplicator,
        string $backupType,
        bool $isScheduled,
        ?string $userId,
        ?string $contentHash,
        array $settings
    ): int {
        // The app runs on UTC, so name the file from the operator's clock;
        // otherwise a 10:47 EAT run looks like 07:47 in the folder listing.
        $timezone = (string) config('backup.timezone', config('app.timezone', 'UTC'));
        $startedAt = now()->setTimezone($timezone);
        $filename = 'backup-'.$startedAt->format('Y-m-d_H-i-s').'.sql';

        // The configured folder is the one place dumps live. storage/app/backups
        // is only the fallback when no folder is set (or the setting points
        // inside public/, which would expose dumps without a login).
        $configuredPath = trim((string) ($settings['destination_path'] ?? ''));
        $destinationPath = '';
        $backupDir = storage_path('app/backups');
        $copyStatus = 'not_configured';

        if ($configuredPath !== '') {
            $resolved = $this->resolveDestinationPath($configuredPath);

            if (Str::startsWith(strtolower($resolved), strtolower(public_path()))) {
                $this->warn('Refusing to store backups inside the public web root: '.$resolved);
                $copyStatus = 'copy_failed';
            } else {
                $destinationPath = $resolved;
                $backupDir = $resolved;
                $copyStatus = 'primary';
            }
        }

        try {
            File::ensureDirectoryExists($backupDir);
        } catch (Throwable) {
            // An unplugged drive is handled by the retry below.
        }

        $path = $backupDir.DIRECTORY_SEPARATOR.$filename;

        [$dumped, $dumpError] = $this->runDump($path);

        // An unplugged or read-only drive must not lose the backup: retry in
        // local storage and flag the run so the page shows where it landed.
        $usedFallback = false;

        if (! $dumped && $copyStatus === 'primary') {
            $backupDir = storage_path('app/backups');
            try {
                File::ensureDirectoryExists($backupDir);
            } catch (Throwable) {
                // Nothing else to fall back to.
            }
            $path = $backupDir.DIRECTORY_SEPARATOR.$filename;

            [$dumped, $dumpError] = $this->runDump($path);
            $usedFallback = $dumped;

            if ($dumped) {
                $this->warn('Stored the backup in local storage because '.$destinationPath.' was not usable.');
            }
        }

        if (! $dumped) {
            ActivityLog::log(
                ActivityLog::TYPE_ADMIN_ACTION,
                null,
                'backup_failed',
                null,
                json_encode([
                    'filename' => $filename,
                    'type' => $backupType,
                    'error' => $dumpError,
                ]),
                ActivityLog::SEVERITY_DANGER,
                "Database backup failed: $filename"
            );

            $this->error("Backup failed. $dumpError");

            return 1;
        }

        if ($usedFallback) {
            $copyStatus = 'fallback';
            $destinationPath = $backupDir;
        }

        if (config('backup.compress', false) && $this->compressFile($path)) {
            File::delete($path);
            $filename .= '.gz';
            $path .= '.gz';
        }

        $user = $userId ? User::find($userId) : null;
        $createdBy = $user ? $user->name : ($isScheduled ? 'Scheduled task' : 'System');

        $backupSize = $this->formatBytes(filesize($path));

        $backup = $deduplicator->recordBackup([
            'filename' => $filename,
            'file_path' => $path,
            'size_bytes' => filesize($path),
            'content_hash' => $contentHash,
            'backup_type' => $backupType,
            'created_by' => $userId ?: null,
            'destination_path' => $destinationPath !== '' ? $destinationPath : null,
            'copy_status' => $copyStatus,
            'email_status' => 'pending',
        ]);

        [$emailStatus, $recipients] = $this->sendBackupNotification(
            $filename,
            $startedAt->toDateTimeString(),
            $backupType,
            $backupSize,
            $createdBy,
            $destinationPath
        );

        $backup->update([
            'email_status' => $emailStatus,
            'email_recipients' => $recipients !== [] ? implode(', ', $recipients) : null,
        ]);

        ActivityLog::log(
            ActivityLog::TYPE_ADMIN_ACTION,
            null,
            'backup_created',
            null,
            json_encode([
                'filename' => $filename,
                'type' => $backupType,
                'destination_path' => $destinationPath,
                'copy_status' => $copyStatus,
                'email_status' => $emailStatus,
                'email_recipients' => implode(', ', $recipients),
                'content_hash' => $contentHash,
                'size' => $backupSize,
            ]),
            ActivityLog::SEVERITY_INFO,
            "Database backup created: $filename"
        );

        $this->info("Backup created successfully: $filename");
        if ($destinationPath !== '') {
            $this->info("Backup stored in: $destinationPath");
        }

        // Only scheduled runs are frequent enough for duplicates to pile up.
        if ($isScheduled) {
            $retention = (int) config('backup.cleanup_duplicates_after_days', 30);
            $removed = $deduplicator->cleanupDuplicates($retention);

            if ($removed !== []) {
                $this->info('Removed '.count($removed).' duplicate backup(s) older than '.$retention.' days.');
            }
        }

        return 0;
    }

    /**
     * Run mysqldump into $path. A failed dump leaves no partial file behind.
     *
     * @return array{0: bool, 1: string} [succeeded, error output]
     */
    private function runDump(string $path): array
    {
        $errorPath = $path.'.err';

        $mysqldump = 'c:\wamp64\bin\mysql\mysql8.4.7\bin\mysqldump.exe';
        if (! file_exists($mysqldump)) {
            $mysqldump = 'mysqldump';
        }

        $connection = config('database.connections.'.config('database.default'));
        $parts = [
            escapeshellarg($mysqldump),
            '--user='.escapeshellarg((string) ($connection['username'] ?? '')),
        ];

        if (! empty($connection['password'])) {
            $parts[] = '--password='.escapeshellarg((string) $connection['password']);
        }

        $parts[] = '--host='.escapeshellarg((string) ($connection['host'] ?? '127.0.0.1'));
        $parts[] = '--port='.escapeshellarg((string) ($connection['port'] ?? '3306'));
        $parts[] = '--no-tablespaces';
        $parts[] = escapeshellarg((string) ($connection['database'] ?? ''));

        $command = implode(' ', $parts)
            .' > '.escapeshellarg($path)
            .' 2> '.escapeshellarg($errorPath);

        $returnVar = null;
        $output = null;
        exec($command, $output, $returnVar);

        $dumpError = File::exists($errorPath) ? trim(File::get($errorPath)) : '';
        File::delete($errorPath);

        $succeeded = $returnVar === 0 && File::exists($path) && File::size($path) > 0;

        if (! $succeeded) {
            File::delete($path);

            return [false, $dumpError !== '' ? $dumpError : 'mysqldump exited with code '.$returnVar];
        }

        return [true, ''];
    }

    /**
     * Gzip a dump in place. Returns true when the compressed file is usable.
     */
    private function compressFile(string $path): bool
    {
        $source = @fopen($path, 'rb');
        $target = @gzopen($path.'.gz', 'wb9');

        if ($source === false || $target === false) {
            if ($source !== false) {
                fclose($source);
            }
            if ($target !== false) {
                gzclose($target);
            }

            return false;
        }

        stream_copy_to_stream($source, $target);
        fclose($source);
        gzclose($target);

        return filesize($path.'.gz') > 0;
    }

    /**
     * Resolve a relative folder against the project root so the CLI and the
     * web server write to the same place.
     */
    private function resolveDestinationPath(string $destinationPath): string
    {
        if (preg_match('/^(?:[A-Za-z]:[\\\\\\/]|\\\\\\\\|\/)/', $destinationPath)) {
            return $destinationPath;
        }

        return base_path($destinationPath);
    }

    /**
     * Email active admins (plus the optional notification address).
     *
     * @return array{0: string, 1: array<int, string>}
     */
    private function sendBackupNotification(
        string $filename,
        string $backupTime,
        string $backupType,
        string $backupSize,
        string $createdBy,
        string $destinationPath
    ): array {
        $recipients = $this->recipients($this->getSettings());

        if ($recipients === []) {
            $this->warn('No notification recipients found; backup email was not sent.');

            return ['no_recipients', []];
        }

        try {
            Mail::to($recipients)->send(new BackupNotification(
                $filename,
                $backupTime,
                $backupType,
                $backupSize,
                $createdBy,
                $destinationPath
            ));

            $this->info('Backup notification sent to: '.implode(', ', $recipients));

            return ['sent', $recipients];
        } catch (Throwable $e) {
            $this->warn('Failed to send backup notification: '.$e->getMessage());

            return ['failed', $recipients];
        }
    }

    /**
     * @return array<int, string>
     */
    private function recipients(array $settings): array
    {
        $recipients = User::whereIn('role', [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN])
            ->where('status', true)
            ->pluck('email')
            ->filter()
            ->all();

        $extra = trim((string) ($settings['notification_email'] ?? ''));

        if ($extra !== '' && ! in_array($extra, $recipients, true)) {
            $recipients[] = $extra;
        }

        return array_values(array_unique($recipients));
    }

    private function getSettings(): array
    {
        $settingsPath = storage_path('app/backup_settings.json');

        if (File::exists($settingsPath)) {
            $settings = json_decode(File::get($settingsPath), true);

            if (is_array($settings)) {
                return $settings;
            }
        }

        return [
            'backup_time' => '00:00',
            'destination_path' => (string) config('backup.destination_path', 'D:\\a_contract_backup'),
        ];
    }

    private function saveSettings(array $settings): void
    {
        File::put(storage_path('app/backup_settings.json'), json_encode($settings));
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}
