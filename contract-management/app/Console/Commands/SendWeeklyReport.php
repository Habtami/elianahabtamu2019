<?php

namespace App\Console\Commands;

use App\Models\Tire;
use App\Models\TireGive;
use App\Models\TireReturn;
use App\Models\Driver;
use App\Mail\WeeklyTireReport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendWeeklyReport extends Command
{
    protected $signature = 'tires:weekly-report {--email=}';
    protected $description = 'Send weekly tire tracking report via email';

    public function handle()
    {
        $startDate = Carbon::now()->subWeek();
        $endDate = Carbon::now();

        $stats = [
            'total_tires' => Tire::count(),
            'total_drivers' => Driver::count(),
            'gave_this_week' => TireGive::whereBetween('give_date', [$startDate, $endDate])->count(),
            'returned_this_week' => TireReturn::whereBetween('return_date', [$startDate, $endDate])->count(),
            'tires_in_use' => Tire::whereHas('tireGives', function ($q) {
                $q->where('is_returned', false);
            })->count(),
        ];

        $gave = TireGive::with(['tire', 'driver'])
            ->whereBetween('give_date', [$startDate, $endDate])
            ->get();
        $returned = TireReturn::with(['tire', 'driver'])
            ->whereBetween('return_date', [$startDate, $endDate])
            ->get();

        $email = $this->option('email');
        if ($email) {
            Mail::to($email)->send(new WeeklyTireReport($stats, $gave, $returned));
            $this->info("Weekly report sent to {$email}");
        } else {
            $admins = \App\Models\User::whereIn('role', ['super_admin', 'admin'])->get();
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new WeeklyTireReport($stats, $gave, $returned));
            }
            $this->info("Weekly report sent to " . $admins->count() . " admin(s).");
        }

        return 0;
    }
}
