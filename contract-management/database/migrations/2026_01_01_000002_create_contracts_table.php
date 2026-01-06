<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('department', ['cmd', 'general_service', 'finance']);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('alert_days');
            $table->decimal('performance_guarantee_amount', 10, 2)->nullable();
            $table->date('performance_guarantee_end_date')->nullable();
            $table->integer('performance_guarantee_alert_days')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->enum('status', ['active', 'expiring', 'expired'])->default('active');
            $table->integer('extended_count')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contracts');
    }
};