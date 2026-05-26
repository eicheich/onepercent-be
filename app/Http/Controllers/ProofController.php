<?php

namespace App\Http\Controllers;

use App\Models\UserDailyChallenge;
use App\Services\GeminiChallengeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\AchievementService;
use Throwable;

class ProofController extends Controller
{
    public function __construct(private GeminiChallengeService $gemini) {}

    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'file'                    => 'required|file|max:51200',
            'user_daily_challenge_id' => 'required|string',
        ]);

        $userId    = (string) $request->user()->getKey();
        $challenge = UserDailyChallenge::with('challenge')
            ->where('_id', $request->user_daily_challenge_id)
            ->where('user_id', $userId)
            ->first();

        if (!$challenge) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Challenge not found.',
            ], 404);
        }

        $file     = $request->file('file');
        $mimeType = $file->getMimeType() ?? 'application/octet-stream';
        $base64   = base64_encode(file_get_contents($file->getRealPath()));

        // Coba score via Gemini
        $score       = 0;
        $feedback    = 'Proof submitted successfully!';
        $suggestions = '';
        $useGemini   = false;

        try {
            $gemini  = app(GeminiChallengeService::class);
            $result  = $gemini->scoreProof(
                $base64,
                $mimeType,
                $challenge->challenge?->title    ?? 'Challenge',
                $challenge->challenge?->content  ?? ''
            );
            $score       = $result['score'];
            $feedback    = $result['feedback'];
            $suggestions = $result['suggestions'] ?? '';
            $useGemini   = true;
        } catch (Throwable $e) {
            // Gemini tidak aktif → simpan tanpa score
            \Illuminate\Support\Facades\Log::warning(
                'Gemini scoring failed: ' . $e->getMessage()
            );
            $score    = 0;
            $feedback = 'Proof submitted! AI scoring unavailable right now.';
        }

        // Update metadata challenge
        $metadata = (array) ($challenge->metadata ?? []);
        $metadata['proof_score']        = $score;
        $metadata['proof_feedback']     = $feedback;
        $metadata['proof_suggestions']  = $suggestions;
        $metadata['proof_submitted_at'] = now()->toIso8601String();
        $metadata['proof_scored']       = $useGemini;
        $challenge->forceFill(['metadata' => $metadata])->save();

        // Check achievements
        app(AchievementService::class)->checkAndUnlock($userId);

        return response()->json([
            'status'  => 'success',
            'message' => 'Proof submitted successfully.',
            'data'    => [
                'score'       => $score,
                'feedback'    => $feedback,
                'suggestions' => $suggestions,
                'scored'      => $useGemini,
            ],
        ]);
    }
}
