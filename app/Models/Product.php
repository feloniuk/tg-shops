<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'characteristics', 
        'image',
        'is_active'
    ];

    protected $casts = [
        'characteristics' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category()
    {
        return $this->belongsTo(ShopCategory::class, 'category_id');
    }

    public function isAvailable(): bool
    {
        return $this->is_active && $this->shop->isActive();
    }
}