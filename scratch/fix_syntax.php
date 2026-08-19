<?php
$files = glob(__DIR__ . '/../tests/Feature/*.php');
foreach ($files as $file) {
    $c = file_get_contents($file);
    $c = str_replace(",\n        , 'contact_number'", ",\n        'contact_number'", $c);
    $c = str_replace("\n        , 'contact_number'", ",\n        'contact_number'", $c);
    $c = preg_replace('/\,\s*\, \'contact_number\'/', ", 'contact_number'", $c);
    file_put_contents($file, $c);
}
