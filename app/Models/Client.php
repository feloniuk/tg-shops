<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'phone',
        'plan_id',
        'plan_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'plan_expires_at' => 'datetime',
        ];
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function shops(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Shop::class);
    }

    public function tickets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    // Проверка лимитов
    public function canCreateShop(): bool
    {
        return $this->shops()->count() < $this->plan->max_shops;
    }

    public function isSubscriptionActive(): bool
    {
        return $this->plan_expires_at && $this->plan_expires_at->isFuture();
    }
}
