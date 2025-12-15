<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <!-- Language Switcher -->
        <div class="absolute top-4 right-4">
            <div class="flex items-center space-x-2">
                <a href="{{ route('language.switch', 'uk') }}"
                   class="px-3 py-1 text-sm rounded {{ app()->getLocale() == 'uk' ? 'bg-blue-600 text-white' : 'text-gray-700 bg-white hover:bg-gray-100' }}">
                    UK
                </a>
                <a href="{{ route('language.switch', 'en') }}"
                   class="px-3 py-1 text-sm rounded {{ app()->getLocale() == 'en' ? 'bg-blue-600 text-white' : 'text-gray-700 bg-white hover:bg-gray-100' }}">
                    EN
                </a>
            </div>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
