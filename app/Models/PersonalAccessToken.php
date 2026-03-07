<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use MongoDB\Laravel\Eloquent\DocumentModel;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use DocumentModel;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
    ];

    protected $hidden = [
        'token',
    ];

    protected function casts(): array
    {
        return [
            'abilities' => 'json',
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function tokenable(): MorphTo
    {
        return $this->morphTo('tokenable');
    }

    public static function findToken($token)
    {
        if (! str_contains($token, '|')) {
            return self::query()->where('token', hash('sha256', $token))->first();
        }

        [, $plainTextToken] = explode('|', $token, 2);
        $instance = self::query()->where('token', hash('sha256', $plainTextToken))->first();

        if (! $instance) {
            return null;
        }

        return $instance;
    }

    public function can($ability): bool
    {
        return in_array('*', $this->abilities ?? [], true)
            || in_array($ability, $this->abilities ?? [], true);
    }

    public function cant($ability): bool
    {
        return ! $this->can($ability);
    }
}
