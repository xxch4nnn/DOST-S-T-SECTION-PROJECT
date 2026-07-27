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
    Volt::route('dashboard', 'dashboard.main')->name('dashboard');

    // Scholars CRUD
    Route::get('/scholars', Index::class)->name('scholars.index');
    Route::get('/scholars/create', AddFile::class)->name('scholars.create');
    Route::get('/scholars/{scholar}', Show::class)->name('scholars.show');
    Route::get('/scholars/{scholar}/edit', Edit::class)->name('scholars.edit');

    // Add New File Wizard
    Route::get('/add-file', AddFile::class)->name('add-file.index');

    // Admin Records CRUD
    Route::get('/admin-records', AdminRecordsIndex::class)->name('admin-records.index');
    Route::get('/admin-records/create', AdminRecordsCreate::class)->name('admin-records.create');
    Route::get('/admin-records/{record}', AdminRecordsShow::class)->name('admin-records.show');
    Route::get('/admin-records/{record}/edit', AdminRecordsEdit::class)->name('admin-records.edit');

    // Audit Logs
    Route::get('/audit-logs', AuditLogsIndex::class)->name('audit-logs.index');

    // Document Download
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
});

Route::middleware('auth')->group(function () {
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
