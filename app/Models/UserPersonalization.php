<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class UserPersonalization extends Model
{
    protected $collection = 'user_personalization';

    protected $fillable = [
        'user_id',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
        ];
    }
}
