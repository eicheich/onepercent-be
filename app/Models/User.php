<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'birth_date',
        'gender',
        'current_streak',
        'longest_streak',
        'last_completed_date',
        'google_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date'  => 'date',
            'last_completed_date' => 'date',
            'current_streak'  => 'integer',
            'longest_streak'  => 'integer',
        ];
    }

    public function personalization(): HasOne
    {
        return $this->hasOne(UserPersonalization::class, 'user_id', '_id');
    }
}
