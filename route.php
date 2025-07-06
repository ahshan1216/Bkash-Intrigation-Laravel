<?php

use App\Http\Controllers\BkashController;



Route::post('/bkash/create', [BkashController::class, 'createPayment']);
Route::post('/bkash/execute', [BkashController::class, 'executePayment']);
Route::get('/bkash/status', [BkashController::class, 'paymentStatus'])->name('bkash.status');
Route::get('/bkash', [BkashController::class, 'index'])->name('bkash.index');