<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DailyChallengeController;
use App\Http\Controllers\ChallengePokeController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\PersonalizationController;
use App\Models\UserFollow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/user', function (Request $request) {
    $user = $request->user();
    $user->load('personalization');

    return response()->json([
        'status' => 'success',
        'data' => [
            'id' => (string) $user->getKey(),
            'name' => $user->name,
            'email' => $user->email,
            'birth_date' => $user->birth_date?->toDateString(),
            'gender' => $user->gender,
            'tags' => $user->personalization?->tags ?? [],
            'followers_count' => UserFollow::query()->where('following_id', (string) $user->getKey())->count(),
            'following_count' => UserFollow::query()->where('follower_id', (string) $user->getKey())->count(),
        ],
    ]);
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'updatePassword']);

    Route::get('/personalization/tags', [PersonalizationController::class, 'tags']);
    Route::post('/personalization', [PersonalizationController::class, 'store']);

    Route::post('/user/follow/{userId}', [FollowController::class, 'follow']);
    Route::delete('/user/follow/{userId}', [FollowController::class, 'unfollow']);
    Route::get('/user/followers', [FollowController::class, 'followers']);
    Route::get('/user/following', [FollowController::class, 'following']);

    Route::get('/challenge/daily', [DailyChallengeController::class, 'today']);
    Route::post('/challenge/daily/generate', [DailyChallengeController::class, 'generate']);
    Route::post('/challenge/daily/complete', [DailyChallengeController::class, 'complete']);
    Route::post('/challenge/poke/{userId}', [ChallengePokeController::class, 'send']);
    Route::get('/challenge/pokes/inbox', [ChallengePokeController::class, 'inbox']);
    Route::post('/challenge/pokes/{pokeId}/read', [ChallengePokeController::class, 'markAsRead']);
    Route::get('/challenge/test-ai', [DailyChallengeController::class, 'testAi']);
});
