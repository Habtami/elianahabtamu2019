<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchasing_activities', function (Blueprint $table) {
            $table->text('remarks')->nullable()->after('procurement_type');
        });
    }

    public function down(): void
    {
        Schema::table('purchasing_activities', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
};
