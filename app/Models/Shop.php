<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'name',
        'telegram_bot_token',
        'welcome_message',
        'footer_message',
        'design_settings',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'design_settings' => 'array',
            'status' => 'string',
        ];
    }

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function categories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ShopCategory::class);
    }

    public function telegramBot(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TelegramBot::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Бизнес-логика
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
