<?php

namespace App\Console\Commands;

use App\Models\Driver;
use App\Notifications\LicenseExpiringNotification;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class NotifyExpiringLicenses extends Command
{
    protected $signature = 'tires:notify-license-expiry {--days=30}';
    protected $description = 'Notify drivers with licenses expiring soon';

    public function handle(TelegramService $telegramService)
    {
        $days = (int) $this->option('days');
        $drivers = Driver::where('status', true)
            ->whereNotNull('license_expiry')
            ->whereDate('license_expiry', '>', now())
            ->whereDate('license_expiry', '<=', now()->addDays($days))
            ->get();

        if ($drivers->isEmpty()) {
            $this->info('No drivers with expiring licenses found.');
            return 0;
        }

        $count = 0;
        foreach ($drivers as $driver) {
            $telegramService->notifyLicenseExpiring($driver);
            $count++;
        }

        $this->info("Sent {$count} license expiry notifications.");
        return 0;
    }
}
