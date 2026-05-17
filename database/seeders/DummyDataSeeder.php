<?php

namespace Database\Seeders;

use App\Models\Challenge;
use App\Models\ChallengePoke;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserDailyChallenge;
use App\Models\UserFollow;
use App\Models\UserPersonalization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Clearing old data...');
        $this->clearData();

        $this->command->info('Seeding users...');
        $finn   = $this->seedFinn();
        $dummies = $this->seedDummyUsers();

        $this->command->info('Seeding personalizations...');
        $this->seedPersonalizations($finn, $dummies);

        $this->command->info('Seeding challenges...');
        $challenges = $this->seedChallenges();

        $this->command->info('Seeding daily challenges...');
        $this->seedDailyChallenges($finn, $dummies, $challenges);

        $this->command->info('Seeding follows...');
        $this->seedFollows($finn, $dummies);

        $this->command->info('Seeding pokes...');
        $this->seedPokes($finn, $dummies, $challenges);

        $this->command->info('Seeding achievements...');
        $this->seedAchievements($finn);

        $this->command->info('Done! ✅');
        $this->command->table(
            ['Collection', 'Count'],
            [
                ['users',               User::count()],
                ['personalizations',    UserPersonalization::count()],
                ['challenges',          Challenge::count()],
                ['daily_challenges',    UserDailyChallenge::count()],
                ['follows',             UserFollow::count()],
                ['pokes',               ChallengePoke::count()],
                ['achievements',        UserAchievement::count()],
            ]
        );
    }

    private function clearData(): void
    {
        // Hapus semua kecuali finn dr
        $finnEmail = 'finndr2005@gmail.com';
        $finn = User::where('email', $finnEmail)->first();

        User::where('email', '!=', $finnEmail)->delete();
        if ($finn) {
            UserPersonalization::where('user_id', (string) $finn->getKey())->delete();
            UserDailyChallenge::where('user_id', (string) $finn->getKey())->delete();
            UserAchievement::where('user_id', (string) $finn->getKey())->delete();
        }
        UserPersonalization::where('user_id', '!=', $finn ? (string) $finn->getKey() : '')->delete();
        UserDailyChallenge::where('user_id', '!=', $finn ? (string) $finn->getKey() : '')->delete();
        UserFollow::truncate();
        ChallengePoke::truncate();
        Challenge::truncate();
        UserAchievement::truncate();
    }

    private function seedFinn(): User
    {
        $finn = User::where('email', 'finndr2005@gmail.com')->first();

        if ($finn === null) {
            $finn = User::create([
                'name'               => 'finn dr',
                'email'              => 'finndr2005@gmail.com',
                'password'           => Hash::make('password123'),
                'google_id'          => '103571788657597080460',
                'avatar'             => 'https://lh3.googleusercontent.com/a/ACg8ocK5sZvXImDd-53diVs10iN_3YPJmCkK-zLp3xrHk5m4rPbW-g=s96-c',
                'current_streak'     => 6,
                'longest_streak'     => 10,
                'last_completed_date'=> now()->toDateString(),
                'gender'             => 'male',
            ]);
        } else {
            $finn->current_streak      = 6;
            $finn->longest_streak      = 10;
            $finn->last_completed_date = now()->toDateString();
            $finn->avatar = 'https://lh3.googleusercontent.com/a/ACg8ocK5sZvXImDd-53diVs10iN_3YPJmCkK-zLp3xrHk5m4rPbW-g=s96-c';
            $finn->save();
        }

        $this->command->info('Finn ID: ' . $finn->getKey());
        return $finn;
    }

    private function seedDummyUsers(): array
    {
        $usersData = [
            ['name' => 'Aria Putri',    'email' => 'aria@test.com',    'streak' => 42, 'gender' => 'female'],
            ['name' => 'Budi Santoso',  'email' => 'budi@test.com',    'streak' => 38, 'gender' => 'male'],
            ['name' => 'Citra Dewi',    'email' => 'citra@test.com',   'streak' => 31, 'gender' => 'female'],
            ['name' => 'Dani Rahmat',   'email' => 'dani@test.com',    'streak' => 25, 'gender' => 'male'],
            ['name' => 'Eva Susanti',   'email' => 'eva@test.com',     'streak' => 19, 'gender' => 'female'],
            ['name' => 'Fajar Nugroho', 'email' => 'fajar@test.com',   'streak' => 12, 'gender' => 'male'],
            ['name' => 'Gita Lestari',  'email' => 'gita@test.com',    'streak' => 3,  'gender' => 'female'],
        ];

        $users = [];
        foreach ($usersData as $data) {
            $user = User::create([
                'name'               => $data['name'],
                'email'              => $data['email'],
                'password'           => Hash::make('password123'),
                'current_streak'     => $data['streak'],
                'longest_streak'     => $data['streak'] + 5,
                'last_completed_date'=> now()->toDateString(),
                'gender'             => $data['gender'],
                'avatar'             => null,
            ]);
            $users[] = $user;
        }

        return $users;
    }

    private function seedPersonalizations(User $finn, array $dummies): void
    {
        UserPersonalization::create([
            'user_id' => (string) $finn->getKey(),
            'tags'    => ['technology', 'education', 'health'],
        ]);

        $tagSets = [
            ['technology', 'business'],
            ['health', 'sports'],
            ['finance', 'education'],
            ['lifestyle', 'technology'],
            ['entertainment', 'health'],
            ['business', 'finance'],
            ['education', 'lifestyle'],
        ];

        foreach ($dummies as $i => $user) {
            UserPersonalization::create([
                'user_id' => (string) $user->getKey(),
                'tags'    => $tagSets[$i] ?? ['technology'],
            ]);
        }
    }

    private function seedChallenges(): array
    {
        $challengesData = [
            [
                'title'             => 'Design a Simple UI Layout',
                'content'           => 'Create a mobile app wireframe with at least 3 screens. Focus on clean layout and user flow.',
                'estimated_minutes' => 15,
                'tags'              => ['technology', 'education'],
                'signature'         => 'sig_ui_001',
            ],
            [
                'title'             => 'Learn One New Tech Concept',
                'content'           => 'Pick one programming concept you have never used. Read about it and write a short summary.',
                'estimated_minutes' => 20,
                'tags'              => ['technology', 'education'],
                'signature'         => 'sig_tech_002',
            ],
            [
                'title'             => '10-Minute Morning Stretch',
                'content'           => 'Do a simple morning stretch routine. Focus on neck, shoulders, back and legs. Repeat 2 sets.',
                'estimated_minutes' => 10,
                'tags'              => ['health'],
                'signature'         => 'sig_stretch_003',
            ],
            [
                'title'             => 'Write 3 Key Takeaways',
                'content'           => 'Reflect on your day. Write 3 specific things you learned or achieved. Be detailed.',
                'estimated_minutes' => 10,
                'tags'              => ['education', 'lifestyle'],
                'signature'         => 'sig_reflect_004',
            ],
            [
                'title'             => 'Build One Small Feature',
                'content'           => 'Code one small feature or fix one bug in any project. Document what you did.',
                'estimated_minutes' => 30,
                'tags'              => ['technology'],
                'signature'         => 'sig_code_005',
            ],
            [
                'title'             => 'Track Your Daily Expenses',
                'content'           => 'List all expenses today. Categorize them and find one area to cut back this week.',
                'estimated_minutes' => 15,
                'tags'              => ['finance', 'lifestyle'],
                'signature'         => 'sig_finance_006',
            ],
            [
                'title'             => 'Read 10 Pages',
                'content'           => 'Pick any book and read 10 pages. Write 2 sentences summarizing what you read.',
                'estimated_minutes' => 20,
                'tags'              => ['education'],
                'signature'         => 'sig_read_007',
            ],
        ];

        $challenges = [];
        foreach ($challengesData as $data) {
            $challenges[] = Challenge::create([
                ...$data,
                'metadata' => ['provider' => 'seeder'],
            ]);
        }

        return $challenges;
    }

    private function seedDailyChallenges(
        User $finn,
        array $dummies,
        array $challenges
    ): void {
        $today = now();

        // Finn — 6 hari completed, 1 missed (hari ke-3), hari ini belum
        for ($d = 6; $d >= 0; $d--) {
            $date      = now()->subDays($d);
            $isMissed  = ($d === 3);
            $isToday   = ($d === 0);
            $completed = !$isMissed && !$isToday;

            UserDailyChallenge::create([
                'user_id'        => (string) $finn->getKey(),
                'challenge_id'   => (string) $challenges[$d % count($challenges)]->getKey(),
                'challenge_date' => $date->toDateString(),
                'is_completed'   => $completed,
                'completed_at'   => $completed ? $date : null,
                'expires_at'     => $date->copy()->addDay(),
                'metadata'       => [
                    'provider'   => 'seeder',
                    'reflection' => $completed ? 'Great session!' : null,
                ],
            ]);
        }

        // Dummy users
        foreach ($dummies as $u => $user) {
            for ($d = 6; $d >= 0; $d--) {
                $date      = now()->subDays($d);
                $completed = $u < 4 ? $d > 0 : ($d % 2 === 0 && $d > 0);

                UserDailyChallenge::create([
                    'user_id'        => (string) $user->getKey(),
                    'challenge_id'   => (string) $challenges[($u + $d) % count($challenges)]->getKey(),
                    'challenge_date' => $date->toDateString(),
                    'is_completed'   => $completed,
                    'completed_at'   => $completed ? $date : null,
                    'expires_at'     => $date->copy()->addDay(),
                    'metadata'       => ['provider' => 'seeder'],
                ]);
            }
        }
    }

    private function seedFollows(User $finn, array $dummies): void
    {
        // Finn mutual follow dengan semua dummy
        foreach ($dummies as $dummy) {
            UserFollow::create([
                'follower_id'  => (string) $finn->getKey(),
                'following_id' => (string) $dummy->getKey(),
            ]);
            UserFollow::create([
                'follower_id'  => (string) $dummy->getKey(),
                'following_id' => (string) $finn->getKey(),
            ]);
        }

        // Beberapa dummy saling follow
        for ($i = 0; $i < 3; $i++) {
            UserFollow::create([
                'follower_id'  => (string) $dummies[$i]->getKey(),
                'following_id' => (string) $dummies[$i + 1]->getKey(),
            ]);
            UserFollow::create([
                'follower_id'  => (string) $dummies[$i + 1]->getKey(),
                'following_id' => (string) $dummies[$i]->getKey(),
            ]);
        }
    }

    private function seedPokes(
        User $finn,
        array $dummies,
        array $challenges
    ): void {
        $finnId = (string) $finn->getKey();

        $pokesData = [
            [
                'sender'    => $dummies[0],
                'type'      => 'boast',
                'message'   => 'Just finished my challenge! 💪 Your turn!',
                'title'     => 'Design a Simple UI Layout',
                'challenge' => $challenges[0],
                'date'      => now()->toDateString(),
                'ago'       => 5,  // minutes ago
            ],
            [
                'sender'    => $dummies[1],
                'type'      => 'remind',
                'message'   => "Hey don't forget your challenge today! 👀",
                'title'     => 'Learn One New Tech Concept',
                'challenge' => $challenges[1],
                'date'      => now()->toDateString(),
                'ago'       => 15,
            ],
            [
                'sender'    => $dummies[2],
                'type'      => 'boast',
                'message'   => 'I completed a 7-day streak! 🔥',
                'title'     => '10-Minute Morning Stretch',
                'challenge' => $challenges[2],
                'date'      => now()->subDay()->toDateString(),
                'ago'       => 60 * 24,
            ],
            [
                'sender'    => $dummies[3],
                'type'      => 'remind',
                'message'   => 'All challenges done. Time to poke friends!',
                'title'     => 'Write 3 Key Takeaways',
                'challenge' => $challenges[3],
                'date'      => now()->subDay()->toDateString(),
                'ago'       => 60 * 26,
            ],
            [
                'sender'    => $dummies[4],
                'type'      => 'boast',
                'message'   => 'Streak unlocked! Come join me! 🎉',
                'title'     => 'Build One Small Feature',
                'challenge' => $challenges[4],
                'date'      => now()->subDays(2)->toDateString(),
                'ago'       => 60 * 48,
            ],
        ];

        foreach ($pokesData as $poke) {
            ChallengePoke::create([
                'sender_id'              => (string) $poke['sender']->getKey(),
                'receiver_id'            => $finnId,
                'user_daily_challenge_id'=> 'seeder_dummy',
                'challenge_id'           => (string) $poke['challenge']->getKey(),
                'type'                   => $poke['type'],
                'message'                => $poke['message'],
                'metadata'               => [
                    'challenge_title' => $poke['title'],
                    'challenge_date'  => $poke['date'],
                    'completed_at'    => now()->subMinutes($poke['ago'])->toIso8601String(),
                ],
                'read_at'    => null,
                'created_at' => now()->subMinutes($poke['ago']),
                'updated_at' => now()->subMinutes($poke['ago']),
            ]);
        }
    }

    private function seedAchievements(User $finn): void
    {
        $finnId = (string) $finn->getKey();

        $achievements = [
            [
                'achievement_key'  => 'first_step',
                'achievement_name' => 'First Step',
                'description'      => 'Complete your first challenge',
                'icon'             => '🏆',
                'unlocked_at'      => now()->subDays(6),
            ],
            [
                'achievement_key'  => 'social_butterfly',
                'achievement_name' => 'Social Butterfly',
                'description'      => 'Follow 5 people',
                'icon'             => '🤝',
                'unlocked_at'      => now()->subDays(2),
            ],
            [
                'achievement_key'  => 'consistent',
                'achievement_name' => 'Consistent',
                'description'      => 'Complete 10 total challenges',
                'icon'             => '💪',
                'unlocked_at'      => now()->subDay(),
            ],
        ];

        foreach ($achievements as $data) {
            UserAchievement::create([
                'user_id'     => $finnId,
                ...$data,
            ]);
        }
    }
}
