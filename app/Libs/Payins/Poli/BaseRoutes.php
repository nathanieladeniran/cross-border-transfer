<?php

use Illuminate\Support\Facades\Route;
use Payins\Poli\BaseStart;

//This is very important for POLI
// Route::post('nudge/checkout/webhook/{country_code?}', [BaseStart::class, 'checkoutWebhook'])
// ->name('api.nudge.checkout.webhook')
// ->withoutMiddleware(['auth:sanctum', 'auth.session', 'is_customer']);

//This is very important for POLI

Route::prefix('poli')->group(function () {

    Route::post('successful', function () {
        return (new BaseStart)->checkoutSuccess();
    })->name('api.poli.successful');

    Route::post('failed', function () {
        return (new BaseStart)->checkoutFailed();
    })->name('api.poli.failed');

    Route::post('cancelled', function () {
        return (new BaseStart)->checkoutCancelled();
    })->name('api.poli.cancelled');
});

Route::prefix('poli_responses')->group(function () {
    Route::get('poli_checkout/{reference?}/{sent_amount?}/{received_amount?}/{status?}/{date_time?}/{transfer_type?}', [BaseStart::class, 'frontend_poli'])->name('poli_responses.poli_checkout');
});
