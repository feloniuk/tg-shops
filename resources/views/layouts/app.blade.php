<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Telegram Shops') }}</title>
</head>
<body>
    <nav>
        @foreach(config('laravellocalization.supportedLocales') as $localeCode => $properties)
            <a href="{{ LaravelLocalization::getLocalizedURL($localeCode) }}">
                {{ $properties['native'] }}
            </a>
        @endforeach
    </nav>

    @yield('content')
</body>
</html>