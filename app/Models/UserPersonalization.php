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

    protected function casts(): array
    {
        return [
            'tags' => 'array',
        ];
    }
}
