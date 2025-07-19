<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PushNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_id',
        'type',
        'title',
        'message',
        'geo_data',
        'is_sent',
        'sent_at'
    ];

    protected $casts = [
        'geo_data' => 'array',
        'is_sent' => 'boolean',
        'sent_at' => 'datetime'
    ];

    public function loyaltyCard()
    {
        return $this->belongsTo(LoyaltyCard::class, 'card_id', 'card_id');
    }
}