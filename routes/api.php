<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DailyChallengeController;
use App\Http\Controllers\ChallengePokeController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\PersonalizationController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'updatePassword']);

        Route::post('/follow/{userId}', [FollowController::class, 'follow']);
        Route::delete('/follow/{userId}', [FollowController::class, 'unfollow']);
        Route::get('/followers', [FollowController::class, 'followers']);
        Route::get('/following', [FollowController::class, 'following']);
    });

    Route::prefix('personalization')->group(function () {
        Route::get('/tags', [PersonalizationController::class, 'tags']);
        Route::post('/', [PersonalizationController::class, 'store']);
    });

    Route::prefix('challenge')->group(function () {
        Route::get('/daily', [DailyChallengeController::class, 'today']);
        Route::post('/daily/generate', [DailyChallengeController::class, 'generate']);
        Route::post('/daily/complete', [DailyChallengeController::class, 'complete']);
        Route::post('/poke/{userId}', [ChallengePokeController::class, 'send']);
        Route::get('/pokes/inbox', [ChallengePokeController::class, 'inbox']);
        Route::post('/pokes/{pokeId}/read', [ChallengePokeController::class, 'markAsRead']);
        Route::get('/test-ai', [DailyChallengeController::class, 'testAi']);
    });
});