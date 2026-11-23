<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $defaults = [
        'Stationery',
        'Sanitary',
        'Service',
        'Spare Parts',
        'Electronics',
        'Uniform',
        'Printing',
        'Other Services',
        'Work',
        'Maintenance',
        'Machinery',
        'Furniture',
    ];

    public function up(): void
    {
        Schema::create('material_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        $rows = [];

        foreach ($this->defaults as $index => $name) {
            $rows[] = [
                'name' => $name,
                'is_active' => true,
                'sort_order' => $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('material_groups')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('material_groups');
    }
};
