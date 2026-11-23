<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchasing_activities', function (Blueprint $table) {
            $table->id();
            $table->date('request_date');
            $table->string('document_type');
            $table->string('material_group');
            $table->string('unit', 50);
            $table->string('procurement_type');
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchasing_activities');
    }
};
