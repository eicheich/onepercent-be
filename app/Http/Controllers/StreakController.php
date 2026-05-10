<?php

namespace App\Http\Controllers;

use App\Models\UserDailyChallenge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StreakController extends Controller
{
    public function myStreak(Request $request): JsonResponse
    {
        $user = $request->user();
        $userId = (string) $user->getKey();

        // Ambil 7 hari terakhir untuk weekly tracker
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $last7Days->push(now()->subDays($i)->toDateString());
        }

        $completedDates = UserDailyChallenge::query()
            ->where('user_id', $userId)
            ->where('is_completed', true)
            ->whereIn('challenge_date', $last7Days->all())
            ->pluck('challenge_date')
            ->map(fn($d) => is_string($d) ? $d : $d->toDateString())
            ->all();

        $weeklyTracker = $last7Days->map(fn($date) => [
            'date'        => $date,
            'day'         => \Carbon\Carbon::parse($date)->format('D'),
            'is_completed'=> in_array($date, $completedDates, true),
        ]);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'current_streak'  => (int) ($user->current_streak ?? 0),
                'longest_streak'  => (int) ($user->longest_streak ?? 0),
                'last_completed'  => $user->last_completed_date?->toDateString(),
                'weekly_tracker'  => $weeklyTracker,
            ],
        ]);
    }
}
