<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class UserAchievement extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'user_achievements';

    protected $fillable = [
        'user_id',
        'achievement_key',
        'achievement_name',
        'description',
        'icon',
        'unlocked_at',
    ];

    protected function casts(): array
    {
        return [
            'unlocked_at' => 'datetime',
        ];
    }
}
