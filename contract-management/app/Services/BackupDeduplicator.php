<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Backup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

/**
 * Change detection for database backups.
 *
 * The fingerprint is a hash of every business table's row count and newest
 * timestamp. It is deliberately cheap (two aggregates per table) because it
 * runs before the dump, and stable enough to answer "did anything change
 * since the last backup?".
 */
class BackupDeduplicator
{
    private readonly string $algorithm;

    public function __construct(?string $algorithm = null)
    {
        $this->algorithm = $algorithm ?? config('backup.hash_algorithm', 'sha256');
    }

    /**
     * @return array<string, array{count: int, max: ?string}>
     */
    public function snapshot(): array
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        // MySQL lists tables from every schema the user can read when no
        // schema is pinned, so restrict the listing to the app database.
        $tables = $driver === 'sqlite'
            ? Schema::getTableListing(null, false)
            : Schema::getTableListing($connection->getConfig('database'), false);

        $excluded = (array) config('backup.excluded_tables', []);
        $grammar = $connection->getQueryGrammar();
        $snapshot = [];

        foreach ($tables as $table) {
            $table = Str::contains($table, '.') ? Str::afterLast($table, '.') : $table;

            if ($table === '' || Str::startsWith($table, 'sqlite_') || in_array($table, $excluded, true)) {
                continue;
            }

            try {
                $columns = array_column(Schema::getColumns($table), 'name');
                $timestamp = in_array('updated_at', $columns, true)
                    ? 'updated_at'
                    : (in_array('created_at', $columns, true) ? 'created_at' : null);

                $query = DB::table($table)->selectRaw('COUNT(*) as row_count');

                if ($timestamp !== null) {
                    $query->selectRaw(
                        'MAX(NULLIF('.$grammar->wrap($timestamp).', ?)) as max_timestamp',
                        ['0000-00-00 00:00:00']
                    );
                }

                $row = $query->first();
                $snapshot[$table] = [
                    'count' => (int) ($row->row_count ?? 0),
                    'max' => $row->max_timestamp ?? null,
                ];
            } catch (Throwable) {
                // A table we cannot read should not prevent a backup; treat
                // it as stable and keep going.
                $snapshot[$table] = ['count' => 0, 'max' => null];
            }
        }

        ksort($snapshot);

        return $snapshot;
    }

    /**
     * @param  array<string, array{count: int, max: ?string}>  $snapshot
     */
    public function hashSnapshot(array $snapshot): string
    {
        $lines = [];

        foreach ($snapshot as $table => $data) {
            ksort($data);
            $lines[$table] = $table.'|'.$data['count'].'|'.($data['max'] ?? '');
        }

        ksort($lines);

        return hash($this->algorithm, implode("\n", $lines));
    }

    /**
     * @return array{hash: string, tables: int, computed_at: \Carbon\CarbonInterface}
     */
    public function fingerprint(): array
    {
        $snapshot = $this->snapshot();

        return [
            'hash' => $this->hashSnapshot($snapshot),
            'tables' => count($snapshot),
            'computed_at' => now(),
        ];
    }

    public function lastSuccessfulHash(): ?string
    {
        return Backup::query()
            ->where('was_skipped', false)
            ->whereNotNull('content_hash')
            ->latest('id')
            ->value('content_hash');
    }

    /**
     * Pure decision table so the rule can be unit-tested without a database.
     *
     * @return array{skip: bool, reason: ?string}
     */
    public function decide(bool $smartMode, bool $forceSkip, ?string $lastHash, ?string $newHash): array
    {
        if ($forceSkip) {
            return ['skip' => true, 'reason' => 'forced'];
        }

        if (! $smartMode || $newHash === null || $lastHash === null) {
            return ['skip' => false, 'reason' => null];
        }

        if (hash_equals($lastHash, $newHash)) {
            return ['skip' => true, 'reason' => 'unchanged'];
        }

        return ['skip' => false, 'reason' => null];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function recordBackup(array $attributes): Backup
    {
        return Backup::create($attributes + [
            'was_skipped' => false,
            'backup_type' => 'scheduled',
        ]);
    }

    public function recordSkip(?string $contentHash, string $skipReason, string $backupType = 'scheduled'): Backup
    {
        return Backup::create([
            'filename' => null,
            'content_hash' => $contentHash,
            'was_skipped' => true,
            'skip_reason' => Str::limit($skipReason, 250),
            'backup_type' => $backupType,
            'email_status' => 'not_sent',
            'copy_status' => null,
            'created_by' => null,
        ]);
    }

    /**
     * Remove stored copies beyond the newest one for each fingerprint once
     * they are older than the retention window. The newest copy always stays.
     *
     * @return array<int, string> deleted filenames
     */
    public function cleanupDuplicates(int $days): array
    {
        if ($days < 1) {
            return [];
        }

        $deleted = [];
        $duplicates = Backup::query()
            ->where('was_skipped', false)
            ->whereNotNull('content_hash')
            ->whereNotNull('filename')
            ->where('created_at', '<', now()->subDays($days))
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('content_hash');

        foreach ($duplicates as $group) {
            foreach ($group->slice(1) as $backup) {
                $storagePath = storage_path('app/backups/'.basename($backup->filename));
                $destinationPath = $backup->destination_path
                    ? rtrim($backup->destination_path, '\\/').DIRECTORY_SEPARATOR.basename($backup->filename)
                    : null;

                foreach ([$backup->file_path, $storagePath, $destinationPath] as $path) {
                    if ($path && File::exists($path)) {
                        File::delete($path);
                    }
                }

                ActivityLog::log(
                    ActivityLog::TYPE_ADMIN_ACTION,
                    null,
                    'backup_deleted',
                    null,
                    json_encode([
                        'filename' => $backup->filename,
                        'reason' => 'duplicate_cleanup',
                        'content_hash' => $backup->content_hash,
                    ]),
                    ActivityLog::SEVERITY_WARNING,
                    'Auto-removed duplicate backup: '.$backup->filename
                );

                $deleted[] = $backup->filename;
                $backup->delete();
            }
        }

        return $deleted;
    }
}
