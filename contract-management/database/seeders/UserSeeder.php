<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // First, let's check what users already exist
        $existingUsers = User::pluck('email')->toArray();
        
        $users = [
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@epss.gov.et',
                'password' => Hash::make('admin123'),
                'role' => User::ROLE_SUPER_ADMIN,
                'department' => User::DEPARTMENT_ICT,
                'status' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'System Administrator',
                'email' => 'admin@epss.gov.et',
                'password' => Hash::make('admin123'),
                'role' => User::ROLE_ADMIN,
                'department' => User::DEPARTMENT_CMD,
                'status' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'General Service Officer',
                'email' => 'gs.officer@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_GENERAL_SERVICE,
                'department' => User::DEPARTMENT_GENERAL_SERVICE,
                'status' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'CMD Officer',
                'email' => 'cmd.officer@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CMD,
                'department' => User::DEPARTMENT_CMD,
                'status' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Finance Officer',
                'email' => 'finance.officer@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_FINANCE,
                'department' => User::DEPARTMENT_FINANCE,
                'status' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'GS Manager',
                'email' => 'gs.manager@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_GS_MANAGER,
                'department' => User::DEPARTMENT_GENERAL_SERVICE,
                'status' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'CMD Manager',
                'email' => 'cmd.manager@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CMD_MANAGER,
                'department' => User::DEPARTMENT_CMD,
                'status' => true,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            if (!in_array($user['email'], $existingUsers)) {
                User::create($user);
            } else {
                // Update existing user with new role and department
                User::where('email', $user['email'])->update([
                    'role' => $user['role'],
                    'department' => $user['department'],
                    'status' => $user['status'],
                ]);
            }
        }

        // If there are existing users without roles, set default roles
        User::whereNull('role')->orWhere('role', '')->update([
            'role' => User::ROLE_GENERAL_SERVICE,
            'department' => User::DEPARTMENT_GENERAL_SERVICE,
            'status' => true,
        ]);
    }
}