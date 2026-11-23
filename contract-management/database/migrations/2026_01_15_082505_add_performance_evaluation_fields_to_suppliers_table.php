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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->decimal('quality_rating', 5, 2)->nullable()->after('status'); // e.g., 95.00 for 95%
            $table->decimal('delivery_rating', 5, 2)->nullable()->after('quality_rating'); // e.g., 98.00 for 98%
            $table->decimal('cost_rating', 3, 1)->nullable()->after('delivery_rating'); // e.g., 4.5 for 4.5/5
            $table->string('communication_rating')->nullable()->after('cost_rating'); // e.g., "Excellent", "Good"
            $table->decimal('overall_rating', 3, 1)->nullable()->after('communication_rating'); // calculated rating
            $table->enum('performance_status', ['Top Performer', 'Average', 'Needs Improvement'])->nullable()->after('overall_rating');
            $table->timestamp('last_evaluation_date')->nullable()->after('performance_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn([
                'quality_rating',
                'delivery_rating',
                'cost_rating',
                'communication_rating',
                'overall_rating',
                'performance_status',
                'last_evaluation_date'
            ]);
        });
    }
};
