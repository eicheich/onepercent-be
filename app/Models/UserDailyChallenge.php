<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MongoDB\Laravel\Eloquent\Model;

class UserDailyChallenge extends Model
{
    protected $collection = 'user_daily_challenges';

    protected $fillable = [
        'user_id',
        'challenge_id',
        'challenge_date',
        'title',
        'content',
        'estimated_minutes',
        'tags',
        'metadata',
        'is_completed',
        'completed_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'challenge_date' => 'date',
            'estimated_minutes' => 'integer',
            'tags' => 'array',
            'metadata' => 'array',
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class, 'challenge_id', '_id');
    }
}
