<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Backup;
use App\Models\User;
use App\Services\BackupDeduplicator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! auth()->check() || ! auth()->user()->hasPermission('admin_panel')) {
                return redirect()->route('dashboard')
                    ->with('error', 'You are not authorized to manage backups.');
            }

            return $next($request);
        });
    }

    public function index()
    {
        $backups = [];
        $settings = $this->getSettings();
        $primaryPath = $this->backupDirectory($settings);
        $displayTz = $this->displayTimezone();

        $records = Backup::with('creator')->get();
        $recordsByFilename = $records
            ->where('was_skipped', false)
            ->filter(fn ($record) => filled($record->filename))
            ->keyBy('filename');

        $adminEmails = $this->recipientEmails($settings);

        // Dumps live in the configured folder; storage/app/backups is still
        // scanned so backups taken before the folder was changed stay listed.
        $seen = [];
        $directories = array_values(array_unique([$primaryPath, storage_path('app/backups')]));

        foreach ($directories as $directory) {
            if (! File::exists($directory)) {
                continue;
            }

            foreach (File::files($directory) as $file) {
                $filename = $file->getFilename();

                if (isset($seen[$filename])) {
                    continue;
                }
                $seen[$filename] = true;

                $record = $recordsByFilename->get($filename);

                $storedAt = $record?->created_at
                    ? $record->created_at->copy()->setTimezone($displayTz)
                    : Carbon::createFromTimestamp($file->getMTime(), $displayTz);

                $backups[] = [
                    'filename' => $filename,
                    'size' => $this->formatBytes($file->getSize()),
                    'date' => $storedAt->toDateTimeString(),
                    'path' => $file->getPathname(),
                    'created_by' => $record?->creator?->name ?? 'Unknown',
                    'type' => $record?->backup_type ?? 'unknown',
                    'copy_status' => $record?->copy_status,
                    'email_status' => $record?->email_status ?? 'unknown',
                    'email_recipients' => $record?->email_recipients,
                    'content_hash' => $record?->content_hash,
                    'was_skipped' => false,
                    'skip_reason' => null,
                ];
            }
        }

        // Skipped runs never produce a file, so their badge lives on a
        // standalone history row instead of a storage entry.
        foreach ($records->where('was_skipped', true) as $record) {
            $backups[] = [
                'filename' => null,
                'size' => '—',
                'date' => $record->created_at?->copy()->setTimezone($displayTz)->toDateTimeString() ?? '',
                'path' => null,
                'created_by' => $record->creator?->name ?? 'Scheduled task',
                'type' => $record->backup_type,
                'copy_status' => null,
                'email_status' => 'not_sent',
                'email_recipients' => null,
                'content_hash' => $record->content_hash,
                'was_skipped' => true,
                'skip_reason' => $record->skip_reason,
            ];
        }

        usort($backups, fn ($a, $b) => strcmp($b['date'], $a['date']));

        $deduplicator = app(BackupDeduplicator::class);

        $lastBackup = Backup::query()
            ->where('was_skipped', false)
            ->whereNotNull('content_hash')
            ->latest('id')
            ->first();

        $currentHash = null;
        try {
            $currentHash = $deduplicator->fingerprint()['hash'];
        } catch (Throwable) {
            // The page must still render when the database cannot be read.
        }

        $hashMatch = ($lastBackup && $currentHash)
            ? hash_equals($lastBackup->content_hash, $currentHash)
            : null;

        $smartMode = filter_var(
            $settings['smart_mode'] ?? config('backup.smart_mode', true),
            FILTER_VALIDATE_BOOLEAN
        );
        $nextLikelySkip = $smartMode && $hashMatch === true;
        $skipNext = (bool) ($settings['skip_next'] ?? false);
        $skippedThisWeek = Backup::query()
            ->where('was_skipped', true)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // Every backup log entry is grouped by the file it belongs to so the
        // list can show a history modal without a second request.
        $histories = ActivityLog::with('actor')
            ->whereIn('field_name', ['backup_created', 'backup_failed', 'backup_skipped', 'backup_deleted'])
            ->latest()
            ->get()
            ->map(function (ActivityLog $log) use ($displayTz) {
                $values = json_decode((string) $log->new_value, true);

                if (! is_array($values) || empty($values['filename'])) {
                    return null;
                }

                return [
                    'action' => $log->field_name,
                    'user' => $log->actor?->name ?? 'System',
                    'at' => $log->created_at?->copy()->setTimezone($displayTz),
                    'values' => $values,
                ];
            })
            ->filter()
            ->groupBy(fn ($event) => $event['values']['filename']);

        $lastBackupAt = $lastBackup?->created_at?->copy()->setTimezone($displayTz);

        return view('backup.index', compact(
            'backups',
            'settings',
            'primaryPath',
            'histories',
            'lastBackup',
            'lastBackupAt',
            'currentHash',
            'hashMatch',
            'smartMode',
            'skipNext',
            'skippedThisWeek',
            'nextLikelySkip',
            'adminEmails'
        ));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'backup_time' => 'required|date_format:H:i',
            'notification_email' => 'nullable|email',
            'destination_path' => 'nullable|string|max:500',
            'smart_mode' => 'nullable|boolean',
        ]);

        $settings = $this->getSettings();
        $settings['backup_time'] = $request->backup_time;
        $settings['smart_mode'] = $request->boolean('smart_mode');
        // Amharic keyboard layouts type a look-alike colon (U+1361), which
        // turns D:\a_contract_backup into a relative path. Fold it back so the
        // drive letter keeps working.
        $destinationPath = trim((string) $request->input('destination_path', ''));
        $settings['destination_path'] = preg_replace('/^([A-Za-z])[\x{1361}-\x{1368}]([\\\\\/])/u', '$1:$2', $destinationPath);

        if (! empty($request->notification_email)) {
            $settings['notification_email'] = $request->notification_email;
        } else {
            unset($settings['notification_email']);
        }

        File::put(storage_path('app/backup_settings.json'), json_encode($settings));

        ActivityLog::log(
            ActivityLog::TYPE_ADMIN_ACTION,
            null,
            'backup_settings',
            null,
            json_encode([
                'backup_time' => $settings['backup_time'],
                'smart_mode' => $settings['smart_mode'],
                'destination_path' => $settings['destination_path'],
            ]),
            ActivityLog::SEVERITY_INFO,
            'Backup settings updated'
        );

        return redirect()->route('backup.index')->with('success', 'Backup settings updated.');
    }

    public function skipNext()
    {
        $settings = $this->getSettings();
        $settings['skip_next'] = ! (bool) ($settings['skip_next'] ?? false);

        File::put(storage_path('app/backup_settings.json'), json_encode($settings));

        return redirect()->route('backup.index')->with(
            'success',
            $settings['skip_next']
                ? 'The next scheduled run will be skipped.'
                : 'Skip request cancelled.'
        );
    }

    public function create()
    {
        $deduplicator = app(BackupDeduplicator::class);

        $fingerprint = null;
        try {
            $fingerprint = $deduplicator->fingerprint()['hash'];
        } catch (Throwable) {
            // If the fingerprint cannot be read, take the dump rather than
            // losing the backup.
        }

        $lastBackup = Backup::query()
            ->where('was_skipped', false)
            ->whereNotNull('filename')
            ->latest('id')
            ->first();

        // Only dump when the database differs from the newest stored backup;
        // clicking twice in a row should not stack identical files. Backups
        // made before fingerprints existed fall back to timestamp checks.
        if ($lastBackup) {
            $unchanged = $lastBackup->content_hash && $fingerprint !== null
                ? hash_equals($lastBackup->content_hash, $fingerprint)
                : ! $this->hasChangesSince($lastBackup);

            if ($unchanged) {
                return redirect()->route('backup.index')
                    ->with('info', 'ከመጨረሻው ባክአፕ በኋላ ምንም ለውጥ የለም። ባክአፕ አልተሰራም።');
            }
        }

        try {
            $exitCode = Artisan::call('db:backup', [
                '--user-id' => auth()->id(),
                '--backup-type' => 'manual',
                '--content-hash' => $fingerprint ?? '',
            ]);
        } catch (Throwable $e) {
            return redirect()->route('backup.index')
                ->with('error', 'Backup failed: '.$e->getMessage());
        }

        if ($exitCode !== 0) {
            return redirect()->route('backup.index')
                ->with('error', 'Backup failed. Check the activity logs for details.');
        }

        $recipients = $this->recipientEmails($this->getSettings());
        $message = $recipients === []
            ? 'Backup created successfully.'
            : 'Backup created successfully. Notification sent to: '.implode(', ', $recipients);

        return redirect()->route('backup.index')->with('success', $message);
    }

    /**
     * Fallback for backups stored before fingerprints were recorded: did any
     * business table get a row newer than the last backup's timestamp?
     */
    private function hasChangesSince(Backup $lastBackup): bool
    {
        $connection = Schema::getConnection();
        $tables = $connection->getDriverName() === 'sqlite'
            ? Schema::getTableListing(null, false)
            : Schema::getTableListing($connection->getConfig('database'), false);

        $excluded = (array) config('backup.excluded_tables', []);

        foreach ($tables as $table) {
            $table = Str::contains($table, '.') ? Str::afterLast($table, '.') : $table;

            if ($table === '' || Str::startsWith($table, 'sqlite_') || in_array($table, $excluded, true)) {
                continue;
            }

            $columns = array_column(Schema::getColumns($table), 'name');
            $timestamp = in_array('updated_at', $columns, true)
                ? 'updated_at'
                : (in_array('created_at', $columns, true) ? 'created_at' : null);

            if ($timestamp === null) {
                continue;
            }

            if (DB::table($table)->where($timestamp, '>', $lastBackup->created_at)->exists()) {
                return true;
            }
        }

        return false;
    }

    public function download($filename)
    {
        $path = $this->locateBackupFile($filename);

        if ($path !== null) {
            return response()->download($path);
        }

        return redirect()->route('backup.index')->with('error', 'File not found.');
    }

    public function destroy($filename)
    {
        $filename = basename($filename);
        $path = $this->locateBackupFile($filename);

        if ($path === null) {
            return redirect()->route('backup.index')->with('error', 'File not found.');
        }

        $backup = Backup::where('filename', $filename)->first();
        $size = $this->formatBytes(File::size($path));

        File::delete($path);
        Backup::where('filename', $filename)->delete();

        ActivityLog::log(
            ActivityLog::TYPE_ADMIN_ACTION,
            null,
            'backup_deleted',
            null,
            json_encode([
                'filename' => $filename,
                'reason' => 'manual_delete',
                'size' => $size,
                'content_hash' => $backup?->content_hash,
            ]),
            ActivityLog::SEVERITY_WARNING,
            "Database backup deleted: $filename"
        );

        return redirect()->route('backup.index')->with('success', 'Backup deleted successfully.');
    }

    /**
     * The register shows the operator's clock (EAT) while the application
     * keeps timestamps in UTC.
     */
    private function displayTimezone(): string
    {
        return (string) config('backup.timezone', config('app.timezone', 'UTC'));
    }

    /**
     * Folder that holds the dumps: the configured destination, otherwise
     * storage/app/backups. Relative folders resolve against the project root.
     */
    private function backupDirectory(array $settings): string
    {
        $configured = trim((string) ($settings['destination_path'] ?? ''));

        if ($configured === '') {
            return storage_path('app/backups');
        }

        $resolved = preg_match('/^(?:[A-Za-z]:[\\\\\\/]|\\\\\\\\|\/)/', $configured)
            ? $configured
            : base_path($configured);

        // Never serve dumps straight out of the public web root.
        if (Str::startsWith(strtolower($resolved), strtolower(public_path()))) {
            return storage_path('app/backups');
        }

        return $resolved;
    }

    private function locateBackupFile(string $filename): ?string
    {
        $filename = basename($filename);
        $record = Backup::where('filename', $filename)->first();

        $candidates = [
            $record?->file_path,
            $this->backupDirectory($this->getSettings()).DIRECTORY_SEPARATOR.$filename,
            storage_path('app/backups'.DIRECTORY_SEPARATOR.$filename),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $candidate !== '' && File::exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Active admins plus the optional extra address from the settings file.
     *
     * @return array<int, string>
     */
    private function recipientEmails(array $settings): array
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
