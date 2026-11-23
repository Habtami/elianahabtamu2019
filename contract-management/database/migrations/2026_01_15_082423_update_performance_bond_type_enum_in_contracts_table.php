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
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('performance_bond_type');
            $table->enum('performance_bond_type', ['CPO', 'Bank Guarantee', 'Insurance Bond', 'Small Enterprise Letter'])->nullable()->after('currency_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('performance_bond_type');
            $table->enum('performance_bond_type', ['CPO', 'Bank Guaranty', 'Insurance Bond'])->nullable()->after('currency_type');
        });
    }
};
