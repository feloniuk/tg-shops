<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Поддерживаемые локали
        $supportedLocales = ['en', 'uk'];

        // Проверяем сессию - приоритет 1
        if (session()->has('locale')) {
            $locale = session('locale');
            if (in_array($locale, $supportedLocales)) {
                app()->setLocale($locale);
                return $next($request);
            }
        }

        // Устанавливаем украинский по умолчанию
        app()->setLocale(config('app.locale', 'uk'));

        return $next($request);
    }
}