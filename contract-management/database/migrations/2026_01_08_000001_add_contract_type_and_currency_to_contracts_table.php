<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->enum('contract_type', ['ICB', 'NCB'])->nullable()->after('department');
            $table->enum('currency_type', ['Dollar', 'ETB'])->nullable()->after('performance_guarantee_amount');
        });
    }

    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['contract_type', 'currency_type']);
        });
    }
};
