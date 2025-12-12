<?php

namespace App\Domains\Telegram\Services;

use App\Models\Shop;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class WebAppService
{
    public function getShopProducts(Shop $shop)
    {
        // Кэширование списка продуктов
        return Cache::remember("shop:{$shop->id}:products", now()->addHours(1), function () use ($shop) {
            return $shop->products()
                ->where('is_active', true)
                ->with('category')
                ->get();
        });
    }

    public function searchProducts(Shop $shop, string $query)
    {
        return Product::where('shop_id', $shop->id)
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->get();
    }
}