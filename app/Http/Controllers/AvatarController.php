<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvatarController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:5120'],
        ]);

        $user = $request->user();

        // Convert ke base64 dan simpan
        $file     = $request->file('avatar');
        $mimeType = $file->getMimeType();
        $base64   = base64_encode(file_get_contents($file->getRealPath()));
        $dataUrl  = "data:{$mimeType};base64,{$base64}";

        $user->avatar = $dataUrl;
        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Avatar updated.',
            'data'    => [
                'avatar' => $user->avatar,
            ],
        ]);
    }
}
