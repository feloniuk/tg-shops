<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BillingController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [ 
        'localeSessionRedirect', 
        'localizationRedirect', 
        'localeViewPath' 
    ]
], function() {
    Route::get('/', [HomeController::class, 'index'])
        ->name('home');

    Route::middleware(['auth'])->group(function () {
        Route::post('/billing/checkout', [BillingController::class, 'createCheckout'])
            ->name('billing.checkout');
        
        Route::get('/billing/success/{client}', [BillingController::class, 'successPayment'])
            ->name('billing.success');
        
        Route::get('/billing/cancel/{client}', [BillingController::class, 'cancelPayment'])
            ->name('billing.cancel');
    });
    
    Route::post('/stripe/webhook', [BillingController::class, 'handleWebhook'])
        ->name('stripe.webhook');
});