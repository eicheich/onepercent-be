<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class GeminiChallengeService
{
    private ?array $resolvedAvailableModels = null;

    public function testConnection(): array
    {
        $configuredModel = (string) config('services.gemini.model', 'gemini-1.5-flash');

        [$text, $usedModel] = $this->requestGenerateContent($configuredModel, 'Reply with exactly: OK', 0.0, 8);

        if ($text === '') {
            throw new RuntimeException('Gemini test call returned empty response.');
        }

        return [
            'model' => $usedModel,
            'response' => $text,
        ];
    }

    public function generateDailyChallenge(array $tags): array
    {
        $apiKey  = (string) config('services.gemini.api_key');
        if (empty($apiKey)) {
            throw new RuntimeException(
                'GEMINI_API_KEY is not configured. Check your .env file.'
            );
        }
        $model   = (string) config('services.gemini.model', 'gemini-1.5-flash');
        $baseUrl = rtrim((string) config(
            'services.gemini.base_url',
            'https://generativelanguage.googleapis.com/v1beta'
        ), '/');

        $tagList = implode(', ', $tags);

        $prompt = <<<PROMPT
You are a daily self-improvement challenge generator.

Generate ONE daily challenge for a user interested in: {$tagList}

Requirements:
- MUST relate to: {$tagList}
- Completable in 10-30 minutes
- Specific and actionable

Rules:
- The challenge MUST be directly related to one or more of these topics: {$tagList}
- It must be completable in 10-30 minutes
- Be specific and actionable, not vague
- Title: max 8 words, no generic words like "daily challenge"

Respond ONLY with valid JSON, no markdown, no explanation:
{
  "title": "specific challenge title here",
  "content": "detailed description of what to do, 2-3 sentences",
  "estimated_minutes": 15,
  "tags": ["{$tags[0]}"]
}
PROMPT;

        $response = Http::timeout(30)
            ->post("{$baseUrl}/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'temperature'     => 0.8,
                    'maxOutputTokens' => 300,
                ],
            ]);

        if (!$response->successful()) {
            throw new RuntimeException(
                'Gemini API error: ' . $response->status() . ' ' . $response->body()
            );
        }

        $text = trim((string) data_get(
            $response->json(),
            'candidates.0.content.parts.0.text',
            ''
        ));

        // Strip markdown code blocks kalau ada
        $text = preg_replace('/```json\s*|\s*```/', '', $text);
        $text = trim($text);

        $data = json_decode($text, true);

        if (!$data || empty($data['title']) || empty($data['content'])) {
            throw new RuntimeException('Invalid Gemini response: ' . $text);
        }

        return [
            'title'             => (string) $data['title'],
            'content'           => (string) $data['content'],
            'estimated_minutes' => (int) ($data['estimated_minutes'] ?? 15),
            'tags'              => (array) ($data['tags'] ?? $tags),
        ];
    }

    private function requestGenerateContent(string $model, string $prompt, float $temperature, int $maxOutputTokens)
    {
        $apiKey = (string) config('services.gemini.api_key');
        $configuredBaseUrl = rtrim((string) config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');

        if ($apiKey === '') {
            throw new RuntimeException('GEMINI_API_KEY is not configured.');
        }

        $modelsToTry = $this->buildModelCandidates($model);
        $baseUrlsToTry = $this->buildBaseUrlCandidates($configuredBaseUrl);
        $availableModels = $this->discoverAvailableModels($apiKey, $baseUrlsToTry);

        if (count($availableModels) > 0) {
            $modelsToTry = array_values(array_unique(array_merge(
                array_values(array_intersect($availableModels, $modelsToTry)),
                $availableModels,
                $modelsToTry
            )));
        }

        $lastResponse = null;
        $attemptedTargets = [];

        foreach ($baseUrlsToTry as $baseUrl) {
            foreach ($modelsToTry as $candidate) {
                $attemptedTargets[] = "{$baseUrl}/models/{$candidate}";

                $response = Http::withQueryParameters(['key' => $apiKey])
                    ->timeout(25)
                    ->retry(2, 300)
                    ->post("{$baseUrl}/models/{$candidate}:generateContent", [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => $temperature,
                            'maxOutputTokens' => $maxOutputTokens,
                        ],
                    ]);

                if ($response->successful()) {
                    $text = trim((string) data_get($response->json(), 'candidates.0.content.parts.0.text', ''));

                    if ($text !== '') {
                        return [$text, $candidate];
                    }

                    $lastResponse = $response;
                    continue;
                }

                $lastResponse = $response;

                if ($response->status() !== 404) {
                    $snippet = Str::limit($response->body(), 220);
                    throw new RuntimeException("Gemini request failed ({$response->status()}): {$snippet}");
                }
            }
        }

        [$openAiText, $openAiModel] = $this->requestOpenAiCompatible(
            $baseUrlsToTry,
            $modelsToTry,
            $apiKey,
            $prompt,
            $temperature,
            $maxOutputTokens
        );

        if ($openAiText !== null) {
            return [$openAiText, $openAiModel];
        }

        $snippet = $lastResponse ? Str::limit($lastResponse->body(), 220) : 'No response body.';
        throw new RuntimeException('Gemini model not found for this API/key. Tried targets: ' . implode(', ', $attemptedTargets) . '. Last response: ' . $snippet);
    }

    private function requestOpenAiCompatible(array $baseUrlsToTry, array $modelsToTry, string $apiKey, string $prompt, float $temperature, int $maxOutputTokens): array
    {
        foreach ($baseUrlsToTry as $baseUrl) {
            $openAiUrl = preg_replace('#/v1beta$|/v1$#', '', $baseUrl) . '/v1beta/openai/chat/completions';

            foreach ($modelsToTry as $candidate) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                    ->timeout(25)
                    ->retry(1, 300)
                    ->post($openAiUrl, [
                        'model' => $candidate,
                        'messages' => [
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'temperature' => $temperature,
                        'max_tokens' => $maxOutputTokens,
                    ]);

                if (! $response->successful()) {
                    continue;
                }

                $text = trim((string) data_get($response->json(), 'choices.0.message.content', ''));

                if ($text !== '') {
                    return [$text, $candidate];
                }
            }
        }

        return [null, null];
    }

    private function buildBaseUrlCandidates(string $baseUrl): array
    {
        $baseUrl = rtrim($baseUrl, '/');
        $candidates = [$baseUrl];

        if (Str::endsWith($baseUrl, '/v1beta')) {
            $candidates[] = (string) Str::replaceLast('/v1beta', '/v1', $baseUrl);
        } elseif (Str::endsWith($baseUrl, '/v1')) {
            $candidates[] = (string) Str::replaceLast('/v1', '/v1beta', $baseUrl);
        } else {
            $candidates[] = $baseUrl . '/v1beta';
            $candidates[] = $baseUrl . '/v1';
        }

        return array_values(array_unique($candidates));
    }

    private function discoverAvailableModels(string $apiKey, array $baseUrlsToTry): array
    {
        if ($this->resolvedAvailableModels !== null) {
            return $this->resolvedAvailableModels;
        }

        foreach ($baseUrlsToTry as $baseUrl) {
            $response = Http::withQueryParameters(['key' => $apiKey])
                ->timeout(20)
                ->retry(1, 250)
                ->get("{$baseUrl}/models");

            if (! $response->successful()) {
                continue;
            }

            $items = data_get($response->json(), 'models', []);

            $models = collect($items)
                ->filter(function ($item) {
                    $methods = data_get($item, 'supportedGenerationMethods', []);

                    return in_array('generateContent', $methods, true);
                })
                ->map(fn($item) => $this->normalizeModelName((string) data_get($item, 'name', '')))
                ->filter(fn($name) => $name !== '' && Str::contains($name, 'gemini'))
                ->values()
                ->all();

            if (count($models) > 0) {
                $this->resolvedAvailableModels = $models;

                return $models;
            }
        }

        $this->resolvedAvailableModels = [];

        return [];
    }

    private function buildModelCandidates(string $model): array
    {
        $normalized = $this->normalizeModelName($model);
        $candidates = [$normalized];

        if (in_array($normalized, ['gemini-1.0-flash', 'gemini-1.0-pro', 'gemini-pro'], true)) {
            $candidates[] = 'gemini-1.5-flash';
            $candidates[] = 'gemini-1.5-flash-latest';
            $candidates[] = 'gemini-2.0-flash';
        }

        if ($normalized === 'gemini-1.5-flash') {
            $candidates[] = 'gemini-1.5-flash-latest';
            $candidates[] = 'gemini-2.0-flash';
        }

        if ($normalized === 'gemini-2.0-flash') {
            $candidates[] = 'gemini-1.5-flash-latest';
        }

        return array_values(array_unique($candidates));
    }

    private function normalizeModelName(string $model): string
    {
        $model = trim($model);

        if (Str::startsWith($model, 'models/')) {
            return (string) Str::after($model, 'models/');
        }

        return $model;
    }

    private function extractTitle(string $text, array $tags): string
    {
        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];

        foreach ($lines as $line) {
            $line = trim($line);

            if (Str::startsWith(Str::lower($line), 'title:')) {
                return $this->sanitizeTitle(trim(Str::after($line, ':')), $tags);
            }
        }

        return $this->sanitizeTitle(Str::limit(trim($lines[0] ?? ''), 180, ''), $tags);
    }

    private function extractChallengeBody(string $text, string $title, array $tags): string
    {
        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
        $cleaned = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            if (Str::startsWith(Str::lower($line), 'title:')) {
                continue;
            }

            if (Str::startsWith(Str::lower($line), 'challenge:')) {
                $cleaned[] = trim(Str::after($line, ':'));
                continue;
            }

            if (Str::startsWith(Str::lower($line), 'description:')) {
                $cleaned[] = trim(Str::after($line, ':'));
                continue;
            }

            if (Str::startsWith(Str::lower($line), 'duration:')) {
                continue;
            }

            $cleaned[] = $line;
        }

        $body = trim(implode(' ', $cleaned));

        if ($body !== '') {
            return $body;
        }

        $focus = $this->inferFocusFromText($title, $tags);

        return "Spend 15 minutes doing one practical {$focus} task, then write 3 key takeaways.";
    }

    private function sanitizeTitle(string $title, array $tags): string
    {
        $title = trim($title, " \t\n\r\0\x0B\"'");

        $genericTitles = [
            'review',
            'daily challenge',
            'challenge',
            'task',
            'today challenge',
        ];

        if ($title === '' || in_array(Str::lower($title), $genericTitles, true) || $this->isWeakTitle($title, $tags)) {
            return $this->buildFallbackTitle($tags);
        }

        return Str::limit($title, 80, '');
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

        // If title has no relation to any user tag, force a safer fallback title.
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

    private function extractEstimatedMinutes(string $text): int
    {
        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];

        foreach ($lines as $line) {
            $line = trim($line);

            if (! Str::startsWith(Str::lower($line), 'duration:')) {
                continue;
            }

            $raw = trim(Str::after($line, ':'));

            if (preg_match('/\d+/', $raw, $matches) === 1) {
                $minutes = (int) $matches[0];

                return max(5, min(60, $minutes));
            }
        }

        return 15;
    }
    public function scoreProof(
        string $base64Data,
        string $mimeType,
        string $challengeTitle,
        string $challengeContent
    ): array {
        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model', 'gemini-1.5-flash');
        $url = config('services.gemini.base_url')
            . "/models/{$model}:generateContent?key={$apiKey}";

        $prompt = "You are evaluating proof of work for a daily challenge.

Challenge: {$challengeTitle}
Description: {$challengeContent}

Please evaluate the uploaded file/image as proof of completing this challenge.
Respond ONLY with valid JSON, no markdown, no explanation:
{
  \"score\": <integer 0-100>,
  \"feedback\": \"<constructive feedback in 1-2 sentences>\",
  \"suggestions\": \"<1 specific suggestion to improve next time>\"
}";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data'      => $base64Data,
                            ],
                        ],
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature'     => 0.3,
                'maxOutputTokens' => 300,
            ],
        ];

        $response = Http::timeout(30)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $payload);

        if (!$response->successful()) {
            throw new RuntimeException(
                'Gemini API error: ' . $response->status()
                    . ' ' . $response->body()
            );
        }

        $data = $response->json();
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        // Clean JSON dari markdown kalau ada
        $text = preg_replace('/```json\s*|\s*```/', '', trim($text));

        $result = json_decode($text, true);

        if (!$result || !isset($result['score'])) {
            // Fallback kalau parse gagal
            return [
                'score'       => 70,
                'feedback'    => 'Good effort! Keep it up.',
                'suggestions' => 'Try to be more detailed next time.',
            ];
        }

        return [
            'score'       => (int) $result['score'],
            'feedback'    => $result['feedback'] ?? 'Good job!',
            'suggestions' => $result['suggestions'] ?? '',
        ];
    }

    private function parseScoreResponse(string $text): array
    {
        $score    = 70; // default
        $feedback = 'Good effort on completing the challenge!';

        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (str_starts_with(strtolower($line), 'score:')) {
                $raw = trim(str_replace('Score:', '', $line));
                if (preg_match('/\d+/', $raw, $m)) {
                    $score = max(0, min(100, (int) $m[0]));
                }
            }
            if (str_starts_with(strtolower($line), 'feedback:')) {
                $feedback = trim(str_replace('Feedback:', '', $line));
            }
        }

        return compact('score', 'feedback');
    }
}
