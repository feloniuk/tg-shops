<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shop;
use App\Mail\OrderStatusChangedMailable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Shop $shop)
    {
        $client = Auth::user()->client;

        if (!$client) {
            abort(403, 'Client profile not found.');
        }

        // Проверяем, что магазин принадлежит текущему клиенту
        if ($shop->client_id !== $client->id) {
            abort(403);
        }

        $orders = Order::where('shop_id', $shop->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('orders.index', compact('shop', 'orders'));
    }

    public function show(Shop $shop, Order $order)
    {
        $client = Auth::user()->client;

        if (!$client) {
            abort(403, 'Client profile not found.');
        }

        // Проверяем, что заказ принадлежит магазину клиента
        if ($shop->client_id !== $client->id || $order->shop_id !== $shop->id) {
            abort(403);
        }

        return view('orders.show', compact('shop', 'order'));
    }

    public function updateStatus(Request $request, Shop $shop, Order $order)
    {
        $client = Auth::user()->client;

        if (!$client) {
            abort(403, 'Client profile not found.');
        }

        // Проверяем, что заказ принадлежит магазину клиента
        if ($shop->client_id !== $client->id || $order->shop_id !== $shop->id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,refunded'
        ]);

        // Сохраняем старый статус для email уведомления
        $oldStatus = $order->status;

        $order->update([
            'status' => $request->status
        ]);

        // Отправка email уведомления клиенту при изменении статуса
        if ($order->customer_email && $oldStatus !== $request->status) {
            try {
                Mail::to($order->customer_email)->send(new OrderStatusChangedMailable($order, $oldStatus));
            } catch (\Exception $e) {
                Log::error('Failed to send order status changed email', [
                    'order_id' => $order->id,
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return redirect()
            ->route('orders.show', ['shop' => $shop, 'order' => $order])
            ->with('success', __('Order status updated successfully'));
    }
}
