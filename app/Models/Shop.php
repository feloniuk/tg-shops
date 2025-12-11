<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'status'
    ];

    protected $casts = [
        'design_settings' => 'array',
        'status' => 'string'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function categories()
    {
        return $this->hasMany(ShopCategory::class);
    }

    public function telegramBot()
    {
        return $this->hasOne(TelegramBot::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Бизнес-логика
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
