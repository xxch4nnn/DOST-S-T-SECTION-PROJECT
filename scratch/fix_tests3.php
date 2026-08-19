<?php

$dir = __DIR__ . '/../tests/Feature/';
$files = glob($dir . '*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // 1. Fix FileType::create without file_group_id
    $content = preg_replace_callback(
        '/FileType::create\(\s*\[(.*?)\]\s*\)/s',
        function ($matches) {
            $inner = $matches[1];
            if (!str_contains($inner, 'file_group_id')) {
                $inner .= ", 'metadata_template' => [], 'file_group_id' => \App\Models\FileGroup::firstOrCreate(['name' => 'Default Group', 'slug' => 'default-group'])->id";
            }
            return "FileType::create([$inner])";
        },
        $content
    );

    // 2. Fix Livewire ->set('spas_no' to also set contact_number and email_address
    $content = str_replace(
        "->set('spas_no', '2023-TEST-0099')",
        "->set('spas_no', '2023-TEST-0099')\n            ->set('contact_number', '09123456789')\n            ->set('email_address', 'test@test.com')",
        $content
    );
    $content = str_replace(
        "->set('spas_no', '2024-STAGED-001')",
        "->set('spas_no', '2024-STAGED-001')\n            ->set('contact_number', '09123456789')\n            ->set('email_address', 'test@test.com')",
        $content
    );
    
    // 3. Fix spas_number -> spas_no in ScholarDocumentUploadTest
    $content = str_replace('spas_number', 'spas_no', $content);
    
    // 4. Fix scholarship_program_id -> scholarship_id
    $content = str_replace('scholarship_program_id', 'scholarship_id', $content);
    $content = str_replace('scholarship_program_type_id', 'scholarship_type_id', $content);

    // 5. Fix AdminRecord imports
    $content = str_replace("use App\\Models\\AdministrativeRecord;\n", "", $content);
    
    // 6. Fix Could not verify the hashed value's configuration in RolesAndPermissionsBaselineTest
    if (str_contains($file, 'RolesAndPermissionsBaselineTest.php')) {
        $content = str_replace(
            "User::factory()->create([",
            "User::factory()->create(['password' => \Illuminate\Support\Facades\Hash::make('password'), ",
            $content
        );
    }
    
    // 7. Fix FileType::firstOrCreate where metadata_template is missing
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
    
    file_put_contents($file, $content);
}
