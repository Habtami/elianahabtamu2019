<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, enum is TEXT, no need to alter
        // For MySQL, this would modify the enum, but since we're using SQLite, skip
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed for SQLite
    }
};
