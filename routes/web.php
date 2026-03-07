<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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
