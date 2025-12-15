<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Shops') }}
            </h2>
            <button onclick="document.getElementById('createShopModal').classList.remove('hidden')"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                {{ __('Create Shop') }}
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($shops->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($shops as $shop)
                                <div class="border rounded-lg p-6 hover:shadow-lg transition">
                                    <div class="flex justify-between items-start mb-4">
                                        <h3 class="text-lg font-semibold">{{ $shop->name }}</h3>
                                        <span class="px-2 py-1 text-xs rounded
                                            {{ $shop->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $shop->status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $shop->status === 'blocked' ? 'bg-red-100 text-red-800' : '' }}
                                        ">
                                            {{ ucfirst($shop->status) }}
                                        </span>
                                    </div>

                                    <p class="text-sm text-gray-600 mb-4">
                                        {{ Str::limit($shop->welcome_message, 100) }}
                                    </p>

                                    <div class="flex gap-2">
                                        <a href="{{ route('products.index', $shop) }}"
                                           class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                                            Products
                                        </a>
                                        <a href="{{ route('orders.index', $shop) }}"
                                           class="flex-1 text-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                                            Orders
                                        </a>
                                    </div>

                                    @if($shop->telegram_bot_token)
                                        <div class="mt-4 text-xs text-gray-500">
                                            <p>Bot configured ✓</p>
                                        </div>
                                    @else
                                        <div class="mt-4 text-xs text-orange-600">
                                            <p>⚠ Bot token not set</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No shops</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new shop.</p>
                            <div class="mt-6">
                                <button onclick="document.getElementById('createShopModal').classList.remove('hidden')"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                                    Create Your First Shop
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Plan info -->
            @if($client && $client->plan)
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-2">Current Plan: {{ $client->plan->name }}</h3>
                        <div class="text-sm text-gray-600">
                            <p>Shops: {{ $shops->count() }} / {{ $client->plan->max_shops }}</p>
                            <p>Max Products: {{ $client->plan->max_products }}</p>
                            @if($client->plan_expires_at)
                                <p>Expires: {{ $client->plan_expires_at->format('d.m.Y') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Create Shop Modal -->
    <div id="createShopModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Create New Shop</h3>
                <button onclick="document.getElementById('createShopModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('shops.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Shop Name</label>
                    <input type="text" name="name" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telegram Bot Token (optional)</label>
                    <input type="text" name="telegram_bot_token"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Welcome Message</label>
                    <textarea name="welcome_message" rows="3"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button"
                            onclick="document.getElementById('createShopModal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Create Shop
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
