<?php

namespace App\Domains\Telegram\Services;

use App\Models\Shop;
use App\Models\Product;
use App\Models\Order;
use App\Models\TelegramSession;
use App\Models\ShopCategory;
use Illuminate\Support\Facades\Log;

class TelegramMessageHandler
{
    private TelegramBotService $botService;

    public function __construct(TelegramBotService $botService)
    {
        $this->botService = $botService;
    }

    public function handleMessage(Shop $shop, array $update): void
    {
        try {
            if (isset($update['message'])) {
                $this->handleTextMessage($shop, $update['message']);
            } elseif (isset($update['callback_query'])) {
                $this->handleCallbackQuery($shop, $update['callback_query']);
            }
        } catch (\Exception $e) {
            Log::error('Telegram Message Handler Error', [
                'shop_id' => $shop->id,
                'error' => $e->getMessage(),
                'update' => $update
            ]);
        }
    }

    private function handleTextMessage(Shop $shop, array $message): void
    {
        $telegramUserId = $message['from']['id'];
        $text = $message['text'] ?? '';
        $chatId = $message['chat']['id'];

        $session = $this->getOrCreateSession($shop, $telegramUserId);
        $session->touchActivity();

        if ($text === '/start') {
            $this->handleStartCommand($shop, $session, $chatId);
            return;
        }

        switch ($session->state) {
            case 'awaiting_contact':
                $this->handleContactInput($shop, $session, $chatId, $text);
                break;
            case 'awaiting_comment':
                $this->handleCommentInput($shop, $session, $chatId, $text);
                break;
            default:
                $this->sendMainMenu($shop, $chatId);
                break;
        }
    }

    private function handleCallbackQuery(Shop $shop, array $callbackQuery): void
    {
        $telegramUserId = $callbackQuery['from']['id'];
        $chatId = $callbackQuery['message']['chat']['id'];
        $data = $callbackQuery['data'];

        $session = $this->getOrCreateSession($shop, $telegramUserId);
        $session->touchActivity();

        $parts = explode(':', $data);
        $action = $parts[0];

        switch ($action) {
            case 'catalog':
                $this->showCatalog($shop, $chatId);
                break;
            case 'category':
                $categoryId = (int)($parts[1] ?? 0);
                $this->showCategory($shop, $chatId, $categoryId);
                break;
            case 'product':
                $productId = (int)($parts[1] ?? 0);
                $this->showProduct($shop, $chatId, $productId);
                break;
            case 'add_to_cart':
                $productId = (int)($parts[1] ?? 0);
                $this->addToCart($shop, $session, $chatId, $productId);
                break;
            case 'view_cart':
                $this->showCart($shop, $session, $chatId);
                break;
            case 'remove_from_cart':
                $productId = (int)($parts[1] ?? 0);
                $this->removeFromCart($shop, $session, $chatId, $productId);
                break;
            case 'checkout':
                $this->startCheckout($shop, $session, $chatId);
                break;
            case 'cancel_order':
                $this->cancelOrder($shop, $session, $chatId);
                break;
            case 'skip_comment':
                $this->createOrder($shop, $session, $chatId);
                break;
        }

        // Answer callback query
        $this->answerCallbackQuery($shop, $callbackQuery['id']);
    }

    private function handleStartCommand(Shop $shop, TelegramSession $session, int $chatId): void
    {
        $welcomeMessage = $shop->welcome_message ?? "Вітаємо в {$shop->name}!";

        $this->sendMessage($shop, $chatId, $welcomeMessage);
        $this->sendMainMenu($shop, $chatId);
    }

