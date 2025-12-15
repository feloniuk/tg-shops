<x-mail::message>
# Новый заказ #{{ $order->id }}

Добрый день!

В вашем магазине **{{ $shop->name }}** был оформлен новый заказ.

## Детали заказа

**Клиент:** {{ $order->customer_name }}
**Телефон:** {{ $order->customer_phone }}
@if($order->customer_email)
**Email:** {{ $order->customer_email }}
@endif

**Сумма заказа:** {{ number_format($order->total_amount, 2) }} грн

@if($order->customer_comment)
**Комментарий клиента:**
{{ $order->customer_comment }}
@endif

## Товары в заказе

<x-mail::table>
| Товар | Количество | Цена | Сумма |
|:------|:-----------|:-----|:------|
@foreach($order->order_details as $item)
| {{ $item['name'] }} | {{ $item['quantity'] }} шт | {{ number_format($item['price'], 2) }} грн | {{ number_format($item['quantity'] * $item['price'], 2) }} грн |
@endforeach
| | | **Итого:** | **{{ number_format($order->total_amount, 2) }} грн** |
</x-mail::table>

<x-mail::button :url="$orderUrl">
Посмотреть заказ
</x-mail::button>

Спасибо, что используете {{ config('app.name') }}!<br>
{{ config('app.name') }}
</x-mail::message>
