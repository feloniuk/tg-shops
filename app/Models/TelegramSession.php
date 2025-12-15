<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramSession extends Model
{
    protected $fillable = [
        'shop_id',
        'telegram_user_id',
        'state',
        'data',
        'last_activity',
    ];

    protected $casts = [
        'data' => 'array',
        'last_activity' => 'datetime',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function addToCart(int $productId, int $quantity = 1): void
    {
        $cart = $this->data['cart'] ?? [];

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = ['quantity' => $quantity];
        }

        $this->data = array_merge($this->data ?? [], ['cart' => $cart]);
        $this->save();
    }

    public function removeFromCart(int $productId): void
    {
        $cart = $this->data['cart'] ?? [];
        unset($cart[$productId]);

        $this->data = array_merge($this->data ?? [], ['cart' => $cart]);
        $this->save();
    }

    public function clearCart(): void
    {
        $this->data = array_merge($this->data ?? [], ['cart' => []]);
        $this->save();
    }

    public function getCart(): array
    {
        return $this->data['cart'] ?? [];
    }

    public function getCartTotal(): float
    {
        $cart = $this->getCart();
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $total += $product->price * $item['quantity'];
            }
        }

        return $total;
    }

    public function touchActivity(): void
    {
        $this->last_activity = now();
        $this->save();
    }
}
