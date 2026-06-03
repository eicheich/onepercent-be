<?php
// Debug script untuk melihat notifikasi di database

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\Notification;
use App\Models\User;
use App\Models\UserFollow;

echo "=== Debugging Notifications ===\n\n";

// 1. Cek total notifications
$totalNotif = Notification::count();
echo "Total Notifications: " . $totalNotif . "\n\n";

// 2. Cek latest notifications
$latest = Notification::orderBy('created_at', 'desc')->limit(5)->get();
echo "Latest 5 Notifications:\n";
foreach ($latest as $n) {
    echo "  ID: " . $n->_id . "\n";
    echo "  Type: " . $n->type . "\n";
    echo "  User ID: " . $n->user_id . "\n";
    echo "  Actor ID: " . $n->actor_id . "\n";
    echo "  Created: " . $n->created_at . "\n";
    echo "  ---\n";
}

// 3. Cek total users
$totalUsers = User::count();
echo "\nTotal Users: " . $totalUsers . "\n\n";

// 4. List all users
echo "All Users:\n";
$users = User::all();
foreach ($users as $u) {
    $followersCount = UserFollow::where('following_id', (string) $u->_id)->count();
    $followingCount = UserFollow::where('follower_id', (string) $u->_id)->count();
    echo "  Name: " . $u->name . " | ID: " . $u->_id . " | Followers: " . $followersCount . " | Following: " . $followingCount . "\n";
}

// 5. Cek total follow relationships
$totalFollows = UserFollow::count();
echo "\nTotal Follow Relationships: " . $totalFollows . "\n";

$follows = UserFollow::all();
echo "All Follows:\n";
foreach ($follows as $f) {
    $follower = User::find($f->follower_id);
    $following = User::find($f->following_id);
    echo "  " . ($follower?->name ?? 'Unknown') . " -> " . ($following?->name ?? 'Unknown') . "\n";
}

echo "\n=== End Debug ===\n";
