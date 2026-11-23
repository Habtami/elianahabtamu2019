<?php

use App\Models\RolePermission;
use App\Services\PermissionService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $permission = 'view_battery_reports';

    public function up(): void
    {
        $service = app(PermissionService::class);
        $service->seedCatalog();

        if (! Schema::hasTable('role_permissions')) {
            return;
        }

        $defaults = config('permissions.permissions.' . $this->permission . '.roles', []);

        foreach (RolePermission::all() as $row) {
            $granted = (array) $row->permissions;

            if (in_array($row->role, $defaults, true) && ! in_array($this->permission, $granted, true)) {
                $granted[] = $this->permission;
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
                fn ($key) => $key !== $this->permission
            ));

            if ($granted !== (array) $row->permissions) {
                $row->permissions = $granted;
                $row->save();
            }
        }

        $service = app(PermissionService::class);
        $service->flush();
    }
};
