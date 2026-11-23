<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batteries', function (Blueprint $table) {
            $table->id();
            $table->date('date_of_manufacture')->nullable();
            $table->string('serial_number')->unique();
            $table->string('battery_type');
            $table->string('brand');
            $table->string('plate_number');
            $table->string('driver_name')->nullable();
            $table->integer('initial_kilometer')->default(0);
            $table->integer('return_kilometer')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batteries');
    }
};
