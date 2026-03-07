<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\UserDailyChallenge;
use App\Models\UserPersonalization;
use App\Services\GeminiChallengeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        $challenge = UserDailyChallenge::query()
            ->with('challenge')
            ->where('user_id', $userId)
            ->where('challenge_date', $today)
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => $challenge ? $this->buildChallengeResponse($challenge, $today) : null,
        ]);
    }

    public function generate(Request $request): JsonResponse
    {
        $user = $request->user();
        $userId = (string) $user->getKey();
        $today = now()->toDateString();

        $existing = UserDailyChallenge::query()
            ->with('challenge')
            ->where('user_id', $userId)
            ->where('challenge_date', $today)
            ->first();

        if ($existing) {
            return response()->json([
                'status' => 'success',
                'message' => 'Today challenge already generated.',
                'data' => $this->buildChallengeResponse($existing, $today),
            ]);
        }

        $personalization = UserPersonalization::query()
            ->where('user_id', $userId)
            ->first();

        $tags = $personalization?->tags ?? [];

        if (count($tags) === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please complete personalization tags first.',
            ], 422);
        }

        $seenChallengeIds = $this->getSeenChallengeIds($userId);
        $reusableChallenge = $this->findReusableChallenge($tags, $seenChallengeIds);

        if ($reusableChallenge !== null) {
            $challenge = $this->createDailyAssignment($userId, $today, $reusableChallenge, $tags, [
                'provider' => 'reuse_pool',
                'source_challenge_id' => (string) $reusableChallenge->getKey(),
                'challenge_signature' => $reusableChallenge->signature,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Daily challenge reused from similar tags pool.',
                'data' => $this->buildChallengeResponse($challenge, $today),
            ], 201);
        }

        try {
            $generated = $this->geminiChallengeService->generateDailyChallenge($tags, (string) ($user->name ?? 'User'));
        } catch (Throwable $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate daily challenge.',
                'error' => $exception->getMessage(),
            ], 502);
        }

        $signature = $this->buildChallengeSignature($generated['title'], $generated['content']);

        $challengePool = Challenge::query()->firstOrCreate(
            ['signature' => $signature],
            [
                'title' => $generated['title'],
                'content' => $generated['content'],
                'estimated_minutes' => (int) ($generated['estimated_minutes'] ?? 15),
                'tags' => $tags,
                'metadata' => [
                    'provider' => 'gemini',
                    'model' => $generated['model'],
                    'raw' => $generated['raw'],
                ],
            ]
        );

        if (in_array((string) $challengePool->getKey(), $seenChallengeIds, true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'A similar challenge already exists for this user. Try generate again later.',
            ], 409);
        }

        $challenge = $this->createDailyAssignment($userId, $today, $challengePool, $tags, [
            'provider' => 'gemini',
            'model' => $generated['model'],
            'challenge_signature' => $signature,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Daily challenge generated.',
            'data' => $this->buildChallengeResponse($challenge, $today),
        ], 201);
    }

    public function complete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reflection' => ['nullable', 'string', 'min:5', 'max:1200'],
        ]);

        $userId = (string) $request->user()->getKey();
        $today = now()->toDateString();

        $challenge = UserDailyChallenge::query()
            ->with('challenge')
            ->where('user_id', $userId)
            ->where('challenge_date', $today)
            ->first();

        if ($challenge === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'No daily challenge found for today.',
            ], 404);
        }

        return $this->completeAssignment($challenge, $today, $validated['reflection'] ?? null);
    }

    private function completeAssignment(UserDailyChallenge $challenge, string $today, ?string $reflection): JsonResponse
    {
        $reflectionText = $reflection !== null ? trim($reflection) : null;
        $metadata = (array) ($challenge->metadata ?? []);

        if ((bool) $challenge->is_completed) {
            return response()->json([
                'status' => 'success',
                'message' => 'Daily challenge already completed.',
                'data' => $this->buildChallengeResponse($challenge, $today),
            ]);
        }

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

        $challenge->forceFill([
            'is_completed' => true,
            'completed_at' => now(),
            'metadata' => $metadata,
        ])->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Daily challenge marked as completed.',
            'data' => $this->buildChallengeResponse($challenge->fresh(['challenge']), $today),
        ]);
    }

    private function buildChallengeResponse(UserDailyChallenge $challenge, string $fallbackDate): array
    {
        $expiresAt = $challenge->expires_at;

        return [
            'id' => (string) $challenge->getKey(),
            'challenge_id' => (string) ($challenge->challenge_id ?? ''),
            'date' => $challenge->challenge_date?->toDateString() ?? $fallbackDate,
            'title' => $this->resolveTitle($challenge),
            'description' => $this->resolveDescription($challenge),
            'estimated_minutes' => (int) ($challenge->estimated_minutes ?? 15),
            'tags' => $challenge->tags ?? [],
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

    private function createDailyAssignment(string $userId, string $challengeDate, Challenge $challengePool, array $userTags, array $metadata): UserDailyChallenge
    {
        $estimatedMinutes = max(1, (int) ($challengePool->estimated_minutes ?? 15));

        return UserDailyChallenge::query()->create([
            'user_id' => $userId,
            'challenge_id' => (string) $challengePool->getKey(),
            'challenge_date' => $challengeDate,
            'title' => $challengePool->title,
            'content' => $challengePool->content,
            'estimated_minutes' => $estimatedMinutes,
            'tags' => $userTags,
            'metadata' => $metadata,
            'is_completed' => false,
            'completed_at' => null,
            'expires_at' => now()->addMinutes($estimatedMinutes),
        ])->load('challenge');
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
}
