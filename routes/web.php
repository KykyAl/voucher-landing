<?php

use App\Http\Controllers\VoucherLandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', VoucherLandingController::class)->name('voucher.landing');
