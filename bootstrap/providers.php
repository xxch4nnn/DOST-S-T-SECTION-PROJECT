<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\QueryServiceProvider;
use App\Providers\VoltServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    QueryServiceProvider::class,
    VoltServiceProvider::class,
];
