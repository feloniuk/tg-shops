<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ShopManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Telegram\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

// Language Switcher
Route::get('/language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch');

// Telegram Webhook
Route::post('/telegram/webhook/{botToken}', [TelegramWebhookController::class, 'handle'])
    ->name('telegram.webhook');

// Stripe Webhook
Route::post('/stripe/webhook', [BillingController::class, 'handleWebhook'])
    ->name('stripe.webhook');

// Home route
Route::get('/', [HomeController::class, 'index'])
    ->name('home');

// Billing Routes
Route::middleware(['auth', 'throttle:10,1'])->group(function () {
    Route::post('/billing/checkout', [BillingController::class, 'createCheckout'])
        ->name('billing.checkout');

    Route::get('/billing/success/{client}', [BillingController::class, 'successPayment'])
        ->name('billing.success');

    Route::get('/billing/cancel/{client}', [BillingController::class, 'cancelPayment'])
        ->name('billing.cancel');
});

// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {
    // Client Dashboard
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');

    // Shops
    Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
    Route::post('/shops', [ShopController::class, 'store'])->name('shops.store');
    Route::put('/shops/{shop}', [ShopController::class, 'update'])->name('shops.update');

    // Products
    Route::get('/shops/{shop}/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/shops/{shop}/products', [ProductController::class, 'store'])->name('products.store');

    // Orders
    Route::get('/shops/{shop}/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/shops/{shop}/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/shops/{shop}/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

    // AI (with rate limiting to prevent abuse)
    Route::prefix('ai')->middleware(['throttle:20,1'])->group(function () {
        Route::post('/generate-product-description', [AIController::class, 'generateProductDescription'])
            ->name('ai.generate-description');

        Route::post('/generate-shop-greeting', [AIController::class, 'generateShopGreeting'])
            ->name('ai.generate-greeting');
    });
});

// Admin Routes
Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Управление пользователями
        Route::prefix('users')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])
                ->name('users.index');

            Route::get('/{user}', [UserManagementController::class, 'show'])
                ->name('users.show');

            Route::put('/{user}/status', [UserManagementController::class, 'updateStatus'])
                ->name('users.update-status');
        });

        // Управление магазинами
        Route::prefix('shops')->group(function () {
            Route::get('/', [ShopManagementController::class, 'index'])
                ->name('shops.index');

            Route::get('/{shop}', [ShopManagementController::class, 'show'])
                ->name('shops.show');

            Route::put('/{shop}/status', [ShopManagementController::class, 'updateStatus'])
                ->name('shops.update-status');
        });
    });
