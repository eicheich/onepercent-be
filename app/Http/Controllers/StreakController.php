<?php

namespace App\Http\Controllers;

use App\Models\UserDailyChallenge;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use \MongoDB\BSON\UTCDateTime;

class StreakController extends Controller
{
    public function myStreak(Request $request): JsonResponse
    {
        $user   = $request->user();
        $userId = (string) $user->getKey();

        // Bangun 7 hari terakhir
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $last7Days[] = now()->subDays($i)->toDateString();
        }

        // Ambil semua challenge dalam range — pakai gte/lte bukan whereIn
        $startDate = $last7Days[0];
        $endDate   = $last7Days[count($last7Days) - 1];

        $challenges = UserDailyChallenge::where('user_id', $userId)
            ->where('challenge_date', '>=', $startDate)
            ->where('challenge_date', '<=', $endDate)
            ->where('is_completed', true)
            ->get();

        // Convert ke array of date strings pakai PHP
        $completedDates = $challenges->map(function ($c) {
            $d = $c->challenge_date;
            if ($d instanceof Carbon) return $d->toDateString();
            if ($d instanceof UTCDateTime) {
                return Carbon::createFromTimestamp(
                    $d->toDateTime()->getTimestamp()
                )->toDateString();
            }
            return (string) $d;
        })->filter()->values()->all();

        $weeklyTracker = array_map(fn($date) => [
            'date'         => $date,
            'day'          => Carbon::parse($date)->format('D'),
            'is_completed' => in_array($date, $completedDates, true),
        ], $last7Days);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'current_streak' => (int) ($user->current_streak ?? 0),
                'longest_streak' => (int) ($user->longest_streak ?? 0),
                'last_completed' => $user->last_completed_date instanceof Carbon
                    ? $user->last_completed_date->toDateString()
                    : (string) ($user->last_completed_date ?? ''),
                'weekly_tracker' => $weeklyTracker,
            ],
        ]);
    }
}
