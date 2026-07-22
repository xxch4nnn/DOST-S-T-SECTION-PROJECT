<?php

// Force testing env before Laravel boots. Docker compose sets APP_ENV=local on the
// app container, which prevents Livewire from registering assertSeeLivewire macros.
putenv('APP_ENV=testing');
$_ENV['APP_ENV'] = 'testing';
$_SERVER['APP_ENV'] = 'testing';

require __DIR__.'/../vendor/autoload.php';
