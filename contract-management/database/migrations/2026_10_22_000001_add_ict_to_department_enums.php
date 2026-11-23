<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY department ENUM('cmd','general_service','finance','ict') NOT NULL");
            DB::statement("ALTER TABLE contracts MODIFY department ENUM('cmd','general_service','finance','ict') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE contracts MODIFY department ENUM('cmd','general_service','finance') NOT NULL");
            DB::statement("ALTER TABLE users MODIFY department ENUM('cmd','general_service','finance') NOT NULL");
        }
    }
};
