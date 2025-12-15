@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">
            Shop Details: {{ $shop->name }}
        </h2>
        <a href="{{ route('admin.shops.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
            &larr; Back to Shops
        </a>
    </div>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Shop Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">ID</p>
                            <p class="font-medium">{{ $shop->id }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Shop Name</p>
                            <p class="font-medium">{{ $shop->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Owner</p>
                            <p class="font-medium">
                                @if($shop->client && $shop->client->user)
                                    <a href="{{ route('admin.users.show', $shop->client->user) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $shop->client->user->name }} ({{ $shop->client->user->email }})
                                    </a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <p class="font-medium">
                                <span class="px-3 py-1 text-sm rounded
                                    {{ $shop->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $shop->status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $shop->status === 'blocked' ? 'bg-red-100 text-red-800' : '' }}
                                ">
                                    {{ ucfirst($shop->status) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Created</p>
                            <p class="font-medium">{{ $shop->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Last Updated</p>
                            <p class="font-medium">{{ $shop->updated_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-600">Welcome Message</p>
                            <p class="font-medium">{{ $shop->welcome_message ?? 'Not set' }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h4 class="text-md font-semibold mb-2">Update Status</h4>
                        <form method="POST" action="{{ route('admin.shops.update-status', $shop) }}" class="flex gap-4 items-center">
                            @csrf
                            @method('PUT')
                            <select name="status" class="rounded-md border-gray-300">
                                <option value="active" {{ $shop->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $shop->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="blocked" {{ $shop->status === 'blocked' ? 'selected' : '' }}>Blocked</option>
                            </select>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Update Status
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-2">Products</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $shop->products->count() }}</p>
                        <p class="text-sm text-gray-600">Total products in shop</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-2">Orders</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $shop->orders->count() }}</p>
                        <p class="text-sm text-gray-600">Total orders received</p>
                    </div>
                </div>
            </div>

            @if($shop->orders->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Recent Orders</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($shop->orders->take(10) as $order)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                {{ $order->customer_name }}<br>
                                                <span class="text-gray-500 text-xs">{{ $order->customer_phone }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                {{ number_format($order->total_amount, 2) }} грн
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 py-1 text-xs rounded
                                                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                                ">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                {{ $order->created_at->format('d.m.Y H:i') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
