<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\User;
use \App\Models\UserFollow;
use App\Models\UserDailyChallenge;
use App\Models\UserPersonalization;
use App\Services\AchievementService;
use App\Services\GeminiChallengeService;
use \App\Models\ChallengePoke;
use Illuminate\Http\Request;
use  \App\Models\Notification;
use Illuminate\Support\Carbon;
use \MongoDB\BSON\UTCDateTime;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = session('web_user.id');
        $user   = User::find($userId);
        $today  = now()->toDateString();

        // Weekly tracker
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $last7Days[] = now()->subDays($i)->toDateString();
        }

        $startDate = $last7Days[0];
        $endDate   = $last7Days[count($last7Days) - 1];

        // Ambil semua challenge dalam range
        $challengesInRange = UserDailyChallenge::where('user_id', $userId)
            ->where('is_completed', true)
            ->where('challenge_date', '>=', $startDate)
            ->where('challenge_date', '<=', $endDate)
            ->get();

        // Convert ke date string pakai PHP
        $completedDates = $challengesInRange->map(function ($c) {
            $d = $c->challenge_date;
            if ($d instanceof \Carbon\Carbon) return $d->toDateString();
            if ($d instanceof \MongoDB\BSON\UTCDateTime) {
                return \Carbon\Carbon::createFromTimestamp(
                    $d->toDateTime()->getTimestamp()
                )->toDateString();
            }
            return (string) $d;
        })->filter()->values()->all();

        $weeklyTracker = array_map(fn($date) => [
            'date'         => $date,
            'day'          => \Carbon\Carbon::parse($date)->format('D'),
            'is_completed' => in_array($date, $completedDates, true),
        ], $last7Days);

        // Today challenge
        $todayChallenge = UserDailyChallenge::with('challenge')
            ->where('user_id', $userId)
            ->where('challenge_date', $today)
            ->first();

        // Top leaderboard
        $leaderboard = User::orderBy('current_streak', 'desc')
            ->limit(10)
            ->get();

        $leaderboard->each(function ($u) use ($userId) {
            $u->is_me = (string) $u->getKey() === $userId;
        });

        $totalCompleted = UserDailyChallenge::where('user_id', $userId)
            ->where('is_completed', true)->count();

        return view('web.dashboard', compact(
            'user',
            'weeklyTracker',
            'todayChallenge',
            'leaderboard',
            'totalCompleted'
        ));
    }

    public function challenge()
    {
        $userId = session('web_user.id');
        $today  = now()->toDateString();

        // Pakai string comparison, bukan date cast
        $todayChallenge = UserDailyChallenge::with('challenge')
            ->where('user_id', $userId)
            ->where('challenge_date', $today)
            ->first();

        $history = UserDailyChallenge::with('challenge')
            ->where('user_id', $userId)
            ->orderBy('challenge_date', 'desc')
            ->limit(14)
            ->get();

        // Weekly progress — pakai gte/lte bukan whereBetween
        $weekStart = now()->startOfWeek()->toDateString();
        $weekEnd   = now()->endOfWeek()->toDateString();

        $weekCompleted = UserDailyChallenge::where('user_id', $userId)
            ->where('is_completed', true)
            ->where('challenge_date', '>=', $weekStart)
            ->where('challenge_date', '<=', $weekEnd)
            ->count();

        return view('web.challenge', compact(
            'todayChallenge',
            'history',
            'weekCompleted'
        ));
    }

    public function leaderboard()
    {
        $userId = session('web_user.id');

        $global = User::orderBy('current_streak', 'desc')
            ->limit(50)
            ->get()
            ->map(fn($u, $i) => [
                'rank' => $i + 1,
                'id' => (string) $u->getKey(),
                'name' => $u->name,
                'avatar' => $u->avatar,
                'current_streak' => $u->current_streak ?? 0,
                'is_me' => (string) $u->getKey() === $userId,
            ]);

        $myRank = $global->firstWhere('is_me', true);

        return view('web.leaderboard', compact('global', 'myRank'));
    }

    public function showTags()
    {
        $tags = UserPersonalization::AVAILABLE_TAGS
            ?? [
                'technology',
                'business',
                'sports',
                'health',
                'finance',
                'education',
                'entertainment',
                'lifestyle'
            ];

        // Ambil dari PersonalizationController
        $availableTags = [
            'technology',
            'business',
            'sports',
            'health',
            'finance',
            'education',
            'entertainment',
            'lifestyle',
        ];

        return view('web.tags', compact('availableTags'));
    }

    public function saveTags(Request $request)
    {
        $request->validate([
            'tags' => 'required|array|min:1|max:5',
            'custom_tags' => 'nullable|array|max:3',
        ]);

        $userId = session('web_user.id');

        $merged = array_unique(array_merge(
            $request->tags ?? [],
            $request->custom_tags ?? []
        ));

        UserPersonalization::updateOrCreate(
            ['user_id' => $userId],
            ['tags' => $merged]
        );

        return redirect()->route('web.dashboard');
    }

    public function notifications()
    {
        $userId = session('web_user.id');

        $pokes = ChallengePoke::where('receiver_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $senderIds = $pokes->pluck('sender_id')
            ->map(fn($id) => (string) $id)
            ->unique()->values()->all();

        $senders = count($senderIds) > 0
            ? User::whereIn('_id', $senderIds)->get()
            ->keyBy(fn($u) => (string) $u->getKey())
            : collect();

        $notifications = $pokes->map(function ($poke) use ($senders) {
            $sender = $senders->get((string) $poke->sender_id);
            return [
                'id' => (string) $poke->getKey(),
                'sender_name' => $sender?->name ?? 'Someone',
                'sender_avatar' => $sender?->avatar,
                'type' => $poke->type,
                'message' => $poke->message,
                'challenge_title' => $poke->metadata['challenge_title'] ?? '',
                'read_at' => $poke->read_at,
                'created_at' => $poke->created_at,
            ];
        });

        $unreadCount = $notifications->whereNull('read_at')->count();

        // Auto mark as read
        Notification::where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('web.notifications', compact('notifications', 'unreadCount'));
    }

    public function uploadProof(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200',
            'user_daily_challenge_id' => 'required|string',
        ]);

        $userId = session('web_user.id');
        $challenge = UserDailyChallenge::with('challenge')
            ->where('_id', $request->user_daily_challenge_id)
            ->where('user_id', $userId)
            ->first();

        if (!$challenge) {
            return back()->withErrors(['error' => 'Challenge not found']);
        }

        $file = $request->file('file');
        $mimeType = $file->getMimeType();
        $base64 = base64_encode(file_get_contents($file->getRealPath()));

        try {
            $gemini = app(GeminiChallengeService::class);
            $score = $gemini->scoreProof(
                $base64,
                $mimeType,
                $challenge->challenge?->title ?? 'Challenge',
                $challenge->challenge?->content ?? ''
            );

            $metadata = (array) ($challenge->metadata ?? []);
            $metadata['proof_score'] = $score['score'];
            $metadata['proof_feedback'] = $score['feedback'];
            $metadata['proof_submitted_at'] = now()->toIso8601String();
            $challenge->forceFill(['metadata' => $metadata])->save();

            // Check achievements
            app(AchievementService::class)->checkAndUnlock($userId);

            return back()->with('proof_result', [
                'score'    => $score['score'],
                'feedback' => $score['feedback'],
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'AI scoring failed: ' . $e->getMessage()]);
        }
    }
    public function pokeFriend(string $userId)
    {
        $myId = session('web_user.id');

        // Validasi target user ada
        $targetUser = User::find($userId);
        if (!$targetUser) {
            return back()->with('errorpoke', 'User not found!');
        }

        // Cek tidak bisa poke diri sendiri
        if ($myId === $userId) {
            return back()->with('errorpoke', 'You cannot poke yourself!');
        }

        // Cek apakah sudah complete challenge hari ini
        $challenge = UserDailyChallenge::where('user_id', $myId)
            ->where('challenge_date', now()->toDateString())
            ->where('is_completed', true)
            ->first();

        if (!$challenge) {
            return back()->with('errorpoke', 'Complete today\'s challenge first before poking!');
        }

        // Cek mutual follow
        $iFollowThem = UserFollow::where('follower_id', $myId)
            ->where('following_id', $userId)->exists();
        $theyFollowMe = UserFollow::where('follower_id', $userId)
            ->where('following_id', $myId)->exists();

        if (!$iFollowThem || !$theyFollowMe) {
            return back()->with('errorpoke', 'You must follow each other (mutual) to poke!');
        }

        // Cek sudah poke hari ini
        $alreadyPoked = ChallengePoke::where('sender_id', $myId)
            ->where('receiver_id', $userId)
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if ($alreadyPoked) {
            return back()->with('errorpoke', 'You already poked ' . $targetUser->name . ' today!');
        }

        ChallengePoke::create([
            'sender_id' => $myId,
            'receiver_id' => $userId,
            'user_daily_challenge_id' => (string) $challenge->getKey(),
            'challenge_id' => (string) ($challenge->challenge_id ?? ''),
            'type' => 'boast',
            'message' => 'I finished today\'s challenge! 💪 Come on!',
            'metadata' => [
                'challenge_title' => $challenge->challenge?->title ?? 'Challenge',
                'challenge_date' => now()->toDateString(),
                'completed_at' => $challenge->completed_at?->toIso8601String(),
            ],
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('successpoke', '👋 Poked ' . $targetUser->name . ' successfully!');
    }
}
