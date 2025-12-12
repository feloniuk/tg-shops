<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TelegramShops') }} - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <div 
        x-data="{ sidebarOpen: false }" 
        class="flex h-screen bg-gray-200 dark:bg-gray-800"
    >
        {{-- Sidebar --}}
        @include('admin.partials.sidebar')

        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Header --}}
            @include('admin.partials.header')

            {{-- Main Content --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 dark:bg-gray-900">
                <div class="container mx-auto px-6 py-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>