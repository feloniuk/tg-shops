<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'max_shops',
        'max_products',
        'ai_enabled',
        'price',
    ];

    protected $casts = [
        'max_shops' => 'integer',
        'max_products' => 'integer',
        'ai_enabled' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function hasUnlimitedProducts(): bool
    {
        return $this->max_products === PHP_INT_MAX;
    }
}
