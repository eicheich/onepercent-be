<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GoogleAuthController extends Controller
{
    public function verifyIdToken(Request $request): JsonResponse
    {
        $request->validate([
            'id_token' => ['required', 'string'],
        ]);

        $idToken = $request->id_token;

        // Verify token ke Google
        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken,
        ]);

        if (!$response->successful()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid Google token.',
            ], 401);
        }

        $payload = $response->json();

        // Validasi audience (pastikan token untuk app kita)
        $validClientIds = [
            env('GOOGLE_WEB_CLIENT_ID'),    // Web client ID
            env('GOOGLE_ANDROID_CLIENT_ID'), // Android client ID
        ];

        if (!in_array($payload['aud'] ?? '', $validClientIds)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Token audience mismatch.',
            ], 401);
        }

        $googleId = $payload['sub'];
        $email    = $payload['email'];
        $name     = $payload['name'] ?? 'User';
        $avatar   = $payload['picture'] ?? null;

        // Cari atau buat user
        $user = User::where('email', $email)->first();

        if ($user === null) {
            $user = User::create([
                'name'      => $name,
                'email'     => $email,
                'password'  => bcrypt(\Illuminate\Support\Str::random(32)),
                'google_id' => $googleId,
                'avatar'    => $avatar,
            ]);
        } else {
            if (empty($user->google_id)) {
                $user->google_id = $googleId;
                $user->avatar    = $avatar;
                $user->save();
            }
        }

        $token = $user->createToken('api-token')->plainTextToken;
        $user->load('personalization');
        $hasTags = !empty($user->personalization?->tags);

        return response()->json([
            'status'  => 'success',
            'message' => 'Google login success.',
            'data'    => [
                'id'       => (string) $user->getKey(),
                'name'     => $user->name,
                'email'    => $user->email,
                'avatar'   => $user->avatar,
                'token'    => $token,
                'has_tags' => $hasTags,
            ],
        ]);
    }
}
