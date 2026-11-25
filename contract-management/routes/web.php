<?php

use App\Http\Controllers\Admin\TelegramLogController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BatteryController;
use App\Http\Controllers\ContractClosureController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PaymentRequestController;
use App\Http\Controllers\ProcurementReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchasingActivityController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TireController;
use App\Http\Controllers\TireGiveController;
use App\Http\Controllers\TireReportController;
use App\Http\Controllers\TireTypeController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Logout: primary method is POST, but provide a safe GET handler that also invalidates session
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// Protected Routes (require authentication)
Route::middleware(['auth', 'NoCache'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/summary/{scope}', [DashboardController::class, 'summary'])->name('dashboard.summary');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::match(['post', 'put', 'patch'], '/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Forced password change
    Route::get('/password/change', [PasswordChangeController::class, 'showChangeForm'])->name('password.change');
    Route::post('/password/change', [PasswordChangeController::class, 'updatePassword'])->name('password.change.update');

    // Contract Management Routes
    Route::get('/contracts/export', [ContractController::class, 'exportList'])->name('contracts.export-list');
    Route::post('/contracts/material-groups', [ContractController::class, 'storeMaterialGroup'])->name('contracts.material-groups.store');
    Route::post('/contracts/departments', [ContractController::class, 'storeDepartment'])->name('contracts.departments.store');
    Route::post('/contracts/contract-types', [ContractController::class, 'storeContractType'])->name('contracts.contract-types.store');
    Route::post('/contracts/currency-types', [ContractController::class, 'storeCurrencyType'])->name('contracts.currency-types.store');
    Route::post('/contracts/performance-bond-types', [ContractController::class, 'storePerformanceBondType'])->name('contracts.performance-bond-types.store');
    Route::resource('contracts', ContractController::class);

    // Contract Alerts
    Route::get('/alerts', [ContractController::class, 'alerts'])->name('contracts.alerts');

    // Contract Reports
    Route::get('/reports', [ContractController::class, 'reports'])->name('contracts.reports');
    Route::get('/reports/export', [ContractController::class, 'exportReports'])->name('contracts.reports.export');
    Route::post('/contracts/export', [ContractController::class, 'export'])->name('contracts.export');

    // Contract End Date Extension
    Route::get('/contracts/{contract}/extend-contract-end', [ContractController::class, 'extendContractEnd'])
        ->name('contracts.extend-contract-end');
    Route::post('/contracts/{contract}/update-contract-end', [ContractController::class, 'updateContractEnd'])
        ->name('contracts.update-contract-end');

    // Performance Guarantee Extension
    Route::get('/contracts/{contract}/extend-performance-guarantee', [ContractController::class, 'extendPerformanceGuarantee'])
        ->name('contracts.extend-performance-guarantee');
    Route::post('/contracts/{contract}/update-performance-guarantee', [ContractController::class, 'updatePerformanceGuarantee'])
        ->name('contracts.update-performance-guarantee');

    // Supplier Performance Evaluation
    Route::get('/suppliers/performance-evaluation', [SupplierController::class, 'performanceEvaluation'])
        ->name('suppliers.performance-evaluation');
    Route::post('/suppliers/{supplier}/approve', [SupplierController::class, 'approveSupplier'])
        ->name('suppliers.approve');
    Route::post('/suppliers/{supplier}/rate', [SupplierController::class, 'updateRating'])
        ->name('suppliers.rate');

    // Supplier API Routes
    Route::get('/api/suppliers/search', [SupplierController::class, 'searchSuppliers'])
        ->name('api.suppliers.search');
    Route::get('/api/suppliers/check', [SupplierController::class, 'checkSupplier'])
        ->name('api.suppliers.check');

    // Tire Tracking Routes
    Route::get('/tires', [TireController::class, 'index'])->name('tires.index');
    Route::get('/tires/summary/{scope}', [TireController::class, 'summary'])
        ->whereIn('scope', ['total-tires', 'in-use', 'returned', 'active-drivers'])
        ->name('tires.summary');
    Route::resource('tires/types', TireTypeController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])->names([
        'index' => 'tires.types.index',
        'create' => 'tires.types.create',
        'store' => 'tires.types.store',
        'edit' => 'tires.types.edit',
        'update' => 'tires.types.update',
        'destroy' => 'tires.types.destroy',
    ]);

    // Tire Give / Return (must come before tires/{tire} wildcard)
    Route::get('/tires/give', [TireGiveController::class, 'create'])->name('tires.give');
    Route::post('/tires/give', [TireGiveController::class, 'store'])->name('tires.give.store');

    Route::get('/tires/create', [TireController::class, 'create'])->name('tires.create');
    Route::post('/tires', [TireController::class, 'store'])->name('tires.store');

    // Tire Reports (must come before tires/{tire} wildcard)
    Route::get('/tires/reports', [TireReportController::class, 'index'])->name('tires.reports');
    Route::get('/tires/reports/export/excel', [TireReportController::class, 'exportCsv'])->name('tires.reports.export.excel');
    Route::get('/tires/reports/export/drivers/excel', [TireReportController::class, 'exportDriversCsv'])->name('tires.reports.export.drivers');
    Route::get('/tires/reports/export/report', [TireReportController::class, 'exportReportCsv'])->name('tires.reports.export.report');

    // Tire Audit Trail (must come before tires/{tire} wildcard)
    Route::get('/tires/audit', [TireController::class, 'audit'])->name('tires.audit');

    // Tire Return List (must come before tires/{tire} wildcard)
    Route::get('/tires/return', [TireController::class, 'returnList'])->name('tires.return.list');
    Route::get('/tires/return/export/excel', [TireController::class, 'exportReturnExcel'])->name('tires.return.excel');
    Route::get('/tires/return/export/pdf', [TireController::class, 'exportReturnPdf'])->name('tires.return.pdf');

    // Tire History (must come before tires/{tire} wildcard)
    Route::get('/tires/{tire}/history', [TireController::class, 'history'])->name('tires.history');

    Route::get('/tires/{tire}/edit', [TireController::class, 'edit'])->name('tires.edit');
    Route::get('/tires/{tire}/label', [TireController::class, 'label'])->name('tires.label');
    Route::get('/tires/{tire}/pdf', [TireController::class, 'pdf'])->name('tires.pdf');
    Route::get('/tires/{tire}', [TireController::class, 'show'])->name('tires.show');
    Route::match(['post', 'put', 'patch'], '/tires/{tire}', [TireController::class, 'update'])->name('tires.update');
    Route::delete('/tires/{tire}', [TireController::class, 'destroy'])->name('tires.destroy');

    // Tire Return (must come before tires/{tire} wildcard)
    Route::get('/tires/{tireGive}/return', [TireGiveController::class, 'returnCreate'])->name('tires.return');
    Route::post('/tires/{tireGive}/return', [TireGiveController::class, 'returnStore'])->name('tires.return.store');

    // Driver Management Routes
    Route::get('/drivers/{driver}/history', [DriverController::class, 'history'])->name('drivers.history');
    Route::resource('drivers', DriverController::class);
    Route::patch('/drivers/{driver}/status', [DriverController::class, 'updateStatus'])->name('drivers.status');
    Route::post('/drivers/quick', [DriverController::class, 'quickStore'])->name('drivers.quick-store');

    // Battery Given and Return Routes
    Route::get('/batteries', [BatteryController::class, 'index'])->name('batteries.index');
    Route::get('/batteries/export/excel', [BatteryController::class, 'exportExcel'])->name('batteries.export.excel');
    Route::get('/batteries/export/pdf', [BatteryController::class, 'exportPdf'])->name('batteries.export.pdf');
    Route::get('/batteries/report', [BatteryController::class, 'report'])->name('batteries.report');
    Route::get('/batteries/report/export/excel', [BatteryController::class, 'exportReportExcel'])->name('batteries.report.export.excel');
    Route::get('/batteries/report/export/pdf', [BatteryController::class, 'exportReportPdf'])->name('batteries.report.export.pdf');
    Route::redirect('/batteries/Report', '/batteries/report');
    Route::get('/batteries/{battery}/history', [BatteryController::class, 'history'])->name('batteries.history');
    Route::patch('/batteries/{battery}/return', [BatteryController::class, 'returnBattery'])->name('batteries.return');
    Route::post('/batteries', [BatteryController::class, 'store'])->name('batteries.store');
    Route::match(['post', 'put', 'patch'], '/batteries/{battery}', [BatteryController::class, 'update'])->name('batteries.update');
    Route::delete('/batteries/{battery}', [BatteryController::class, 'destroy'])->name('batteries.destroy');

    // Purchase Order Processing
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    Route::get('/purchase-orders/export/excel', [PurchaseOrderController::class, 'exportExcel'])->name('purchase-orders.export.excel');
    Route::get('/purchase-orders/export/pdf', [PurchaseOrderController::class, 'exportPdf'])->name('purchase-orders.export.pdf');
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
    Route::patch('/purchase-orders/{purchaseOrder}/approve-gs', [PurchaseOrderController::class, 'approveGs'])->name('purchase-orders.approve-gs');
    Route::patch('/purchase-orders/{purchaseOrder}/approve-ddg', [PurchaseOrderController::class, 'approveDdg'])->name('purchase-orders.approve-ddg');

    // Payment Request Processing
    Route::get('/payment-requests', [PaymentRequestController::class, 'index'])->name('payment-requests.index');
    Route::get('/payment-requests/export/excel', [PaymentRequestController::class, 'exportExcel'])->name('payment-requests.export.excel');
    Route::get('/payment-requests/export/pdf', [PaymentRequestController::class, 'exportPdf'])->name('payment-requests.export.pdf');
    Route::get('/payment-requests/summary/{scope}', [PaymentRequestController::class, 'summary'])
        ->whereIn('scope', ['total', 'pending', 'complete'])
        ->name('payment-requests.summary');
    Route::post('/payment-requests', [PaymentRequestController::class, 'store'])->name('payment-requests.store');
    Route::post('/payment-requests/procurement-types', [PaymentRequestController::class, 'storeProcurementType'])
        ->name('payment-requests.procurement-types.store');
    Route::post('/payment-requests/payment-types', [PaymentRequestController::class, 'storePaymentType'])
        ->name('payment-requests.payment-types.store');
    Route::get('/payment-requests/{paymentRequest}', [PaymentRequestController::class, 'show'])->name('payment-requests.show');
    Route::patch('/payment-requests/{paymentRequest}/approve-gs', [PaymentRequestController::class, 'approveGs'])->name('payment-requests.approve-gs');
    Route::patch('/payment-requests/{paymentRequest}/complete', [PaymentRequestController::class, 'completeFinance'])->name('payment-requests.complete');

    // Contract Closing
    Route::get('/contract-closures', [ContractClosureController::class, 'index'])->name('contract-closures.index');
    Route::post('/contract-closures', [ContractClosureController::class, 'store'])->name('contract-closures.store');
    Route::get('/contract-closures/{contractClosure}', [ContractClosureController::class, 'show'])->name('contract-closures.show');
    Route::patch('/contract-closures/{contractClosure}/complete', [ContractClosureController::class, 'complete'])->name('contract-closures.complete');

    // Procurement Report
    Route::get('/procurement-report', [ProcurementReportController::class, 'index'])->name('procurement-report.index');
    Route::get('/procurement-report/export/{format}', [ProcurementReportController::class, 'export'])->name('procurement-report.export');

    // Purchasing Daily Activity
    Route::get('/purchasing-activities', [PurchasingActivityController::class, 'index'])->name('purchasing-activities.index');
    Route::post('/purchasing-activities', [PurchasingActivityController::class, 'store'])->name('purchasing-activities.store');
    Route::post('/purchasing-activities/material-groups', [PurchasingActivityController::class, 'storeMaterialGroup'])->name('purchasing-activities.material-groups.store');
    Route::post('/purchasing-activities/units', [PurchasingActivityController::class, 'storeUnit'])->name('purchasing-activities.units.store');
    Route::patch('/purchasing-activities/{purchasingActivity}/complete', [PurchasingActivityController::class, 'complete'])->name('purchasing-activities.complete');

    // Tire Tracking API Routes
    Route::get('/api/drivers/search', [DriverController::class, 'searchDrivers'])->name('api.drivers.search');
    Route::get('/api/tires/search', [TireController::class, 'searchTires'])->name('api.tires.search');
    Route::get('/api/tires/check-serial', [TireController::class, 'checkSerial'])->name('api.tires.check-serial');
    Route::get('/api/tires/{tire}', [TireController::class, 'showApi'])->name('api.tires.show');

    // Tire Tracking Dashboard
    Route::get('/tire-tracking', [TireController::class, 'dashboard'])->name('tires.dashboard');

    // Export Logs
    Route::get('/exports/logs', [ExportController::class, 'index'])->name('exports.logs');
    Route::get('/exports', [ExportController::class, 'logs'])->name('exports.index');
    Route::get('/exports/drivers', [ExportController::class, 'drivers'])->name('exports.drivers');
    Route::get('/exports/tires', [ExportController::class, 'tires'])->name('exports.tires');
    Route::get('/exports/returns', [ExportController::class, 'returns'])->name('exports.returns');

    // Profile Telegram Settings
    Route::get('/profile/telegram', [ProfileController::class, 'telegram'])->name('profile.telegram');
    Route::put('/profile/telegram', [ProfileController::class, 'updateTelegram'])->name('profile.telegram.update');

    // Admin Telegram Logs
    Route::get('/admin/telegram-logs', [TelegramLogController::class, 'index'])->name('admin.telegram-logs');

    // Admin Routes (require the admin panel permission)
    Route::middleware(['auth', 'CheckPermission:admin_panel'])->group(function () {
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/users', [AdminController::class, 'index'])->name('users.index');
            Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
            Route::get('/users/export', [AdminController::class, 'exportUsers'])->name('users.export');
            Route::get('/users/details', [AdminController::class, 'userDetails'])->name('users.details');
            Route::post('/users', [AdminController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [AdminController::class, 'edit'])->name('users.edit');
            Route::get('/users/{user}/history', [AdminController::class, 'userHistory'])->name('users.history');
            Route::match(['post', 'put', 'patch'], '/users/{user}', [AdminController::class, 'update'])->name('users.update');
            Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleStatus'])->name('users.toggleStatus');
            Route::post('/users/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('users.resetPassword');
            Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
            Route::get('/permissions', [AdminController::class, 'permissions'])->name('permissions');
            Route::post('/permissions', [AdminController::class, 'updatePermissions'])->name('permissions.update');
            Route::post('/permissions/reset', [AdminController::class, 'resetPermissions'])->name('permissions.reset');
            Route::get('/roles', [AdminController::class, 'roles'])->name('roles');
            Route::post('/roles', [AdminController::class, 'storeRole'])->name('roles.store');
            Route::post('/roles/{role}', [AdminController::class, 'updateRole'])->name('roles.update');
            Route::delete('/roles/{role}', [AdminController::class, 'destroyRole'])->name('roles.destroy');
            Route::get('/activity-logs', [AdminController::class, 'activityLogs'])->name('activity-logs');
        });
    });
});

// Database backups (any signed-in user; the controller restricts it to admins)
Route::middleware(['auth'])->group(function () {
    Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
    Route::post('/backup/create', [BackupController::class, 'create'])->name('backup.create');
    Route::post('/backup/settings', [BackupController::class, 'updateSettings'])->name('backup.settings');
    Route::post('/backup/skip-next', [BackupController::class, 'skipNext'])->name('backup.skip-next');
    Route::get('/backup/download/{file}', [BackupController::class, 'download'])->name('backup.download');
    Route::delete('/backup/delete/{file}', [BackupController::class, 'destroy'])->name('backup.delete');
});

// Super Admin Routes (require super admin role)
Route::middleware(['auth'])->group(function () {
    Route::get('/super-admin/dashboard', [App\Http\Controllers\SuperAdminController::class, 'dashboard'])->name('super-admin.dashboard');
});
