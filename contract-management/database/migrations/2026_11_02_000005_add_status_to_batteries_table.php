<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batteries', function (Blueprint $table) {
            $table->string('status')->default('in_service')->after('return_kilometer');
        });

        DB::table('batteries')->update([
            'status' => DB::raw("CASE WHEN return_kilometer > initial_kilometer THEN 'returned' ELSE 'in_service' END"),
        ]);
    }

    public function down(): void
    {
        Schema::table('batteries', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
