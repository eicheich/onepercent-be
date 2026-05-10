<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserFollow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchUsers(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));
        $authId = (string) $request->user()->getKey();

        if (strlen($query) < 2) {
            return response()->json([
                'status' => 'error',
                'message' => 'Search query must be at least 2 characters.',
            ], 422);
        }

        $users = User::query()
            ->where('_id', '!=', $authId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get();

        // Cek following status
        $followingIds = UserFollow::query()
            ->where('follower_id', $authId)
            ->pluck('following_id')
            ->map(fn($id) => (string) $id)
            ->all();

        $data = $users->map(function (User $user) use ($followingIds, $authId) {
            $userId = (string) $user->getKey();
            return [
                'id'           => $userId,
                'name'         => $user->name,
                'email'        => $user->email,
                'current_streak' => (int) ($user->current_streak ?? 0),
                'is_following' => in_array($userId, $followingIds, true),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => [
                'count' => $data->count(),
                'users' => $data->values(),
            ],
        ]);
    }
}
