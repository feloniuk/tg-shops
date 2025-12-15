<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(Request $request, $locale)
    {
        // Проверяем что локаль поддерживается
        if (! in_array($locale, ['en', 'uk'])) {
            abort(404);
        }

        // Сохраняем в сессию
        session(['locale' => $locale]);

        // Редиректим назад
        return redirect()->back();
    }
}
