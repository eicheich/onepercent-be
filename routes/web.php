<?php

use App\Models\User;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ProfileWebController;
use App\Http\Controllers\Web\FriendsWebController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('landing'))->name('landing');

Route::middleware('guest.web')->group(function () {
    Route::get('/login', [AuthWebController::class, 'showLogin'])->name('web.login');
    Route::post('/login', [AuthWebController::class, 'login'])->name('web.login.post');
    Route::get('/register', [AuthWebController::class, 'showRegister'])->name('web.register');
    Route::post('/register', [AuthWebController::class, 'register'])->name('web.register.post');
    // Google OAuth web
    Route::get(
        '/auth/google',
        [AuthWebController::class, 'redirectToGoogle']
    )->name('web.google');
    Route::get(
        '/auth/google/callback',
        [AuthWebController::class, 'handleGoogleCallback']
    )->name('web.google.callback');
});
Route::get('/notifications', [DashboardController::class, 'notifications'])
    ->name('web.notifications');
Route::post('/notifications/read-all', [DashboardController::class, 'readAllNotifications'])
    ->name('web.notifications.read-all');

// ─── User routes ──────────────────────────────────
Route::middleware('auth.web')->prefix('app')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('web.dashboard');
    Route::get('/challenge', [DashboardController::class, 'challenge'])->name('web.challenge');
    Route::get('/leaderboard', [DashboardController::class, 'leaderboard'])->name('web.leaderboard');
    Route::get('/profile', [ProfileWebController::class, 'index'])->name('web.profile');
    Route::post('/profile', [ProfileWebController::class, 'update'])->name('web.profile.update');
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('web.logout');
    Route::post('/account/delete', [ProfileWebController::class, 'deleteAccount'])->name('web.account.delete');
    Route::post('/account/change-password', [ProfileWebController::class, 'changePassword'])->name('web.account.password');
    Route::get('/account/settings', [ProfileWebController::class, 'settings'])->name('web.settings');
    Route::get('/tags/edit', [DashboardController::class, 'showTags'])->name('web.tags.edit');
    Route::post('/tags/edit', [DashboardController::class, 'saveTags'])->name('web.tags.update');
    Route::post('/challenge/poke/{userId}', [DashboardController::class, 'pokeFriend'])->name('web.challenge.poke');
    Route::get('/tags', [DashboardController::class, 'showTags'])->name('web.tags');
    Route::post('/tags', [DashboardController::class, 'saveTags'])->name('web.tags.save');
    Route::post('/challenge/upload-proof', [DashboardController::class, 'uploadProof'])->name('web.challenge.upload');
    Route::get('/friends', [FriendsWebController::class, 'index'])->name('web.friends');
    Route::get('/friends/search', [FriendsWebController::class, 'search'])->name('web.friends.search');
    Route::post('/friends/follow/{id}', [FriendsWebController::class, 'follow'])->name('web.friends.follow');
    Route::post('/friends/unfollow/{id}', [FriendsWebController::class, 'unfollow'])->name('web.friends.unfollow');
    Route::get('/about', fn() => view('web.about'))->name('web.about');
    Route::get('/terms', fn() => view('web.terms'))->name('web.terms');
    Route::get('/admin/users/{id}', [AdminController::class, 'userDetail'])->name('admin.users.show');
    Route::post('/admin/challenge/generate/{userId}', [AdminController::class, 'generateForUser'])->name('admin.challenge.generate');
    Route::get('/admin/achievements', [AdminController::class, 'achievements'])->name('admin.achievements');
});

// ─── Admin routes ─────────────────────────────────
Route::middleware('auth.admin')->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/challenges', [AdminController::class, 'challenges'])->name('admin.challenges');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
});

Route::get('/test-users', function () {
    try {
        // Mengambil semua data dari collection 'users'
        $users = User::all();

        if ($users->isEmpty()) {
            return response()->json([
                'status' => 'Koneksi Berhasil!',
                'pesan' => 'Tapi collection users kamu masih kosong nih.'
            ]);
        }

        return response()->json([
            'status' => 'Koneksi Sukses!',
            'jumlah_user' => $users->count(),
            'data_users' => $users
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'Waduh, error!',
            'pesan' => $e->getMessage()
        ], 500);
    }
});

Route::get('/test-mongo', function () {
    try {
        DB::connection('mongodb')->getDatabase()->command(['ping' => 1]);

        return response()->json([
            'status' => 'ok',
            'message' => 'MongoDB connected',
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'MongoDB not connected',
            'error' => $e->getMessage(),
        ], 500);
    }
});
