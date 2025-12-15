@props(['orders'])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
        Recent Orders
    </h2>
    <div class="divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($orders ?? [] as $order)
            <div class="flex items-center justify-between py-3">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        Order #{{ $order->id }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $order->customer_name }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        ${{ number_format($order->total_amount / 100, 2) }}
                    </p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                        @if($order->status === 'completed') bg-green-100 text-green-800
                        @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400 py-3">
                No recent orders
            </p>
        @endforelse
    </div>
</div>
