<?php

namespace App\Domains\Shop\Repositories;

use App\Models\Shop;
use Illuminate\Support\Collection;

class ShopRepository
{
    public function findByClientId(int $clientId): Collection
    {
        return Shop::where('client_id', $clientId)->get();
    }

    public function create(array $data): Shop
    {
        return Shop::create($data);
    }

    public function update(Shop $shop, array $data): bool
    {
        return $shop->update($data);
    }
}