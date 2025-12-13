<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Shop $shop)
    {
        // Проверяем, что магазин принадлежит текущему клиенту
        if ($shop->client_id !== Auth::user()->client->id) {
            abort(403);
        }

        $orders = Order::where('shop_id', $shop->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('orders.index', compact('shop', 'orders'));
    }

    public function show(Shop $shop, Order $order)
    {
        // Проверяем, что заказ принадлежит магазину клиента
        if ($shop->client_id !== Auth::user()->client->id || $order->shop_id !== $shop->id) {
            abort(403);
        }

        return view('orders.show', compact('shop', 'order'));
    }

    public function updateStatus(Request $request, Shop $shop, Order $order)
    {
        // Проверяем, что заказ принадлежит магазину клиента
        if ($shop->client_id !== Auth::user()->client->id || $order->shop_id !== $shop->id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,refunded'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return redirect()
            ->route('orders.show', ['shop' => $shop, 'order' => $order])
            ->with('success', __('Order status updated successfully'));
    }
}
