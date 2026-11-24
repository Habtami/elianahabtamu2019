<?php

namespace App\Console\Commands;

use App\Mail\PerformanceGuaranteeAlertMail;
use App\Models\Contract;
use App\Services\LegalSystemService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPerformanceGuaranteeAlerts extends Command
{
    protected $signature = 'contracts:send-guarantee-alerts {--dry-run : List due contracts without sending emails}';

    protected $description = 'Email the concerned party when the performance guarantee alert day is reached';

    public function handle(LegalSystemService $legalSystem): int
    {
        $today = Carbon::today();

        $due = Contract::query()
            ->whereNotNull('performance_guarantee_end_date')
            ->whereNotNull('alert_days_before_guarantee_end')
            ->whereNull('guarantee_alert_sent_at')
            ->get()
            ->filter(function (Contract $contract) use ($today) {
                return $contract->performance_guarantee_end_date
                    ->copy()
                    ->startOfDay()
                    ->subDays((int) $contract->alert_days_before_guarantee_end)
                    ->isSameDay($today);
            });

        if ($due->isEmpty()) {
            $this->info('No performance guarantee alerts are due today.');
            return Command::SUCCESS;
        }

        $sent = 0;

        foreach ($due as $contract) {
            if ($this->option('dry-run')) {
                $this->line(sprintf(
                    '[dry-run] #%d %s — guarantee ends %s, alert %d day(s) before',
                    $contract->id,
                    $contract->name,
                    $contract->performance_guarantee_end_date->format('Y-m-d'),
                    (int) $contract->alert_days_before_guarantee_end
                ));
                continue;
            }

            $recipients = $legalSystem->getConcernedPartyEmails($contract);

            if ($recipients === []) {
                Log::error('Performance guarantee alert skipped: no recipient email could be resolved.', [
                    'contract_id' => $contract->id,
                ]);
                $this->error("No recipient resolved for contract #{$contract->id} ({$contract->name}).");
                continue;
            }

            try {
                Mail::to($recipients)->queue(new PerformanceGuaranteeAlertMail($contract));

                $contract->forceFill(['guarantee_alert_sent_at' => now()])->save();
                $sent++;

                $this->line(sprintf(
                    'Sent performance guarantee alert for #%d to %s',
                    $contract->id,
                    implode(', ', $recipients)
                ));
            } catch (\Throwable $e) {
                Log::error('Failed to queue the performance guarantee alert.', [
                    'contract_id' => $contract->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Failed to send alert for contract #{$contract->id}: {$e->getMessage()}");
            }
        }

        if (! $this->option('dry-run')) {
            $this->info("Sent {$sent} performance guarantee alert email(s).");
        }

        return Command::SUCCESS;
    }
}
