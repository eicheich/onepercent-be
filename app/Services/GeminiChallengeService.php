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

    public function generateDailyChallenge(array $tags, string $userName): array
    {
        $configuredModel = (string) config('services.gemini.model', 'gemini-1.5-flash');

        $tagList = implode(', ', $tags);

        $prompt = <<<PROMPT
    Create ONE lightweight daily challenge for a general user (not specifically office/corporate).
    User name: {$userName}
    Tags: {$tagList}

    Guidelines:
    - Keep it practical for everyday life.
    - Match challenge topic to tags.
    - Use simple language.
    - Title must be specific, minimum 3 words, and should not end with hanging words like "your" or "the".
    - Avoid corporate wording like stakeholder, meeting, KPI, sprint, report.

    Examples:
    - If tag includes uiux: "Design a simple login screen wireframe with email + password + one social login button."
    - If tag includes logic: "Solve 3 short logic puzzles and write your reasoning."
    - If tag includes education/lifestyle: "Read 10 pages of a book and summarize 3 key points."

    Return only plain text in this exact format:
    Title: <max 8 words>
    Description: <max 30 words, one actionable task>
    Duration: <estimated minutes, integer between 5 and 60>
PROMPT;

        [$text, $usedModel] = $this->requestGenerateContent($configuredModel, $prompt, 0.3, 120);

        if ($text === '') {
            throw new RuntimeException('Gemini returned empty challenge content.');
        }

        $title = $this->extractTitle($text, $tags);
        $content = $this->extractChallengeBody($text, $title, $tags);
        $estimatedMinutes = $this->extractEstimatedMinutes($text);

        return [
            'title' => $title,
            'content' => $content,
            'estimated_minutes' => $estimatedMinutes,
            'raw' => $text,
            'model' => $usedModel,
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
}
