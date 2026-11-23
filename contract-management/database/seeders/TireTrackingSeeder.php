<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Tire;
use App\Models\TireGive;
use App\Models\TireReturn;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TireTrackingSeeder extends Seeder
{
    public function run()
    {
        $drivers = [
            ['name' => 'John Smith', 'phone' => '+251912345678', 'license_number' => 'DRV001', 'license_expiry' => '2026-12-31', 'status' => true],
            ['name' => 'Mike Johnson', 'phone' => '+251912345679', 'license_number' => 'DRV002', 'license_expiry' => '2026-11-15', 'status' => true],
            ['name' => 'David Wilson', 'phone' => '+251912345680', 'license_number' => 'DRV003', 'license_expiry' => '2026-10-20', 'status' => true],
        ];

        foreach ($drivers as $driver) {
            Driver::create($driver);
        }

        $tires = [
            ['tire_type' => '12R22.5', 'serial_number' => 'TR001', 'is_active' => true, 'initial_kilometer' => 0],
            ['tire_type' => '11R22.5', 'serial_number' => 'TR002', 'is_active' => true, 'initial_kilometer' => 0],
            ['tire_type' => '12R22.5', 'serial_number' => 'TR003', 'is_active' => true, 'initial_kilometer' => 0],
            ['tire_type' => '315/70R22.5', 'serial_number' => 'TR004', 'is_active' => true, 'initial_kilometer' => 0],
            ['tire_type' => '12R22.5', 'serial_number' => 'TR005', 'is_active' => true, 'initial_kilometer' => 0],
        ];

        foreach ($tires as $tire) {
            Tire::firstOrCreate(
                ['serial_number' => $tire['serial_number']],
                $tire
            );
        }

        $tire1 = Tire::where('serial_number', 'TR001')->first();
        $tire2 = Tire::where('serial_number', 'TR002')->first();
        $driver1 = Driver::where('license_number', 'DRV001')->first();
        $driver2 = Driver::where('license_number', 'DRV002')->first();

        TireGive::create([
            'tire_id' => $tire2->id,
            'driver_id' => $driver1->id,
            'given_by' => 1,
            'given_kilometer' => 12000,
            'give_date' => Carbon::now()->subDays(5),
        ]);

        TireGive::create([
            'tire_id' => $tire1->id,
            'driver_id' => $driver2->id,
            'given_by' => 1,
            'given_kilometer' => 15000,
            'give_date' => Carbon::now()->subDays(3),
        ]);

        // Update initial_kilometer to match the first give record
        $tire1->update(['initial_kilometer' => 15000]);
        $tire2->update(['initial_kilometer' => 12000]);

        $tireGive = TireGive::where('tire_id', $tire1->id)->where('is_returned', false)->first();

        TireReturn::create([
            'tire_give_id' => $tireGive ? $tireGive->id : 1,
            'tire_id' => $tire1->id,
            'driver_id' => $driver2->id,
            'returned_by' => 1,
            'serial_number' => 'TR001',
            'tire_type' => '12R22.5',
            'current_kilometer' => 18000,
            'registered_kilometer' => 15000,
            'km_difference' => 3000,
            'notes' => 'Normal wear',
            'return_date' => Carbon::now()->subDays(1),
        ]);

        if ($tireGive) {
            $tireGive->update(['is_returned' => true]);
        }

        // Sample: Tire with multiple give/return cycles (demonstrates full lifecycle)
        $tireCycle = Tire::firstOrCreate(
            ['serial_number' => 'CYCLE001'],
            ['tire_type' => '12R22.5', 'is_active' => true, 'initial_kilometer' => 100000, 'photo' => null]
        );

        $tireCycle->tireGives()->create([
            'driver_id' => $driver1->id,
            'given_by' => 1,
            'given_kilometer' => 100500,
            'give_date' => Carbon::now()->subDays(30),
            'plate_number' => 'ABC-1001',
        ]);

        $cycleGive1 = $tireCycle->tireGives()->where('is_returned', false)->first();
        TireReturn::create([
            'tire_give_id' => $cycleGive1->id,
            'tire_id' => $tireCycle->id,
            'driver_id' => $driver1->id,
            'returned_by' => 1,
            'serial_number' => 'CYCLE001',
            'tire_type' => '12R22.5',
            'current_kilometer' => 115000,
            'registered_kilometer' => 100500,
            'km_difference' => 14500,
            'return_date' => Carbon::now()->subDays(25),
        ]);
         $cycleGive1->update(['is_returned' => true]);
    }
}
