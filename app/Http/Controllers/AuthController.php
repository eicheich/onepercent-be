<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserFollow;
use App\Models\UserPersonalization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private const MAX_TAGS = 5;

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $userId = (string) $user->getKey();
        $user->load('personalization');

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $userId,
                'name' => $user->name,
                'email' => $user->email,
                'birth_date' => $user->birth_date?->toDateString(),
                'gender' => $user->gender,
                'tags' => $user->personalization?->tags ?? [],
                'followers_count' => UserFollow::query()->where('following_id', $userId)->count(),
                'following_count' => UserFollow::query()->where('follower_id', $userId)->count(),
            ],
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
        ]);

        $normalizedEmail = strtolower((string) $validated['email']);

        if (User::where('email', $normalizedEmail)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email already registered.',
            ], 422);
        }

        $payload = [
            'name' => $validated['name'],
            'email' => $normalizedEmail,
            'password' => $validated['password'],
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'] ?? null,
        ];

        $user = User::create($payload);
        $token = $user->createToken('api-token')->plainTextToken;
        $user->load('personalization');

        return response()->json([
            'status' => 'success',
            'message' => 'Register success.',
            'data' => [
                'id' => (string) $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
                'birth_date' => $user->birth_date?->toDateString(),
                'gender' => $user->gender,
                'tags' => $user->personalization?->tags ?? [],
                'token' => $token,
                'next_step' => [
                    'action' => 'personalization',
                    'endpoint' => '/api/personalization',
                    'method' => 'POST',
                    'max_tags' => 5,
                ],
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email:rfc,dns'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials.',
            ], 401);
        }
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login success.',
            'data' => [
                'id' => (string) $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
                'birth_date' => $user->birth_date?->toDateString(),
                'gender' => $user->gender,
                'token' => $token,
            ],
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'birth_date' => ['sometimes', 'nullable', 'date', 'before_or_equal:today'],
            'gender' => ['sometimes', 'nullable', 'string', 'in:male,female,other'],
            'tags' => ['sometimes', 'array', 'min:1', 'max:' . self::MAX_TAGS],
            'tags.*' => ['string', 'min:2', 'max:30'],
        ]);

        if (
            ! array_key_exists('name', $validated)
            && ! array_key_exists('birth_date', $validated)
            && ! array_key_exists('gender', $validated)
            && ! array_key_exists('tags', $validated)
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'No profile fields provided for update.',
            ], 422);
        }

        if (array_key_exists('name', $validated)) {
            $user->name = $validated['name'];
        }

        if (array_key_exists('birth_date', $validated)) {
            $user->birth_date = $validated['birth_date'];
        }

        if (array_key_exists('gender', $validated)) {
            $user->gender = $validated['gender'];
        }

        $user->save();

        if (array_key_exists('tags', $validated)) {
            $normalizedTags = array_values(array_unique(array_map(
                static fn(string $tag): string => strtolower(trim($tag)),
                $validated['tags']
            )));

            UserPersonalization::query()->updateOrCreate(
                ['user_id' => (string) $user->getKey()],
                ['tags' => $normalizedTags]
            );
        }

        $user->load('personalization');

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully.',
            'data' => [
                'id' => (string) $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
                'birth_date' => $user->birth_date?->toDateString(),
                'gender' => $user->gender,
                'tags' => $user->personalization?->tags ?? [],
            ],
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (! Hash::check((string) $validated['current_password'], (string) $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $user->password = $validated['password'];
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully.',
        ]);
    }
}
