<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixDatabase extends Command
{
    protected $signature = 'db:fix';
    protected $description = 'Fix database structure for contract management system';

    public function handle()
    {
        $this->info('Fixing database structure...');

        // 1. Fix users table
        $this->fixUsersTable();

        // 2. Create contracts table
        $this->createContractsTable();

        // 3. Create activity_logs table
        $this->createActivityLogsTable();

        $this->info('Database structure fixed successfully!');
    }

    private function fixUsersTable()
    {
        $this->info('Checking users table...');

        // Check and fix id column
        $columnInfo = DB::select("SHOW COLUMNS FROM users WHERE Field = 'id'");
        if ($columnInfo) {
            $type = $columnInfo[0]->Type;
            if (strpos($type, 'bigint') === false) {
                $this->warn('Fixing users.id column type...');
                DB::statement("ALTER TABLE users MODIFY id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL");
            }
        }

        // Add role column if not exists
        if (!Schema::hasColumn('users', 'role')) {
            $this->info('Adding role column to users table...');
            DB::statement("ALTER TABLE users ADD COLUMN role ENUM('admin', 'general_service', 'cmd', 'finance', 'gs_manager', 'cmd_manager') NOT NULL DEFAULT 'general_service'");
        }

        // Add department column if not exists
        if (!Schema::hasColumn('users', 'department')) {
            $this->info('Adding department column to users table...');
            DB::statement("ALTER TABLE users ADD COLUMN department ENUM('cmd', 'general_service', 'finance') NOT NULL DEFAULT 'general_service'");
        }

        // Add status column if not exists
        if (!Schema::hasColumn('users', 'status')) {
            $this->info('Adding status column to users table...');
            DB::statement("ALTER TABLE users ADD COLUMN status BOOLEAN NOT NULL DEFAULT true");
        }
    }

    private function createContractsTable()
    {
        $this->info('Creating contracts table...');

        if (!Schema::hasTable('contracts')) {
            DB::statement("
                CREATE TABLE contracts (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    description TEXT,
                    department ENUM('cmd', 'general_service', 'finance') NOT NULL,
                    start_date DATE NOT NULL,
                    end_date DATE NOT NULL,
                    alert_days INT NOT NULL,
                    performance_guarantee_amount DECIMAL(10,2),
                    performance_guarantee_end_date DATE,
                    performance_guarantee_alert_days INT,
                    created_by BIGINT UNSIGNED NOT NULL,
                    status ENUM('active', 'expiring', 'expired') DEFAULT 'active',
                    extended_count INT DEFAULT 0,
                    deleted_at TIMESTAMP NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // Add foreign key
            try {
                DB::statement("ALTER TABLE contracts ADD FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE");
            } catch (\Exception $e) {
                $this->warn('Could not add foreign key constraint: ' . $e->getMessage());
            }
        }
    }

    private function createActivityLogsTable()
    {
        $this->info('Creating activity_logs table...');

        if (!Schema::hasTable('activity_logs')) {
            DB::statement("
                CREATE TABLE activity_logs (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    contract_id BIGINT UNSIGNED NOT NULL,
                    field_name VARCHAR(255) NOT NULL,
                    old_value TEXT,
                    new_value TEXT,
                    edited_by VARCHAR(255) NOT NULL,
                    reason TEXT,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }
}