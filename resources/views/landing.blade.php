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
                        // Palette Utama
                        primary: '#A06AF9',
                        'primary-dark': '#8344E8',
                        'primary-light': '#F1EBFE',

                        // Background Utama
                        background: '#FCFAFF',

                        // Text Palette Khusus
                        heading: '#1F143D', // Deep purple-black untuk judul
                        body: '#60567A',    // Muted purple-gray untuk deskripsi

                        // Warna Kartu Pastel
                        'card-purple': '#F5EDFF',
                        'card-orange': '#FFF2EA',
                        'card-blue': '#EBF4FF',
                        'card-yellow': '#FFF9E5'
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-delayed': 'float 6s ease-in-out 3s infinite',
                        'wiggle': 'wiggle 3s ease-in-out infinite',
                        'bounce-slow': 'bounce 3s infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0) rotate(0deg)' },
                            '50%': { transform: 'translateY(-20px) rotate(2deg)' },
                        },
                        wiggle: {
                            '0%, 100%': { transform: 'rotate(-3deg)' },
                            '50%': { transform: 'rotate(3deg)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes scaleUp {
            from { opacity: 0; transform: scale(0.95) translateY(30px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-pop { animation: scaleUp 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
    </style>
</head>

<body class="bg-background font-sans text-body antialiased selection:bg-primary selection:text-white overflow-x-hidden relative">

    {{-- Latar Belakang Ceria --}}
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-5%] w-[40rem] md:w-[60rem] h-[40rem] md:h-[60rem] bg-primary/10 rounded-full blur-[100px] animate-float"></div>
        <div class="absolute top-[20%] right-[-10%] w-[35rem] h-[35rem] bg-orange-400/10 rounded-full blur-[90px] animate-float-delayed"></div>
    </div>

    {{-- Navbar Besar & Melayang --}}
    <div class="fixed top-0 w-full z-50 px-6 py-6">
        <nav class="max-w-6xl mx-auto bg-white/80 backdrop-blur-2xl border-2 border-primary-light rounded-full px-8 py-4 flex items-center justify-between shadow-[0_10px_40px_rgba(160,106,249,0.08)] transition-all hover:shadow-[0_10px_40px_rgba(160,106,249,0.12)]">
            <div class="flex items-center gap-3 cursor-pointer group">
                <div class="bg-primary-light p-2 rounded-2xl group-hover:bg-primary/20 transition-colors">
                    <img src="/img/onboard1.png" alt="Logo" class="w-10 h-10 object-contain group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">
                </div>
                <span class="font-black text-2xl text-heading tracking-tight">OnePercent</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('web.login') }}" class="hidden sm:block text-body hover:text-heading text-lg font-black px-4 py-2 transition-colors">
                    Log In
                </a>
                <a href="{{ route('web.register') }}"
                    class="bg-heading text-white px-8 py-3.5 rounded-full text-lg font-black hover:bg-primary hover:text-white hover:-translate-y-1 hover:shadow-xl hover:shadow-primary/30 active:scale-95 transition-all duration-300">
                    Let's Go! 🔥
                </a>
            </div>
        </nav>
    </div>

    {{-- Hero Section (Besar, Lega, Playful) --}}
    <section class="pt-52 pb-24 px-6 relative z-10">
        <div class="max-w-5xl mx-auto text-center animate-pop">

            {{-- Badge --}}
            <div class="inline-flex items-center gap-3 bg-white border-2 border-primary-light px-6 py-3 rounded-full text-base font-black mb-8 shadow-sm hover:scale-105 transition-transform cursor-default">
                <img src="/img/Streak.png" alt="Streak" class="w-6 h-6 object-contain animate-bounce-slow">
                <span class="text-heading">Level up your daily habits</span>
            </div>

            <h1 class="text-6xl sm:text-7xl md:text-8xl font-black text-heading leading-[1.05] mb-8 tracking-tighter relative inline-block">
                Get <span class="text-primary">1% Better</span><br>
                Every Single Day.
            </h1>

            <p class="text-xl md:text-2xl text-body mb-14 max-w-3xl mx-auto leading-relaxed font-bold">
                Stop setting boring goals. Let Gemini AI generate fun daily quests, stack your streaks, and outplay your friends on the leaderboard.
            </p>
            <a href="{{ route('web.register') }}"
                class="inline-flex items-center justify-center gap-4 w-full sm:w-auto bg-heading text-white px-10 py-5 rounded-[2rem] font-black text-xl hover:bg-primary-dark hover:scale-105 hover:shadow-2xl hover:shadow-primary/20 active:scale-95 transition-all group">
                Start Playing Now
            </a>
        </div>
    </section>

    {{-- Fun Bento Grid Section --}}
    <section class="py-24 px-6 relative z-10">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20 animate-pop opacity-0 delay-100">
                <h2 class="text-5xl font-black text-heading mb-4 tracking-tight">How We Play</h2>
                <p class="text-body font-bold text-xl">Not your average to-do list.</p>
            </div>

            {{-- Grid yang lebih lega (Gap besar, Padding besar) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 md:gap-10">

                {{-- Card 1: AI Mascot (Warna Ungu Pastel, Span 2) --}}
                <div class="lg:col-span-2 bg-card-purple rounded-[3rem] p-10 md:p-14 border-4 border-white shadow-xl shadow-primary/10 hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary/20 transition-all duration-300 relative overflow-hidden group">
                    <div class="relative z-10 md:w-3/5">
                        <div class="bg-white w-20 h-20 rounded-3xl flex items-center justify-center mb-8 shadow-sm group-hover:rotate-6 transition-transform">
                            <img src="/img/logo_onepercent.png" alt="AI" class="w-12 h-12 object-contain">
                        </div>
                        <h3 class="font-black text-4xl text-heading mb-4 tracking-tight">AI Daily Quests</h3>
                        <p class="text-body text-xl font-medium leading-relaxed">
                            No boring routines. Gemini AI creates a unique, hyper-personalized challenge for you every single day based on your vibe.
                        </p>
                    </div>
                    {{-- Gambar Maskot Raksasa di Kanan --}}
                    <img src="/img/onboard2.png" alt="Mascot" class="w-64 md:w-80 absolute -bottom-10 -right-10 md:-right-4 group-hover:scale-110 transition-transform duration-500 origin-bottom-right">
                </div>

                {{-- Card 2: Streak (Warna Oranye Pastel) --}}
                <div class="bg-card-orange rounded-[3rem] p-10 md:p-14 border-4 border-white shadow-xl shadow-orange-500/10 hover:-translate-y-2 hover:shadow-2xl hover:shadow-orange-500/20 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="bg-white w-20 h-20 rounded-3xl flex items-center justify-center mb-8 shadow-sm group-hover:-rotate-6 transition-transform">
                            <img src="/img/Streak.png" alt="Streak" class="w-12 h-12 object-contain">
                        </div>
                        <h3 class="font-black text-3xl text-heading mb-4 tracking-tight">Don't Break It</h3>
                        <p class="text-body text-lg font-medium leading-relaxed mb-8">
                            Consistency is everything. Stack those daily wins and watch your fire grow.
                        </p>
                    </div>
                    {{-- Visual Check & Miss Besar --}}
                    <div class="flex gap-3 justify-center items-center bg-white/60 p-4 rounded-3xl backdrop-blur-sm">
                        <img src="/img/ic_day_done.png" alt="Done" class="w-14 h-14 object-contain hover:scale-110 transition-transform">
                        <img src="/img/ic_day_done.png" alt="Done" class="w-14 h-14 object-contain hover:scale-110 transition-transform">
                        <img src="/img/ic_day_missed.png" alt="Miss" class="w-14 h-14 object-contain opacity-80 hover:scale-110 transition-transform">
                    </div>
                </div>

                {{-- Card 3: Social/Friends (Warna Biru Pastel) --}}
                <div class="bg-card-blue rounded-[3rem] p-10 md:p-14 border-4 border-white shadow-xl shadow-blue-500/10 hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="bg-white w-20 h-20 rounded-3xl flex items-center justify-center mb-8 shadow-sm group-hover:rotate-12 transition-transform">
                            <img src="/img/friend.png" alt="Friends" class="w-12 h-12 object-contain">
                        </div>
                        <h3 class="font-black text-3xl text-heading mb-4 tracking-tight">Poke Friends</h3>
                        <p class="text-body text-lg font-medium leading-relaxed mb-8">
                            See your friends slacking off? Poke them to wake them up. Accountability made fun.
                        </p>
                    </div>
                    {{-- Tangan Poke Besar --}}
                    <div class="bg-white p-6 rounded-3xl shadow-sm text-center flex flex-col items-center gap-2">
                        <img src="/img/ic_hand.png" alt="Poke" class="w-16 h-16 object-contain animate-wiggle">
                        <span class="font-black text-heading text-lg">Poke Sent!</span>
                    </div>
                </div>

                {{-- Card 4: Podium (Warna Kuning Pastel, Span 2) --}}
                <div class="lg:col-span-2 bg-card-yellow rounded-[3rem] p-10 md:p-14 border-4 border-white shadow-xl shadow-yellow-500/10 hover:-translate-y-2 hover:shadow-2xl hover:shadow-yellow-500/20 transition-all duration-300 relative overflow-hidden group flex flex-col md:flex-row items-center gap-10">
                    <div class="relative z-10 md:w-1/2">
                        <div class="bg-white w-20 h-20 rounded-3xl flex items-center justify-center mb-8 shadow-sm group-hover:-rotate-6 transition-transform">
                            <img src="/img/ic_crown.png" alt="Crown" class="w-12 h-12 object-contain">
                        </div>
                        <h3 class="font-black text-4xl text-heading mb-4 tracking-tight">Climb the Ranks</h3>
                        <p class="text-body text-xl font-medium leading-relaxed">
                            Prove you're the most consistent. Rise up the global leaderboard and flex your 1st place medal.
                        </p>
                    </div>

                    {{-- Medali Raksasa --}}
                    <div class="md:w-1/2 flex items-center justify-center gap-4 md:gap-6 relative z-10 w-full mt-8 md:mt-0">
                        <img src="/img/2nd.png" alt="2nd" class="w-24 h-24 md:w-32 md:h-32 object-contain transform translate-y-8 hover:-translate-y-2 transition-transform">
                        <img src="/img/1st.png" alt="1st" class="w-32 h-32 md:w-44 md:h-44 object-contain z-20 animate-float drop-shadow-2xl">
                        <img src="/img/3rd.png" alt="3rd" class="w-20 h-20 md:w-28 md:h-28 object-contain transform translate-y-12 hover:-translate-y-2 transition-transform">
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- CTA Section Besar & Berani --}}
    <section class="py-24 px-6 relative z-10 animate-pop opacity-0 delay-300">
        <div class="max-w-6xl mx-auto text-center">
            <div class="bg-gradient-to-br from-primary to-primary-dark rounded-[4rem] p-16 md:p-24 shadow-2xl shadow-primary/30 relative overflow-hidden group">

                {{-- Aset dekoratif memantul di dalam CTA --}}
                <img src="/img/Streak.png" alt="Fire" class="absolute top-10 left-10 w-24 h-24 object-contain opacity-20 animate-wiggle">
                <img src="/img/task.png" alt="Task" class="absolute bottom-10 right-10 w-24 h-24 object-contain opacity-20 animate-bounce-slow">

                <div class="relative z-10 max-w-2xl mx-auto">
                    <h2 class="text-5xl md:text-7xl font-black mb-6 text-white tracking-tight leading-tight drop-shadow-md">
                        Ready to join the 1% club?
                    </h2>
                    <p class="text-white/90 mb-12 text-xl md:text-2xl font-bold">
                        Stop scrolling. Start doing. Get your first AI quest today.
                    </p>
                    <a href="{{ route('web.register') }}"
                        class="inline-flex items-center justify-center gap-3 bg-white text-heading px-12 py-6 rounded-[2rem] font-black text-2xl hover:bg-primary-light hover:scale-105 active:scale-95 transition-all shadow-xl">
                        Create Free Account
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer Lega --}}
    <footer class="bg-white py-12 px-6 text-base font-bold text-body relative z-10 mt-10">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6 border-t-4 border-primary-light pt-10">
            <div class="flex items-center gap-4 hover:scale-105 transition-transform cursor-pointer">
                <div class="bg-primary-light p-3 rounded-2xl">
                    <img src="/img/onboard1.png" alt="OnePercent" class="w-8 h-8 object-contain">
                </div>
                <span class="font-black text-2xl text-heading tracking-tight">OnePercent</span>
            </div>
            <p class="font-bold text-body text-lg flex items-center gap-2">
                © 2026 OnePercent. Keep grinding. <img src="/img/Streak.png" alt="Streak" class="w-5 h-5 object-contain">
            </p>
        </div>
    </footer>

</body>

</html>
