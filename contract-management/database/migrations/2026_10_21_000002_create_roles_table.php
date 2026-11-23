<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->string('color', 20)->default('#6c757d');
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Roles that ship with the system
        $systemRoles = [
            ['key' => 'super_admin', 'name' => 'Super Administrator', 'description' => 'Full system access including user management, contract oversight, tyre tracking and system administration.', 'color' => '#6f42c1', 'is_admin' => 1, 'is_active' => 1, 'sort_order' => 0],
            ['key' => 'admin', 'name' => 'System Administrator', 'description' => 'Full system access including user management, contract oversight and system administration.', 'color' => '#dc3545', 'is_admin' => 1, 'is_active' => 1, 'sort_order' => 1],
            ['key' => 'general_service', 'name' => 'General Service Officer', 'description' => 'Handles contract creation and management for the General Service department.', 'color' => '#6c757d', 'is_admin' => 0, 'is_active' => 1, 'sort_order' => 2],
            ['key' => 'cmd', 'name' => 'CMD Officer', 'description' => 'Manages contracts for the CMD department with full CRUD capabilities.', 'color' => '#6c757d', 'is_admin' => 0, 'is_active' => 1, 'sort_order' => 3],
            ['key' => 'finance', 'name' => 'Finance Officer', 'description' => 'Oversees financial aspects of contracts with comprehensive access.', 'color' => '#17a2b8', 'is_admin' => 0, 'is_active' => 1, 'sort_order' => 4],
            ['key' => 'gs_manager', 'name' => 'GS Manager', 'description' => 'Supervisory role with read-only access to all contracts and reports.', 'color' => '#28a745', 'is_admin' => 0, 'is_active' => 1, 'sort_order' => 5],
            ['key' => 'cmd_manager', 'name' => 'CMD Manager', 'description' => 'Supervisory role with read-only access to all contracts and reports.', 'color' => '#28a745', 'is_admin' => 0, 'is_active' => 1, 'sort_order' => 6],
        ];

        $now = now();

        foreach ($systemRoles as $role) {
            DB::table('roles')->insert(array_merge($role, ['created_at' => $now, 'updated_at' => $now]));
        }

        // Roles are managed from the admin panel, so the column can no longer be an enum
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'admin', 'general_service', 'cmd', 'finance', 'gs_manager', 'cmd_manager'])->change();
        });

        Schema::dropIfExists('roles');
    }
};
