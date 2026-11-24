<?php

namespace App\Console\Commands;

use App\Mail\PerformanceGuaranteeAlertMail;
use App\Models\Contract;
use Database\Seeders\ContractTestSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestPerformanceGuaranteeAlert extends Command
{
    protected $signature = 'contracts:send-test-alert
                            {--to= : Override the recipient email (defaults to services.legal_system.test_recipient)}
                            {--id= : Use a specific contract instead of the seeded test contract}';

    protected $description = 'Send a sample performance guarantee alert email immediately (bypasses the queue)';

    public function handle(): int
    {
        $recipient = mb_strtolower(trim((string) ($this->option('to') ?: config('services.legal_system.test_recipient'))));

        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $this->error('A valid recipient email is required. Pass --to=email@example.com or set LEGAL_SYSTEM_TEST_RECIPIENT.');

            return Command::INVALID;
        }

        $contract = $this->resolveContract();

        if (! $contract instanceof Contract) {
            return Command::FAILURE;
        }

        try {
            Mail::to($recipient)->sendNow(new PerformanceGuaranteeAlertMail($contract));
        } catch (\Throwable $e) {
            $this->error("Failed to send the test alert: {$e->getMessage()}");

            return Command::FAILURE;
        }

        $this->info("Test alert sent to {$recipient} using contract #{$contract->id} ({$contract->name}).");
        $this->line('guarantee_alert_sent_at was left untouched, so the scheduled alert can still fire.');

        return Command::SUCCESS;
    }

    private function resolveContract(): ?Contract
    {
        if ($id = $this->option('id')) {
            $contract = Contract::find($id);

            if (! $contract) {
                $this->error("Contract #{$id} was not found.");
            }

            return $contract;
        }

        $contract = Contract::query()
            ->where('name', ContractTestSeeder::TEST_CONTRACT_NAME)
            ->latest('id')
            ->first();

        if (! $contract) {
            $this->error('The seeded test contract was not found. Run: php artisan db:seed --class=ContractTestSeeder');
        }

        return $contract;
    }
}
