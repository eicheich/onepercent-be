<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvatarController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|file|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $user = $request->user();
        $file = $request->file('avatar');

        // Resize
        $imageData = file_get_contents($file->getRealPath());
        $image = imagecreatefromstring($imageData);

        if ($image === false) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid image file.',
            ], 422);
        }

        $width = imagesx($image);
        $height = imagesy($image);

        // Resize ke max 200x200 kalau lebih besar
        if ($width > 200 || $height > 200) {
            $ratio = min(200 / $width, 200 / $height);
            $newW = (int) ($width  * $ratio);
            $newH = (int) ($height * $ratio);
            $resized = imagecreatetruecolor($newW, $newH);
            imagecopyresampled(
                $resized,
                $image,
                0,
                0,
                0,
                0,
                $newW,
                $newH,
                $width,
                $height
            );
            imagedestroy($image);
            $image = $resized;
        }

        // Convert ke JPEG base64
        ob_start();
        imagejpeg($image, null, 85);
        $jpegData = ob_get_clean();
        imagedestroy($image);

        $base64Avatar = 'data:image/jpeg;base64,' . base64_encode($jpegData);

        $user->avatar = $base64Avatar;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Avatar updated.',
            'data' => [
                'avatar' => $base64Avatar,
            ],
        ]);
    }
}
