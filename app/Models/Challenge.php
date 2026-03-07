<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Challenge extends Model
{
    protected $collection = 'challenges';

    protected $fillable = [
        'title',
        'content',
        'estimated_minutes',
        'tags',
        'signature',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'estimated_minutes' => 'integer',
            'tags' => 'array',
            'metadata' => 'array',
        ];
    }
}
