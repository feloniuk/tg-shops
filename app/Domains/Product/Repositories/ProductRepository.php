<?php

namespace App\Domains\Product\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function findByShopId(int $shopId)
    {
        return Product::where('shop_id', $shopId)->get();
    }
}