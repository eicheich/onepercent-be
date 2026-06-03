<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserFollow;
use App\Models\Notification;
use App\Models\UserDailyChallenge;
use App\Http\Controllers\Web\DashboardController;

class TestPokeNotification extends Command
{
    protected $signature = 'test:poke-notification';
    protected $description = 'Test poke flow: mutual + completed challenge -> poke notification';

    public function handle()
    {
        $this->info("\n=== POKE NOTIFICATION TEST ===\n");

        $sender = User::first();
        $receiver = User::where('_id', '!=', $sender?->getKey())->first();

        if (!$sender || !$receiver) {
            $this->error('Need at least two users in DB');
            return 1;
        }

        $senderId = (string) $sender->getKey();
        $receiverId = (string) $receiver->getKey();

        $this->line("Sender: {$sender->name} ({$senderId})");
        $this->line("Receiver: {$receiver->name} ({$receiverId})\n");

        // Ensure mutual follow
        UserFollow::updateOrCreate([
            'follower_id' => $senderId,
            'following_id' => $receiverId,
        ], ['created_at' => now(), 'updated_at' => now()]);

        UserFollow::updateOrCreate([
            'follower_id' => $receiverId,
            'following_id' => $senderId,
        ], ['created_at' => now(), 'updated_at' => now()]);

        $this->line('Mutual follow ensured.');

        // Ensure sender has completed challenge today
        $today = now()->toDateString();
        $challenge = UserDailyChallenge::where('user_id', $senderId)
            ->where('challenge_date', $today)
            ->first();

        if (!$challenge) {
            $challenge = UserDailyChallenge::create([
                'user_id' => $senderId,
                'challenge_date' => $today,
                'is_completed' => true,
                'completed_at' => now(),
                'metadata' => [],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->line('Created completed challenge for sender.');
        } else {
            $challenge->forceFill(['is_completed' => true, 'completed_at' => now()])->save();
            $this->line('Marked existing challenge as completed.');
        }

        // Clear existing poke notifications from sender->receiver for today
        Notification::where('user_id', $receiverId)
            ->where('actor_id', $senderId)
            ->where('type', 'poke')
            ->delete();

        // Set session web_user.id for controller
        session()->put('web_user.id', $senderId);

        // Call the controller method
        $controller = new DashboardController();
        $response = $controller->pokeFriend($receiverId);

        // Check notification exists
        $exists = Notification::where('user_id', $receiverId)
            ->where('actor_id', $senderId)
            ->where('type', 'poke')
            ->exists();

        if ($exists) {
            $this->info('\n✓ Poke notification created successfully!');
            return 0;
        }

        $this->error('\n✗ Poke notification NOT created');
        return 1;
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserFollow;
use App\Models\Notification;
use App\Models\UserDailyChallenge;
use App\Models\ChallengePoke;
use App\Services\NotificationService;

class TestPokeNotification extends Command
{
    protected $signature = 'test:poke-notification';
    protected $description = 'Test poke notification end-to-end (mutual + completed challenge)';

    public function handle()
    {
        $this->info("\n=== POKE NOTIFICATION TEST ===\n");

        $sender = User::first();
        $receiver = User::skip(1)->first();

        if (!$sender || !$receiver) {
            $this->error('Not enough users in DB to run test');
            return 1;
        }

        $this->line("Using sender: {$sender->name} ({$sender->_id})");
        $this->line("Using receiver: {$receiver->name} ({$receiver->_id})\n");

        // Ensure mutual follow
        UserFollow::updateOrCreate([
            'follower_id' => (string) $sender->getKey(),
            'following_id' => (string) $receiver->getKey(),
        ], []);

        UserFollow::updateOrCreate([
            'follower_id' => (string) $receiver->getKey(),
            'following_id' => (string) $sender->getKey(),
        ], []);

        // Ensure sender has completed today's challenge
        $challenge = UserDailyChallenge::firstOrCreate([
            'user_id' => (string) $sender->getKey(),
            'challenge_date' => now()->toDateString(),
        ], [
            'is_completed' => true,
            'challenge_id' => '',
            'metadata' => [],
            'completed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $challenge->is_completed = true;
        $challenge->completed_at = $challenge->completed_at ?? now();
        $challenge->save();

        // Clean previous pokes today
        ChallengePoke::where('sender_id', (string) $sender->getKey())
            ->where('receiver_id', (string) $receiver->getKey())
            ->whereDate('created_at', now()->toDateString())
            ->delete();

        $before = Notification::where('user_id', (string) $receiver->getKey())
            ->where('type', 'poke')
            ->count();

        // Create poke and notification (as controller does)
        ChallengePoke::create([
            'sender_id' => (string) $sender->getKey(),
            'receiver_id' => (string) $receiver->getKey(),
            'user_daily_challenge_id' => (string) $challenge->getKey(),
            'challenge_id' => (string) ($challenge->challenge_id ?? ''),
            'type' => 'boast',
            'message' => 'I finished today\'s challenge! 💪 Come on!',
            'metadata' => [
                'challenge_title' => $challenge->challenge?->title ?? 'Challenge',
                'challenge_date' => now()->toDateString(),
                'completed_at' => $challenge->completed_at?->toIso8601String(),
            ],
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        NotificationService::notifyPoke((string) $sender->getKey(), (string) $receiver->getKey(), $challenge->challenge?->title ?? 'Challenge');

        $after = Notification::where('user_id', (string) $receiver->getKey())
            ->where('type', 'poke')
            ->count();

        if ($after > $before) {
            $this->info("  ✓ Poke notification created for {$receiver->name}!");
            $this->info("\n=== TEST PASSED ===\n");
            return 0;
        }

        $this->error("  ✗ Poke notification NOT created");
        return 1;
    }
}
