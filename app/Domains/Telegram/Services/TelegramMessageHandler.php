<?php

namespace App\Domains\Telegram\Services;

use App\Models\Shop;
use App\Models\Product;
use App\Models\Order;
use App\Models\TelegramSession;
use App\Models\ShopCategory;
use App\Mail\OrderCreatedMailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
            case 'my_orders':
                $this->showMyOrders($shop, $session, $chatId);
                break;
            case 'main_menu':
                $this->sendMainMenu($shop, $chatId);
                break;
            case 'order_details':
                $orderId = (int)($parts[1] ?? 0);
                $this->showOrderDetails($shop, $chatId, $orderId);
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
                    ['text' => '📦 Каталог товарів', 'callback_data' => 'catalog'],
                    ['text' => '🛒 Кошик', 'callback_data' => 'view_cart']
                ],
                [
                    ['text' => '📋 Мої замовлення', 'callback_data' => 'my_orders'],
                    ['text' => '🏠 Головна', 'callback_data' => 'main_menu']
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
        $description .= "💰 Ціна: *{$product->price} грн*\n\n";

        // Информация о наличии
        if ($product->track_stock) {
            if ($product->stock_quantity > 0) {
                $description .= "📦 В наявності: {$product->stock_quantity} шт\n";
            } elseif ($product->allow_backorder) {
                $description .= "📦 Товар під замовлення\n";
            } else {
                $description .= "❌ Немає в наявності\n";
            }
        }

        $keyboard = [
            'inline_keyboard' => []
        ];

        // Кнопка добавления в корзину только если товар доступен
        if ($product->isInStock(1)) {
            $keyboard['inline_keyboard'][] = [
                ['text' => '➕ Додати в кошик', 'callback_data' => "add_to_cart:{$product->id}"]
            ];
        }

        $keyboard['inline_keyboard'][] = [
            ['text' => '◀️ Назад', 'callback_data' => $product->category_id ? "category:{$product->category_id}" : 'catalog']
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

        // Проверка наличия товара
        $cart = $session->getCart();
        $currentQuantity = $cart[$productId]['quantity'] ?? 0;
        $newQuantity = $currentQuantity + 1;

        if (!$product->isInStock($newQuantity)) {
            $this->sendMessage($shop, $chatId, "❌ На жаль, товар '{$product->name}' немає в достатній кількості");
            $this->sendMainMenu($shop, $chatId);
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

        if (empty($cart)) {
            $this->sendMessage($shop, $chatId, '❌ Ваш кошик порожній');
            $this->sendMainMenu($shop, $chatId);
            return;
        }

        $orderDetails = [];
        $outOfStockProducts = [];

        // Проверяем наличие всех товаров и собираем детали заказа
        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);

            if (!$product) {
                $outOfStockProducts[] = "Товар #{$productId} (не знайдено)";
                continue;
            }

            // Проверяем наличие
            if (!$product->isInStock($item['quantity'])) {
                $outOfStockProducts[] = $product->name;
                continue;
            }

            $orderDetails[] = [
                'product_id' => $productId,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $item['quantity'],
                'total' => $product->price * $item['quantity']
            ];
        }

        // Если есть товары не в наличии, уведомляем пользователя
        if (!empty($outOfStockProducts)) {
            $message = "❌ На жаль, наступні товари закінчилися:\n\n";
            foreach ($outOfStockProducts as $productName) {
                $message .= "• {$productName}\n";
            }
            $message .= "\nБудь ласка, видаліть їх з кошика та спробуйте знову.";

            $this->sendMessage($shop, $chatId, $message);
            $this->showCart($shop, $session, $chatId);
            return;
        }

        // Если все товары удалены из-за отсутствия в наличии
        if (empty($orderDetails)) {
            $this->sendMessage($shop, $chatId, '❌ Товари з вашого кошика закінчилися. Кошик очищено.');
            $session->clearCart();
            $session->save();
            $this->sendMainMenu($shop, $chatId);
            return;
        }

        $contact = $session->data['contact'] ?? 'Не вказано';
        $comment = $session->data['comment'] ?? '';

        $order = Order::create([
            'shop_id' => $shop->id,
            'telegram_user_id' => $session->telegram_user_id,
            'customer_name' => $contact,
            'customer_phone' => $contact,
            'total_amount' => $session->getCartTotal(),
            'status' => 'pending',
            'order_details' => $orderDetails,
            'customer_comment' => $comment
        ]);

        // Отправка email уведомления владельцу магазина
        try {
            if ($shop->client && $shop->client->user && $shop->client->user->email) {
                $shopOwnerEmail = $shop->client->user->email;
                Mail::to($shopOwnerEmail)->send(new OrderCreatedMailable($order));
            } else {
                Log::warning('Cannot send order created email - shop owner email not found', [
                    'order_id' => $order->id,
                    'shop_id' => $shop->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send order created email', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }

        // Декремент остатков товаров
        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $product->decrementStock($item['quantity']);
            }
        }

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

    private function showMyOrders(Shop $shop, TelegramSession $session, int $chatId): void
    {
        $orders = Order::getByTelegramUser($shop->id, $session->telegram_user_id);

        if ($orders->isEmpty()) {
            $this->sendMessage($shop, $chatId, "📋 У вас поки немає замовлень.\n\nОберіть товари з каталогу!");
            $this->sendMainMenu($shop, $chatId);
            return;
        }

        $message = "📋 *Ваші замовлення:*\n\n";

        $keyboard = ['inline_keyboard' => []];
        $statusLabels = [
            'pending' => '⏳ Очікує обробки',
            'processing' => '⚙️ В обробці',
            'completed' => '✅ Виконано',
            'cancelled' => '❌ Скасовано',
            'refunded' => '↩️ Повернення'
        ];

        foreach ($orders->take(10) as $order) {
            $statusEmoji = match($order->status) {
                'pending' => '⏳',
                'processing' => '⚙️',
                'completed' => '✅',
                'cancelled' => '❌',
                'refunded' => '↩️',
                default => '📦'
            };

            $message .= "{$statusEmoji} *Замовлення #{$order->id}*\n";
            $message .= "Сума: {$order->total_amount} грн\n";
            $message .= "Статус: {$statusLabels[$order->status]}\n";
            $message .= "Дата: {$order->created_at->format('d.m.Y H:i')}\n\n";

            $keyboard['inline_keyboard'][] = [
                ['text' => "📦 Замовлення #{$order->id}", 'callback_data' => "order_details:{$order->id}"]
            ];
        }

        $keyboard['inline_keyboard'][] = [
            ['text' => '🏠 Головна', 'callback_data' => 'main_menu']
        ];

        $this->sendMessage($shop, $chatId, $message, $keyboard, 'Markdown');
    }

    private function showOrderDetails(Shop $shop, int $chatId, int $orderId): void
    {
        $order = Order::find($orderId);

        if (!$order || $order->shop_id !== $shop->id) {
            $this->sendMessage($shop, $chatId, '❌ Замовлення не знайдено');
            $this->sendMainMenu($shop, $chatId);
            return;
        }

        $statusLabels = [
            'pending' => '⏳ Очікує обробки',
            'processing' => '⚙️ В обробці',
            'completed' => '✅ Виконано',
            'cancelled' => '❌ Скасовано',
            'refunded' => '↩️ Повернення'
        ];

        $message = "📦 *Замовлення #{$order->id}*\n\n";
        $message .= "📅 Дата: {$order->created_at->format('d.m.Y H:i')}\n";
        $message .= "📊 Статус: {$statusLabels[$order->status]}\n\n";

        $message .= "*Товари:*\n";
        foreach ($order->order_details as $item) {
            $message .= "• {$item['name']}\n";
            $message .= "  {$item['quantity']} шт × {$item['price']} грн = " . ($item['quantity'] * $item['price']) . " грн\n";
        }

        $message .= "\n*Загальна сума: {$order->total_amount} грн*\n\n";

        if ($order->customer_comment) {
            $message .= "💬 Ваш коментар: {$order->customer_comment}\n\n";
        }

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '📋 Мої замовлення', 'callback_data' => 'my_orders']
                ],
                [
                    ['text' => '🏠 Головна', 'callback_data' => 'main_menu']
                ]
            ]
        ];

        $this->sendMessage($shop, $chatId, $message, $keyboard, 'Markdown');
    }
}
