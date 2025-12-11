<?php

namespace App\Domains\Shop\Services;

use App\Domains\Shop\Repositories\ShopRepository;
use App\Models\Client;
use App\Models\Shop;
use Illuminate\Validation\ValidationException;

class ShopCreationService
{
    public function __construct(
        private ShopRepository $shopRepository
    ) {}

    /**
     * Создание магазина с проверкой лимитов
     */
    public function createShop(Client $client, array $data): Shop
    {
        // Проверка лимита магазинов
        if (!$client->canCreateShop()) {
            throw ValidationException::withMessages([
                'shops' => __('shop.limits.max_shops_reached', [
                    'limit' => $client->plan->max_shops
                ])
            ]);
        }

        // Проверка уникальности имени магазина
        $this->validateShopName($client, $data['name']);

        // Добавляем client_id в данные
        $data['client_id'] = $client->id;

        // Создаем магазин
        return $this->shopRepository->create($data);
    }

    /**
     * Проверка уникальности имени магазина для клиента
     */
    private function validateShopName(Client $client, string $name): void
    {
        $existingShop = $client->shops()->where('name', $name)->exists();
        
        if ($existingShop) {
            throw ValidationException::withMessages([
                'name' => __('shop.validation.unique_name')
            ]);
        }
    }
}