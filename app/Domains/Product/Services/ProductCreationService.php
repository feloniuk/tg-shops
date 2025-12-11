<?php

namespace App\Domains\Product\Services;

use App\Domains\Product\Repositories\ProductRepository;
use App\Models\Shop;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class ProductCreationService
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    /**
     * Создание продукта с проверкой лимитов
     */
    public function createProduct(Shop $shop, array $data): Product
    {
        // Получаем клиента через связь
        $client = $shop->client;

        // Проверка лимита продуктов для тарифного плана
        $currentProductCount = $shop->products()->count();
        $maxProducts = $client->plan->max_products;

        if ($currentProductCount >= $maxProducts) {
            throw ValidationException::withMessages([
                'products' => __('product.limits.max_products_reached', [
                    'limit' => $maxProducts
                ])
            ]);
        }

        // Проверка уникальности названия продукта в магазине
        $this->validateProductName($shop, $data['name']);

        // Добавляем shop_id в данные
        $data['shop_id'] = $shop->id;

        // Создаем продукт
        return $this->productRepository->create($data);
    }

    /**
     * Проверка уникальности названия продукта
     */
    private function validateProductName(Shop $shop, string $name): void
    {
        $existingProduct = $shop->products()->where('name', $name)->exists();
        
        if ($existingProduct) {
            throw ValidationException::withMessages([
                'name' => __('product.validation.unique_name')
            ]);
        }
    }
}