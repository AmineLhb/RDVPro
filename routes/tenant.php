<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\Auth\AuthController;
use App\Http\Controllers\Tenant\Admin\DashboardController;
use App\Http\Controllers\Tenant\Admin\AppointmentController;
use App\Http\Controllers\Tenant\Admin\CustomerController;
use App\Http\Controllers\Tenant\Admin\ServiceController;
use App\Http\Controllers\Tenant\Admin\ResourceController;
use App\Http\Controllers\Tenant\Admin\MessageTemplateController;
use App\Http\Controllers\Tenant\Admin\ReportController;
use App\Http\Controllers\Tenant\Admin\ConfigController;
use App\Http\Controllers\Tenant\Admin\SubscriptionController;
use App\Http\Controllers\Tenant\Admin\RecordController;
use App\Http\Controllers\Tenant\Admin\AttachmentController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    // Root route redirects to login
    Route::get('/', function () {
        return redirect()->route('tenant.login');
    });

    // Tenant Auth
    Route::get('/login', [AuthController::class, 'showLogin'])->name('tenant.login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('tenant.logout');

    // Tenant Admin Area (Requires auth, admin status, and subscription check)
    Route::middleware(['admin', 'subscription.active'])->prefix('admin')->name('tenant.')->group(function () {
        // Subscription pay routes
        Route::get('/subscription/pay', [SubscriptionController::class, 'showPay'])->name('subscription.pay');
        Route::post('/subscription/pay', [SubscriptionController::class, 'storePay'])->name('subscription.store');

        Route::get('/home', [DashboardController::class, 'home'])->name('home');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Appointments CRUD
        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
        Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
        Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
        Route::patch('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
        Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');

        // Customers CRM CRUD
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
        Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

        // Customer Records Custom Form Entries
        Route::post('/customers/{customer}/records', [RecordController::class, 'store'])->name('records.store');
        Route::delete('/records/{record}', [RecordController::class, 'destroy'])->name('records.destroy');
        Route::post('/config/record-template', [RecordController::class, 'updateTemplate'])->name('config.record-template');

        // Customer Attachments
        Route::post('/customers/{customer}/attachments', [AttachmentController::class, 'store'])->name('attachments.store');
        Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');
        Route::post('/attachments/{attachment}/rename', [AttachmentController::class, 'rename'])->name('attachments.rename');
        Route::get('/attachments/{attachment}/serve', [AttachmentController::class, 'serve'])->name('attachments.serve');

        // Services CRUD
        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

        // Resources CRUD
        Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
        Route::get('/resources/create', [ResourceController::class, 'create'])->name('resources.create');
        Route::post('/resources', [ResourceController::class, 'store'])->name('resources.store');
        Route::get('/resources/{resource}/edit', [ResourceController::class, 'edit'])->name('resources.edit');
        Route::put('/resources/{resource}', [ResourceController::class, 'update'])->name('resources.update');
        Route::delete('/resources/{resource}', [ResourceController::class, 'destroy'])->name('resources.destroy');

        // Message Templates
        Route::get('/templates', [MessageTemplateController::class, 'index'])->name('templates.index');
        Route::put('/templates/{template}', [MessageTemplateController::class, 'update'])->name('templates.update');
        Route::post('/templates/preview', [MessageTemplateController::class, 'previewRender'])->name('templates.preview');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');

        // Config
        Route::get('/config', [ConfigController::class, 'index'])->name('config.index');
        Route::post('/config', [ConfigController::class, 'update'])->name('config.update');
    });
});
