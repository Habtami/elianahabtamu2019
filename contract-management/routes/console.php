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
