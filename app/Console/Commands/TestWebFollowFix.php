<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserFollow;
use App\Models\Notification;
use App\Services\NotificationService;

class TestWebFollowFix extends Command
{
    protected $signature = 'test:web-follow-fix';
    protected $description = 'Test web follow flow after fix';

    public function handle()
    {
        $this->info("=== Testing Web Follow Fix ===\n");

        // Use different users for fresh test
        $follower = User::find('6a1f057c3ceb8cda0e0bf7be');  // Janew
        $target = User::find('6a1ef65b3ceb8cda0e0bf7b2');   // salsabila

        if (!$follower || !$target) {
            $this->error("Users not found!");
            return 1;
        }

        $this->line("Follower: {$follower->name} ({$follower->_id})");
        $this->line("Target: {$target->name} ({$target->_id})\n");

        // Check if already follows
        $followerId = (string) $follower->getKey();
        $targetId = (string) $target->getKey();
        
        $alreadyFollows = UserFollow::where('follower_id', $followerId)
            ->where('following_id', $targetId)
            ->exists();

        if ($alreadyFollows) {
            $this->line("Already following, deleting first...");
            UserFollow::where('follower_id', $followerId)
                ->where('following_id', $targetId)
                ->delete();
        }

        // Count notifications before
        $notifBefore = Notification::where('user_id', $targetId)
            ->where('type', 'follow')
            ->count();
        $this->line("\nNotifications BEFORE: {$notifBefore}\n");

        // Simulate web follow (like FriendsWebController.follow does now)
        $this->line("Simulating web follow...");
        
        UserFollow::create([
            'follower_id'  => $followerId,
            'following_id' => $targetId,
        ]);

        // Call notification service (should now be in FriendsWebController)
        try {
            NotificationService::notifyFollow($followerId, $targetId);
            $this->info("✓ notifyFollow() called successfully");
        } catch (\Exception $e) {
            $this->error("✗ Error: " . $e->getMessage());
            return 1;
        }

        // Check for mutual follow and notify back
        $theyFollowMe = UserFollow::where('follower_id', $targetId)
            ->where('following_id', $followerId)
            ->exists();

        if ($theyFollowMe) {
            try {
                NotificationService::notifyFollowBack($followerId, $targetId);
                $this->info("✓ notifyFollowBack() called (mutual follow)");
            } catch (\Exception $e) {
                $this->error("✗ Error: " . $e->getMessage());
            }
        }

        // Count notifications after
        $notifAfter = Notification::where('user_id', $targetId)
            ->where('type', 'follow')
            ->count();
        $this->line("\nNotifications AFTER: {$notifAfter}");

        if ($notifAfter > $notifBefore) {
            $this->info("✓ SUCCESS! New follow notification created!");
            $newNotif = Notification::where('user_id', $targetId)
                ->where('actor_id', $followerId)
                ->where('type', 'follow')
                ->orderBy('created_at', 'desc')
                ->first();
            if ($newNotif) {
                $this->line("  Title: {$newNotif->title}");
                $this->line("  Created: {$newNotif->created_at}");
            }
        } else {
            $this->error("✗ No new notification created!");
            return 1;
        }

        $this->info("\n=== Test Complete ===");
        return 0;
    }
}
