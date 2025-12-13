<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Order') }} #{{ $order->id }}
            </h2>
            <a href="{{ route('orders.index', ['shop' => $shop]) }}" class="text-sm text-blue-600 hover:text-blue-800">
                {{ __('Back to Orders') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Order Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Order Information') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">{{ __('Order Number') }}</p>
                            <p class="font-medium">#{{ $order->id }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">{{ __('Date') }}</p>
                            <p class="font-medium">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">{{ __('Customer Name') }}</p>
                            <p class="font-medium">{{ $order->customer_name }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">{{ __('Customer Phone') }}</p>
                            <p class="font-medium">{{ $order->customer_phone }}</p>
                        </div>

                        @if($order->customer_email)
                        <div>
                            <p class="text-sm text-gray-500">{{ __('Customer Email') }}</p>
                            <p class="font-medium">{{ $order->customer_email }}</p>
                        </div>
                        @endif

                        <div>
                            <p class="text-sm text-gray-500">{{ __('Total Amount') }}</p>
                            <p class="font-medium text-lg">{{ number_format($order->total_amount, 2) }} {{ __('UAH') }}</p>
                        </div>
                    </div>

                    @if($order->customer_comment)
                    <div class="mt-4">
                        <p class="text-sm text-gray-500">{{ __('Customer Comment') }}</p>
                        <p class="font-medium">{{ $order->customer_comment }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Order Items') }}</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        {{ __('Product') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        {{ __('Price') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        {{ __('Quantity') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        {{ __('Total') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($order->order_details as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $item['name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ number_format($item['price'], 2) }} {{ __('UAH') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item['quantity'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ number_format($item['total'], 2) }} {{ __('UAH') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right font-semibold">
                                        {{ __('Total:') }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-lg">
                                        {{ number_format($order->total_amount, 2) }} {{ __('UAH') }}
                                    </td>
                                </tr>
            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Update Status -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Update Order Status') }}</h3>

                    <form method="POST" action="{{ route('orders.update-status', ['shop' => $shop, 'order' => $order]) }}">
                        @csrf
                        @method('PUT')

                        <div class="flex items-end gap-4">
                            <div class="flex-1">
                                <label for="status" class="block text-sm font-medium text-gray-700">
                                    {{ __('Status') }}
                                </label>
                                <select id="status" name="status" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>
                                        {{ __('Pending') }}
                                    </option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>
                                        {{ __('Processing') }}
                                    </option>
                                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>
                                        {{ __('Completed') }}
                                    </option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>
                                        {{ __('Cancelled') }}
                                    </option>
                                    <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>
                                        {{ __('Refunded') }}
                                    </option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                {{ __('Update Status') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
