<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id', 
        'customer_name', 
        'customer_phone', 
        'customer_email', 
        'total_amount', 
        'status', 
        'order_details',
        'customer_comment'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'order_details' => 'array',
        'status' => 'string'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}