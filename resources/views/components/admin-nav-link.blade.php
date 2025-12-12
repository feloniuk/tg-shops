@props(['href', 'active' => false])

<a 
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => ($active 
            ? 'flex items-center mt-4 py-2 px-6 bg-gray-700 bg-opacity-25 text-gray-100' 
            : 'flex items-center mt-4 py-2 px-6 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100')
    ]) }}
>
    {{ $slot }}
</a>