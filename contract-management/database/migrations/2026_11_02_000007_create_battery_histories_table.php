<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('battery_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('battery_id')->constrained()->cascadeOnDelete();
            $table->string('action');
            $table->string('status')->nullable();
            $table->string('driver_name')->nullable();
            $table->integer('initial_kilometer')->nullable();
            $table->integer('return_kilometer')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        $now = now();
        $rows = DB::table('batteries')->orderBy('id')->get()->map(function ($battery) use ($now) {
            return [
                'battery_id' => $battery->id,
                'action' => 'registered',
                'status' => $battery->status,
                'driver_name' => $battery->driver_name,
                'initial_kilometer' => $battery->initial_kilometer,
                'return_kilometer' => $battery->return_kilometer,
                'notes' => 'Initial record',
                'created_by' => $battery->created_by,
                'created_at' => $battery->created_at ?? $now,
                'updated_at' => $battery->created_at ?? $now,
            ];
        })->all();

        if (!empty($rows)) {
            DB::table('battery_histories')->insert($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('battery_histories');
    }
};
