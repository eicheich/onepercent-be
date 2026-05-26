<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserDailyChallenge;
use App\Models\UserFollow;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use \App\Models\UserPersonalization;
use  \App\Models\ChallengePoke;

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

    public function settings()
    {
        $userId = session('web_user.id');
        $user   = User::find($userId);
        return view('web.settings', compact('user'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        $userId = session('web_user.id');
        $user   = User::find($userId);

        if (!$user || !\Illuminate\Support\Facades\Hash::check(
            $request->current_password,
            $user->password
        )) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.'
            ]);
        }

        $user->password = $request->password;
        $user->save();

        return back()->with('success', 'Password changed successfully!');
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'confirm_text' => 'required|in:DELETE',
        ]);

        $userId = session('web_user.id');
        $user   = User::find($userId);

        if (!$user) {
            return redirect()->route('web.login');
        }

        // Hapus semua data
        UserPersonalization::where('user_id', $userId)->delete();
        UserDailyChallenge::where('user_id', $userId)->delete();
        UserFollow::where('follower_id', $userId)->delete();
        UserFollow::where('following_id', $userId)->delete();
        ChallengePoke::where('sender_id', $userId)->delete();
        ChallengePoke::where('receiver_id', $userId)->delete();
        UserAchievement::where('user_id', $userId)->delete();
        $user->tokens()->delete();
        $user->delete();

        // Clear session
        $request->session()->flush();

        return redirect()->route('landing')
            ->with('success', 'Account deleted successfully.');
    }
}
