<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Sponsorships\Index as SponsorshipsIndex;
use App\Livewire\Contacts\Index as ContactsIndex;
use App\Livewire\Reports\Index as ReportsIndex;
use App\Livewire\FormBuilder\Index as FormBuilderIndex;
use App\Livewire\Pipelines\Index as PipelinesIndex;
use App\Livewire\Users\Index as UsersIndex;
use App\Livewire\Settings\Index as SettingsIndex;
use App\Livewire\Workflows\Index as WorkflowsIndex;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PublicFormController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\ReportExportController;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', Login::class)->name('login');
    Route::get('/forgot-password', \App\Livewire\Auth\ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', \App\Livewire\Auth\ResetPassword::class)->name('password.reset');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/sponsorships', SponsorshipsIndex::class)->name('sponsorships.index');
    Route::get('/contacts', ContactsIndex::class)->name('contacts.index');
    Route::get('/reports', ReportsIndex::class)->name('reports.index');
    Route::get('/form-builder', FormBuilderIndex::class)->name('form-builder.index');
    Route::get('/pipelines', PipelinesIndex::class)->name('pipelines.index');
    Route::get('/users', UsersIndex::class)->name('users.index');
    Route::get('/settings', SettingsIndex::class)->name('settings.index');
    Route::get('/workflows', WorkflowsIndex::class)->name('workflows.index');
    
    // Report Exports
    Route::get('/reports/export/csv', [ReportExportController::class, 'exportCSV'])->name('reports.export.csv');
    Route::get('/reports/export/pipeline', [ReportExportController::class, 'exportPipelineReport'])->name('reports.export.pipeline');
    Route::get('/reports/export/performance', [ReportExportController::class, 'exportPerformanceReport'])->name('reports.export.performance');
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Public Form Routes
Route::get('/f/{uuid}', [PublicFormController::class, 'show'])->name('form.show');
Route::post('/f/{uuid}', [PublicFormController::class, 'submit'])
    ->middleware('throttle:5,1')
    ->name('form.submit');

// API Routes
Route::post('/api/leads/ingest', [LeadController::class, 'ingest'])
    ->middleware(['api.key', 'throttle:api']);
