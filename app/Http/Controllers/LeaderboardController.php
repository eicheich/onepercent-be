<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserFollow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function global(Request $request): JsonResponse
    {
        $authId = (string) $request->user()->getKey();

        // Top 50 by current_streak
        $users = User::query()
            ->orderBy('current_streak', 'desc')
            ->orderBy('longest_streak', 'desc')
            ->limit(50)
            ->get();

        $authRank = null;
        $data = $users->values()->map(function (User $user, int $index) use ($authId, &$authRank) {
            $userId = (string) $user->getKey();
            $rank = $index + 1;

            if ($userId === $authId) {
                $authRank = $rank;
            }

            return [
                'rank'           => $rank,
                'id'             => $userId,
                'name'           => $user->name,
                'current_streak' => (int) ($user->current_streak ?? 0),
                'longest_streak' => (int) ($user->longest_streak ?? 0),
                'is_me'          => $userId === $authId,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => [
                'my_rank'     => $authRank,
                'leaderboard' => $data,
            ],
        ]);
    }

    public function friends(Request $request): JsonResponse
    {
        $authId = (string) $request->user()->getKey();

        // Ambil mutual followers (friends)
        $followingIds = UserFollow::query()
            ->where('follower_id', $authId)
            ->pluck('following_id')
            ->map(fn($id) => (string) $id)
            ->all();

        $followerIds = UserFollow::query()
            ->where('following_id', $authId)
            ->pluck('follower_id')
            ->map(fn($id) => (string) $id)
            ->all();

        $mutualIds = array_values(array_intersect($followingIds, $followerIds));
        $mutualIds[] = $authId; // Include diri sendiri

        $users = User::query()
            ->whereIn('_id', $mutualIds)
            ->orderBy('current_streak', 'desc')
            ->get();

        $authRank = null;
        $data = $users->values()->map(function (User $user, int $index) use ($authId, &$authRank) {
            $userId = (string) $user->getKey();
            $rank = $index + 1;

            if ($userId === $authId) {
                $authRank = $rank;
            }

            return [
                'rank'           => $rank,
                'id'             => $userId,
                'name'           => $user->name,
                'current_streak' => (int) ($user->current_streak ?? 0),
                'longest_streak' => (int) ($user->longest_streak ?? 0),
                'is_me'          => $userId === $authId,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => [
                'my_rank'     => $authRank,
                'leaderboard' => $data,
            ],
        ]);
    }
}
