<?php

namespace App\Console\Commands;

use App\Mail\ContractExpiryWarning;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class SendContractExpiryWarnings extends Command
{
    protected $signature = 'contracts:send-expiry-warnings {--dry-run : List recipients without sending emails}';

    protected $description = 'Email authorized users about expiring and expired contracts';

    public function handle()
    {
        $rows = $this->collectContracts();

        if ($rows->isEmpty()) {
            $this->info('No expiring or expired contracts found.');
            return Command::SUCCESS;
        }

        $users = User::where('status', true)
            ->whereIn('role', [
                User::ROLE_GENERAL_SERVICE,
                User::ROLE_CMD,
                User::ROLE_FINANCE,
            ])
            ->orderBy('name')
            ->get();

        $sent = 0;

        foreach ($users as $user) {
            $mine = $rows->filter(fn ($row) => $row['contract']->canUserEdit($user));

            if ($mine->isEmpty()) {
                continue;
            }

            $expiring = $mine->where('bucket', 'expiring')->sortBy(fn ($row) => $row['days'])->values();
            $expired = $mine->where('bucket', 'expired')->sortByDesc(fn ($row) => $row['days'])->values();

            if ($this->option('dry-run')) {
                $this->line(sprintf(
                    '[dry-run] %s <%s>: %d expiring, %d expired',
                    $user->name,
                    $user->email,
                    $expiring->count(),
                    $expired->count()
                ));
                continue;
            }

            try {
                Mail::to($user->email)->send(new ContractExpiryWarning($user, $expiring, $expired));
                $sent++;
                $this->line("Sent warning to {$user->name} <{$user->email}>");
            } catch (\Throwable $e) {
                $this->error("Failed to send to {$user->email}: {$e->getMessage()}");
            }
        }

        if ($sent === 0 && !$this->option('dry-run')) {
            $this->info('No authorized recipients required a warning email.');
        } elseif (!$this->option('dry-run')) {
            $this->info("Sent {$sent} warning email(s).");
        }

        return Command::SUCCESS;
    }

    private function collectContracts(): Collection
    {
        return Contract::whereNotNull('end_date')
            ->get()
            ->map(function (Contract $contract) {
                $days = $contract->getDaysUntilContractEnd();

                if ($days < 0) {
                    return ['contract' => $contract, 'bucket' => 'expired', 'days' => abs($days)];
                }

                if ($days <= (int) $contract->alert_days) {
                    return ['contract' => $contract, 'bucket' => 'expiring', 'days' => $days];
                }

                return null;
            })
            ->filter()
            ->values();
    }
}
