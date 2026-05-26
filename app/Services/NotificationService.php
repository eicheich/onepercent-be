<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    // Follow
    public static function notifyFollow(string $followerId, string $targetId): void
    {
        $follower = User::find($followerId);
        if (!$follower || $followerId === $targetId) return;

        Notification::create([
            'user_id'  => $targetId,
            'actor_id' => $followerId,
            'type'     => 'follow',
            'title'    => $follower->name . ' followed you',
            'body'     => 'You have a new follower!',
            'icon'     => '👥',
            'data'     => [
                'actor_name'   => $follower->name,
                'actor_avatar' => $follower->avatar,
            ],
            'read_at'  => null,
        ]);
    }

    // Follow back / mutual
    public static function notifyFollowBack(string $followerId, string $targetId): void
    {
        $follower = User::find($followerId);
        if (!$follower) return;

        Notification::create([
            'user_id'  => $targetId,
            'actor_id' => $followerId,
            'type'     => 'follow_back',
            'title'    => $follower->name . ' followed you back!',
            'body'     => 'You are now mutual friends 🤝',
            'icon'     => '🤝',
            'data'     => [
                'actor_name'   => $follower->name,
                'actor_avatar' => $follower->avatar,
            ],
            'read_at'  => null,
        ]);
    }

    // Poke
    public static function notifyPoke(
        string $senderId,
        string $receiverId,
        string $challengeTitle
    ): void {
        $sender = User::find($senderId);
        if (!$sender) return;

        Notification::create([
            'user_id'  => $receiverId,
            'actor_id' => $senderId,
            'type'     => 'poke',
            'title'    => $sender->name . ' poked you! 👋',
            'body'     => 'Don\'t forget your challenge today!',
            'icon'     => '👋',
            'data'     => [
                'actor_name'      => $sender->name,
                'actor_avatar'    => $sender->avatar,
                'challenge_title' => $challengeTitle,
            ],
            'read_at'  => null,
        ]);
    }

    // Challenge completed — notify friends
    public static function notifyChallengeComplete(
        string $userId,
        string $challengeTitle,
        array $friendIds
    ): void {
        $user = User::find($userId);
        if (!$user) return;

        foreach ($friendIds as $friendId) {
            if ($friendId === $userId) continue;
            Notification::create([
                'user_id'  => $friendId,
                'actor_id' => $userId,
                'type'     => 'friend_completed',
                'title'    => $user->name . ' completed a challenge! 🎉',
                'body'     => $challengeTitle,
                'icon'     => '🎉',
                'data'     => [
                    'actor_name'      => $user->name,
                    'actor_avatar'    => $user->avatar,
                    'challenge_title' => $challengeTitle,
                ],
                'read_at'  => null,
            ]);
        }
    }

    // Achievement unlocked
    public static function notifyAchievement(
        string $userId,
        string $achievementName,
        string $achievementIcon
    ): void {
        Notification::create([
            'user_id'  => $userId,
            'actor_id' => null,
            'type'     => 'achievement',
            'title'    => 'Achievement Unlocked! ' . $achievementIcon,
            'body'     => $achievementName,
            'icon'     => $achievementIcon,
            'data'     => [
                'achievement_name' => $achievementName,
                'achievement_icon' => $achievementIcon,
            ],
            'read_at'  => null,
        ]);
    }

    // Get unread count
    public static function unreadCount(string $userId): int
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }
}
