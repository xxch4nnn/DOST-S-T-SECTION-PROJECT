<?php

$files = [
    __DIR__ . '/../app/Livewire/Scholars/Index.php',
    __DIR__ . '/../app/Livewire/Scholars/Create.php',
    __DIR__ . '/../app/Livewire/Scholars/Delete.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Replace table names
    $content = str_replace('scholarship_programs', 'scholarships', $content);
    $content = str_replace('scholarship_program_types', 'scholarship_types', $content);
    
    // Replace column names in SQL and validation rules
    $content = str_replace('scholarship_program_id', 'scholarship_id', $content);
    $content = str_replace('scholarship_program_type_id', 'scholarship_type_id', $content);
    
    // Replace aliases in SELECT
    $content = str_replace('scholarship_program,', 'scholarship,', $content);
    $content = str_replace('scholarship_program_type,', 'scholarship_type,', $content);
    
    file_put_contents($file, $content);
}
