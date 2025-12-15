<?php

namespace App\Domains\Product\Repositories;

use App\Models\Product;
use Illuminate\Support\Collection;

class ProductRepository
{
    public function findByShopId(int $shopId): Collection
    {
        return Product::where('shop_id', $shopId)->get();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function findActiveProductsByShop(int $shopId): Collection
    {
        return Product::where('shop_id', $shopId)
            ->where('is_active', true)
            ->get();
    }

    public function countByShop(int $shopId): int
    {
        return Product::where('shop_id', $shopId)->count();
    }
}
