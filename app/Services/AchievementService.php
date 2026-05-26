<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserDailyChallenge;
use App\Models\UserFollow;

class AchievementService
{
    const ACHIEVEMENTS = [
        'first_step' => [
            'name'        => 'First Step',
            'description' => 'Complete your first challenge',
            'icon' => '🏆',
            'image' => 'ic_achievement_1'
        ],
        'on_fire' => [
            'name'        => 'On Fire',
            'description' => '7-day streak',
            'icon'        => '🔥',
            'image' => 'ic_achievement_2'
        ],
        'unstoppable' => [
            'name'        => 'Unstoppable',
            'description' => '30-day streak',
            'icon'        => '⚡',
            'image' => 'ic_achievement_3'
        ],
        'champion' => [
            'name'        => 'Champion',
            'description' => 'Reach top 10 leaderboard',
            'icon'        => '👑',
            'image' => 'ic_achievement_4'
        ],
        'social_butterfly' => [
            'name'        => 'Social Butterfly',
            'description' => 'Follow 5 people',
            'icon'        => '🤝',
            'image' => 'ic_achievement_5'
        ],
        'proof_master' => [
            'name'        => 'Proof Master',
            'description' => 'Upload your first proof',
            'icon'        => '📤',
            'image'     => 'ic_achievement_6'
        ],
        'consistent' => [
            'name'        => 'Consistent',
            'description' => 'Complete 10 total challenges',
            'icon'        => '💪',
            'image' => 'ic_achievement_7'
        ],
        'all_star' => [
            'name'        => 'All Star',
            'description' => 'Complete all 7 days in a week',
            'icon'        => '🌟',
            'image' => 'ic_achievement_8'
        ],
    ];

    public function checkAndUnlock(string $userId): array
    {
        $user = User::find($userId);
        if ($user === null) return [];

        $newlyUnlocked = [];

        foreach (self::ACHIEVEMENTS as $key => $data) {
            // Skip kalau sudah unlock
            $exists = UserAchievement::query()
                ->where('user_id', $userId)
                ->where('achievement_key', $key)
                ->exists();

            if ($exists) continue;

            if ($this->isUnlocked($key, $userId, $user)) {
                UserAchievement::query()->create([
                    'user_id'          => $userId,
                    'achievement_key'  => $key,
                    'achievement_name' => $data['name'],
                    'description'      => $data['description'],
                    'icon'             => $data['icon'],
                    'unlocked_at'      => now(),
                ]);

                $newlyUnlocked[] = [
                    'key'         => $key,
                    'name'        => $data['name'],
                    'description' => $data['description'],
                    'icon'        => $data['icon'],
                ];
            }
        }

        return $newlyUnlocked;
    }

    private function isUnlocked(string $key, string $userId, User $user): bool
    {
        return match ($key) {
            'first_step' => $this->checkFirstStep($userId),
            'on_fire'    => ($user->current_streak ?? 0) >= 7,
            'unstoppable' => ($user->current_streak ?? 0) >= 30,
            'champion'   => $this->checkChampion($userId),
            'social_butterfly' => $this->checkSocialButterfly($userId),
            'proof_master'     => $this->checkProofMaster($userId),
            'consistent'       => $this->checkConsistent($userId),
            'all_star'         => $this->checkAllStar($userId),
            default      => false,
        };
    }

    private function checkFirstStep(string $userId): bool
    {
        return UserDailyChallenge::query()
            ->where('user_id', $userId)
            ->where('is_completed', true)
            ->exists();
    }

    private function checkChampion(string $userId): bool
    {
        $top10 = User::query()
            ->orderBy('current_streak', 'desc')
            ->limit(10)
            ->pluck('_id')
            ->map(fn($id) => (string) $id)
            ->all();

        return in_array($userId, $top10, true);
    }

    private function checkSocialButterfly(string $userId): bool
    {
        return UserFollow::query()
            ->where('follower_id', $userId)
            ->count() >= 5;
    }

    private function checkProofMaster(string $userId): bool
    {
        return UserDailyChallenge::query()
            ->where('user_id', $userId)
            ->whereNotNull('metadata.proof_score')
            ->exists();
    }

    private function checkConsistent(string $userId): bool
    {
        return UserDailyChallenge::query()
            ->where('user_id', $userId)
            ->where('is_completed', true)
            ->count() >= 10;
    }

    private function checkAllStar(string $userId): bool
    {
        $weekStart = now()->startOfWeek()->toDateString();
        $weekEnd   = now()->endOfWeek()->toDateString();

        $completedThisWeek = UserDailyChallenge::where('user_id', $userId)
            ->where('is_completed', true)
            ->where('challenge_date', '>=', $weekStart)
            ->where('challenge_date', '<=', $weekEnd)
            ->count();

        return $completedThisWeek >= 7;
    }

    public function getUserAchievements(string $userId): array
    {
        $unlocked = UserAchievement::query()
            ->where('user_id', $userId)
            ->get()
            ->keyBy('achievement_key')
            ->all();

        $result = [];
        foreach (self::ACHIEVEMENTS as $key => $data) {
            $isUnlocked = isset($unlocked[$key]);
            $result[] = [
                'key'          => $key,
                'name'         => $data['name'],
                'description'  => $data['description'],
                'icon'         => $data['icon'],
                'image'       => $data['image'],
                'is_unlocked'  => $isUnlocked,
                'unlocked_at'  => $isUnlocked
                    ? $unlocked[$key]->unlocked_at?->toIso8601String()
                    : null,
            ];
        }

        return $result;
    }
}
