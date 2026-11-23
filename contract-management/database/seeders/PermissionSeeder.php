<?php

namespace Database\Seeders;

use App\Services\PermissionService;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = app(PermissionService::class);

        $permissions->seedCatalog();
        $permissions->seedDefaults();
    }
}
