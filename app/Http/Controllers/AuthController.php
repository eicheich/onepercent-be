<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if (User::where('email', $validated['email'])->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email already registered.',
            ], 422);
        }

        $user = User::create($validated);
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Register success.',
            'data' => [
                'id' => (string) $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
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
                'token' => $token,
            ],
        ]);
    }
}
