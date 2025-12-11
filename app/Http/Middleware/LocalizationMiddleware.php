<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Определяем локаль из URL или используем дефолтную
        $locale = $request->segment(1);
        
        $supportedLocales = ['en', 'uk'];
        
        if (in_array($locale, $supportedLocales)) {
            app()->setLocale($locale);
        } else {
            app()->setLocale(config('app.locale', 'en'));
        }

        return $next($request);
    }
}