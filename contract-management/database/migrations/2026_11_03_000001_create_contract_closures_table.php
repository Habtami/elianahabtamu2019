<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_closures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id')->unique();
            $table->date('closing_date');
            $table->string('status')->default('pending');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_closures');
    }
};
