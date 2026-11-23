<?php

use App\Http\Controllers\ContractController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('contracts.index');
});

// Contract Management Routes
Route::get('/contracts/data', [ContractController::class, 'data'])->name('contracts.data');
Route::resource('contracts', ContractController::class);

// Contract Alerts
Route::get('/alerts', [ContractController::class, 'alerts'])->name('contracts.alerts');

// Contract End Date Extension
Route::get('/contracts/{contract}/extend-contract-end', [ContractController::class, 'extendContractEndDate'])
    ->name('contracts.extend-contract-end');
Route::post('/contracts/{contract}/update-contract-end', [ContractController::class, 'updateContractEndDate'])
    ->name('contracts.update-contract-end');

// Performance Guarantee Extension
Route::get('/contracts/{contract}/extend-performance-guarantee', [ContractController::class, 'extendPerformanceGuarantee'])
    ->name('contracts.extend-performance-guarantee');
Route::post('/contracts/{contract}/update-performance-guarantee', [ContractController::class, 'updatePerformanceGuarantee'])
    ->name('contracts.update-performance-guarantee');
