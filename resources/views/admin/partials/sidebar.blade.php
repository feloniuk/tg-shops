<aside 
    x-show="sidebarOpen" 
    x-transition:enter="transition ease-in-out duration-300"
    x-transition:enter-start="opacity-0 transform -translate-x-full"
    x-transition:enter-end="opacity-100 transform translate-x-0"
    x-transition:leave="transition ease-in-out duration-300"
    x-transition:leave-start="opacity-100 transform translate-x-0"
    x-transition:leave-end="opacity-0 transform -translate-x-full"
    class="fixed z-20 inset-y-0 left-0 w-64 transition duration-300 transform bg-white dark:bg-gray-800 overflow-y-auto lg:translate-x-0 lg:static lg:inset-0"
>
    <div class="flex items-center justify-center mt-8">
        <div class="flex items-center">
            <span class="text-2xl font-bold text-gray-800 dark:text-white">
                Admin Panel
            </span>
        </div>
    </div>

    <nav class="mt-10">
        <x-admin-nav-link href="{{ route('admin.dashboard') }}">
            <x-heroicon-o-home class="h-6 w-6 mr-3" />
            Dashboard
        </x-admin-nav-link>

        <x-admin-nav-link href="{{ route('admin.users.index') }}">
            <x-heroicon-o-users class="h-6 w-6 mr-3" />
            Users
        </x-admin-nav-link>

        <x-admin-nav-link href="{{ route('admin.shops.index') }}">
            <x-heroicon-o-shopping-bag class="h-6 w-6 mr-3" />
            Shops
        </x-admin-nav-link>

        {{-- Динамически генерируемые пункты меню --}}
    </nav>
</aside>