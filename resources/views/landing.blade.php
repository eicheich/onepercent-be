<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OnePercent - 1% Better Every Day</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#C2A3FF',
                        'primary-dark': '#9B71EE',
                        'primary-light': '#F3EFFF',
                        background: '#F5F5F7'
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-delayed': 'float 6s ease-in-out 3s infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
    </style>
</head>

<body class="bg-background font-sans text-slate-700 antialiased selection:bg-primary selection:text-white overflow-x-hidden relative">

    {{-- Dekorasi Latar Belakang (Blobs) --}}
    <div class="absolute top-0 left-0 w-full h-screen overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40rem] h-[40rem] bg-[#C2A3FF]/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute top-[20%] right-[-10%] w-[30rem] h-[30rem] bg-orange-400/10 rounded-full blur-3xl animate-float-delayed"></div>
    </div>

    {{-- Navbar --}}
    <nav class="fixed top-0 w-full bg-white/80 backdrop-blur-lg border-b-2 border-slate-100 z-50 transition-all">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3 group cursor-pointer">
                <div class="w-10 h-10 rounded-[1rem] bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center transform group-hover:rotate-12 transition-transform shadow-md">
                    <span class="text-white font-black text-lg">1%</span>
                </div>
                <span class="font-black text-xl text-slate-800 tracking-tight">OnePercent</span>
            </div>
            <div class="flex items-center gap-2 md:gap-4">
                <a href="{{ route('web.login') }}" class="text-slate-500 hover:text-primary-dark text-sm font-extrabold px-4 py-2 transition-colors">
                    Sign In
                </a>
                <a href="{{ route('web.register') }}"
                    class="bg-primary text-white px-6 py-2.5 rounded-full text-sm font-black hover:bg-primary-dark hover:scale-105 active:scale-95 transition-all shadow-sm border-2 border-transparent hover:border-white">
                    Get Started
                </a>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="pt-40 pb-20 px-6 min-h-[90vh] flex flex-col justify-center relative z-10">
        <div class="max-w-4xl mx-auto text-center fade-in">
            <div class="inline-flex items-center gap-2 bg-white border-2 border-orange-100 text-orange-500 px-5 py-2.5 rounded-full text-sm font-black mb-8 shadow-sm cursor-default hover:scale-105 transition-transform">
                <span class="animate-pulse text-lg">🔥</span> Start your streak today
            </div>

            <h1 class="text-5xl md:text-7xl font-black text-slate-800 leading-[1.1] mb-6 tracking-tight">
                Become <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#C2A3FF] to-[#9B71EE]">1% Better</span><br>Every Single Day
            </h1>

            <p class="text-lg md:text-xl text-slate-500 mb-10 max-w-2xl mx-auto leading-relaxed font-medium">
                OnePercent helps you build consistent habits through daily AI-powered challenges,
                streak tracking, and friendly competition with your peers.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('web.register') }}"
                    class="w-full sm:w-auto bg-slate-800 text-white px-8 py-4 rounded-full font-black text-lg hover:bg-slate-700 hover:-translate-y-1 active:translate-y-0 transition-all shadow-xl shadow-slate-800/20 group">
                    Start for Free
                    <span class="inline-block transform group-hover:translate-x-1 transition-transform">→</span>
                </a>
                <a href="{{ route('web.login') }}"
                    class="w-full sm:w-auto bg-white text-slate-600 border-2 border-slate-200 px-8 py-4 rounded-full font-extrabold text-lg hover:border-slate-300 hover:bg-slate-50 transition-all">
                    Sign In
                </a>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="py-24 px-6 relative z-10">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16 fade-in opacity-0 delay-100">
                <h2 class="text-4xl md:text-5xl font-black text-slate-800 mb-4 tracking-tight">Why OnePercent?</h2>
                <p class="text-slate-500 font-bold text-lg">Everything you need to build better habits</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                @foreach ([
                    ['icon' => '🤖', 'title' => 'AI-Powered Challenges', 'desc' => 'Daily challenges generated by Gemini AI based on your personal interests and goals.'],
                    ['icon' => '🔥', 'title' => 'Streak Tracking', 'desc' => 'Build momentum with daily streaks. Miss a day and start again — consistency is key.'],
                    ['icon' => '👑', 'title' => 'Leaderboard', 'desc' => 'Compete with friends and the global community. See how you rank on the leaderboard.'],
                    ['icon' => '👋', 'title' => 'Poke Friends', 'desc' => 'Motivate your friends by poking them when you complete a challenge. Social accountability works!'],
                    ['icon' => '🏆', 'title' => 'Achievements', 'desc' => 'Unlock achievements as you hit milestones. From your first challenge to 30-day streaks.'],
                    ['icon' => '📤', 'title' => 'Upload Proof', 'desc' => 'Submit proof of your work and get AI-scored feedback to improve your performance.']
                ] as $index => $f)
                    <div class="bg-white rounded-[2rem] p-8 shadow-sm border-2 border-slate-100 hover:border-primary hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group fade-in opacity-0" style="animation-delay: {{ ($index % 3) * 100 + 200 }}ms;">
                        <div class="w-16 h-16 bg-slate-50 rounded-[1.5rem] flex items-center justify-center text-4xl mb-6 group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300 shadow-inner">
                            {{ $f['icon'] }}
                        </div>
                        <h3 class="font-black text-xl text-slate-800 mb-3">{{ $f['title'] }}</h3>
                        <p class="text-slate-500 text-sm font-medium leading-relaxed">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-20 px-6 relative z-10 fade-in opacity-0 delay-300">
        <div class="max-w-4xl mx-auto text-center">
            <div class="bg-gradient-to-br from-[#C2A3FF] to-[#9B71EE] rounded-[3rem] p-12 md:p-16 shadow-2xl relative overflow-hidden">
                {{-- Decorative elements inside CTA --}}
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-2xl transform translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full blur-2xl transform -translate-x-1/2 translate-y-1/2"></div>

                <div class="relative z-10">
                    <h2 class="text-4xl md:text-5xl font-black mb-6 text-white tracking-tight drop-shadow-sm">Ready to start your journey?</h2>
                    <p class="text-white/90 mb-10 text-lg font-bold">Join thousands of people improving 1% every day.</p>
                    <a href="{{ route('web.register') }}"
                        class="inline-block bg-white text-slate-800 px-10 py-5 rounded-full font-black text-lg hover:bg-slate-50 hover:scale-105 active:scale-95 transition-all shadow-lg group">
                        Get Started Free
                        <span class="inline-block transform group-hover:translate-x-1 transition-transform">🚀</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t-2 border-slate-100 bg-white py-10 px-6 text-center text-sm font-bold text-slate-400 relative z-10">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 grayscale opacity-60">
                <div class="w-6 h-6 rounded-md bg-gradient-to-br from-[#C2A3FF] to-[#9B71EE] flex items-center justify-center">
                    <span class="text-white font-black text-[10px]">1%</span>
                </div>
                <span class="font-black text-slate-800 tracking-tight">OnePercent</span>
            </div>
            <p>© 2026 OnePercent. 1% Better Every Day. 🔥</p>
        </div>
    </footer>

</body>

</html>
