# Telegram Shops - SaaS Platform

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white)
![Tailwind](https://img.shields.io/badge/Tailwind-4.x-38B2AC?style=flat&logo=tailwind-css&logoColor=white)

SaaS платформа для создания и управления телеграм магазинами. Поддержка английского и украинского языков.

## Основной функционал

### Для клиентов
- Создание и управление телеграм магазинами
- Управление товарами с категориями
- Складской учет (отслеживание остатков)
- Множественные изображения товаров
- Управление заказами со статусами
- Базовая аналитика и статистика
- AI-генерация описаний товаров (OpenAI)
- Тарифные планы (Free, Base, Pro)
- Email уведомления

### Telegram Bot
- Каталог товаров с категориями
- Корзина покупок
- Оформление заказов
- История заказов клиента
- Отображение наличия товаров
- Проверка остатков при добавлении в корзину

### Для администраторов
- Управление пользователями
- Управление магазинами
- Глобальная статистика
- Система ролей и разрешений

## Технологический стек

- **Backend:** Laravel 12
- **Database:** MySQL 8+ / SQLite
- **Frontend:** Tailwind CSS 4, Alpine.js, Vite
- **Локализация:** mcamara/laravel-localization (EN, UK)
- **Разрешения:** Spatie Laravel Permission
- **Платежи:** Stripe
- **Telegram Bot API:** telegram-bot/api

## Требования

- PHP 8.2+
- Composer
- Node.js 18+ и NPM
- MySQL 8+ или SQLite
- Telegram Bot Token (для тестирования бота)

## Установка

### 1. Клонирование и установка зависимостей

```bash
# Клонировать репозиторий
git clone <repository-url>
cd telegram-shops

# Установить PHP зависимости
composer install

# Установить Node.js зависимости
npm install
```

### 2. Настройка окружения

```bash
# Скопировать .env.example в .env
cp .env.example .env

# Сгенерировать ключ приложения
php artisan key:generate
```

### 3. Настройка .env файла

```env
APP_NAME="Telegram Shops"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=en
APP_FALLBACK_LOCALE=en

# База данных (MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=telegram_shops
DB_USERNAME=root
DB_PASSWORD=

# Или SQLite для разработки
# DB_CONNECTION=sqlite

# Stripe (для платежей)
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=your_webhook_secret

# OpenAI (для AI функций)
OPENAI_API_KEY=your_openai_api_key

# Mail (для уведомлений)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@telegram-shops.com
MAIL_FROM_NAME="${APP_NAME}"

# Cache & Queue
CACHE_DRIVER=database
QUEUE_CONNECTION=database
```

### 4. Создание базы данных

**Для MySQL:**
```bash
mysql -u root -p
CREATE DATABASE telegram_shops CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

**Для SQLite:**
```bash
# Windows
type nul > database\database.sqlite

# Linux/Mac
touch database/database.sqlite
```

### 5. Запуск миграций и seeders

```bash
# Запустить миграции
php artisan migrate

# Запустить базовые seeders
php artisan db:seed

# Для добавления демо-данных (опционально)
# Раскомментируйте DemoDataSeeder в database/seeders/DatabaseSeeder.php
# Затем запустите:
php artisan db:seed --class=DemoDataSeeder
```

### 6. Создать символическую ссылку для storage

```bash
php artisan storage:link
```

### 7. Сборка frontend

```bash
# Development
npm run dev

# Production
npm run build
```

### 8. Запуск сервера

```bash
# Запустить веб-сервер
php artisan serve

# В отдельном терминале запустить queue worker
php artisan queue:work
```

Приложение будет доступно по адресу: `http://localhost:8000`

## Тестовые учетные данные

После запуска `DemoDataSeeder`:

- **Админ:** admin@example.com / password
- **Клиент:** client@example.com / password

## Настройка Telegram Bot

### 1. Создание бота

1. Откройте [@BotFather](https://t.me/BotFather) в Telegram
2. Отправьте `/newbot`
3. Следуйте инструкциям для создания бота
4. Сохраните токен бота (формат: `1234567890:ABCdefGHIjklMNOpqrsTUVwxyz`)

### 2. Настройка бота в приложении

1. Войдите как клиент
2. Перейдите в "Магазины"
3. Откройте магазин или создайте новый
4. Добавьте Telegram Bot Token
5. Сохраните (webhook будет автоматически настроен)

### 3. Локальное тестирование с ngrok

Для локального тестирования Telegram webhook используйте [ngrok](https://ngrok.com/):

```bash
# Запустите ngrok
ngrok http 8000

# Обновите APP_URL в .env на https URL от ngrok
APP_URL=https://your-ngrok-url.ngrok.io
```

**Формат Webhook URL:**
```
https://your-domain.com/telegram/webhook/{botToken}
```

## Мультиязычность

Приложение поддерживает английский (EN) и украинский (UK) языки.

**Переключение языка через URL:**
- Английский: `/en/dashboard`
- Украинский: `/uk/dashboard`

**Файлы переводов:**
- `lang/en/` - английские переводы
- `lang/uk/` - украинские переводы

**Использование в Blade:**
```blade
{{ __('app.dashboard.welcome') }}
{{ __('auth.failed') }}
```

## Тарифные планы

| План | Цена | Магазины | Товары | AI |
|------|------|----------|---------|-----|
| Free | $0 | 1 | 10 | Нет |
| Base | $9.99/мес | 3 | 100 | Нет |
| Pro | $29.99/мес | 10 | 999,999 | Да |

## Структура проекта

```
telegram-shops/
├── app/
│   ├── Domains/           # Доменная логика
│   │   ├── AI/           # AI генерация
│   │   ├── Billing/      # Stripe интеграция
│   │   ├── Product/      # Управление товарами
│   │   ├── Shop/         # Управление магазинами
│   │   ├── Telegram/     # Telegram Bot
│   │   ├── Audit/        # Аудит логи
│   │   └── Support/      # Система поддержки
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Admin/    # Админ контроллеры
│   │       └── Client/   # Клиентские контроллеры
│   └── Models/           # Eloquent модели
├── database/
│   ├── migrations/       # Миграции БД (19 файлов)
│   └── seeders/          # Seeders
├── lang/                 # Файлы локализации
│   ├── en/              # Английский
│   │   ├── auth.php
│   │   ├── validation.php
│   │   ├── passwords.php
│   │   ├── pagination.php
│   │   └── app.php      # Кастомные переводы
│   └── uk/              # Украинский
│       ├── auth.php
│       ├── validation.php
│       ├── passwords.php
│       ├── pagination.php
│       └── app.php
└── resources/
    └── views/           # Blade шаблоны
        ├── admin/       # Админка
        │   ├── users/
        │   └── shops/
        ├── client/      # Клиентская панель
        ├── orders/      # Управление заказами
        └── emails/      # Email шаблоны
```

## Основные маршруты

### Веб-интерфейс
- `/{locale}/` - Главная страница
- `/{locale}/login` - Вход
- `/{locale}/register` - Регистрация
- `/{locale}/dashboard` - Панель клиента
- `/{locale}/shops` - Магазины
- `/{locale}/shops/{shop}/products` - Товары
- `/{locale}/shops/{shop}/orders` - Заказы
- `/{locale}/admin` - Админ-панель

### API
- `POST /telegram/webhook/{botToken}` - Telegram webhook
- `POST /stripe/webhook` - Stripe webhook
- `POST /{locale}/ai/generate-product-description` - AI генерация
- `POST /{locale}/ai/generate-shop-greeting` - AI приветствие

## Production Deployment

### Требования для production
- PHP 8.2+ с необходимыми расширениями
- MySQL 8+
- Redis (рекомендуется для кеша и очередей)
- HTTPS
- Supervisor для управления queue workers

### Шаги развертывания

1. **Обновите .env**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

2. **Оптимизируйте приложение**
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

3. **Настройте Supervisor для queue workers**
```ini
[program:telegram-shops-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --tries=3 --timeout=90
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/worker.log
stopwaitsecs=3600
```

4. **Настройте Cron для scheduled tasks**
```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

5. **Настройте веб-сервер (Nginx)**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

6. **Настройте SSL (Let's Encrypt)**
```bash
sudo certbot --nginx -d your-domain.com
```

## Мониторинг и логи

**Логи приложения:**
```bash
tail -f storage/logs/laravel.log
```

**Queue workers:**
```bash
tail -f storage/logs/worker.log
```

## Документация

Дополнительная документация:
- `FUNCTIONALITY.md` - Полный список реализованного функционала
- `IMPLEMENTATION_REPORT.md` - Отчет о реализации
- `BUGFIXES_REPORT.md` - Исправленные ошибки

## Разработка

### Запуск в режиме разработки

```bash
# Терминал 1: Веб-сервер
php artisan serve

# Терминал 2: Queue worker
php artisan queue:work

# Терминал 3: Vite dev server
npm run dev
```

### Rate Limiting

- Billing endpoints: 10 запросов/минуту
- AI генерация: 20 запросов/минуту

### Кеш и Очереди

По умолчанию используется `database` драйвер.

Для production рекомендуется Redis:
```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```

## Troubleshooting

### Проблема: Telegram webhook не работает локально
**Решение:** Используйте ngrok для создания публичного URL

### Проблема: Email не отправляются
**Решение:**
1. Проверьте настройки MAIL_ в .env
2. Убедитесь что queue worker запущен
3. Используйте Mailtrap для тестирования

### Проблема: Ошибки при запуске seeders
**Решение:**
1. Очистите кеш: `php artisan config:clear`
2. Запустите seeders по отдельности:
   - `php artisan db:seed --class=PlanSeeder`
   - `php artisan db:seed --class=RoleAndPermissionSeeder`

## Лицензия

MIT License

## Авторы

Разработано с использованием Laravel 12 и современных веб-технологий.

---

**Версия:** 1.0.0
**Laravel:** 12.x
**Дата:** Декабрь 2025
