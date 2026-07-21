<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $encoder = Role::firstOrCreate(['name' => 'Encoder']);

        Permission::firstOrCreate(['name' => 'viewAuditLogs', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo('viewAuditLogs');
        $admin->givePermissionTo('viewAuditLogs');

        // Assign super admin role to the test user if exists
        $user = \App\Models\User::where('email', 'test@example.com')->first();
        if ($user) {
            $user->assignRole($superAdmin);
        }
    }
}
