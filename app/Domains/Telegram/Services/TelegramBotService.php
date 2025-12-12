<?php

namespace App\Domains\Telegram\Services;

use App\Models\Shop;
use Telegram\Bot\Api;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    public function registerWebhook(Shop $shop, string $webhookUrl): bool
    {
        try {
            $telegram = new Api($shop->telegram_bot_token);
            
            $response = $telegram->setWebhook([
                'url' => $webhookUrl,
                'allowed_updates' => ['message', 'callback_query']
            ]);

            // Сохраняем информацию о вебхуке
            $shop->telegramBot()->updateOrCreate(
                ['shop_id' => $shop->id],
                [
                    'bot_token' => $shop->telegram_bot_token,
                    'webhook_info' => $response
                ]
            );

            return true;
        } catch (\Exception $e) {
            Log::error('Telegram Webhook Registration Failed', [
                'shop_id' => $shop->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}