<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $client = $user->client;

        // If user doesn't have a client record, create one with default plan
        if (!$client) {
            $defaultPlan = \App\Models\Plan::where('name', 'Free')->first();
            if (!$defaultPlan) {
                abort(500, 'Default plan not found. Please run database seeders.');
            }

            $client = \App\Models\Client::create([
                'user_id' => $user->id,
                'company_name' => $user->name,
                'plan_id' => $defaultPlan->id,
                'plan_expires_at' => now()->addYear(),
            ]);

            $user->load('client');
        }

        $shops = $client->shops;
        $shopIds = $shops->pluck('id')->toArray();

        // Handle empty shops array
        if (empty($shopIds)) {
            $shopIds = [0]; // Use impossible ID to avoid SQL errors
        }

        // Базовая статистика
        $stats = [
            'total_orders' => Order::whereIn('shop_id', $shopIds)->count(),
            'pending_orders' => Order::whereIn('shop_id', $shopIds)->where('status', 'pending')->count(),
            'completed_orders' => Order::whereIn('shop_id', $shopIds)->where('status', 'completed')->count(),
            'total_revenue' => Order::whereIn('shop_id', $shopIds)
                ->where('status', 'completed')
                ->sum('total_amount'),
            'total_products' => Product::whereIn('shop_id', $shopIds)->count(),
            'active_products' => Product::whereIn('shop_id', $shopIds)->where('is_active', true)->count(),
        ];

        // Средний чек
        $stats['average_order_value'] = $stats['total_orders'] > 0
            ? $stats['total_revenue'] / $stats['total_orders']
            : 0;

        // Заказы за последние 7 дней (для графика)
        $ordersLastWeek = Order::whereIn('shop_id', $shopIds)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Заказы за последние 30 дней
        $ordersLastMonth = Order::whereIn('shop_id', $shopIds)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // Доход за последние 30 дней
        $revenueLastMonth = Order::whereIn('shop_id', $shopIds)
            ->where('created_at', '>=', now()->subDays(30))
            ->where('status', 'completed')
            ->sum('total_amount');

        // Топ-5 товаров по продажам
        $topProducts = Order::whereIn('shop_id', $shopIds)
            ->where('status', 'completed')
            ->get()
            ->flatMap(function ($order) {
                return collect($order->order_details)->map(function ($item) use ($order) {
                    return [
                        'product_id' => $item['product_id'] ?? null,
                        'name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'total' => $item['total']
                    ];
                });
            })
            ->groupBy('product_id')
            ->map(function ($items) {
                return [
                    'name' => $items->first()['name'],
                    'quantity' => $items->sum('quantity'),
                    'revenue' => $items->sum('total')
                ];
            })
            ->sortByDesc('quantity')
            ->take(5);

        // Последние заказы
        $recentOrders = Order::whereIn('shop_id', $shopIds)
            ->with('shop')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('client.dashboard', compact(
            'client',
            'stats',
            'ordersLastWeek',
            'ordersLastMonth',
            'revenueLastMonth',
            'topProducts',
            'recentOrders'
        ));
    }
}
