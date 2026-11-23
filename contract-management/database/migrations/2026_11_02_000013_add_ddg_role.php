<?php

use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Role::firstOrCreate(
            ['key' => 'ddg'],
            [
                'name' => 'DDG',
                'description' => 'Deputy Director General — second and final approval for purchase orders.',
                'color' => '#6f42c1',
                'is_admin' => false,
                'is_active' => true,
                'sort_order' => 7,
            ]
        );

        User::flushRoleCache();

        $service = app(PermissionService::class);
        $defaults = $service->defaults();

        RolePermission::firstOrCreate(
            ['role' => 'ddg'],
            ['permissions' => $defaults['ddg'] ?? []]
        );

        $service->flush();
    }

    public function down(): void
    {
        RolePermission::where('role', 'ddg')->delete();
        Role::where('key', 'ddg')->delete();

        User::flushRoleCache();
        app(PermissionService::class)->flush();
    }
};
