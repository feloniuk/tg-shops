<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Environments;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\LocalizationMiddleware;
use App\Http\Middleware\EnsureUserHasClient;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            LocalizationMiddleware::class,
            RoleMiddleware::class,
            EnsureUserHasClient::class
        ]);
    })
    ->withExceptions(function ($exceptions): void {
        //
    })->create();