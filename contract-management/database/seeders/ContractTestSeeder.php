<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContractTestSeeder extends Seeder
{
    public const TEST_CONTRACT_NAME = 'Test Contract for Email Notification';

    public function run(): void
    {
        $creatorId = User::query()->orderBy('id')->value('id');

        if (! $creatorId) {
            $this->command?->error('No user exists to own the test contract. Create a user first, then rerun this seeder.');

            return;
        }

        $contract = Contract::updateOrCreate(
            ['name' => self::TEST_CONTRACT_NAME],
            [
                'description' => 'This is a test contract to verify the performance guarantee alert email.',
                'supplier_name' => 'Habtamu Trading PLC',
                'department' => 'cmd',
                'contract_type' => 'NCB',
                'material_group' => Contract::getMaterialGroups()[0] ?? 'General',
                'contract_amount' => 250000.00,
                'start_date' => now()->subMonths(6)->toDateString(),
                'end_date' => now()->addMonths(6)->toDateString(),
                'alert_days' => 30,
                'performance_bond_type' => Contract::getPerformanceBondTypes()[0] ?? 'CPO',
                'issued_by' => 'Procurement Department',
                'currency_type' => 'ETB',
                'performance_guarantee_amount' => 25000.00,
                'performance_guarantee_end_date' => now()->addDays(30)->toDateString(),
                'alert_days_before_guarantee_end' => 30,
                'guarantee_alert_sent_at' => null,
                'created_by' => $creatorId,
                'status' => Contract::STATUS_ACTIVE,
            ]
        );

        $this->command?->info(
            "Seeded test contract #{$contract->id} ({$contract->name}); its performance guarantee alert is due today."
        );
    }
}