    private function sendMainMenu(Shop $shop, int $chatId): void
    {
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '📦 Каталог товарів', 'callback_data' => 'catalog']
                ],
                [
                    ['text' => '🛒 Кошик', 'callback_data' => 'view_cart']
                ]
            ]
        ];

        $this->sendMessage($shop, $chatId, 'Оберіть дію:', $keyboard);
    }

    private function showCatalog(Shop $shop, int $chatId): void
    {
        $categories = $shop->categories()->get();

        if ($categories->isEmpty()) {
            $this->showAllProducts($shop, $chatId);
            return;
        }

        $keyboard = ['inline_keyboard' => []];

        foreach ($categories as $category) {
            $keyboard['inline_keyboard'][] = [
                ['text' => $category->name, 'callback_data' => "category:{$category->id}"]
            ];
        }

        $keyboard['inline_keyboard'][] = [
            ['text' => '◀️ Назад', 'callback_data' => 'start']
        ];

        $this->sendMessage($shop, $chatId, '📂 Оберіть категорію:', $keyboard);
    }

    private function showCategory(Shop $shop, int $chatId, int $categoryId): void
    {
        $category = ShopCategory::find($categoryId);

        if (!$category) {
            $this->sendMessage($shop, $chatId, 'Категорія не знайдена');
            return;
        }

        $products = Product::where('shop_id', $shop->id)
            ->where('category_id', $categoryId)
            ->get();

        if ($products->isEmpty()) {
            $this->sendMessage($shop, $chatId, 'В цій категорії поки немає товарів');
            return;
        }

        $keyboard = ['inline_keyboard' => []];

        foreach ($products as $product) {
            $keyboard['inline_keyboard'][] = [
                ['text' => "{$product->name} - {$product->price} грн", 'callback_data' => "product:{$product->id}"]
            ];
        }

        $keyboard['inline_keyboard'][] = [
            ['text' => '◀️ Назад до категорій', 'callback_data' => 'catalog']
        ];

        $this->sendMessage($shop, $chatId, "📂 {$category->name}", $keyboard);
    }

    private function showAllProducts(Shop $shop, int $chatId): void
    {
        $products = Product::where('shop_id', $shop->id)->get();

        if ($products->isEmpty()) {
            $this->sendMessage($shop, $chatId, 'Поки немає товарів');
            return;
        }

        $keyboard = ['inline_keyboard' => []];

        foreach ($products as $product) {
            $keyboard['inline_keyboard'][] = [
                ['text' => "{$product->name} - {$product->price} грн", 'callback_data' => "product:{$product->id}"]
            ];
        }

        $keyboard['inline_keyboard'][] = [
            ['text' => '◀️ Назад', 'callback_data' => 'start']
        ];

        $this->sendMessage($shop, $chatId, '📦 Всі товари:', $keyboard);
    }

    private function showProduct(Shop $shop, int $chatId, int $productId): void
    {
        $product = Product::find($productId);

        if (!$product || $product->shop_id !== $shop->id) {
            $this->sendMessage($shop, $chatId, 'Товар не знайдено');
            return;
        }

        $description = "*{$product->name}*\n\n";
        $description .= $product->description ? "{$product->description}\n\n" : '';
        $description .= "💰 Ціна: *{$product->price} грн*";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '➕ Додати в кошик', 'callback_data' => "add_to_cart:{$product->id}"]
                ],
                [
                    ['text' => '◀️ Назад', 'callback_data' => $product->category_id ? "category:{$product->category_id}" : 'catalog']
                ]
            ]
        ];

        $this->sendMessage($shop, $chatId, $description, $keyboard, 'Markdown');
    }

    private function addToCart(Shop $shop, TelegramSession $session, int $chatId, int $productId): void
    {
        $product = Product::find($productId);

        if (!$product || $product->shop_id !== $shop->id) {
            $this->sendMessage($shop, $chatId, 'Товар не знайдено');
            return;
        }

        $session->addToCart($productId);

        $this->sendMessage($shop, $chatId, "✅ {$product->name} додано в кошик!");
        $this->sendMainMenu($shop, $chatId);
    }

    private function showCart(Shop $shop, TelegramSession $session, int $chatId): void
    {
        $cart = $session->getCart();

        if (empty($cart)) {
            $this->sendMessage($shop, $chatId, '🛒 Ваш кошик порожній');
            $this->sendMainMenu($shop, $chatId);
            return;
        }

        $message = "🛒 *Ваш кошик:*\n\n";
        $keyboard = ['inline_keyboard' => []];

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $total = $product->price * $item['quantity'];
                $message .= "• {$product->name}\n";
                $message .= "  {$item['quantity']} x {$product->price} грн = {$total} грн\n\n";

                $keyboard['inline_keyboard'][] = [
                    ['text' => "❌ Видалити {$product->name}", 'callback_data' => "remove_from_cart:{$productId}"]
                ];
            }
        }

        $total = $session->getCartTotal();
        $message .= "*Загальна сума: {$total} грн*";

        $keyboard['inline_keyboard'][] = [
            ['text' => '✅ Оформити замовлення', 'callback_data' => 'checkout']
        ];
        $keyboard['inline_keyboard'][] = [
            ['text' => '◀️ Назад', 'callback_data' => 'start']
        ];

        $this->sendMessage($shop, $chatId, $message, $keyboard, 'Markdown');
    }

    private function removeFromCart(Shop $shop, TelegramSession $session, int $chatId, int $productId): void
    {
        $session->removeFromCart($productId);
        $this->sendMessage($shop, $chatId, '✅ Товар видалено з кошика');
        $this->showCart($shop, $session, $chatId);
    }

    private function startCheckout(Shop $shop, TelegramSession $session, int $chatId): void
    {
        if (empty($session->getCart())) {
            $this->sendMessage($shop, $chatId, '🛒 Ваш кошик порожній');
            return;
        }

        $session->state = 'awaiting_contact';
        $session->save();

        $this->sendMessage($shop, $chatId, "Будь ласка, введіть ваше ім'я та номер телефону\n\nНаприклад: Іван, +380123456789");
    }

    private function handleContactInput(Shop $shop, TelegramSession $session, int $chatId, string $text): void
    {
        $data = $session->data ?? [];
        $data['contact'] = $text;
        $session->data = $data;
        $session->state = 'awaiting_comment';
        $session->save();

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Без коментаря', 'callback_data' => 'skip_comment']
                ]
            ]
        ];

        $this->sendMessage($shop, $chatId, "Додайте коментар до замовлення або натисніть 'Без коментаря'", $keyboard);
    }

    private function handleCommentInput(Shop $shop, TelegramSession $session, int $chatId, string $text): void
    {
        $data = $session->data ?? [];
        $data['comment'] = $text;
        $session->data = $data;
        $session->save();

        $this->createOrder($shop, $session, $chatId);
    }

    private function createOrder(Shop $shop, TelegramSession $session, int $chatId): void
    {
        $cart = $session->getCart();
        $orderDetails = [];

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $orderDetails[] = [
                    'product_id' => $productId,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'total' => $product->price * $item['quantity']
                ];
            }
        }

        $contact = $session->data['contact'] ?? 'Не вказано';
        $comment = $session->data['comment'] ?? '';

        $order = Order::create([
            'shop_id' => $shop->id,
            'customer_name' => $contact,
            'customer_phone' => $contact,
            'total_amount' => $session->getCartTotal(),
            'status' => 'pending',
            'order_details' => $orderDetails,
            'customer_comment' => $comment
        ]);

        $session->clearCart();
        $session->state = 'browsing';
        $session->save();

        $message = "✅ *Замовлення #{$order->id} оформлено!*\n\n";
        $message .= "Загальна сума: *{$order->total_amount} грн*\n\n";
        $message .= "Ми зв'яжемося з вами найближчим часом!";

        $this->sendMessage($shop, $chatId, $message, null, 'Markdown');
        $this->sendMainMenu($shop, $chatId);

        Log::info('Order created via Telegram', [
            'order_id' => $order->id,
            'shop_id' => $shop->id,
            'total' => $order->total_amount
        ]);
    }

    private function cancelOrder(Shop $shop, TelegramSession $session, int $chatId): void
    {
        $session->clearCart();
        $session->state = 'browsing';
        $session->save();

        $this->sendMessage($shop, $chatId, '❌ Замовлення скасовано');
        $this->sendMainMenu($shop, $chatId);
    }

    private function getOrCreateSession(Shop $shop, int $telegramUserId): TelegramSession
    {
        return TelegramSession::firstOrCreate(
            [
                'shop_id' => $shop->id,
                'telegram_user_id' => $telegramUserId
            ],
            [
                'state' => 'browsing',
                'data' => []
            ]
        );
    }

    private function sendMessage(Shop $shop, int $chatId, string $text, ?array $replyMarkup = null, string $parseMode = null): void
    {
        try {
            $botToken = $shop->telegram_bot_token;
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

            $params = [
                'chat_id' => $chatId,
                'text' => $text
            ];

            if ($replyMarkup) {
                $params['reply_markup'] = json_encode($replyMarkup);
            }

            if ($parseMode) {
                $params['parse_mode'] = $parseMode;
            }

            $client = new \GuzzleHttp\Client();
            $client->post($url, ['form_params' => $params]);
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram message', [
                'shop_id' => $shop->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function answerCallbackQuery(Shop $shop, string $callbackQueryId): void
    {
        try {
            $botToken = $shop->telegram_bot_token;
            $url = "https://api.telegram.org/bot{$botToken}/answerCallbackQuery";

            $client = new \GuzzleHttp\Client();
            $client->post($url, [
                'form_params' => [
                    'callback_query_id' => $callbackQueryId
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to answer callback query', [
                'shop_id' => $shop->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
