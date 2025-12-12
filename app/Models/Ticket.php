<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id', 
        'manager_id', 
        'title', 
        'description', 
        'status', 
        'priority',
        'last_response_at'
    ];

    protected $casts = [
        'status' => 'string',
        'priority' => 'string',
        'last_response_at' => 'datetime'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
}
