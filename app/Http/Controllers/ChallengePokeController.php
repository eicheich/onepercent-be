<?php

namespace App\Http\Controllers;

use App\Models\ChallengePoke;
use App\Models\User;
use App\Models\UserDailyChallenge;
use App\Models\UserFollow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChallengePokeController extends Controller
{
    public function send(Request $request, string $userId): JsonResponse
    {
        $validated = $request->validate([
            'user_daily_challenge_id' => ['required', 'string', 'max:120'],
            'type' => ['nullable', 'string', 'in:boast,remind'],
            'message' => ['nullable', 'string', 'max:220'],
        ]);

        $senderId = (string) $request->user()->getKey();
        $receiverId = trim($userId);

        if ($receiverId === '' || $receiverId === $senderId) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot poke this user.',
            ], 422);
        }

        $receiver = User::query()->find($receiverId);

        if ($receiver === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Target user not found.',
            ], 404);
        }

        $isSenderFollowingReceiver = UserFollow::query()
            ->where('follower_id', $senderId)
            ->where('following_id', $receiverId)
            ->exists();

        $isReceiverFollowingSender = UserFollow::query()
            ->where('follower_id', $receiverId)
            ->where('following_id', $senderId)
            ->exists();

        if (! $isSenderFollowingReceiver || ! $isReceiverFollowingSender) {
            return response()->json([
                'status' => 'error',
                'message' => 'Poke is only available for mutual followers.',
            ], 403);
        }

        $assignment = UserDailyChallenge::query()
            ->with('challenge')
            ->where('_id', (string) $validated['user_daily_challenge_id'])
            ->where('user_id', $senderId)
            ->first();

        if ($assignment === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Completed challenge assignment not found.',
            ], 404);
        }

        if (! (bool) $assignment->is_completed) {
            return response()->json([
                'status' => 'error',
                'message' => 'You can only poke friends after completing the challenge.',
            ], 422);
        }

        $type = (string) ($validated['type'] ?? 'boast');
        $message = isset($validated['message']) ? trim((string) $validated['message']) : null;

        if ($message === '') {
            $message = null;
        }

        $poke = ChallengePoke::query()->create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'user_daily_challenge_id' => (string) $assignment->getKey(),
            'challenge_id' => (string) ($assignment->challenge_id ?? ''),
            'type' => $type,
            'message' => $message,
            'metadata' => [
                'challenge_title' => $assignment->challenge?->title,
                'challenge_date' => $assignment->challenge_date?->toDateString(),
                'completed_at' => $assignment->completed_at?->toIso8601String(),
            ],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Poke sent successfully.',
            'data' => [
                'id' => (string) $poke->getKey(),
                'to_user_id' => $receiverId,
                'type' => $poke->type,
            ],
        ], 201);
    }

    public function inbox(Request $request): JsonResponse
    {
        $authId = (string) $request->user()->getKey();

        $rows = ChallengePoke::query()
            ->where('receiver_id', $authId)
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        $senderIds = $rows
            ->pluck('sender_id')
            ->map(fn($id) => (string) $id)
            ->unique()
            ->values()
            ->all();

        $users = count($senderIds) === 0
            ? collect()
            : User::query()
                ->whereIn('_id', $senderIds)
                ->get()
                ->keyBy(fn($user) => (string) $user->getKey());

        $items = $rows
            ->map(function (ChallengePoke $row) use ($users) {
                $sender = $users->get((string) $row->sender_id);

                return [
                    'id' => (string) $row->getKey(),
                    'sender' => $sender === null ? null : [
                        'id' => (string) $sender->getKey(),
                        'name' => $sender->name,
                    ],
                    'user_daily_challenge_id' => (string) $row->user_daily_challenge_id,
                    'challenge_id' => (string) ($row->challenge_id ?? ''),
                    'type' => $row->type,
                    'message' => $row->message,
                    'metadata' => $row->metadata ?? [],
                    'read_at' => $row->read_at?->toIso8601String(),
                    'created_at' => $row->created_at?->toIso8601String(),
                ];
            })
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => [
                'count' => $items->count(),
                'unread_count' => $rows->whereNull('read_at')->count(),
                'pokes' => $items,
            ],
        ]);
    }

    public function markAsRead(Request $request, string $pokeId): JsonResponse
    {
        $authId = (string) $request->user()->getKey();
        $id = trim($pokeId);

        $poke = ChallengePoke::query()
            ->where('_id', $id)
            ->where('receiver_id', $authId)
            ->first();

        if ($poke === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Poke not found.',
            ], 404);
        }

        if ($poke->read_at === null) {
            $poke->read_at = now();
            $poke->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Poke marked as read.',
            'data' => [
                'id' => (string) $poke->getKey(),
                'read_at' => $poke->read_at?->toIso8601String(),
            ],
        ]);
    }
}
