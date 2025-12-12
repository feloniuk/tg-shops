<?php

namespace App\Domains\Telegram\Services;

use GuzzleHttp\Client;
use App\Models\Shop;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramBotService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.telegram.org/bot'
        ]);
    }

    public function registerWebhook(Shop $shop, string $webhookUrl): bool
    {
        try {
            $response = $this->client->post($shop->telegram_bot_token . '/setWebhook', [
                'form_params' => [
                    'url' => $webhookUrl,
                    'allowed_updates' => json_encode(['message', 'callback_query', 'inline_query'])
                ]
            ]);

            $botInfo = $this->getBotInfo($shop->telegram_bot_token);

            $shop->telegramBot()->updateOrCreate(
                ['shop_id' => $shop->id],
                [
                    'bot_token' => $shop->telegram_bot_token,
                    'bot_username' => $botInfo['username'] ?? ('tg_shop_' . Str::random(8)),
                    'webhook_info' => json_decode($response->getBody(), true),
                    'is_active' => true
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

    private function getBotInfo(string $token): ?array
    {
        try {
            $response = $this->client->get($token . '/getMe');
            $data = json_decode($response->getBody(), true);
            
            return $data['result'] ?? null;
        } catch (\Exception $e) {
            Log::error('Failed to get Telegram Bot Info', [
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    public function validateBotToken(string $token): bool
    {
        try {
            $response = $this->client->get($token . '/getMe');
            $data = json_decode($response->getBody(), true);
            
            return $data['ok'] === true;
        } catch (\Exception $e) {
            return false;
        }
    }
}