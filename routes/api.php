<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChallengeHistoryController;
use App\Http\Controllers\ChallengePokeController;
use App\Http\Controllers\DailyChallengeController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\PersonalizationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ProofController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\StreakController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/google', [GoogleAuthController::class, 'verifyIdToken']);
});

Route::middleware('auth:sanctum')->group(function () {

    // User
    Route::prefix('user')->group(function () {
        Route::get('/', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'updatePassword']);
        Route::get('/streak', [StreakController::class, 'myStreak']);
        Route::get('/search', [SearchController::class, 'searchUsers']);

        Route::post('/follow/{userId}', [FollowController::class, 'follow']);
        Route::delete('/follow/{userId}', [FollowController::class, 'unfollow']);
        Route::get('/followers', [FollowController::class, 'followers']);
        Route::get('/following', [FollowController::class, 'following']);
    });

    // Personalization
    Route::prefix('personalization')->group(function () {
        Route::get('/tags', [PersonalizationController::class, 'tags']);
        Route::post('/', [PersonalizationController::class, 'store']);
    });

    // Challenge
    Route::prefix('challenge')->group(function () {
        Route::get('/daily', [DailyChallengeController::class, 'today']);
        Route::post('/daily/generate', [DailyChallengeController::class, 'generate']);
        Route::post('/daily/complete', [DailyChallengeController::class, 'complete']);
        Route::post('/daily/submit-proof', [ProofController::class, 'submit']);
        Route::get('/history', [ChallengeHistoryController::class, 'index']);
        Route::post('/poke/{userId}', [ChallengePokeController::class, 'send']);
        Route::get('/pokes/inbox', [ChallengePokeController::class, 'inbox']);
        Route::post('/pokes/{pokeId}/read', [ChallengePokeController::class, 'markAsRead']);
        Route::get('/test-ai', [DailyChallengeController::class, 'testAi']);
    });

    // Leaderboard
    Route::prefix('leaderboard')->group(function () {
        Route::get('/global', [LeaderboardController::class, 'global']);
        Route::get('/friends', [LeaderboardController::class, 'friends']);
    });
});
