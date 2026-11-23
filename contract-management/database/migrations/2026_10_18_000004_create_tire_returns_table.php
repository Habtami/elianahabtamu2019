<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tire_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tire_give_id')->constrained('tire_gives')->cascadeOnDelete();
            $table->foreignId('tire_id')->constrained('tires')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('returned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('return_date');
            $table->integer('current_kilometer');
            $table->integer('registered_kilometer');
            $table->integer('km_difference');
            $table->string('tire_type')->nullable();
            $table->string('plate_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tire_returns');
    }
};