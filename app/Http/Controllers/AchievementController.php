<?php

namespace App\Http\Controllers;

use App\Services\AchievementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function __construct(private AchievementService $achievementService) {}

    public function index(Request $request): JsonResponse
    {
        $userId       = (string) $request->user()->getKey();
        $achievements = $this->achievementService->getUserAchievements($userId);

        $unlocked = array_filter($achievements, fn($a) => $a['is_unlocked']);

        return response()->json([
            'status' => 'success',
            'data'=> [
                'total' => count($achievements),
                'unlocked' => count($unlocked),
                'achievements' => $achievements,
            ],
        ]);
    }

    public function check(Request $request): JsonResponse
    {
        $userId = (string) $request->user()->getKey();
        $newlyUnlocked = $this->achievementService->checkAndUnlock($userId);

        return response()->json([
            'status' => 'success',
            'data'=> [
                'newly_unlocked' => $newlyUnlocked,
                'count' => count($newlyUnlocked),
            ],
        ]);
    }
}
