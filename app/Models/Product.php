<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'category_id',
        'name',
        'description',
        'price',
        'stock_quantity',
        'track_stock',
        'allow_backorder',
        'characteristics',
        'image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'characteristics' => 'array',
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'track_stock' => 'boolean',
            'allow_backorder' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function shop(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ShopCategory::class, 'category_id');
    }

    public function images(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function primaryImage(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function isAvailable(): bool
    {
        return $this->is_active && $this->shop->isActive();
    }

    // Проверка наличия на складе
    public function isInStock(int $quantity = 1): bool
    {
        // Если не отслеживаем остатки, товар всегда доступен
        if (! $this->track_stock) {
            return true;
        }

        // Если разрешены backorders, товар всегда доступен
        if ($this->allow_backorder) {
            return true;
        }

        return $this->stock_quantity >= $quantity;
    }

    // Уменьшить остаток
    public function decrementStock(int $quantity): bool
    {
        if (! $this->track_stock) {
            return true;
        }

        if ($this->stock_quantity < $quantity && ! $this->allow_backorder) {
            return false;
        }

        $this->decrement('stock_quantity', $quantity);

        return true;
    }

    // Увеличить остаток (при отмене заказа)
    public function incrementStock(int $quantity): void
    {
        if ($this->track_stock) {
            $this->increment('stock_quantity', $quantity);
        }
    }

    // Получить URL главного изображения (для обратной совместимости)
    public function getPrimaryImageUrlAttribute(): ?string
    {
        // Сначала пробуем получить из новой таблицы
        if ($this->relationLoaded('primaryImage') && $this->primaryImage) {
            return $this->primaryImage->url;
        }

        // Если есть изображения, берем первое
        if ($this->relationLoaded('images') && $this->images->isNotEmpty()) {
            return $this->images->first()->url;
        }

        // Fallback на старое поле image
        return $this->image ? asset('storage/'.$this->image) : null;
    }
}
