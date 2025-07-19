<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoyaltyCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_id',
        'google_wallet_object_id',
        'card_number',
        'points',
        'metadata',
        'issued_at',
        'expires_at',
        'is_active'
    ];

    protected $casts = [
        'metadata' => 'array',
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function notifications()
    {
        return $this->hasMany(PushNotification::class, 'card_id', 'card_id');
    }
}