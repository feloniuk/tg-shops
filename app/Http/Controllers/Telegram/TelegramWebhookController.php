<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Domains\Telegram\Services\TelegramMessageHandler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    private TelegramMessageHandler $messageHandler;

    public function __construct(TelegramMessageHandler $messageHandler)
    {
        $this->messageHandler = $messageHandler;
    }

    public function handle(Request $request, string $botToken): JsonResponse
    {
        try {
            $shop = Shop::where('telegram_bot_token', $botToken)->first();

            if (!$shop || !$shop->isActive()) {
                Log::warning('Telegram webhook received for inactive or non-existent shop', [
                    'bot_token' => substr($botToken, 0, 10) . '...'
                ]);

                return response()->json(['ok' => false]);
            }

            $update = $request->all();

            Log::info('Telegram webhook received', [
                'shop_id' => $shop->id,
                'update_id' => $update['update_id'] ?? null
            ]);

            $this->messageHandler->handleMessage($shop, $update);

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            Log::error('Telegram webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['ok' => false], 500);
        }
    }
}
