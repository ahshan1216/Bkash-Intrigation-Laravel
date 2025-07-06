<?php

use App\Http\Controllers\BkashController;



Route::post('/bkash/create', [BkashController::class, 'createPayment']);
Route::get('/bkash/execute', [BkashController::class, 'executePayment'])->name('bkash.execute');
Route::get('/bkash/status', [BkashController::class, 'paymentStatus'])->name('bkash.status');
Route::get('/bkash', [BkashController::class, 'index'])->name('bkash.index');