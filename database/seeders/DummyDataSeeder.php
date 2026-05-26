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
        $this->command->info('🗑️  Clearing all collections...');
        $this->clearAll();

        $this->command->info('👤 Creating finn dr (admin)...');
        $finn = $this->createFinn();

        $this->command->info('👥 Creating dummy users...');
        $dummies = $this->createDummyUsers();

        $this->command->info('🏷️  Setting up personalizations...');
        $this->createPersonalizations($finn, $dummies);

        $this->command->info('🎯 Creating challenge pool...');
        $challenges = $this->createChallenges();

        $this->command->info('📅 Creating daily challenge history...');
        $this->createDailyChallenges($finn, $dummies, $challenges);

        $this->command->info('🤝 Creating follow relationships...');
        $this->createFollows($finn, $dummies);

        $this->command->info('👋 Creating poke notifications...');
        $this->createPokes($finn, $dummies, $challenges);

        $this->command->info('🏆 Creating achievements...');
        $this->createAchievements($finn);

        $this->command->info('✅ Done! Summary:');
        $this->command->table(
            ['Collection', 'Count'],
            [
                ['users',            User::count()],
                ['personalizations', UserPersonalization::count()],
                ['challenges',       Challenge::count()],
                ['daily_challenges', UserDailyChallenge::count()],
                ['follows',          UserFollow::count()],
                ['pokes',            ChallengePoke::count()],
                ['achievements',     UserAchievement::count()],
            ]
        );

        $this->command->info('');
        $this->command->info('🔑 Admin access: finndr2005@gmail.com / password123');
        $this->command->info('🌐 Admin URL: http://127.0.0.1:8000/admin');
        $this->command->info('📱 App URL:   http://127.0.0.1:8000/app/dashboard');
    }

    private function clearAll(): void
    {
        User::truncate();
        UserPersonalization::truncate();
        Challenge::truncate();
        UserDailyChallenge::truncate();
        UserFollow::truncate();
        ChallengePoke::truncate();
        UserAchievement::truncate();

        // Hapus tokens juga
        \App\Models\PersonalAccessToken::truncate();
    }

    private function createFinn(): User
    {
        return User::create([
            'name'                => 'finn dr',
            'email'               => 'finndr2005@gmail.com',
            'password'            => Hash::make('password123'),
            'google_id'           => '103571788657597080460',
            'avatar'              => 'https://lh3.googleusercontent.com/a/ACg8ocK5sZvXImDd-53diVs10iN_3YPJmCkK-zLp3xrHk5m4rPbW-g=s96-c',
            'current_streak'      => 6,
            'longest_streak'      => 10,
            'last_completed_date' => now()->toDateString(),
            'gender'              => 'male',
        ]);
    }

    private function createDummyUsers(): array
    {
        $data = [
            ['name' => 'Aria Putri',    'email' => 'aria@test.com',    'streak' => 42, 'gender' => 'female'],
            ['name' => 'Budi Santoso',  'email' => 'budi@test.com',    'streak' => 38, 'gender' => 'male'],
            ['name' => 'Citra Dewi',    'email' => 'citra@test.com',   'streak' => 31, 'gender' => 'female'],
            ['name' => 'Dani Rahmat',   'email' => 'dani@test.com',    'streak' => 25, 'gender' => 'male'],
            ['name' => 'Eva Susanti',   'email' => 'eva@test.com',     'streak' => 19, 'gender' => 'female'],
            ['name' => 'Fajar Nugroho', 'email' => 'fajar@test.com',   'streak' => 12, 'gender' => 'male'],
            ['name' => 'Gita Lestari',  'email' => 'gita@test.com',    'streak' => 3,  'gender' => 'female'],
        ];

        $users = [];
        foreach ($data as $d) {
            $users[] = User::create([
                'name'                => $d['name'],
                'email'               => $d['email'],
                'password'            => Hash::make('password123'),
                'current_streak'      => $d['streak'],
                'longest_streak'      => $d['streak'] + 5,
                'last_completed_date' => now()->toDateString(),
                'gender'              => $d['gender'],
                'avatar'              => null,
            ]);
        }
        return $users;
    }

    private function createPersonalizations(User $finn, array $dummies): void
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

    private function createChallenges(): array
    {
        $data = [
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
        foreach ($data as $d) {
            $challenges[] = Challenge::create([
                ...$d,
                'metadata' => ['provider' => 'seeder'],
            ]);
        }
        return $challenges;
    }

    private function createDailyChallenges(
        User $finn,
        array $dummies,
        array $challenges
    ): void {
        $count = count($challenges);

        // Finn: 6 hari completed, 1 missed (hari ke-3), hari ini BELUM complete
        for ($d = 6; $d >= 0; $d--) {
            $date      = now()->subDays($d);
            $isMissed  = ($d === 3);
            $isToday   = ($d === 0);
            $completed = !$isMissed && !$isToday;

            UserDailyChallenge::create([
                'user_id'        => (string) $finn->getKey(),
                'challenge_id'   => (string) $challenges[$d % $count]->getKey(),
                'challenge_date' => $date->toDateString(),
                'is_completed'   => $completed,
                'completed_at'   => $completed ? $date->copy()->addHours(9) : null,
                'expires_at'     => $date->copy()->addDay(),
                'metadata'       => [
                    'provider'   => 'seeder',
                    'reflection' => $completed ? 'Great progress today!' : null,
                ],
            ]);
        }

        // Dummy users
        foreach ($dummies as $u => $user) {
            for ($d = 6; $d >= 0; $d--) {
                $date      = now()->subDays($d);
                $completed = $u < 4
                    ? $d > 0   // top 4 users complete semua kecuali hari ini
                    : ($d % 2 === 0 && $d > 0); // sisanya selang-seling

                UserDailyChallenge::create([
                    'user_id'        => (string) $user->getKey(),
                    'challenge_id'   => (string) $challenges[($u + $d) % $count]->getKey(),
                    'challenge_date' => $date->toDateString(),
                    'is_completed'   => $completed,
                    'completed_at'   => $completed ? $date->copy()->addHours(8) : null,
                    'expires_at'     => $date->copy()->addDay(),
                    'metadata'       => ['provider' => 'seeder'],
                ]);
            }
        }
    }

    private function createFollows(User $finn, array $dummies): void
    {
        $finnId = (string) $finn->getKey();

        // Finn mutual follow dengan semua dummy
        foreach ($dummies as $dummy) {
            $dummyId = (string) $dummy->getKey();
            UserFollow::create(['follower_id' => $finnId,   'following_id' => $dummyId]);
            UserFollow::create(['follower_id' => $dummyId,  'following_id' => $finnId]);
        }

        // Beberapa dummy saling follow
        for ($i = 0; $i < min(3, count($dummies) - 1); $i++) {
            $a = (string) $dummies[$i]->getKey();
            $b = (string) $dummies[$i + 1]->getKey();
            UserFollow::create(['follower_id' => $a, 'following_id' => $b]);
            UserFollow::create(['follower_id' => $b, 'following_id' => $a]);
        }
    }

    private function createPokes(
        User $finn,
        array $dummies,
        array $challenges
    ): void {
        $finnId = (string) $finn->getKey();

        $pokesConfig = [
            [
                'sender'  => $dummies[0],
                'type'    => 'boast',
                'message' => 'Just finished my challenge! 💪 Your turn!',
                'title'   => 'Design a Simple UI Layout',
                'c_idx'   => 0,
                'mins_ago' => 5,
            ],
            [
                'sender'  => $dummies[1],
                'type'    => 'remind',
                'message' => "Hey don't forget your challenge today! 👀",
                'title'   => 'Learn One New Tech Concept',
                'c_idx'   => 1,
                'mins_ago' => 30,
            ],
            [
                'sender'  => $dummies[2],
                'type'    => 'boast',
                'message' => 'Completed a 7-day streak! 🔥',
                'title'   => '10-Minute Morning Stretch',
                'c_idx'   => 2,
                'mins_ago' => 60 * 24, // yesterday
            ],
            [
                'sender'  => $dummies[3],
                'type'    => 'remind',
                'message' => 'All challenges done. Time to poke your friends!',
                'title'   => 'Write 3 Key Takeaways',
                'c_idx'   => 3,
                'mins_ago' => 60 * 26,
            ],
            [
                'sender'  => $dummies[4],
                'type'    => 'boast',
                'message' => 'Streak unlocked! Come join me! 🎉',
                'title'   => 'Build One Small Feature',
                'c_idx'   => 4,
                'mins_ago' => 60 * 48, // 2 days ago
            ],
        ];

        foreach ($pokesConfig as $cfg) {
            ChallengePoke::create([
                'sender_id'               => (string) $cfg['sender']->getKey(),
                'receiver_id'             => $finnId,
                'user_daily_challenge_id' => 'seeder_dummy',
                'challenge_id'            => (string) $challenges[$cfg['c_idx']]->getKey(),
                'type'                    => $cfg['type'],
                'message'                 => $cfg['message'],
                'metadata'                => [
                    'challenge_title' => $cfg['title'],
                    'challenge_date'  => now()->subMinutes($cfg['mins_ago'])->toDateString(),
                    'completed_at'    => now()->subMinutes($cfg['mins_ago'])->toIso8601String(),
                ],
                'read_at'    => null,
                'created_at' => now()->subMinutes($cfg['mins_ago']),
                'updated_at' => now()->subMinutes($cfg['mins_ago']),
            ]);
        }
    }

    private function createAchievements(User $finn): void
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
                'user_id' => $finnId,
                ...$data,
            ]);
        }
    }
}
