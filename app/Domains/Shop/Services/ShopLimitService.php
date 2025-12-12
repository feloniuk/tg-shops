<?php

namespace App\Domains\Shop\Services;

use App\Models\Client;
use App\Domains\Shop\Repositories\ShopRepository;
use App\Domains\Product\Repositories\ProductRepository;
use Illuminate\Validation\ValidationException;

class ShopLimitService
{
    public function __construct(
        private ShopRepository $shopRepository,
        private ProductRepository $productRepository
    ) {}

    public function checkShopCreationLimits(Client $client): void
    {
        $currentShopsCount = $this->shopRepository->countByClient($client->id);

        if ($currentShopsCount >= $client->plan->max_shops) {
            throw ValidationException::withMessages([
                'shops' => __('shop.limits.max_shops_reached', [
                    'limit' => $client->plan->max_shops
                ])
            ]);
        }
    }

    public function checkProductCreationLimits(Client $client, int $shopId): void
    {
        // Проверка лимита продуктов в магазине
        $currentProductsCount = $this->productRepository->countByShop($shopId);

        // Проверка лимита продуктов в плане
        if (!$client->plan->hasUnlimitedProducts() && 
            $currentProductsCount >= $client->plan->max_products) {
            throw ValidationException::withMessages([
                'products' => __('product.limits.max_products_reached', [
                    'limit' => $client->plan->max_products
                ])
            ]);
        }
    }
}