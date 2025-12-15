<?php

namespace App\Domains\Shop\Services;

use App\Domains\Shop\Repositories\ShopRepository;
use App\Domains\Telegram\Services\TelegramBotService;
use App\Models\Client;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;

class ShopCreationService
{
    public function __construct(
        private ShopRepository $shopRepository,
        private ShopLimitService $shopLimitService,
        private TelegramBotService $telegramBotService
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
            if (! empty($data['telegram_bot_token'])) {
                $this->registerTelegramBot($shop, $data['telegram_bot_token']);
            }

            return $shop;
        });
    }

    private function registerTelegramBot(Shop $shop, string $botToken)
    {
        // Генерируем URL для webhook
        $webhookUrl = route('telegram.webhook', ['botToken' => $botToken]);

        // Регистрируем webhook в Telegram
        $this->telegramBotService->registerWebhook($shop, $webhookUrl);
    }
}
