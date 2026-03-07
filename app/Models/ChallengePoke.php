<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ChallengePoke extends Model
{
    protected $collection = 'challenge_pokes';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'user_daily_challenge_id',
        'challenge_id',
        'type',
        'message',
        'metadata',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'read_at' => 'datetime',
        ];
    }
}
