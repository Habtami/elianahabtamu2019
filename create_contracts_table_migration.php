<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('alert_days_before')->default(30); // Days before end date to alert

            // Performance guarantee fields
            $table->decimal('performance_guarantee_amount', 15, 2)->nullable();
            $table->date('performance_guarantee_end_date')->nullable();
            $table->integer('performance_guarantee_alert_days')->default(30); // Days before PG end date to alert

            // Edit counters
            $table->integer('contract_end_date_edit_count')->default(0);
            $table->integer('performance_guarantee_edit_count')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
