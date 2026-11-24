<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('contracts', 'alert_days_before_guarantee_end')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->integer('alert_days_before_guarantee_end')
                    ->nullable()
                    ->after('performance_guarantee_alert_days');
            });
        }

        if (! Schema::hasColumn('contracts', 'guarantee_alert_sent_at')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->timestamp('guarantee_alert_sent_at')
                    ->nullable()
                    ->after('alert_days_before_guarantee_end');
            });
        }

        // Preserve alert days entered before the column was introduced.
        if (Schema::hasColumn('contracts', 'performance_guarantee_alert_days')) {
            DB::table('contracts')
                ->whereNull('alert_days_before_guarantee_end')
                ->whereNotNull('performance_guarantee_alert_days')
                ->update([
                    'alert_days_before_guarantee_end' => DB::raw('performance_guarantee_alert_days'),
                ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('contracts', 'guarantee_alert_sent_at')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->dropColumn('guarantee_alert_sent_at');
            });
        }

        if (Schema::hasColumn('contracts', 'alert_days_before_guarantee_end')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->dropColumn('alert_days_before_guarantee_end');
            });
        }
    }
};
