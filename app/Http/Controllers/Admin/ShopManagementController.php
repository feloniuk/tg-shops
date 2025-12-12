<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Shop::with('client');

        // Фильтрация
        if ($request->has('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $shops = $query->paginate(20);

        return view('admin.shops.index', [
            'shops' => $shops
        ]);
    }

    public function show(Shop $shop)
    {
        $shop->load(['client', 'products', 'orders']);
        return view('admin.shops.show', [
            'shop' => $shop
        ]);
    }

    public function updateStatus(Request $request, Shop $shop)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,blocked'
        ]);

        $shop->update($validated);

        return redirect()
            ->route('admin.shops.show', $shop)
            ->with('success', 'Shop status updated successfully');
    }
}