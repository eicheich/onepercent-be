<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserDailyChallenge;
use App\Models\UserFollow;
use App\Services\AchievementService;
use Illuminate\Http\Request;

class ProfileWebController extends Controller
{
    public function index()
    {
        $userId = session('web_user.id');
        $user   = User::find($userId);

        $achievements = (new AchievementService())->getUserAchievements($userId);

        $followersCount = UserFollow::where('following_id', $userId)->count();
        $followingCount = UserFollow::where('follower_id', $userId)->count();
        $totalCompleted = UserDailyChallenge::where('user_id', $userId)
            ->where('is_completed', true)->count();

        return view('web.profile', compact(
            'user',
            'achievements',
            'followersCount',
            'followingCount',
            'totalCompleted'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:male,female,other',
        ]);

        $userId = session('web_user.id');
        $user   = User::find($userId);
        $user->name = $request->name;
        $user->gender = $request->gender;
        $user->save();

        session(['web_user.name' => $user->name]);

        return back()->with('success', 'Profile updated!');
    }
}
