<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
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
});