<?php

namespace App\Http\Controllers;

use App\Models\UserPersonalization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonalizationController extends Controller
{
    private const MAX_TAGS = 5;

    private const AVAILABLE_TAGS = [
        'technology',
        'business',
        'sports',
        'health',
        'finance',
        'education',
        'entertainment',
        'lifestyle',
    ];

    public function tags(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'available_tags' => self::AVAILABLE_TAGS,
                'max_tags' => self::MAX_TAGS,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'selected_tags' => ['sometimes', 'array', 'max:' . self::MAX_TAGS],
            'selected_tags.*' => ['string', 'in:' . implode(',', self::AVAILABLE_TAGS)],
            'custom_tags' => ['sometimes', 'array', 'max:' . self::MAX_TAGS],
            'custom_tags.*' => ['string', 'min:2', 'max:30'],
        ]);

        $selectedTags = $validated['selected_tags'] ?? [];
        $customTags = $validated['custom_tags'] ?? [];

        $mergedTags = array_values(array_unique(array_map(
            static fn(string $tag): string => strtolower(trim($tag)),
            array_merge($selectedTags, $customTags)
        )));

        if (count($mergedTags) === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'At least one tag is required.',
            ], 422);
        }

        if (count($mergedTags) > self::MAX_TAGS) {
            return response()->json([
                'status' => 'error',
                'message' => 'Maximum 5 tags are allowed (combined selected + custom).',
            ], 422);
        }

        $personalization = UserPersonalization::query()->updateOrCreate(
            ['user_id' => (string) $request->user()->getKey()],
            ['tags' => $mergedTags]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Personalization saved.',
            'data' => [
                'user_id' => $personalization->user_id,
                'tags' => $personalization->tags,
            ],
        ]);
    }
}
