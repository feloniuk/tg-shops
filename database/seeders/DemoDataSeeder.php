<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create demo admin user
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $adminUser->assignRole('admin');

        echo "✓ Admin created: admin@example.com / password\n";

        // 2. Create demo client user
        $clientUser = User::create([
            'name' => 'Demo Client',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $clientUser->assignRole('client');

        echo "✓ Client created: client@example.com / password\n";

        // 3. Get Pro plan
        $proPlan = Plan::where('name', 'Pro')->first();

        // 4. Create client profile
        $client = Client::create([
            'user_id' => $clientUser->id,
            'company_name' => 'Demo Shop Company',
            'phone' => '+380501234567',
            'plan_id' => $proPlan->id,
            'plan_expires_at' => now()->addYear(),
        ]);

        echo "✓ Client profile created with Pro plan\n";

        // 5. Create demo shop (without bot token for now)
        $shop = Shop::create([
            'client_id' => $client->id,
            'name' => 'Демо Магазин Електроніки',
            'telegram_bot_token' => null, // User needs to add real bot token
            'welcome_message' => 'Вітаємо в нашому магазині! Виберіть товар з каталогу.',
            'footer_message' => 'Дякуємо за покупку!',
            'design_settings' => [
                'theme_color' => '#4F46E5',
                'button_style' => 'rounded',
            ],
            'status' => 'active',
        ]);

        echo "✓ Shop created: {$shop->name}\n";

        // 6. Create categories
        $categories = [
            ShopCategory::create([
                'shop_id' => $shop->id,
                'name' => 'Смартфони',
                'description' => 'Найновіші моделі смартфонів',
            ]),
            ShopCategory::create([
                'shop_id' => $shop->id,
                'name' => 'Ноутбуки',
                'description' => 'Потужні ноутбуки для роботи та ігор',
            ]),
            ShopCategory::create([
                'shop_id' => $shop->id,
                'name' => 'Аксесуари',
                'description' => 'Навушники, чохли, зарядки',
            ]),
        ];

        echo '✓ Created '.count($categories)." categories\n";

        // 7. Create products
        $products = [
            // Smartphones
            Product::create([
                'shop_id' => $shop->id,
                'category_id' => $categories[0]->id,
                'name' => 'iPhone 15 Pro',
                'description' => 'Найновіший флагман від Apple з чіпом A17 Pro',
                'price' => 45999,
                'characteristics' => [
                    'Екран' => '6.1" Super Retina XDR',
                    'Процесор' => 'A17 Pro',
                    'Пам\'ять' => '256GB',
                    'Камера' => '48MP основна',
                ],
            ]),
            Product::create([
                'shop_id' => $shop->id,
                'category_id' => $categories[0]->id,
                'name' => 'Samsung Galaxy S24',
                'description' => 'Флагманський смартфон з AI функціями',
                'price' => 38999,
                'characteristics' => [
                    'Екран' => '6.2" Dynamic AMOLED',
                    'Процесор' => 'Snapdragon 8 Gen 3',
                    'Пам\'ять' => '256GB',
                    'Камера' => '50MP основна',
                ],
            ]),
            // Laptops
            Product::create([
                'shop_id' => $shop->id,
                'category_id' => $categories[1]->id,
                'name' => 'MacBook Air M3',
                'description' => 'Легкий та потужний ноутбук для повсякденних задач',
                'price' => 54999,
                'characteristics' => [
                    'Процесор' => 'Apple M3',
                    'Оперативна пам\'ять' => '16GB',
                    'Накопичувач' => '512GB SSD',
                    'Екран' => '13.6" Retina',
                ],
            ]),
            Product::create([
                'shop_id' => $shop->id,
                'category_id' => $categories[1]->id,
                'name' => 'Lenovo ThinkPad X1 Carbon',
                'description' => 'Бізнес-ноутбук преміум класу',
                'price' => 62999,
                'characteristics' => [
                    'Процесор' => 'Intel Core i7-13700H',
                    'Оперативна пам\'ять' => '32GB',
                    'Накопичувач' => '1TB SSD',
                    'Екран' => '14" 2.8K OLED',
                ],
            ]),
            // Accessories
            Product::create([
                'shop_id' => $shop->id,
                'category_id' => $categories[2]->id,
                'name' => 'AirPods Pro 2',
                'description' => 'Бездротові навушники з активним шумозаглушенням',
                'price' => 9999,
                'characteristics' => [
                    'Тип' => 'TWS навушники',
                    'Шумозаглушення' => 'Активне ANC',
                    'Час роботи' => 'До 6 годин',
                    'Зарядка' => 'USB-C',
                ],
            ]),
            Product::create([
                'shop_id' => $shop->id,
                'category_id' => $categories[2]->id,
                'name' => 'Anker PowerCore 20000mAh',
                'description' => 'Потужний павербанк для всіх пристроїв',
                'price' => 1299,
                'characteristics' => [
                    'Ємність' => '20000mAh',
                    'Порти' => '2x USB-A, 1x USB-C',
                    'Швидка зарядка' => 'Power Delivery 20W',
                    'Вага' => '365г',
                ],
            ]),
        ];

        echo '✓ Created '.count($products)." products\n";

        // 8. Create demo orders
        $orders = [
            Order::create([
                'shop_id' => $shop->id,
                'customer_name' => 'Іван Петренко',
                'customer_phone' => '+380501234567',
                'customer_email' => 'ivan@example.com',
                'total_amount' => 55998,
                'status' => 'pending',
                'order_details' => [
                    [
                        'product_id' => $products[0]->id,
                        'name' => $products[0]->name,
                        'price' => $products[0]->price,
                        'quantity' => 1,
                        'total' => $products[0]->price,
                    ],
                    [
                        'product_id' => $products[4]->id,
                        'name' => $products[4]->name,
                        'price' => $products[4]->price,
                        'quantity' => 1,
                        'total' => $products[4]->price,
                    ],
                ],
                'customer_comment' => 'Прошу передзвонити перед доставкою',
            ]),
            Order::create([
                'shop_id' => $shop->id,
                'customer_name' => 'Марія Коваленко',
                'customer_phone' => '+380672345678',
                'total_amount' => 64298,
                'status' => 'processing',
                'order_details' => [
                    [
                        'product_id' => $products[3]->id,
                        'name' => $products[3]->name,
                        'price' => $products[3]->price,
                        'quantity' => 1,
                        'total' => $products[3]->price,
                    ],
                    [
                        'product_id' => $products[5]->id,
                        'name' => $products[5]->name,
                        'price' => $products[5]->price,
                        'quantity' => 1,
                        'total' => $products[5]->price,
                    ],
                ],
            ]),
            Order::create([
                'shop_id' => $shop->id,
                'customer_name' => 'Олександр Шевченко',
                'customer_phone' => '+380933456789',
                'total_amount' => 38999,
                'status' => 'completed',
                'order_details' => [
                    [
                        'product_id' => $products[1]->id,
                        'name' => $products[1]->name,
                        'price' => $products[1]->price,
                        'quantity' => 1,
                        'total' => $products[1]->price,
                    ],
                ],
            ]),
        ];

        echo '✓ Created '.count($orders)." demo orders\n";

        echo "\n";
        echo "========================================\n";
        echo "✓ Demo data seeded successfully!\n";
        echo "========================================\n";
        echo "\nLogin credentials:\n";
        echo "  Admin: admin@example.com / password\n";
        echo "  Client: client@example.com / password\n";
        echo "\nNote: Add real Telegram bot token in Shop settings to test bot functionality\n";
    }
}
