<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */

    public function run()
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Create permissions
        $createPostPermission = Permission::create(['name' => 'create post']);
        $editPostPermission = Permission::create(['name' => 'edit post']);

        // Assign permissions to roles
        $adminRole->givePermissionTo($createPostPermission, $editPostPermission);
        $userRole->givePermissionTo($createPostPermission);
    }
}
