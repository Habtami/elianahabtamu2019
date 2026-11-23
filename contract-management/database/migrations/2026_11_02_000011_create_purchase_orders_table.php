<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->date('approval_date');
            $table->unsignedBigInteger('gs_manager_approved_by')->nullable();
            $table->timestamp('gs_manager_approved_at')->nullable();
            $table->unsignedBigInteger('ddg_approved_by')->nullable();
            $table->timestamp('ddg_approved_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
