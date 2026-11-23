<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tire_gives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tire_id')->constrained('tires')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->foreignId('given_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('give_date');
            $table->integer('given_kilometer');
            $table->string('plate_number')->nullable();
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_returned')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tire_gives');
    }
};