<?php

namespace App\Domains\Shop\Services;

use App\Domains\Shop\Repositories\ShopRepository;
use App\Domains\Shop\Services\ShopLimitService;
use App\Models\Client;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShopCreationService
{
    public function __construct(
        private ShopRepository $shopRepository,
        private ShopLimitService $shopLimitService
    ) {}

    public function createShop(Client $client, array $data): Shop
    {
        // Проверка лимитов перед созданием
        $this->shopLimitService->checkShopCreationLimits($client);

        return DB::transaction(function () use ($client, $data) {
            // Добавляем client_id в данные
            $data['client_id'] = $client->id;
            $data['status'] = 'active'; // Дефолтный статус

            // Создаем магазин
            $shop = $this->shopRepository->create($data);

            // Если передан токен Telegram Bot, регистрируем
            if (!empty($data['telegram_bot_token'])) {
                $this->registerTelegramBot($shop, $data['telegram_bot_token']);
            }

            return $shop;
        });
    }

    private function registerTelegramBot(Shop $shop, string $botToken)
    {
        // Логика регистрации Telegram Bot
        $shop->telegramBot()->create([
            'bot_token' => $botToken,
            'is_active' => true
        ]);
    }
}