<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Shop;

class ShopPolicy
{
    public function view(User $user, Shop $shop): bool
    {
        return $user->client->id === $shop->client_id;
    }

    public function create(User $user): bool
    {
        $client = $user->client;
        return $client->canCreateShop() && $client->isSubscriptionActive();
    }

    public function update(User $user, Shop $shop): bool
    {
        return $user->client->id === $shop->client_id;
    }

    public function delete(User $user, Shop $shop): bool
    {
        return $user->client->id === $shop->client_id;
    }
}