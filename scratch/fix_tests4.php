<?php

$files = [
    __DIR__ . '/../app/Livewire/Scholars/Edit.php',
    __DIR__ . '/../app/Livewire/Scholars/Create.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Remove program from validation and property
    $content = preg_replace("/\n\s*public string \\\$program = '';/", '', $content);
    $content = preg_replace("/\n\s*'program' => 'nullable\|string\|max:150',/", '', $content);
    
    // Also remove from mount() if it exists
    $content = preg_replace("/\n\s*\\\$this->program = \\\$scholar->program \?\? '';/", '', $content);
    
    file_put_contents($file, $content);
}
