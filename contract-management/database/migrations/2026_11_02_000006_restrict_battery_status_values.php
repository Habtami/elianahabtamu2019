<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('batteries')
            ->whereNotIn('status', ['in_service', 'returned'])
            ->update(['status' => 'in_service']);

        Schema::table('batteries', function (Blueprint $table) {
            $table->enum('status', ['in_service', 'returned'])
                ->default('in_service')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('batteries', function (Blueprint $table) {
            $table->string('status')->default('in_service')->change();
        });
    }
};
