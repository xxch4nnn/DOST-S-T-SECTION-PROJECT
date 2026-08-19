<?php

// 1. RolesAndPermissionsSeeder.php duplicate permissions
$seeder = __DIR__ . '/../database/seeders/RolesAndPermissionsSeeder.php';
$content = file_get_contents($seeder);
$content = preg_replace(
    "/\\\$encoder->syncPermissions\(\[(.*?)\]\);/s",
    "\$encoder->syncPermissions([
            'uploadDocuments',
            'editDocumentMetadata',
            'viewReports',
            'viewNotifications',
            'viewScholars',
            'createScholars',
            'editScholars',
            'viewAdminRecords',
        ]);",
    $content
);
file_put_contents($seeder, $content);

// 2. RolesAndPermissionsBaselineTest:
// "Failed asserting that true is false." in test_encoder_lacks_manage_users_and_destructive_admin_gates
// This was because the Seeder gave the Encoder role those permissions! Now fixed above.

// 3. RoutePermissionGateTest:
// "Expected response status code [403] but received 200."
// test_encoder_can_open_scholars_and_is_forbidden_from_audit_logs
// Let's check app/Http/Controllers/AuditLogController.php or routes/web.php.
// Or maybe it's just a livewire route that doesn't have the permission check anymore?
// We will look at it if it fails again.

