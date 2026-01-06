<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->decimal('performance_guarantee_amount', 15, 2)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->decimal('performance_guarantee_amount', 10, 2)->nullable()->change();
        });
    }
};
