<?php

use App\Http\Controllers\Central\Auth\SuperAdminAuthController;
use App\Http\Controllers\Central\SuperAdmin\DashboardController;
use App\Http\Controllers\Central\SuperAdmin\TenantController;
use App\Http\Controllers\Central\SuperAdmin\DomainController;
use App\Http\Controllers\Central\SuperAdmin\ExportController;
use Illuminate\Support\Facades\Route;

// Serve the landing page at the central root
Route::get('/', function () {
    return response()
        ->view('landing')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
});

// Central Auth
Route::get('/central/login', [SuperAdminAuthController::class, 'showLogin'])->name('central.login');
Route::post('/central/login', [SuperAdminAuthController::class, 'login']);
Route::post('/central/logout', [SuperAdminAuthController::class, 'logout'])->name('central.logout');

// Central SuperAdmin Dashboard (requires superadmin guard and IsSuperAdmin middleware)
Route::middleware(['superadmin'])->prefix('central/admin')->name('central.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Tenants Management
    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::get('/tenants/trash', [TenantController::class, 'trash'])->name('tenants.trash');
    Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
    Route::post('/tenants/{tenant}/restore', [TenantController::class, 'restore'])->name('tenants.restore');
    Route::delete('/tenants/{tenant}/force', [TenantController::class, 'forceDestroy'])->name('tenants.force-destroy');
    Route::get('/tenants/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
    Route::post('/tenants/{tenant}/toggle', [TenantController::class, 'toggleStatus'])->name('tenants.toggle');
    Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');
    Route::post('/tenants/{tenant}/approve-receipt', [TenantController::class, 'approveReceipt'])->name('tenants.approve-receipt');
    Route::post('/tenants/{tenant}/reject-receipt', [TenantController::class, 'rejectReceipt'])->name('tenants.reject-receipt');

    // Domains Management
    Route::get('/domains', [DomainController::class, 'index'])->name('domains.index');
    Route::post('/domains', [DomainController::class, 'store'])->name('domains.store');
    Route::delete('/domains/{domain}', [DomainController::class, 'destroy'])->name('domains.destroy');

    // Exports Management
    Route::get('/exports', [ExportController::class, 'index'])->name('exports.index');
    Route::post('/exports/zip', [ExportController::class, 'exportZip'])->name('exports.zip');
});
