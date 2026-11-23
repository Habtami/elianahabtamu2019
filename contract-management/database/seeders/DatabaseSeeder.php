<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            DriverSeeder::class,
            TireTrackingSeeder::class,
        ]);

        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make(config('app.default_password')), 'role' => 'super_admin', 'must_change_password' => true, 'email_verified_at' => now()]
        );
        if (!$user->wasRecentlyCreated) {
            $user->update(['password' => Hash::make(config('app.default_password')), 'role' => 'super_admin', 'email_verified_at' => now()]);
        }
    }
}
