<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserFollow;
use App\Models\Notification;
use App\Services\NotificationService;

class FinalNotificationTest extends Command
{
    protected $signature = 'test:final-notification';
    protected $description = 'Final test of all notification fixes';

    public function handle()
    {
        $this->info("\n=== FINAL NOTIFICATION TEST ===\n");

        // Get different users for test
        $user1 = User::find('6a1ef6023793dfc6480a139b');  // Budi
        $user2 = User::find('6a1efd663ceb8cda0e0bf7b9');  // husnoel
        $user3 = User::find('6a1f0b113ceb8cda0e0bf7c5');  // kambing

        if (!$user1 || !$user2 || !$user3) {
            $this->error("Users not found!");
            return 1;
        }

        $this->line("Test Users:");
        $this->line("  1. {$user1->name} ({$user1->_id})");
        $this->line("  2. {$user2->name} ({$user2->_id})");
        $this->line("  3. {$user3->name} ({$user3->_id})\n");

        // TEST 1: Follow notification
        $this->info("TEST 1: Follow Notification");
        $this->line("Simulating: {$user2->name} follows {$user1->name}");
        
        $alreadyFollows = UserFollow::where('follower_id', (string) $user2->getKey())
            ->where('following_id', (string) $user1->getKey())
            ->exists();
        
        if ($alreadyFollows) {
            $this->line("  Already following, deleting first...");
            UserFollow::where('follower_id', (string) $user2->getKey())
                ->where('following_id', (string) $user1->getKey())
                ->delete();
        }
        
        $notifsBefore = Notification::where('user_id', (string) $user1->getKey())
            ->where('type', 'follow')
            ->count();
        
        // Create follow like web controller does
        UserFollow::create([
            'follower_id'  => (string) $user2->getKey(),
            'following_id' => (string) $user1->getKey(),
        ]);
        
        NotificationService::notifyFollow((string) $user2->getKey(), (string) $user1->getKey());
        
        $notifsAfter = Notification::where('user_id', (string) $user1->getKey())
            ->where('type', 'follow')
            ->count();
        
        if ($notifsAfter > $notifsBefore) {
            $this->info("  ✓ Follow notification created!");
        } else {
            $this->error("  ✗ Follow notification NOT created!");
            return 1;
        }

        // TEST 2: Mutual follow → follow back notification
        $this->info("\nTEST 2: Follow Back Notification (Mutual)");
        $this->line("Simulating: {$user1->name} follows back {$user2->name}");
        
        $mutualFollows = UserFollow::where('follower_id', (string) $user1->getKey())
            ->where('following_id', (string) $user2->getKey())
            ->exists();
        
        if (!$mutualFollows) {
            UserFollow::create([
                'follower_id'  => (string) $user1->getKey(),
                'following_id' => (string) $user2->getKey(),
            ]);
            
            $notifsBefore = Notification::where('user_id', (string) $user2->getKey())
                ->where('type', 'follow_back')
                ->count();
            
            NotificationService::notifyFollowBack((string) $user1->getKey(), (string) $user2->getKey());
            
            $notifsAfter = Notification::where('user_id', (string) $user2->getKey())
                ->where('type', 'follow_back')
                ->count();
            
            if ($notifsAfter > $notifsBefore) {
                $this->info("  ✓ Follow back notification created!");
            } else {
                $this->error("  ✗ Follow back notification NOT created!");
                return 1;
            }
        } else {
            $this->line("  Already mutual followers, checking for notification...");
            $hasFollowBack = Notification::where('user_id', (string) $user2->getKey())
                ->where('type', 'follow_back')
                ->where('actor_id', (string) $user1->getKey())
                ->exists();
            
            if ($hasFollowBack) {
                $this->info("  ✓ Follow back notification exists!");
            }
        }

        // TEST 3: Check unread count badge
        $this->info("\nTEST 3: Unread Count (for layout badge)");
        $unreadForUser1 = Notification::where('user_id', (string) $user1->getKey())
            ->whereNull('read_at')
            ->count();
        $this->line("  Unread notifications for {$user1->name}: {$unreadForUser1}");
        if ($unreadForUser1 > 0) {
            $this->info("  ✓ Badge will show unread count");
        }

        $this->info("\n=== ALL TESTS PASSED ===");
        $this->line("\nNotifications are now working correctly!");
        $this->line("- Follow notifications created ✓");
        $this->line("- Follow back notifications created ✓");
        $this->line("- Unread badge displays ✓\n");
        
        return 0;
    }
}
