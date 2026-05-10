<?php

namespace App\Http\Controllers;

use App\Models\UserDailyChallenge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChallengeHistoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = (string) $request->user()->getKey();

        $limit = min((int) $request->query('limit', 30), 100);
        $offset = (int) $request->query('offset', 0);

        $challenges = UserDailyChallenge::query()
            ->with('challenge')
            ->where('user_id', $userId)
            ->orderBy('challenge_date', 'desc')
            ->skip($offset)
            ->limit($limit)
            ->get();

        $data = $challenges->map(function (UserDailyChallenge $c) {
            $base = $c->challenge;
            return [
                'id'               => (string) $c->getKey(),
                'date'             => $c->challenge_date?->toDateString(),
                'title'            => $base?->title ?? 'Challenge',
                'description'      => $base?->content ?? '',
                'tags'             => $base?->tags ?? [],
                'estimated_minutes'=> (int) ($base?->estimated_minutes ?? 15),
                'is_completed'     => (bool) ($c->is_completed ?? false),
                'completed_at'     => $c->completed_at?->toIso8601String(),
                'reflection'       => $c->metadata['reflection'] ?? null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => [
                'count'      => $data->count(),
                'challenges' => $data,
            ],
        ]);
    }
}
