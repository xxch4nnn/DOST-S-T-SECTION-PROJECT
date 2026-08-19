<?php

// Fix DatabaseSeeder.php hash error - needs Hash::make() for the hashed cast in Laravel 11
$t4 = __DIR__ . '/../database/seeders/DatabaseSeeder.php';
if (file_exists($t4)) {
    $c = file_get_contents($t4);
    $c = str_replace(
        "'password' => 'password'",
        "'password' => \Illuminate\Support\Facades\Hash::make('password')",
        $c
    );
    $c = str_replace(
        "'password' => 'admin'",
        "'password' => \Illuminate\Support\Facades\Hash::make('admin')",
        $c
    );
    file_put_contents($t4, $c);
}
