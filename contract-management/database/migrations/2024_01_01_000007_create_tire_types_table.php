<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tire_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('category')->nullable();
            $table->decimal('width', 5, 1)->nullable();
            $table->decimal('aspect_ratio', 5, 1)->nullable();
            $table->decimal('rim_diameter', 5, 1)->nullable();
            $table->integer('load_index')->nullable();
            $table->string('season')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('tire_types')->insert([
            ['name' => 'Michelin', 'code' => 'MIC', 'category' => 'Truck', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bridgestone', 'code' => 'BRD', 'category' => 'Truck', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Goodyear', 'code' => 'GDY', 'category' => 'Truck', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Continental', 'code' => 'CON', 'category' => 'Truck', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pirelli', 'code' => 'PRL', 'category' => 'Truck', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dunlop', 'code' => 'DNP', 'category' => 'Truck', 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Other', 'code' => 'OTH', 'category' => 'Other', 'sort_order' => 99, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('tire_types');
    }
};
