<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DailyChallengeController;
use App\Http\Controllers\PersonalizationController;
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
            'tags' => $user->personalization?->tags ?? [],
        ],
    ]);
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/personalization/tags', [PersonalizationController::class, 'tags']);
    Route::post('/personalization', [PersonalizationController::class, 'store']);

    Route::get('/challenge/daily', [DailyChallengeController::class, 'today']);
    Route::post('/challenge/daily/generate', [DailyChallengeController::class, 'generate']);
    Route::post('/challenge/daily/complete', [DailyChallengeController::class, 'complete']);
    Route::get('/challenge/test-ai', [DailyChallengeController::class, 'testAi']);
});
