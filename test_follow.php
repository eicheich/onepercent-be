<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\User;
use App\Models\UserFollow;
use App\Models\Notification;

echo "=== Testing Follow Notification Flow ===\n\n";

$user1Id = '6a1ef6013793dfc6480a1399';  // finn dr
$user2Id = '6a1efd663ceb8cda0e0bf7b9';  // husnoel

$user1 = User::find($user1Id);
$user2 = User::find($user2Id);

echo "User 1 (target): " . ($user1?->name ?? 'Not found') . "\n";
echo "User 2 (follower): " . ($user2?->name ?? 'Not found') . "\n\n";

if (!$user1 || !$user2) {
    die("Users not found!\n");
}

// 1. Check existing follow relationship
echo "1. Checking existing follow relationship...\n";
$existingFollow = UserFollow::where('follower_id', $user2Id)
    ->where('following_id', $user1Id)
    ->first();

if ($existingFollow) {
    echo "   ✓ Follow relationship exists (created: {$existingFollow->created_at})\n";
} else {
    echo "   ✗ No follow relationship found\n";
}
echo "\n";

// 2. Create a follow relationship if it doesn't exist
echo "2. Creating follow relationship...\n";
if (!$existingFollow) {
    UserFollow::create([
        'follower_id'  => $user2Id,
        'following_id' => $user1Id,
    ]);
    echo "   ✓ Follow relationship created\n";
} else {
    echo "   Already exists, skipping...\n";
}
echo "\n";

// 3. Check notifications for the target user
echo "3. Checking follow notifications for " . $user1->name . "...\n";
$followNotifs = Notification::where('user_id', $user1Id)
    ->where('type', 'follow')
    ->get();

echo "   Found " . $followNotifs->count() . " follow notification(s)\n";
if ($followNotifs->count() > 0) {
    foreach ($followNotifs as $n) {
        echo "    - {$n->title} (Created: {$n->created_at})\n";
    }
}
echo "\n";

// 4. Now let's test the NotificationService directly
echo "4. Testing NotificationService::notifyFollow()...\n";
try {
    \App\Services\NotificationService::notifyFollow($user2Id, $user1Id);
    echo "   ✓ notifyFollow() completed without error\n";
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 5. Verify the notification was created
echo "5. Verifying notification was created...\n";
$newNotif = Notification::where('user_id', $user1Id)
    ->where('actor_id', $user2Id)
    ->where('type', 'follow')
    ->orderBy('created_at', 'desc')
    ->first();

if ($newNotif) {
    echo "   ✓ Notification found!\n";
    echo "     Type: {$newNotif->type}\n";
    echo "     Title: {$newNotif->title}\n";
    echo "     Body: {$newNotif->body}\n";
    echo "     Created: {$newNotif->created_at}\n";
    echo "     Read: " . ($newNotif->read_at ? 'Yes' : 'No') . "\n";
} else {
    echo "   ✗ No notification found\n";
}

echo "\n=== End Test ===\n";
