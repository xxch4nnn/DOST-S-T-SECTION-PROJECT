<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * V1 baseline roles + permission matrix.
     * Super Admin has all; Encoder must not receive manageUsers.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'viewAuditLogs',
            'manageUsers',
            'uploadDocuments',
            'editDocumentMetadata',
            'strikeOffDocuments',
            'viewReports',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $encoder = Role::firstOrCreate(['name' => 'Encoder', 'guard_name' => 'web']);

        $superAdmin->syncPermissions($permissions);

        $admin->syncPermissions([
            'viewAuditLogs',
            'manageUsers',
            'uploadDocuments',
            'editDocumentMetadata',
            'strikeOffDocuments',
            'viewReports',
        ]);

        $encoder->syncPermissions([
            'uploadDocuments',
            'editDocumentMetadata',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        if ($user) {
            $user->syncRoles([$superAdmin]);
        }
    }
}
