<?php

use App\Http\Controllers\DashboardSessionController;
use App\Http\Controllers\VoucherLandingController;
use App\Http\Controllers\VoucherSalesController;
use Illuminate\Support\Facades\Route;

Route::get('/', VoucherLandingController::class)->name('voucher.landing');

Route::get('/dashboard/preview', [VoucherSalesController::class, 'preview'])->name('dashboard.preview');

Route::get('/login', [DashboardSessionController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [DashboardSessionController::class, 'store'])->middleware(['guest', 'throttle:10,1'])->name('login.store');
Route::post('/logout', [DashboardSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'can:view-voucher-sales', 'cache.headers:no_store;private'])->group(function () {
    Route::get('/dashboard', [VoucherSalesController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/pembelian', [VoucherSalesController::class, 'store'])->name('dashboard.sales.store');
});
