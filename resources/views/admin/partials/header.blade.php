<header class="flex justify-between items-center py-4 px-6 bg-white dark:bg-gray-800">
    <div class="flex items-center">
        <button 
            @click="sidebarOpen = !sidebarOpen" 
            class="text-gray-500 focus:outline-none lg:hidden"
        >
            <x-heroicon-o-menu-alt-2 class="h-6 w-6" />
        </button>
    </div>

    <div class="flex items-center">
        <div x-data="{ dropdownOpen: false }" class="relative">
            <button 
                @click="dropdownOpen = !dropdownOpen"
                class="relative block h-8 w-8 rounded-full overflow-hidden shadow focus:outline-none"
            >
                <img 
                    class="h-full w-full object-cover" 
                    src="{{ auth()->user()->avatar ?? 'default-avatar.png' }}"
                    alt="{{ auth()->user()->name }}"
                />
            </button>

            <div 
                x-show="dropdownOpen" 
                @click.away="dropdownOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md overflow-hidden shadow-xl z-10"
            >
                <a 
                    href="{{ route('profile.edit') }}" 
                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                >
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button 
                        type="submit" 
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                    >
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>