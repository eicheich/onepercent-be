<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\ChallengePoke;
use App\Models\User;
use App\Models\UserDailyChallenge;
use App\Models\UserFollow;
use Illuminate\Http\Request;
use \App\Models\UserAchievement;
use \App\Services\AchievementService;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_challenges' => UserDailyChallenge::where('is_completed', true)->count(),
            'total_pokes' => ChallengePoke::count(),
            'active_today' => UserDailyChallenge::where('challenge_date', now()->toDateString())->count(),
            'avg_streak' => round(User::avg('current_streak') ?? 0, 1),
            'top_streak' => User::max('current_streak') ?? 0,
        ];

        $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();
        $topUsers = User::orderBy('current_streak', 'desc')->limit(10)->get();

        $dailyActivity = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $dailyActivity->push([
                'date' => $date,
                'day' => now()->subDays($i)->format('D'),
                'count' => UserDailyChallenge::where('challenge_date', $date)
                    ->where('is_completed', true)->count(),
            ]);
        }

        return view('web.admin.dashboard', compact(
            'stats',
            'recentUsers',
            'topUsers',
            'dailyActivity'
        ));
    }

    public function users(Request $request)
    {
        $search = $request->query('search');
        $query  = User::orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(20);

        return view('web.admin.users', compact('users', 'search'));
    }

    public function challenges(Request $request)
    {
        $challenges = Challenge::orderBy('created_at', 'desc')->paginate(20);
        $stats = [
            'total' => Challenge::count(),
            'completed' => UserDailyChallenge::where('is_completed', true)->count(),
            'today' => UserDailyChallenge::where('challenge_date', now()->toDateString())->count(),
        ];

        return view('web.admin.challenges', compact('challenges', 'stats'));
    }

    public function deleteUser(string $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->tokens()->delete();
            $user->delete();
        }
        return back()->with('success', 'User deleted!');
    }
    public function userDetail(string $id)
    {
        $user = User::find($id);
        if (!$user) return abort(404);

        $challenges =  UserDailyChallenge::with('challenge')
            ->where('user_id', $id)
            ->orderBy('challenge_date', 'desc')
            ->limit(14)->get();

        $achievements = UserAchievement::where('user_id', $id)->get();

        $followersCount = UserFollow::where('following_id', $id)->count();
        $followingCount = UserFollow::where('follower_id', $id)->count();

        return view(
            'web.admin.user-detail',
            compact('user', 'challenges', 'achievements', 'followersCount', 'followingCount')
        );
    }

    public function achievements()
    {
        $allAchievements = UserAchievement::all();

        $stats = collect(AchievementService::ACHIEVEMENTS)
            ->map(fn($data, $key) => [
                'key' => $key,
                'name' => $data['name'],
                'icon' => $data['icon'],
                'image' => $data['image'],
                'description' => $data['description'],
                'count' => $allAchievements->where('achievement_key', $key)->count(),
            ])->values();

        $totalUnlocked = $allAchievements->count();

        return view('web.admin.achievements', compact('stats', 'totalUnlocked'));
    }
}
