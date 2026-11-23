<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->date('payment_request_date');
            $table->unsignedBigInteger('gs_approved_by')->nullable();
            $table->dateTime('gs_approved_at')->nullable();
            $table->unsignedBigInteger('finance_approved_by')->nullable();
            $table->dateTime('finance_approved_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};
