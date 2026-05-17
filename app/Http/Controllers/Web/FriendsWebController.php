<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserFollow;
use Illuminate\Http\Request;

class FriendsWebController extends Controller
{
    public function index()
    {
        $userId = session('web_user.id');

        $followers = UserFollow::where('following_id', $userId)
            ->pluck('follower_id')
            ->map(fn($id) => (string) $id)->all();

        $following = UserFollow::where('follower_id', $userId)
            ->pluck('following_id')
            ->map(fn($id) => (string) $id)->all();

        $followerUsers = count($followers) > 0
            ? User::whereIn('_id', $followers)->get() : collect();

        $followingUsers = count($following) > 0
            ? User::whereIn('_id', $following)->get() : collect();

        return view('web.friends', compact(
            'followerUsers',
            'followingUsers',
            'following'
        ));
    }

    public function search(Request $request)
    {
        $query  = $request->query('q', '');
        $userId = session('web_user.id');

        $users = collect();
        if (strlen($query) >= 2) {
            $users = User::where('_id', '!=', $userId)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                })
                ->limit(20)->get();
        }

        $following = UserFollow::where('follower_id', $userId)
            ->pluck('following_id')
            ->map(fn($id) => (string) $id)->all();

        return view('web.friends', compact('users', 'query', 'following'));
    }

    public function follow(string $id)
    {
        $userId = session('web_user.id');

        if ($id === $userId) {
            return back()->withErrors(['error' => 'Cannot follow yourself']);
        }

        $exists = UserFollow::where('follower_id', $userId)
            ->where('following_id', $id)->exists();

        if (!$exists) {
            UserFollow::create([
                'follower_id'  => $userId,
                'following_id' => $id,
            ]);
        }

        return back()->with('success', 'Followed successfully!');
    }

    public function unfollow(string $id)
    {
        $userId = session('web_user.id');
        UserFollow::where('follower_id', $userId)
            ->where('following_id', $id)->delete();

        return back()->with('success', 'Unfollowed.');
    }
}
