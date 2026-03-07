<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class UserFollow extends Model
{
    protected $collection = 'user_follows';

    protected $fillable = [
        'follower_id',
        'following_id',
    ];
}
