<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SetupDatabase extends Command
{
    protected $signature = 'setup:database';

    protected $description = 'Setup the complete database structure for Contract Management System';

    public function handle()
    {
        $this->info('=== Contract Management System Database Setup ===');

        if (! $this->confirm('This will modify your database structure. Continue?')) {
            $this->info('Setup cancelled.');

            return;
        }

        $this->setupDatabase();
        $this->createDefaultUsers();

        $this->info(PHP_EOL.'✅ Database setup completed successfully!');
        $this->info('You can now login with:');
        $this->info('Email: admin@epss.gov.et');
        $this->info('Password: admin123');
    }

    private function setupDatabase()
    {
        $this->info(PHP_EOL.'Setting up database structure...');

        DB::transaction(function () {
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            // 1. Update users table
            $this->updateUsersTable();

            // 2. Create contracts table
            $this->createContractsTable();

            // 3. Create activity_logs table
            $this->createActivityLogsTable();

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        });
    }

    private function updateUsersTable()
    {
        $this->info('Updating users table...');

        // Add role column if not exists
        if (! Schema::hasColumn('users', 'role')) {
            DB::statement("
                ALTER TABLE users 
                ADD COLUMN role ENUM('admin', 'general_service', 'cmd', 'finance', 'gs_manager', 'cmd_manager') 
                NOT NULL DEFAULT 'general_service'
                AFTER password
            ");
            $this->info('✓ Added role column');
        }

        // Add department column if not exists
        if (! Schema::hasColumn('users', 'department')) {
            DB::statement("
                ALTER TABLE users 
                ADD COLUMN department ENUM('cmd', 'general_service', 'finance') 
                NOT NULL DEFAULT 'general_service'
                AFTER role
            ");
            $this->info('✓ Added department column');
        }

        // Add status column if not exists
        if (! Schema::hasColumn('users', 'status')) {
            DB::statement('
                ALTER TABLE users 
                ADD COLUMN status BOOLEAN NOT NULL DEFAULT true
                AFTER department
            ');
            $this->info('✓ Added status column');
        }
    }

    private function createContractsTable()
    {
        $this->info('Creating contracts table...');

        if (! Schema::hasTable('contracts')) {
            DB::statement("
                CREATE TABLE contracts (
                    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    description TEXT,
                    department ENUM('cmd', 'general_service', 'finance') NOT NULL,
                    start_date DATE NOT NULL,
                    end_date DATE NOT NULL,
                    alert_days INT NOT NULL,
                    performance_guarantee_amount DECIMAL(10,2) NULL,
                    performance_guarantee_end_date DATE NULL,
                    alert_days_before_guarantee_end INT NULL,
                    guarantee_alert_sent_at TIMESTAMP NULL,
                    created_by INT UNSIGNED NOT NULL,
                    status ENUM('active', 'expiring', 'expired') DEFAULT 'active',
                    extended_count INT DEFAULT 0,
                    deleted_at TIMESTAMP NULL,
                    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
                    INDEX idx_department (department),
                    INDEX idx_status (status),
                    INDEX idx_end_date (end_date)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            $this->info('✓ Created contracts table');
        } else {
            $this->info('✓ Contracts table already exists');
        }
    }

    private function createActivityLogsTable()
    {
        $this->info('Creating activity_logs table...');

        if (! Schema::hasTable('activity_logs')) {
            DB::statement("
                CREATE TABLE activity_logs (
                    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    user_id INT UNSIGNED NULL,
                    activity_type VARCHAR(255) NOT NULL DEFAULT 'contract_update',
                    severity ENUM('info', 'warning', 'danger') NOT NULL DEFAULT 'info',
                    contract_id INT UNSIGNED NULL,
                    driver_id INT UNSIGNED NULL,
                    field_name VARCHAR(255) NOT NULL,
                    old_value TEXT,
                    new_value TEXT,
                    ip_address VARCHAR(45) NULL,
                    user_agent TEXT NULL,
                    edited_by VARCHAR(255) NOT NULL,
                    reason TEXT,
                    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
                    FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE,
                    INDEX idx_contract_id (contract_id),
                    INDEX idx_driver_id (driver_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            $this->info('✓ Created activity_logs table');
        } else {
            $this->info('✓ Activity_logs table already exists');
        }
    }

    private function createDefaultUsers()
    {
        $this->info(PHP_EOL.'Creating default users...');

        $users = [
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

        foreach ($users as $userData) {
            $existingUser = User::where('email', $userData['email'])->first();

            if ($existingUser) {
                // Update existing user
                $existingUser->update([
                    'role' => $userData['role'],
                    'department' => $userData['department'],
                    'status' => $userData['status'],
                ]);
                $this->info("✓ Updated: {$userData['email']}");
            } else {
                // Create new user
                User::create($userData);
                $this->info("✓ Created: {$userData['email']}");
            }
        }
    }
}
