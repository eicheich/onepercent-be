<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserFollow;
use App\Models\Notification;
use App\Services\NotificationService;

class TestFollowNotification extends Command
{
    protected $signature = 'test:follow-notification';
    protected $description = 'Test follow notification flow';

    public function handle()
    {
        $user1Id = '6a1ef6013793dfc6480a1399';  // finn dr
        $user2Id = '6a1efd663ceb8cda0e0bf7b9';  // husnoel

        $user1 = User::find($user1Id);
        $user2 = User::find($user2Id);

        $this->info("=== Testing Follow Notification Flow ===\n");
        $this->line("User 1 (target): " . ($user1?->name ?? 'Not found'));
        $this->line("User 2 (follower): " . ($user2?->name ?? 'Not found') . "\n");

        if (!$user1 || !$user2) {
            $this->error("Users not found!");
            return 1;
        }

        // 1. Check existing follow relationship
        $this->line("1. Checking existing follow relationship...");
        $existingFollow = UserFollow::where('follower_id', $user2Id)
            ->where('following_id', $user1Id)
            ->first();

        if ($existingFollow) {
            $this->line("   ✓ Follow relationship exists (created: {$existingFollow->created_at})");
        } else {
            $this->line("   ✗ No follow relationship found");
        }

        // 2. Create a follow relationship if it doesn't exist
        $this->line("\n2. Creating follow relationship...");
        if (!$existingFollow) {
            UserFollow::create([
                'follower_id'  => $user2Id,
                'following_id' => $user1Id,
            ]);
            $this->line("   ✓ Follow relationship created");
        } else {
            $this->line("   Already exists, skipping...");
        }

        // 3. Check current notifications
        $this->line("\n3. Checking follow notifications for " . $user1->name . " BEFORE...");
        $beforeCount = Notification::where('user_id', $user1Id)
            ->where('type', 'follow')
            ->count();
        $this->line("   Found {$beforeCount} follow notification(s)");

        // 4. Now let's test the NotificationService directly
        $this->line("\n4. Testing NotificationService::notifyFollow()...");
        try {
            NotificationService::notifyFollow($user2Id, $user1Id);
            $this->line("   ✓ notifyFollow() completed without error");
        } catch (\Exception $e) {
            $this->error("   ✗ Error: " . $e->getMessage());
            return 1;
        }

        // 5. Verify the notification was created
        $this->line("\n5. Checking follow notifications for " . $user1->name . " AFTER...");
        $afterCount = Notification::where('user_id', $user1Id)
            ->where('type', 'follow')
            ->count();
        $this->line("   Found {$afterCount} follow notification(s)");

        if ($afterCount > $beforeCount) {
            $this->info("   ✓ New notification created!");
            $newNotif = Notification::where('user_id', $user1Id)
                ->where('actor_id', $user2Id)
                ->where('type', 'follow')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($newNotif) {
                $this->line("     Type: {$newNotif->type}");
                $this->line("     Title: {$newNotif->title}");
                $this->line("     Body: {$newNotif->body}");
                $this->line("     Created: {$newNotif->created_at}");
            }
        } else {
            $this->error("   ✗ No new notification was created!");
        }

        $this->info("\n=== End Test ===");
        return 0;
    }
}
