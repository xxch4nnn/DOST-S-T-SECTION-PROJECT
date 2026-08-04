<?php

declare(strict_types=1);

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\HealthController;
use App\Livewire\AddFile;
use App\Livewire\AdminRecords\Create as AdminRecordsCreate;
use App\Livewire\AdminRecords\Edit as AdminRecordsEdit;
use App\Livewire\AdminRecords\Index as AdminRecordsIndex;
use App\Livewire\AdminRecords\Show as AdminRecordsShow;
use App\Livewire\AuditLogs\Index as AuditLogsIndex;
use App\Livewire\Scholars\Edit;
use App\Livewire\Scholars\Index;
use App\Livewire\Scholars\Show;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::redirect('/', '/login');

Route::get('/health', HealthController::class)->name('health');

Route::middleware(['auth', 'verified'])->group(function () {
    // Any staff role may open the dashboard shell.
    Route::middleware('role:Super Admin|Admin|Encoder')->group(function () {
        Volt::route('dashboard', 'dashboard.main')->name('dashboard');
    });

    Route::middleware('permission:viewScholars')->group(function () {
        Route::get('/scholars', Index::class)->name('scholars.index');
        Route::get('/scholars/{scholar}', Show::class)->name('scholars.show');
    });

    Route::middleware('permission:editScholars')->group(function () {
        Route::get('/scholars/{scholar}/edit', Edit::class)->name('scholars.edit');
    });

    Route::middleware('permission:uploadDocuments')->group(function () {
        Route::get('/add-file', AddFile::class)->name('add-file.index');
    });

    Route::middleware('permission:viewAdminRecords')->group(function () {
        Route::get('/admin-records', AdminRecordsIndex::class)->name('admin-records.index');
    });

    Route::middleware('permission:createAdminRecords')->group(function () {
        Route::get('/admin-records/create', AdminRecordsCreate::class)->name('admin-records.create');
    });

    Route::middleware('permission:viewAdminRecords')->group(function () {
        Route::get('/admin-records/{record}', AdminRecordsShow::class)->name('admin-records.show');
    });

    Route::middleware('permission:editAdminRecords')->group(function () {
        Route::get('/admin-records/{record}/edit', AdminRecordsEdit::class)->name('admin-records.edit');
    });

    Route::middleware('permission:viewAuditLogs')->group(function () {
        Route::get('/audit-logs', AuditLogsIndex::class)->name('audit-logs.index');
    });

    // Download: auth + verified; DocumentPolicy::download enforces document-type access (403).
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])
        ->name('documents.download');
});

Route::middleware('auth')->group(function () {
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
