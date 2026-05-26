<?php

namespace App\Http\Controllers;

use App\Models\UserDailyChallenge;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use \MongoDB\BSON\UTCDateTime;
class ChallengeHistoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = (string) $request->user()->getKey();
        $limit  = min((int) $request->query('limit', 30), 100);

        // Gunakan limit saja, tanpa skip — skip tidak support di semua MongoDB driver
        $challenges = UserDailyChallenge::with('challenge')
            ->where('user_id', $userId)
            ->orderBy('challenge_date', 'desc')
            ->limit($limit)
            ->get();

        $data = $challenges->map(function (UserDailyChallenge $c) {
            $base = $c->challenge;

            // Handle date field dengan aman
            $dateStr = null;
            if ($c->challenge_date instanceof Carbon) {
                $dateStr = $c->challenge_date->toDateString();
            } elseif ($c->challenge_date instanceof UTCDateTime) {
                $dateStr = Carbon::createFromTimestamp(
                    $c->challenge_date->toDateTime()->getTimestamp()
                )->toDateString();
            } else {
                $dateStr = (string) ($c->challenge_date ?? '');
            }

            return [
                'id'                => (string) $c->getKey(),
                'date'              => $dateStr,
                'title'             => $base?->title ?? 'Challenge',
                'description'       => $base?->content ?? '',
                'tags'              => $base?->tags ?? [],
                'estimated_minutes' => (int) ($base?->estimated_minutes ?? 15),
                'is_completed'      => (bool) ($c->is_completed ?? false),
                'completed_at'      => $c->completed_at instanceof Carbon
                    ? $c->completed_at->toIso8601String()
                    : null,
                'reflection'        => $c->metadata['reflection'] ?? null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => [
                'count'      => $data->count(),
                'challenges' => $data->values(),
            ],
        ]);
    }
}
