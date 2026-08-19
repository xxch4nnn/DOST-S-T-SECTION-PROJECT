<?php

$routesFile = __DIR__ . '/../routes/web.php';
$content = file_get_contents($routesFile);

// 1. Remove unprotected scholars duplicate routes
$content = preg_replace(
    "/    \/\/ Scholars CRUD\n    Route::get\('\/scholars', Index::class\)->name\('scholars\.index'\);\n    Route::get\('\/scholars\/\{scholar\}', Show::class\)->name\('scholars\.show'\);\n    Route::get\('\/scholars\/\{scholar\}\/edit', Edit::class\)->name\('scholars\.edit'\);\n    Route::post\('\/scholars\/create', Create::class\)->name\('scholars\.create'\);\n    Route::delete\('\/scholars\/\{scholar\}\/delete', Delete::class\)->name\('scholars\.delete'\);\n\n/",
    "",
    $content
);

// 2. Add missing routes to editScholars group (create, delete)
$content = preg_replace(
    "/    Route::middleware\('permission:editScholars'\)->group\(function \(\) \{\n        Route::get\('\/scholars\/\{scholar\}\/edit', Edit::class\)->name\('scholars\.edit'\);\n/",
    "    Route::middleware('permission:editScholars')->group(function () {\n        Route::get('/scholars/{scholar}/edit', Edit::class)->name('scholars.edit');\n        Route::post('/scholars/create', Create::class)->name('scholars.create');\n        Route::delete('/scholars/{scholar}/delete', Delete::class)->name('scholars.delete');\n",
    $content
);

// 3. Wrap audit logs in permission
$content = preg_replace(
    "/    \/\/ Audit Logs\n    Route::get\('\/audit-logs', AuditLogsIndex::class\)->name\('audit-logs\.index'\);/",
    "    // Audit Logs\n    Route::middleware('permission:viewAuditLogs')->group(function () {\n        Route::get('/audit-logs', AuditLogsIndex::class)->name('audit-logs.index');\n    });",
    $content
);

file_put_contents($routesFile, $content);

