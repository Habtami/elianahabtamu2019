<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PermissionService
{
    protected ?array $matrix = null;

    protected ?array $catalog = null;

    /**
     * Permission definitions coming from config/permissions.php
     */
    public function definitions(): array
    {
        return config('permissions.permissions', []);
    }

    /**
     * Permission modules coming from config/permissions.php
     */
    public function modules(): array
    {
        return config('permissions.modules', []);
    }

    /**
     * All system roles
     */
    public function roles(): array
    {
        return User::getRoles();
    }

    /**
     * Permission catalog keyed by permission key. Labels and descriptions are
     * taken from the database when a record exists so they can be customised.
     */
    public function catalog(): array
    {
        if ($this->catalog !== null) {
            return $this->catalog;
        }

        $catalog = [];
        $definitions = $this->definitions();

        foreach ($definitions as $key => $definition) {
            $catalog[$key] = [
                'key' => $key,
                'label' => $definition['label'] ?? ucfirst(str_replace('_', ' ', $key)),
                'module' => $definition['module'] ?? 'general',
                'description' => $definition['description'] ?? null,
            ];
        }

        if ($this->tableExists('permissions')) {
            foreach (Permission::all() as $permission) {
                if (! isset($catalog[$permission->key])) {
                    continue;
                }

                $catalog[$permission->key]['label'] = $permission->label;
                $catalog[$permission->key]['description'] = $permission->description ?: $catalog[$permission->key]['description'];
            }
        }

        return $this->catalog = $catalog;
    }

    /**
     * Default grants per role, derived from the configuration file
     */
    public function defaults(): array
    {
        $defaults = [];

        foreach (array_keys($this->roles()) as $role) {
            $defaults[$role] = array_values(array_filter(
                array_keys($this->definitions()),
                function ($key) use ($role) {
                    return in_array($role, $this->definitions()[$key]['roles'] ?? [], true);
                }
            ));
        }

        return $defaults;
    }

    /**
     * Current grants per role. Roles that have never been configured fall back
     * to the default grants, an explicitly emptied role keeps no permissions.
     */
    public function matrix(): array
    {
        if ($this->matrix !== null) {
            return $this->matrix;
        }

        $defaults = $this->defaults();
        $known = array_keys($this->catalog());

        if (! $this->tableExists('role_permissions')) {
            return $this->matrix = $defaults;
        }

        $rows = RolePermission::all()->keyBy('role');
        $matrix = [];

        foreach (array_keys($this->roles()) as $role) {
            $row = $rows->get($role);

            $matrix[$role] = $row
                ? array_values(array_intersect((array) $row->permissions, $known))
                : ($defaults[$role] ?? []);
        }

        return $this->matrix = $matrix;
    }

    /**
     * Permission keys granted to a role
     */
    public function grantsFor(string $role): array
    {
        return $this->matrix()[$role] ?? [];
    }

    /**
     * Forget the cached matrix and catalog
     */
    public function flush(): void
    {
        $this->matrix = null;
        $this->catalog = null;
    }

    /**
     * Determine whether a role holds a permission
     */
    public function allows(?string $role, string $permission): bool
    {
        if (! $role) {
            return false;
        }

        // The super administrator always keeps every permission
        if ($role === User::ROLE_SUPER_ADMIN) {
            return true;
        }

        return in_array($permission, $this->grantsFor($role), true);
    }

    /**
     * Persist a role => permission matrix
     *
     * @param  array<string, array<int, string>>  $matrix
     */
    public function sync(array $matrix): void
    {
        $known = array_keys($this->catalog());

        DB::transaction(function () use ($matrix, $known) {
            foreach (array_keys($this->roles()) as $role) {
                $granted = array_values(array_intersect($matrix[$role] ?? [], $known));

                if ($role === User::ROLE_SUPER_ADMIN) {
                    $granted = $known;
                }

                RolePermission::updateOrCreate(
                    ['role' => $role],
                    ['permissions' => $granted]
                );
            }
        });

        $this->matrix = null;
        $this->catalog = null;
    }

    /**
     * Restore the default permission matrix
     */
    public function reset(): void
    {
        $this->sync($this->defaults());
    }

    /**
     * Create the default grants for roles that were never configured. Existing
     * customisations are kept untouched.
     */
    public function seedDefaults(): void
    {
        if (! $this->tableExists('role_permissions')) {
            return;
        }

        $defaults = $this->defaults();

        DB::transaction(function () use ($defaults) {
            foreach ($defaults as $role => $granted) {
                RolePermission::firstOrCreate(
                    ['role' => $role],
                    ['permissions' => $granted]
                );
            }
        });

        $this->matrix = null;
    }

    /**
     * Seed the permission catalog rows
     */
    public function seedCatalog(): void
    {
        if (! $this->tableExists('permissions')) {
            return;
        }

        $order = 0;
        foreach ($this->definitions() as $key => $definition) {
            Permission::updateOrCreate(
                ['key' => $key],
                [
                    'label' => $definition['label'] ?? ucfirst(str_replace('_', ' ', $key)),
                    'module' => $definition['module'] ?? 'general',
                    'description' => $definition['description'] ?? null,
                    'sort_order' => $order++,
                ]
            );
        }
    }

    protected function tableExists(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
