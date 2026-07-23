<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\HealthController;
use App\Livewire\Scholars\Create;
use App\Livewire\Scholars\Edit;
use App\Livewire\Scholars\Index;
use App\Livewire\Scholars\Show;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/health', HealthController::class)->name('health');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Scholars CRUD
    Route::get('/scholars', Index::class)->name('scholars.index');
    Route::get('/scholars/create', Create::class)->name('scholars.create');
    Route::get('/scholars/{scholar}', Show::class)->name('scholars.show');
    Route::get('/scholars/{scholar}/edit', Edit::class)->name('scholars.edit');

    // Admin Records CRUD
    Route::get('/admin-records', App\Livewire\AdminRecords\Index::class)->name('admin-records.index');
    Route::get('/admin-records/create', App\Livewire\AdminRecords\Create::class)->name('admin-records.create');
    Route::get('/admin-records/{record}', App\Livewire\AdminRecords\Show::class)->name('admin-records.show');
    Route::get('/admin-records/{record}/edit', App\Livewire\AdminRecords\Edit::class)->name('admin-records.edit');

    // Audit Logs
    Route::get('/audit-logs', App\Livewire\AuditLogs\Index::class)->name('audit-logs.index');

    // Document Download
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
});

Route::middleware('auth')->group(function () {
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
