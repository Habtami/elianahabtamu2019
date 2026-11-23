<?php

namespace App\Console\Commands;

use App\Models\Contract;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckContractAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:check-alerts
                          {--email= : Send alerts to this email address}
                          {--notify-all : Notify about all expiring contracts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for contract and performance guarantee expiration alerts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking contract alerts...');

        $contracts = Contract::all();
        $alertsFound = 0;
        $alerts = [];

        foreach ($contracts as $contract) {
            // Update contract status before checking alerts
            $contract->updateStatus();
            
            $contractAlerts = [];

            // Check contract end date alert
            if ($contract->shouldAlertContractEndDate()) {
                $daysRemaining = $contract->getDaysUntilContractEnd();
                $message = "Contract '{$contract->name}' (ID: {$contract->id}) is ending in {$daysRemaining} days (End Date: {$contract->end_date->format('Y-m-d')})";

                $this->warn($message);
                $contractAlerts[] = [
                    'type' => 'Contract End Date',
                    'message' => $message,
                    'days_remaining' => $daysRemaining,
                ];
                $alertsFound++;
            }

            // Check performance guarantee alert
            if ($contract->shouldAlertPerformanceGuarantee()) {
                $daysRemaining = $contract->getDaysUntilPerformanceGuaranteeEnd();
                $message = "Performance Guarantee for '{$contract->name}' (ID: {$contract->id}) is ending in {$daysRemaining} days (End Date: {$contract->performance_guarantee_end_date->format('Y-m-d')}, Amount: {$contract->performance_guarantee_amount})";

                $this->warn($message);
                $contractAlerts[] = [
                    'type' => 'Performance Guarantee',
                    'message' => $message,
                    'days_remaining' => $daysRemaining,
                ];
                $alertsFound++;
            }

            if (!empty($contractAlerts)) {
                $alerts[] = [
                    'contract' => $contract,
                    'alerts' => $contractAlerts,
                ];
            }
        }

        if ($alertsFound === 0) {
            $this->info('No alerts found.');
        } else {
            $this->info("Found {$alertsFound} alert(s).");

            // Log alerts
            Log::channel('daily')->info('Contract Alerts', [
                'count' => $alertsFound,
                'alerts' => $alerts,
            ]);

            // Send email if requested
            if ($this->option('email')) {
                $this->sendAlertEmail($this->option('email'), $alerts);
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Send alert email
     */
    protected function sendAlertEmail($email, $alerts)
    {
        $this->info("Sending alert email to {$email}...");

        try {
            Mail::send('emails.contract-alerts', ['alerts' => $alerts], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Contract Management System - Alerts');
            });

            $this->info('Email sent successfully.');
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
        }
    }
}
