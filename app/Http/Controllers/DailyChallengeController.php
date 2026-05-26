<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\UserDailyChallenge;
use App\Models\UserPersonalization;
use App\Services\GeminiChallengeService;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class DailyChallengeController extends Controller
{
    public function __construct(private GeminiChallengeService $geminiChallengeService) {}

    public function testAi(): JsonResponse
    {
        $startedAt = microtime(true);

        try {
            $result = $this->geminiChallengeService->testConnection();
        } catch (Throwable $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gemini test request failed.',
                'error' => $exception->getMessage(),
            ], 502);
        }

        $elapsedMs = (int) round((microtime(true) - $startedAt) * 1000);

        return response()->json([
            'status' => 'success',
            'message' => 'Gemini test request succeeded.',
            'data' => [
                'model' => $result['model'],
                'response' => $result['response'],
                'latency_ms' => $elapsedMs,
            ],
        ]);
    }

    public function today(Request $request): JsonResponse
    {
        $userId = (string) $request->user()->getKey();
        $today = now()->toDateString();
        $challenge = $this->findDailyChallengeForDate($userId, $today);

        return response()->json([
            'status' => 'success',
            'data' => $challenge ? $this->buildChallengeResponse($challenge, $today) : null,
        ]);
    }

    public function generate(Request $request): JsonResponse
    {
        $userId = (string) $request->user()->getKey();
        $today  = now()->toDateString();

        // Cek existing — pakai first() bukan create langsung
        $existing = UserDailyChallenge::with('challenge')
            ->where('user_id', $userId)
            ->where('challenge_date', $today)
            ->first();

        if ($existing !== null) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Challenge already exists for today.',
                'data'    => $this->buildChallengeResponse($existing, $today),
            ]);
        }

        // Ambil tags user
        $personalization = UserPersonalization::where('user_id', $userId)->first();
        $tags = $personalization?->tags ?? ['technology', 'education'];

        // Coba Gemini
        $challengeData = null;
        $useGemini     = false;

        try {
            $gemini        = app(GeminiChallengeService::class);
            $challengeData = $gemini->generateDailyChallenge($tags);
            $useGemini     = true;
        } catch (Throwable $e) {
            Log::warning(
                'Gemini failed: ' . $e->getMessage()
            );
        }

        // Wrap dalam try-catch untuk handle duplicate key
        try {
            if (!$useGemini || !$challengeData) {
                // Fallback pool — PHP filter, bukan whereIn
                $allChallenges = Challenge::all();

                if ($allChallenges->isEmpty()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'No challenges available.',
                    ], 503);
                }

                $matching = $allChallenges->filter(
                    fn($c) => count(array_intersect((array)($c->tags ?? []), $tags)) > 0
                );

                $poolChallenge = $matching->isNotEmpty()
                    ? $matching->random()
                    : $allChallenges->random();

                $userChallenge = UserDailyChallenge::create([
                    'user_id'        => $userId,
                    'challenge_id'   => (string) $poolChallenge->getKey(),
                    'challenge_date' => $today,
                    'is_completed'   => false,
                    'completed_at'   => null,
                    'expires_at'     => now()->addDay()->startOfDay(),
                    'metadata'       => ['provider' => 'pool'],
                ]);

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Challenge generated.',
                    'data'    => $this->buildChallengeResponse(
                        $userChallenge->load('challenge'),
                        $today
                    ),
                ]);
            }

            // Dari Gemini
            $challenge = Challenge::create([
                'title'             => $challengeData['title'],
                'content'           => $challengeData['content'],
                'estimated_minutes' => (int) ($challengeData['estimated_minutes'] ?? 15),
                'tags'              => $challengeData['tags'] ?? $tags,
                'signature'         => md5($challengeData['title'] . $today . $userId),
                'metadata'          => ['provider' => 'gemini'],
            ]);

            $userChallenge = UserDailyChallenge::create([
                'user_id'        => $userId,
                'challenge_id'   => (string) $challenge->getKey(),
                'challenge_date' => $today,
                'is_completed'   => false,
                'completed_at'   => null,
                'expires_at'     => now()->endOfDay(),
                'metadata'       => ['provider' => 'gemini'],
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Challenge generated.',
                'data'    => $this->buildChallengeResponse(
                    $userChallenge->load('challenge'),
                    $today
                ),
            ]);
        } catch (\Exception $e) {
            Log::error('Generate error: ' . $e->getMessage());
            // Handle duplicate key — ambil yang sudah ada
            if (
                str_contains($e->getMessage(), 'E11000') ||
                str_contains($e->getMessage(), 'duplicate key')
            ) {

                $existing = UserDailyChallenge::with('challenge')
                    ->where('user_id', $userId)
                    ->where('challenge_date', $today)
                    ->first();

                if ($existing !== null) {
                    return response()->json([
                        'status'  => 'success',
                        'message' => 'Challenge already exists.',
                        'data'    => $this->buildChallengeResponse($existing, $today),
                    ]);
                }
            }
            Log::error('Generate error: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to generate challenge. Please try again.',
            ], 500);
        }
    }

    public function complete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_daily_challenge_id' => ['nullable', 'string', 'max:120'],
            'reflection' => ['nullable', 'string', 'max:1200'],
        ]);

        $userId = (string) $request->user()->getKey();
        $today = now()->toDateString();
        $userDailyChallengeId = trim((string) ($validated['user_daily_challenge_id'] ?? ''));

        $challengeQuery = UserDailyChallenge::query()
            ->with('challenge')
            ->where('user_id', $userId);

        if ($userDailyChallengeId !== '') {
            $challengeQuery->where('_id', $userDailyChallengeId);
        } else {
            // Pakai string comparison saja — bukan Carbon/whereBetween
            $challengeQuery->where('challenge_date', $today);
        }

        $challenge = $challengeQuery->first();

        if ($challenge === null) {
            return response()->json([
                'status'  => 'error',
                'message' => $userDailyChallengeId !== ''
                    ? 'Daily challenge with the given id was not found.'
                    : 'No daily challenge found for today.',
            ], 404);
        }

        $challengeDate = $today;
        if ($challenge->challenge_date instanceof \Carbon\Carbon) {
            $challengeDate = $challenge->challenge_date->toDateString();
        } elseif (is_string($challenge->challenge_date)) {
            $challengeDate = $challenge->challenge_date;
        }

        return $this->completeAssignment(
            $challenge,
            $challengeDate,
            $validated['reflection'] ?? null
        );
    }

    private function buildChallengeResponse(UserDailyChallenge $challenge, string $fallbackDate): array
    {
        $expiresAt = $challenge->expires_at;
        $baseChallenge = $this->resolveBaseChallenge($challenge);
        $estimatedMinutes = (int) ($baseChallenge?->estimated_minutes ?? $challenge->estimated_minutes ?? 15);
        $tags = $baseChallenge?->tags ?? $challenge->tags ?? [];

        return [
            'id' => (string) $challenge->getKey(),
            'challenge_id' => (string) ($challenge->challenge_id ?? ''),
            'date' => $challenge->challenge_date?->toDateString() ?? $fallbackDate,
            'title' => $this->resolveTitle($challenge),
            'description' => $this->resolveDescription($challenge),
            'estimated_minutes' => $estimatedMinutes,
            'tags' => $tags,
            'is_completed' => (bool) ($challenge->is_completed ?? false),
            'completed_at' => $challenge->completed_at?->toIso8601String(),
            'expires_at' => $expiresAt?->toIso8601String(),
            'remaining_seconds' => $expiresAt === null ? null : max(0, now()->diffInSeconds($expiresAt, false)),
            'reflection' => $challenge->metadata['reflection'] ?? null,
        ];
    }

    private function resolveTitle(UserDailyChallenge $challenge): string
    {
        $baseChallenge = $this->resolveBaseChallenge($challenge);
        $title = trim((string) ($baseChallenge?->title ?? $challenge->title ?? ''));
        $tags = (array) ($challenge->tags ?? []);
        $genericTitles = ['review', 'daily challenge', 'challenge', 'task', 'today challenge'];

        if ($title !== '' && ! in_array(Str::lower($title), $genericTitles, true) && ! $this->isWeakTitle($title, $tags)) {
            return $title;
        }

        return $this->buildFallbackTitle($tags);
    }

    private function isWeakTitle(string $title, array $tags): bool
    {
        $words = preg_split('/\s+/', Str::lower(trim($title))) ?: [];
        $words = array_values(array_filter($words, static fn($word) => $word !== ''));

        if (count($words) < 3) {
            return true;
        }

        $hangingTailWords = ['your', 'the', 'a', 'an', 'to', 'for', 'with', 'and', 'or', 'of', 'in', 'on'];
        $lastWord = end($words);

        if (is_string($lastWord) && in_array($lastWord, $hangingTailWords, true)) {
            return true;
        }

        $normalizedTitle = Str::lower($title);

        foreach ($tags as $tag) {
            $tag = Str::lower(trim((string) $tag));

            if ($tag !== '' && Str::contains($normalizedTitle, $tag)) {
                return false;
            }
        }

        return true;
    }

    private function buildFallbackTitle(array $tags): string
    {
        $primary = Str::title(trim((string) ($tags[0] ?? 'Skill')));
        $secondary = Str::title(trim((string) ($tags[1] ?? '')));

        if ($secondary !== '') {
            return "{$primary} and {$secondary} Mini Sprint";
        }

        return "{$primary} Practical Challenge";
    }

    private function resolveDescription(UserDailyChallenge $challenge): string
    {
        $baseChallenge = $this->resolveBaseChallenge($challenge);
        $description = trim((string) ($baseChallenge?->content ?? $challenge->content ?? ''));

        if ($description !== '') {
            return $description;
        }

        $focus = $this->inferFocusFromText((string) ($challenge->title ?? ''), (array) ($challenge->tags ?? []));

        return "Complete one practical {$focus} task for 15 minutes and note 3 takeaways.";
    }
    public function regenerate(Request $request): JsonResponse
    {
        $userId = (string) $request->user()->getKey();
        $today  = now()->toDateString();

        $existing = UserDailyChallenge::with('challenge')
            ->where('user_id', $userId)
            ->where('challenge_date', $today)
            ->first();

        if ($existing === null) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No challenge found for today.',
            ], 404);
        }

        // Cek sudah completed
        if ((bool) $existing->is_completed) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot regenerate a completed challenge.',
            ], 422);
        }

        // Cek regenerate count
        $metadata       = (array) ($existing->metadata ?? []);
        $regenCount     = (int) ($metadata['regenerate_count'] ?? 0);
        $maxRegen       = 3;

        if ($regenCount >= $maxRegen) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Regenerate limit reached (max ' . $maxRegen . 'x per day).',
                'data'    => ['remaining' => 0],
            ], 422);
        }

        // Generate challenge baru
        $personalization = UserPersonalization::where('user_id', $userId)->first();
        $tags = $personalization?->tags ?? ['technology', 'education'];

        $challengeData = null;

        try {
            $gemini        = app(GeminiChallengeService::class);
            $challengeData = $gemini->generateDailyChallenge($tags);
        } catch (Throwable $e) {
            Log::warning('Regen Gemini failed: ' . $e->getMessage());
        }

        // Buat atau ambil challenge
        if ($challengeData !== null) {
            $newChallenge = Challenge::create([
                'title'             => $challengeData['title'],
                'content'           => $challengeData['content'],
                'estimated_minutes' => (int) ($challengeData['estimated_minutes'] ?? 15),
                'tags'              => $challengeData['tags'] ?? $tags,
                'signature'         => md5($challengeData['title'] . $today . $userId . $regenCount),
                'metadata'          => ['provider' => 'gemini'],
            ]);
        } else {
            $allChallenges = Challenge::all();
            $matching = $allChallenges->filter(
                fn($c) => count(array_intersect((array)($c->tags ?? []), $tags)) > 0
                    && (string) $c->getKey() !== (string) ($existing->challenge_id ?? '')
            );
            $newChallenge = $matching->isNotEmpty()
                ? $matching->random()
                : $allChallenges->filter(
                    fn($c) => (string) $c->getKey() !== (string) ($existing->challenge_id ?? '')
                )->random();
        }

        // Update existing record — ganti challenge_id + increment count
        $metadata['regenerate_count']  = $regenCount + 1;
        $metadata['last_regenerated']  = now()->toIso8601String();
        $metadata['provider']          = $challengeData !== null ? 'gemini' : 'pool';

        $existing->forceFill([
            'challenge_id' => (string) $newChallenge->getKey(),
            'metadata'     => $metadata,
        ])->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Challenge regenerated.',
            'data'    => array_merge(
                $this->buildChallengeResponse($existing->load('challenge'), $today),
                ['remaining_regenerates' => $maxRegen - ($regenCount + 1)]
            ),
        ]);
    }
    private function inferFocusFromText(string $text, array $tags): string
    {
        $normalized = Str::lower($text);
        $tagSet = collect($tags)
            ->map(fn($tag) => Str::lower(trim((string) $tag)))
            ->filter()
            ->values()
            ->all();

        foreach ($tagSet as $tag) {
            if (Str::contains($normalized, $tag)) {
                return $tag;
            }
        }

        $keywordMap = [
            'finance' => ['expense', 'expenses', 'budget', 'money', 'cash', 'saving', 'savings', 'spending', 'income'],
            'technology' => ['tech', 'app', 'code', 'coding', 'software', 'automation', 'digital', 'tool'],
            'health' => ['health', 'workout', 'exercise', 'sleep', 'nutrition', 'diet', 'wellness'],
            'startup' => ['startup', 'product', 'mvp', 'market', 'customer', 'pitch', 'business'],
            'ai' => ['ai', 'llm', 'model', 'prompt', 'machine learning', 'ml'],
        ];

        foreach ($keywordMap as $focus => $keywords) {
            foreach ($keywords as $keyword) {
                if (Str::contains($normalized, $keyword)) {
                    return $focus;
                }
            }
        }

        return (string) ($tagSet[0] ?? 'selected topic');
    }

    private function getSeenChallengeIds(string $userId): array
    {
        return UserDailyChallenge::query()
            ->where('user_id', $userId)
            ->whereNotNull('challenge_id')
            ->pluck('challenge_id')
            ->map(fn($id) => (string) $id)
            ->values()
            ->all();
    }

    private function findReusableChallenge(array $userTags, array $seenChallengeIds): ?Challenge
    {
        $candidates = Challenge::query()
            ->orderBy('created_at', 'desc')
            ->limit(400)
            ->get();

        $bestCandidate = null;
        $bestOverlap = 0;

        foreach ($candidates as $candidate) {
            $candidateTags = (array) ($candidate->tags ?? []);
            $overlap = count(array_intersect($userTags, $candidateTags));

            if ($overlap <= 0) {
                continue;
            }

            if (in_array((string) $candidate->getKey(), $seenChallengeIds, true)) {
                continue;
            }

            if ($bestCandidate === null || $overlap > $bestOverlap) {
                $bestCandidate = $candidate;
                $bestOverlap = $overlap;
            }
        }

        return $bestCandidate;
    }

    private function buildChallengeSignature(string $title, string $content): string
    {
        return sha1(Str::lower(trim($title) . '|' . trim($content)));
    }

    private function createDailyAssignment(string $userId, string $challengeDate, Challenge $challengePool, array $metadata): UserDailyChallenge
    {
        $estimatedMinutes = max(1, (int) ($challengePool->estimated_minutes ?? 15));

        return UserDailyChallenge::query()->create([
            'user_id' => $userId,
            'challenge_id' => (string) $challengePool->getKey(),
            'challenge_date' => $challengeDate,
            // Keep assignment lean: source of truth for challenge content is the challenges collection.
            'estimated_minutes' => null,
            'tags' => null,
            'metadata' => $metadata,
            'is_completed' => false,
            'completed_at' => null,
            'expires_at' => now()->addMinutes($estimatedMinutes),
        ])->load('challenge');
    }

    private function findDailyChallengeForDate(string $userId, string $date): ?UserDailyChallenge
    {
        return UserDailyChallenge::query()
            ->with('challenge')
            ->where('user_id', $userId)
            ->where('challenge_date', $date)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    private function resolveBaseChallenge(UserDailyChallenge $challenge): ?Challenge
    {
        if ($challenge->relationLoaded('challenge')) {
            return $challenge->challenge;
        }

        if ((string) ($challenge->challenge_id ?? '') === '') {
            return null;
        }

        return $challenge->challenge()->first();
    }
    private function completeAssignment(UserDailyChallenge $challenge, string $today, ?string $reflection): JsonResponse
    {
        $reflectionText = $reflection !== null ? trim($reflection) : null;
        $metadata = (array) ($challenge->metadata ?? []);

        // 1. Cek jika sudah selesai
        if ((bool) $challenge->is_completed) {
            return response()->json([
                'status' => 'success',
                'message' => 'Daily challenge already completed.',
                'data' => $this->buildChallengeResponse($challenge, $today),
            ]);
        }

        // 2. Cek waktu kedaluwarsa
        if ($challenge->expires_at !== null && now()->greaterThan($challenge->expires_at)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Challenge completion time has expired.',
                'data' => $this->buildChallengeResponse($challenge, $today),
            ], 422);
        }

        if ($reflectionText !== null) {
            $metadata['reflection'] = $reflectionText;
            $metadata['reflection_submitted_at'] = now()->toIso8601String();
        }

        // 3. Simpan perubahan challenge ke database
        $challenge->forceFill([
            'is_completed' => true,
            'completed_at' => now(),
            'metadata'     => $metadata,
        ])->save();

        // 4. Update streak user
        $this->updateStreak($challenge->user_id);

        // 5. Cek dan proses achievement
        $achievementService = app(\App\Services\AchievementService::class);
        $newlyUnlocked = $achievementService->checkAndUnlock($challenge->user_id);

        // Kirim notifikasi jika ada achievement baru
        foreach ($newlyUnlocked as $ach) {
            \App\Services\NotificationService::notifyAchievement(
                $challenge->user_id,
                $ach['name'],
                $ach['icon']
            );
        }

        // 6. Cek mutual friend untuk notifikasi sosial
        $followingIds = \App\Models\UserFollow::where('follower_id', $challenge->user_id)
            ->pluck('following_id')
            ->map(fn($id) => (string) $id)
            ->all();

        $followerIds = \App\Models\UserFollow::where('following_id', $challenge->user_id)
            ->pluck('follower_id')
            ->map(fn($id) => (string) $id)
            ->all();

        $mutualIds = array_intersect($followingIds, $followerIds);

        // Kirim notifikasi ke mutual friend jika ada
        if (!empty($mutualIds)) {
            $challengeTitle = $challenge->fresh(['challenge'])->challenge?->title ?? 'Daily Challenge';

            \App\Services\NotificationService::notifyChallengeComplete(
                $challenge->user_id,
                $challengeTitle,
                array_values($mutualIds)
            );
        }

        // 7. Bangun response data dan return paling akhir
        $responseData = $this->buildChallengeResponse($challenge->fresh(['challenge']), $today);
        $responseData['newly_unlocked_achievements'] = $newlyUnlocked;

        return response()->json([
            'status'  => 'success',
            'message' => 'Daily challenge marked as completed.',
            'data'    => $responseData,
        ]);
    }

    private function updateStreak(string $userId): void
    {
        $user = \App\Models\User::find($userId);
        if ($user === null) return;

        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $lastDate = $user->last_completed_date?->toDateString();

        if ($lastDate === $today) {
            // Sudah complete hari ini, tidak perlu update
            return;
        }

        if ($lastDate === $yesterday) {
            // Lanjut streak
            $user->current_streak = ($user->current_streak ?? 0) + 1;
        } else {
            // Streak putus, mulai dari 1
            $user->current_streak = 1;
        }

        if ($user->current_streak > ($user->longest_streak ?? 0)) {
            $user->longest_streak = $user->current_streak;
        }

        $user->last_completed_date = $today;
        $user->save();
    }
}
