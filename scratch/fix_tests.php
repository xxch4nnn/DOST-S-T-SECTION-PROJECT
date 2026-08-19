<?php

$dir = __DIR__ . '/../tests/Feature/';
$files = glob($dir . '*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    $original = $content;

    // 1. Fix FileType::firstOrCreate
    $content = preg_replace_callback(
        '/FileType::firstOrCreate\(\s*\[(.*?)\]\s*\)/s',
        function ($matches) {
            $inner = $matches[1];
            if (!str_contains($inner, 'file_group_id')) {
                $inner .= ", 'metadata_template' => [], 'file_group_id' => \App\Models\FileGroup::firstOrCreate(['name' => 'Default Group', 'slug' => 'default-group'])->id";
            }
            return "FileType::firstOrCreate([$inner])";
        },
        $content
    );

    // 2. Fix Scholar::create
    $content = preg_replace_callback(
        '/Scholar::create\(\s*\[(.*?)\]\s*\)/s',
        function ($matches) {
            $inner = $matches[1];
            if (!str_contains($inner, 'contact_number')) {
                $inner .= ", 'contact_number' => '09123456789', 'email_address' => 'test@example.com'";
            }
            return "Scholar::create([$inner])";
        },
        $content
    );

    // 3. Fix RolesAndPermissionsBaselineTest
    if (str_contains($file, 'RolesAndPermissionsBaselineTest.php')) {
        $content = str_replace(
            "User::factory()->create([",
            "User::factory()->create([\n            'password' => \Illuminate\Support\Facades\Hash::make('password'),",
            $content
        );
    }
    
    // 4. Fix RoutePermissionGateTest AdministrativeRecord
    if (str_contains($file, 'RoutePermissionGateTest.php')) {
        // Just comment out the test_encoder_can_view_admin_records_index_but_not_edit since Admin Records was removed
        $content = preg_replace('/public function test_encoder_can_view_admin_records_index_but_not_edit\(\).*?\{.*?\}/s', '/* obsolete admin record test removed */', $content);
    }

    if ($content !== $original) {
        file_put_contents($file, $content);
        echo "Updated " . basename($file) . "\n";
    }
}
