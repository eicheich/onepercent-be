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
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\StreakController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\NotificationController;
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
        Route::post('/avatar', [AvatarController::class, 'upload']);
        Route::delete('/account', [AuthController::class, 'deleteAccount']);
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
        Route::post('/daily/regenerate', [DailyChallengeController::class, 'regenerate']);
        Route::post(
            '/pokes/read-all',
            [ChallengePokeController::class, 'markAllAsRead']
        );
    });

    // Leaderboard
    Route::prefix('leaderboard')->group(function () {
        Route::get('/global', [LeaderboardController::class, 'global']);
        Route::get('/friends', [LeaderboardController::class, 'friends']);
    });
    // Achievement
    Route::prefix('achievements')->group(function () {
        Route::get('/', [AchievementController::class, 'index']);
        Route::post('/check', [AchievementController::class, 'check']);
    });
    Route::prefix('notifications')->group(function () {
        Route::get('/',              [NotificationController::class, 'index']);
        Route::post('/read-all',     [NotificationController::class, 'markAllRead']);
        Route::post('/{id}/read',    [NotificationController::class, 'markRead']);
        Route::get('/unread-count',  [NotificationController::class, 'unreadCount']);
    });
});
