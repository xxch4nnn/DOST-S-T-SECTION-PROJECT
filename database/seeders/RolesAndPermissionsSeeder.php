<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * V1 baseline roles + permission matrix.
     * Super Admin bypasses via Gate::before; Encoder must not receive manageUsers / strikeOff / audit.
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define baseline permissions
        $permissions = [
            // Documents / ops
            'viewAuditLogs',
            'manageUsers',
            'uploadDocuments',
            'editDocumentMetadata',
            'strikeOffDocuments',
            'viewReports',
            'viewNotifications',
            // Scholars
            'viewScholars',
            'createScholars',
            'editScholars',
            'deleteScholars',
            // Admin records
            'viewAdminRecords',
            'createAdminRecords',
            'editAdminRecords',
            'deleteAdminRecords',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions($permissions);

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions($permissions);

        $encoder = Role::firstOrCreate(['name' => 'Encoder']);
        $encoder->syncPermissions([
            'uploadDocuments',
            'editDocumentMetadata',
            'viewReports',
            'viewNotifications',
            'viewScholars',
            'createScholars',
            'editScholars',
            'viewAdminRecords',
        ]);
    }
}
