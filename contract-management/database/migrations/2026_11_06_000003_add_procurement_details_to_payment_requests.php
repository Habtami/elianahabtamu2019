<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->string('document_no')->nullable()->after('payment_request_date');
            $table->string('payment_type')->nullable()->after('document_no');
            $table->decimal('amount', 15, 2)->nullable()->after('payment_type');
            $table->decimal('quantity', 15, 2)->nullable()->after('amount');
            $table->string('material_group')->nullable()->after('quantity');
            $table->string('procurement_type')->nullable()->after('material_group');
        });
    }

    public function down(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->dropColumn([
                'document_no',
                'payment_type',
                'amount',
                'quantity',
                'material_group',
                'procurement_type',
            ]);
        });
    }
};
