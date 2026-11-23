<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY department ENUM('cmd','general_service','finance','ict','ddg_fpa_gs') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE users SET department = 'general_service' WHERE department = 'ddg_fpa_gs'");
            DB::statement("ALTER TABLE users MODIFY department ENUM('cmd','general_service','finance','ict') NOT NULL");
        }
    }
};
