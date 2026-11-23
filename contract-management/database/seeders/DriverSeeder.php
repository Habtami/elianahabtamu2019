<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    public function run()
    {
        $drivers = [
            ['name' => 'John Smith', 'phone' => '+251912345678', 'license_number' => 'DRV001', 'license_expiry' => '2026-12-31', 'status' => true],
            ['name' => 'Mike Johnson', 'phone' => '+251912345679', 'license_number' => 'DRV002', 'license_expiry' => '2026-11-15', 'status' => true],
            ['name' => 'David Wilson', 'phone' => '+251912345680', 'license_number' => 'DRV003', 'license_expiry' => '2026-10-20', 'status' => true],
            ['name' => 'James Brown', 'phone' => '+251912345681', 'license_number' => 'DRV004', 'license_expiry' => '2026-09-30', 'status' => false],
            ['name' => 'Robert Davis', 'phone' => '+251912345682', 'license_number' => 'DRV005', 'license_expiry' => '2026-11-01', 'status' => true],
        ];

        foreach ($drivers as $driver) {
            Driver::firstOrCreate(
                ['license_number' => $driver['license_number']],
                $driver
            );
        }
    }
}
