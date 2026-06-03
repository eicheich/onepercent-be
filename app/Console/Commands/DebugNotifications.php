<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserFollow;

class DebugNotifications extends Command
{
    protected $signature = 'debug:notifications';
    protected $description = 'Debug notifications and follow data';

    public function handle()
    {
        $this->info("=== Debugging Notifications ===\n");

        // 1. Cek total notifications
        $totalNotif = Notification::count();
        $this->line("Total Notifications: " . $totalNotif);

        // 2. Cek latest notifications
        $latest = Notification::orderBy('created_at', 'desc')->limit(10)->get();
        $this->line("\nLatest 10 Notifications:");
        foreach ($latest as $n) {
            $this->line("  Type: {$n->type} | User: {$n->user_id} | Actor: {$n->actor_id} | Created: {$n->created_at}");
        }

        // 3. Cek total users
        $totalUsers = User::count();
        $this->line("\nTotal Users: " . $totalUsers);

        // 4. List all users with stats
        $this->line("\nAll Users:");
        $users = User::all();
        foreach ($users as $u) {
            $followersCount = UserFollow::where('following_id', (string) $u->_id)->count();
            $followingCount = UserFollow::where('follower_id', (string) $u->_id)->count();
            $notifCount = Notification::where('user_id', (string) $u->_id)->count();
            $this->line("  Name: {$u->name} | ID: {$u->_id} | Followers: {$followersCount} | Following: {$followingCount} | Notifs: {$notifCount}");
        }

        // 5. Cek total follow relationships
        $totalFollows = UserFollow::count();
        $this->line("\nTotal Follow Relationships: " . $totalFollows);

        $follows = UserFollow::all();
        if ($totalFollows > 0) {
            $this->line("All Follows:");
            foreach ($follows as $f) {
                $follower = User::find($f->follower_id);
                $following = User::find($f->following_id);
                $this->line("  " . ($follower?->name ?? 'Unknown') . " ({$f->follower_id}) -> " . ($following?->name ?? 'Unknown') . " ({$f->following_id})");
            }
        }

        $this->info("\n=== End Debug ===");
    }
}
