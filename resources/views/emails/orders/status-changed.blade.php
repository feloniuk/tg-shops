<x-mail::message>
# Статус заказа изменен

@php
$statusLabels = [
    'pending' => 'Ожидает обработки',
    'processing' => 'В обработке',
    'completed' => 'Выполнен',
    'cancelled' => 'Отменен',
    'refunded' => 'Возврат'
];
@endphp

Добрый день, {{ $order->customer_name }}!

Статус вашего заказа #{{ $order->id }} в магазине **{{ $shop->name }}** был изменен.

**Прежний статус:** {{ $statusLabels[$oldStatus] ?? $oldStatus }}
**Новый статус:** {{ $statusLabels[$newStatus] ?? $newStatus }}

## Детали заказа

**Сумма заказа:** {{ number_format($order->total_amount, 2) }} грн
**Дата создания:** {{ $order->created_at->format('d.m.Y H:i') }}

@if($newStatus === 'completed')
<x-mail::panel>
**Ваш заказ успешно выполнен!**
Благодарим вас за покупку. Надеемся увидеть вас снова!
</x-mail::panel>
@elseif($newStatus === 'processing')
<x-mail::panel>
**Ваш заказ в обработке**
Мы уже начали обрабатывать ваш заказ. Скоро с вами свяжется наш менеджер.
</x-mail::panel>
@elseif($newStatus === 'cancelled')
<x-mail::panel>
**Заказ отменен**
К сожалению, ваш заказ был отменен. Если у вас есть вопросы, свяжитесь с нами.
</x-mail::panel>
@endif

Если у вас есть вопросы по заказу, пожалуйста, свяжитесь с магазином **{{ $shop->name }}**.

С уважением,<br>
{{ $shop->name }}
</x-mail::message>
