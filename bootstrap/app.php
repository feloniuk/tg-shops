<?php

use App\Http\Middleware\EnsureUserHasClient;
use App\Http\Middleware\LocalizationMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'ensure.client' => EnsureUserHasClient::class,
        ]);

        // Add localization middleware to web group
        $middleware->web(append: [
            LocalizationMiddleware::class,
        ]);
    })
    ->withExceptions(function ($exceptions): void {
        //
    })->create();
