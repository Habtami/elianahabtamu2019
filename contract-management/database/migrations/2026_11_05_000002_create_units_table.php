<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $defaults = [
        'Pcs',
        'Kg',
        'Liter',
        'Box',
        'Pack',
        'Roll',
        'Set',
        'Pair',
        'Meter',
        'Square Meter',
        'Carton',
        'Dozen',
        'Bottle',
        'Bag',
        'Ream',
        'Tube',
        'Can',
        'Bundle',
        'Drum',
    ];

    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        $names = $this->defaults;
        $known = array_map('strtolower', $names);

        // Keep units already used on existing purchasing activities selectable.
        if (Schema::hasTable('purchasing_activities')) {
            $existing = DB::table('purchasing_activities')
                ->whereNotNull('unit')
                ->distinct()
                ->pluck('unit');

            foreach ($existing as $unit) {
                $unit = trim((string) $unit);

                if ($unit === '' || in_array(strtolower($unit), $known, true)) {
                    continue;
                }

                $names[] = $unit;
                $known[] = strtolower($unit);
            }
        }

        $now = now();
        $rows = [];

        foreach ($names as $index => $name) {
            $rows[] = [
                'name' => $name,
                'is_active' => true,
                'sort_order' => $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('units')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
