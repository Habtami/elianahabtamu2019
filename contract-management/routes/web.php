<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
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

// Logout: primary method is POST, but provide a safe GET handler that also invalidates session
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// Protected Routes (require authentication)
Route::middleware(['auth', 'NoCache'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::match(['post', 'put', 'patch'], '/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Contract Management Routes
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

    // Admin Routes (require admin role)
    Route::middleware(['auth', 'CheckRole:admin'])->group(function () {
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/users', [AdminController::class, 'index'])->name('users.index');
            Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
            Route::post('/users', [AdminController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [AdminController::class, 'edit'])->name('users.edit');
            Route::match(['post', 'put', 'patch'], '/users/{user}', [AdminController::class, 'update'])->name('users.update');
            Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleStatus'])->name('users.toggleStatus');
            Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
            Route::get('/permissions', [AdminController::class, 'permissions'])->name('permissions');
        });
    });
});
