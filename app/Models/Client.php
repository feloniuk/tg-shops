<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    // Проверка лимитов
    public function canCreateShop(): bool
    {
        return $this->shops()->count() < $this->plan->max_shops;
    }
}
