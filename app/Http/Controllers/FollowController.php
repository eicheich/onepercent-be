<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserFollow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\NotificationService;

class FollowController extends Controller
{


    public function follow(Request $request, string $userId): JsonResponse
    {
        $authId = (string) $request->user()->getKey();

        if ($authId === $userId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot follow yourself.',
            ], 422);
        }

        $exists = UserFollow::where('follower_id', $authId)
            ->where('following_id', $userId)
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Already following.',
            ]);
        }

        UserFollow::create([
            'follower_id'  => $authId,
            'following_id' => $userId,
        ]);

        // Notif ke target
        try {
                        \Log::info('Attempting to create follow notification', ['follower' => $authId, 'target' => $userId]);
            NotificationService::notifyFollow($authId, $userId);
        } catch (\Exception $e) {
            \Log::error('Failed to create follow notification: ' . $e->getMessage());
        }

        // Cek apakah mutual → notif follow back
        $theyFollowMe = UserFollow::where('follower_id', $userId)
            ->where('following_id', $authId)
            ->exists();

        if ($theyFollowMe) {
            try {
                NotificationService::notifyFollowBack($authId, $userId);
            } catch (\Exception $e) {
                \Log::error('Failed to create follow_back notification: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Followed successfully.',
        ]);
    }

    public function unfollow(Request $request, string $userId): JsonResponse
    {
        $authId = (string) $request->user()->getKey();
        $targetUserId = trim($userId);

        if ($targetUserId === '' || $targetUserId === $authId) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot unfollow this user.',
            ], 422);
        }

        UserFollow::query()
            ->where('follower_id', $authId)
            ->where('following_id', $targetUserId)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User unfollowed successfully.',
            'data' => [
                'following_user_id' => $targetUserId,
            ],
        ]);
    }

    public function followers(Request $request): JsonResponse
    {
        $authId = (string) $request->user()->getKey();

        $followRows = UserFollow::query()
            ->where('following_id', $authId)
            ->orderBy('created_at', 'desc')
            ->get();

        $followerIds = $followRows
            ->pluck('follower_id')
            ->map(fn($id) => (string) $id)
            ->unique()
            ->values()
            ->all();

        $users = count($followerIds) === 0
            ? collect()
            : User::query()
            ->whereIn('_id', $followerIds)
            ->get()
            ->keyBy(fn($user) => (string) $user->getKey());

        $data = $followRows
            ->map(function (UserFollow $row) use ($users) {
                $user = $users->get((string) $row->follower_id);

                if ($user === null) {
                    return null;
                }

                return [
                    'id' => (string) $user->getKey(),
                    'name' => $user->name,
                    'email' => $user->email,
                    'birth_date' => $user->birth_date?->toDateString(),
                    'gender' => $user->gender,
                    'followed_at' => $row->created_at?->toIso8601String(),
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => [
                'count' => $data->count(),
                'followers' => $data,
            ],
        ]);
    }

    public function following(Request $request): JsonResponse
    {
        $authId = (string) $request->user()->getKey();

        $followRows = UserFollow::query()
            ->where('follower_id', $authId)
            ->orderBy('created_at', 'desc')
            ->get();

        $followingIds = $followRows
            ->pluck('following_id')
            ->map(fn($id) => (string) $id)
            ->unique()
            ->values()
            ->all();

        $users = count($followingIds) === 0
            ? collect()
            : User::query()
            ->whereIn('_id', $followingIds)
            ->get()
            ->keyBy(fn($user) => (string) $user->getKey());

        $data = $followRows
            ->map(function (UserFollow $row) use ($users) {
                $user = $users->get((string) $row->following_id);

                if ($user === null) {
                    return null;
                }

                return [
                    'id' => (string) $user->getKey(),
                    'name' => $user->name,
                    'email' => $user->email,
                    'birth_date' => $user->birth_date?->toDateString(),
                    'gender' => $user->gender,
                    'followed_at' => $row->created_at?->toIso8601String(),
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => [
                'count' => $data->count(),
                'following' => $data,
            ],
        ]);
    }
}
