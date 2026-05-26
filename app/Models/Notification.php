<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Notification extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'notifications';

    protected $fillable = [
        'user_id',
        'actor_id',
        'type',
        'title',
        'body',
        'data',
        'read_at',
        'icon',
    ];

    protected function casts(): array
    {
        return [
            'data'    => 'array',
            'read_at' => 'datetime',
        ];
    }
}
