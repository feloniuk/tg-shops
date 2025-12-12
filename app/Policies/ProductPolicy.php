<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use App\Models\Shop;

class ProductPolicy
{
    public function view(User $user, Product $product): bool
    {
        return $user->client->id === $product->shop->client_id;
    }

    public function create(User $user, Shop $shop): bool
    {
        $client = $user->client;
        return $client->id === $shop->client_id 
            && $client->isSubscriptionActive();
    }

    public function update(User $user, Product $product): bool
    {
        return $user->client->id === $product->shop->client_id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->client->id === $product->shop->client_id;
    }
}