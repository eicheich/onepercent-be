<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = (string) $request->user()->getKey();

        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(fn($n) => [
                'id'         => (string) $n->getKey(),
                'type'       => $n->type,
                'title'      => $n->title,
                'body'       => $n->body,
                'icon'       => $n->icon,
                'data'       => $n->data ?? [],
                'read_at'    => $n->read_at?->toIso8601String(),
                'created_at' => $n->created_at?->toIso8601String(),
            ]);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'unread_count'  => NotificationService::unreadCount($userId),
                'notifications' => $notifications,
            ],
        ]);
    }

    public function markRead(Request $request, string $id): JsonResponse
    {
        $userId = (string) $request->user()->getKey();

        Notification::where('_id', $id)
            ->where('user_id', $userId)
            ->update(['read_at' => now()]);

        return response()->json(['status' => 'success']);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $userId = (string) $request->user()->getKey();

        Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['status' => 'success']);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $userId = (string) $request->user()->getKey();
        return response()->json([
            'status' => 'success',
            'data'   => ['count' => NotificationService::unreadCount($userId)],
        ]);
    }
}
