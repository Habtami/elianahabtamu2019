<?php

namespace Database\Seeders;

use App\Models\Battery;
use App\Models\BatteryHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class BatteryIssueSamplesSeeder extends Seeder
{
    /**
     * Insert demo batteries that trigger each check in the Battery Report.
     * Serials are prefixed with SAMPLE- so they can be found and removed easily.
     */
    public function run(): void
    {
        $userId = User::query()->orderBy('id')->value('id');

        $samples = [
            [
                'serial_number' => 'SAMPLE-HIGH-KM-001',
                'material_code' => '1000000502',
                'battery_type' => 'Car Battery 12V 90A',
                'brand' => 'Varta',
                'plate_number' => 'AA-1001',
                'driver_name' => 'Sample Driver One',
                'initial_kilometer' => 12000,
                'return_kilometer' => 900,
                'status' => Battery::STATUS_RETURNED,
                'date_of_manufacture' => '2025-03-10',
                'given_at' => '2026-06-10',
                'with_history' => true,
            ],
            [
                'serial_number' => 'SAMPLE-HIGH-NOREAD-002',
                'material_code' => '1000000500',
                'battery_type' => 'Car Battery 12V 120A',
                'brand' => 'Bosch',
                'plate_number' => 'AA-1002',
                'driver_name' => 'Sample Driver Two',
                'initial_kilometer' => 8500,
                'return_kilometer' => 0,
                'status' => Battery::STATUS_RETURNED,
                'date_of_manufacture' => '2025-04-15',
                'given_at' => '2026-06-28',
                'with_history' => true,
            ],
            [
                'serial_number' => 'SAMPLE-MED-DRIVER-003',
                'material_code' => '1000000501',
                'battery_type' => 'Car Battery 12V 150A',
                'brand' => 'Exide',
                'plate_number' => 'AA-1003',
                'driver_name' => null,
                'initial_kilometer' => 15000,
                'return_kilometer' => 0,
                'status' => Battery::STATUS_IN_SERVICE,
                'date_of_manufacture' => '2025-05-20',
                'given_at' => '2026-07-08',
                'with_history' => true,
            ],
            [
                'serial_number' => 'SAMPLE-MED-PLATE-004',
                'material_code' => '1000000496',
                'battery_type' => 'Car Battery 12V 100A',
                'brand' => 'Optima',
                'plate_number' => '',
                'driver_name' => 'Sample Driver Four',
                'initial_kilometer' => 22000,
                'return_kilometer' => 0,
                'status' => Battery::STATUS_IN_SERVICE,
                'date_of_manufacture' => '2025-06-01',
                'given_at' => '2026-07-22',
                'with_history' => true,
            ],
            [
                'serial_number' => 'SAMPLE-MED-CODE-005',
                'material_code' => null,
                'battery_type' => 'Car Battery 12V 60A',
                'brand' => 'ACDelco',
                'plate_number' => 'AA-1005',
                'driver_name' => 'Sample Driver Five',
                'initial_kilometer' => 6400,
                'return_kilometer' => 0,
                'status' => Battery::STATUS_IN_SERVICE,
                'date_of_manufacture' => '2025-06-12',
                'given_at' => '2026-08-05',
                'with_history' => true,
            ],
            [
                'serial_number' => 'SAMPLE-MED-TYPE-006',
                'material_code' => null,
                'battery_type' => '',
                'brand' => 'Varta',
                'plate_number' => 'AA-1006',
                'driver_name' => 'Sample Driver Six',
                'initial_kilometer' => 3300,
                'return_kilometer' => 0,
                'status' => Battery::STATUS_IN_SERVICE,
                'date_of_manufacture' => '2025-07-05',
                'given_at' => '2026-08-19',
                'with_history' => true,
            ],
            [
                'serial_number' => 'SAMPLE-LOW-BRAND-007',
                'material_code' => '1000000495',
                'battery_type' => 'Car Battery 12V 70A',
                'brand' => '',
                'plate_number' => 'AA-1007',
                'driver_name' => 'Sample Driver Seven',
                'initial_kilometer' => 23000,
                'return_kilometer' => 0,
                'status' => Battery::STATUS_IN_SERVICE,
                'date_of_manufacture' => '2025-07-18',
                'given_at' => '2026-09-03',
                'with_history' => true,
            ],
            [
                'serial_number' => 'SAMPLE-LOW-DATE-008',
                'material_code' => '1000000497',
                'battery_type' => 'Car Battery 12V 50A',
                'brand' => 'Bosch',
                'plate_number' => 'AA-1008',
                'driver_name' => 'Sample Driver Eight',
                'initial_kilometer' => 9100,
                'return_kilometer' => 0,
                'status' => Battery::STATUS_IN_SERVICE,
                'date_of_manufacture' => null,
                'given_at' => '2026-09-21',
                'with_history' => true,
            ],
            [
                'serial_number' => 'SAMPLE-LOW-HISTORY-009',
                'material_code' => '1000000400',
                'battery_type' => 'Car Battery 12V 75A',
                'brand' => 'Exide',
                'plate_number' => 'AA-1009',
                'driver_name' => 'Sample Driver Nine',
                'initial_kilometer' => 11200,
                'return_kilometer' => 0,
                'status' => Battery::STATUS_IN_SERVICE,
                'date_of_manufacture' => '2025-08-02',
                'given_at' => '2026-10-06',
                'with_history' => false,
            ],
        ];

        $inserted = 0;

        foreach ($samples as $sample) {
            if (Battery::withTrashed()->where('serial_number', $sample['serial_number'])->exists()) {
                continue;
            }

            $battery = Battery::create([
                'serial_number' => $sample['serial_number'],
                'material_code' => $sample['material_code'],
                'battery_type' => $sample['battery_type'],
                'brand' => $sample['brand'],
                'plate_number' => $sample['plate_number'],
                'driver_name' => $sample['driver_name'],
                'initial_kilometer' => $sample['initial_kilometer'],
                'return_kilometer' => $sample['return_kilometer'],
                'status' => $sample['status'],
                'date_of_manufacture' => $sample['date_of_manufacture'],
                'is_active' => true,
                'created_by' => $userId,
            ]);

            $battery->forceFill([
                'created_at' => $sample['given_at'] . ' 09:00:00',
                'updated_at' => $sample['given_at'] . ' 09:00:00',
            ])->save();

            if ($sample['with_history']) {
                BatteryHistory::create([
                    'battery_id' => $battery->id,
                    'action' => 'registered',
                    'status' => $sample['status'],
                    'driver_name' => $sample['driver_name'],
                    'initial_kilometer' => $sample['initial_kilometer'],
                    'return_kilometer' => null,
                    'notes' => 'Sample data for the Battery Report demonstration.',
                    'created_by' => $userId,
                ]);

                if ($sample['status'] === Battery::STATUS_RETURNED) {
                    BatteryHistory::create([
                        'battery_id' => $battery->id,
                        'action' => 'returned',
                        'status' => Battery::STATUS_RETURNED,
                        'driver_name' => $sample['driver_name'],
                        'initial_kilometer' => $sample['initial_kilometer'],
                        'return_kilometer' => $sample['return_kilometer'],
                        'notes' => 'Sample return history for the Battery Report demonstration.',
                        'created_by' => $userId,
                    ]);
                }
            }

            $inserted++;
        }

        $this->command?->info('Inserted ' . $inserted . ' battery issue sample(s); existing samples were skipped.');
    }
}
