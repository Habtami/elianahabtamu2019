<?php

use App\Console\Commands\NotifyExpiringLicenses;
use App\Console\Commands\SendContractExpiryWarnings;
use App\Console\Commands\SendPerformanceGuaranteeAlerts;
use App\Console\Commands\SendWeeklyReport;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Email authorized users about expiring/expired contracts every morning
Schedule::command(SendContractExpiryWarnings::class)->dailyAt('08:00');

// Email the concerned party when the performance guarantee alert day is reached
Schedule::command(SendPerformanceGuaranteeAlerts::class)->dailyAt('08:00');

// Schedule license expiry notifications daily at 9:00 AM
Schedule::command(NotifyExpiringLicenses::class)->dailyAt('09:00');

// Schedule weekly tire report every Monday at 8:00 AM
Schedule::command(SendWeeklyReport::class)->mondays()->at('08:00');

/*
 | Database backups. The time saved on the Backups page is the operator's
 | wall-clock time (EAT), while the application itself runs on UTC, so the
 | event is pinned to the same timezone as the register.
 */
$backupTime = '00:00';
$backupSettingsPath = storage_path('app/backup_settings.json');

if (is_file($backupSettingsPath)) {
    $backupSettings = json_decode((string) file_get_contents($backupSettingsPath), true);

    if (is_array($backupSettings) && preg_match('/^\d{2}:\d{2}$/', $backupSettings['backup_time'] ?? '') === 1) {
        $backupTime = $backupSettings['backup_time'];
    }
}

Schedule::command('db:backup --backup-type=scheduled')
    ->dailyAt($backupTime)
    ->timezone(config('backup.timezone', 'Africa/Addis_Ababa'))
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/backup-schedule.log'));

/*
 | Weekly summary of skipped backups, so operators still hear from the
 | system while the database is not changing.
 */
$summaryDay = array_search(
    strtolower((string) config('backup.weekly_summary_day', 'sunday')),
    ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
    true
);

Schedule::command('backups:weekly-summary')
    ->weeklyOn($summaryDay === false ? 0 : $summaryDay, config('backup.weekly_summary_time', '23:30'))
    ->timezone(config('backup.timezone', 'Africa/Addis_Ababa'))
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/backup-weekly-summary.log'));
