<?php

namespace App\Http\Controllers;

use App\Models\UserDailyChallenge;
use App\Services\GeminiChallengeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ProofController extends Controller
{
    public function __construct(private GeminiChallengeService $gemini) {}

    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'user_daily_challenge_id' => ['required', 'string'],
            'file' => ['required', 'file', 'max:51200'], // max 50MB
        ]);

        $userId = (string) $request->user()->getKey();

        $challenge = UserDailyChallenge::query()
            ->with('challenge')
            ->where('_id', $request->user_daily_challenge_id)
            ->where('user_id', $userId)
            ->first();

        if ($challenge === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Challenge not found.',
            ], 404);
        }

        if (!(bool) $challenge->is_completed) {
            return response()->json([
                'status' => 'error',
                'message' => 'Complete the challenge first before submitting proof.',
            ], 422);
        }

        // Read file content
        $file = $request->file('file');
        $mimeType = $file->getMimeType();
        $fileContent = file_get_contents($file->getRealPath());
        $base64 = base64_encode($fileContent);

        $challengeTitle = $challenge->challenge?->title ?? 'Daily Challenge';
        $challengeDesc  = $challenge->challenge?->content ?? '';

        try {
            $score = $this->gemini->scoreProof(
                $base64,
                $mimeType,
                $challengeTitle,
                $challengeDesc
            );
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'AI scoring failed: ' . $e->getMessage(),
            ], 502);
        }

        // Save score to metadata
        $metadata = (array) ($challenge->metadata ?? []);
        $metadata['proof_score']       = $score['score'];
        $metadata['proof_feedback']    = $score['feedback'];
        $metadata['proof_submitted_at'] = now()->toIso8601String();
        $challenge->forceFill(['metadata' => $metadata])->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Proof submitted and scored.',
            'data' => [
                'score'    => $score['score'],
                'feedback' => $score['feedback'],
                'max_score' => 100,
            ],
        ]);
    }
}
