<?php

namespace App\Console\Commands;

use App\Mail\BackupWeeklySummary;
use App\Models\Backup;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendBackupWeeklySummary extends Command
{
    protected $signature = 'backups:weekly-summary {--force}';

    protected $description = 'Email a weekly summary of backups skipped because nothing changed';

    public function handle()
    {
        $settings = $this->getSettings();

        if (! $this->option('force') && $this->alreadySentThisWeek($settings)) {
            $this->info('Weekly summary already sent on '.$settings['last_weekly_summary_at'].'.');

            return 0;
        }

        $end = now();
        $start = $end->copy()->subDays(7);

        $skipped = Backup::query()
            ->where('was_skipped', true)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        if ($skipped === 0) {
            $this->info('No skipped backups in the last 7 days; nothing to summarise.');

            return 0;
        }

        $recipients = $this->recipients($settings);

        if ($recipients === []) {
            $this->warn('No notification recipients found; weekly summary was not sent.');

            return 1;
        }

        try {
            Mail::to($recipients)->send(new BackupWeeklySummary(
                $skipped,
                $start->format('Y-m-d H:i'),
                $end->format('Y-m-d H:i')
            ));
        } catch (Throwable $e) {
            $this->error('Failed to send the weekly summary: '.$e->getMessage());

            return 1;
        }

        $settings['last_weekly_summary_at'] = $end->toDateTimeString();
        File::put(storage_path('app/backup_settings.json'), json_encode($settings));

        $this->info('Weekly summary sent to: '.implode(', ', $recipients).' ('.$skipped.' skipped backup(s)).');

        return 0;
    }

    private function alreadySentThisWeek(array $settings): bool
    {
        $last = $settings['last_weekly_summary_at'] ?? null;

        return filled($last) && now()->diffInDays($last) < 6;
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

        return ['backup_time' => '00:00'];
    }
}
