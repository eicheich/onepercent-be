<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class UserPersonalization extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'user_personalizations';

    protected $fillable = [
        'user_id',
        'tags',
    ];
     public const AVAILABLE_TAGS = [
        'productivity',
        'career',
        'finance',
        'health',
        'study',
        'self_growth',
        'motivation',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
        ];
    }
}
