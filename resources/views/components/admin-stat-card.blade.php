@props(['title', 'value', 'icon', 'color'])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 flex items-center">
    <div class="flex-shrink-0 {{ $color }}">
        <x-dynamic-component :component="$icon" class="h-8 w-8" />
    </div>
    <div class="ml-5">
        <h3 class="text-sm text-gray-500 dark:text-gray-300">{{ $title }}</h3>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $value }}</p>
    </div>
</div>