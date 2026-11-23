<?php

use App\Models\RolePermission;
use App\Services\PermissionService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $permissions = [
        'view_purchasing_activities',
        'create_purchasing_activities',
        'complete_purchasing_activities',
    ];

    public function up(): void
    {
        $service = app(PermissionService::class);
        $service->seedCatalog();

        if (! Schema::hasTable('role_permissions')) {
            return;
        }

        foreach (RolePermission::all() as $row) {
            $granted = (array) $row->permissions;

            foreach ($this->permissions as $permission) {
                $defaults = config('permissions.permissions.' . $permission . '.roles', []);

                if (in_array($row->role, $defaults, true) && ! in_array($permission, $granted, true)) {
                    $granted[] = $permission;
                }
            }

            if ($granted !== (array) $row->permissions) {
                $row->permissions = array_values($granted);
                $row->save();
            }
        }

        $service->flush();
    }

    public function down(): void
    {
        if (! Schema::hasTable('role_permissions')) {
            return;
        }

        foreach (RolePermission::all() as $row) {
            $granted = array_values(array_filter(
                (array) $row->permissions,
                fn ($key) => ! in_array($key, $this->permissions, true)
            ));

            if ($granted !== (array) $row->permissions) {
                $row->permissions = $granted;
                $row->save();
            }
        }

        app(PermissionService::class)->flush();
    }
};
