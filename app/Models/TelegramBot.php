<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramBot extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'bot_token',
        'bot_username',
        'is_active',
        'webhook_info',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'webhook_info' => 'array',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function isWebhookConfigured(): bool
    {
        return ! empty($this->webhook_info);
    }
}
