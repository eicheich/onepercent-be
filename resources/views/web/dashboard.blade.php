@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Hi, ' . session('web_user.name') . '! 👋')
@section('page-subtitle', 'Start improving 1% today')

@section('content')
    <div class="space-y-6 fade-in pb-10">

        {{-- Stats row (Bento Floating Cards) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            {{-- Streak Card --}}
            <div
                class="bg-white rounded-[2rem] p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white hover:border-orange-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 flex items-center justify-between group cursor-default">
                <div>
                    <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1">Current Streak</p>
                    <p class="text-3xl font-black text-slate-800 flex items-center gap-1">
                        {{ $user->current_streak ?? 0 }}
                        <span class="text-xl mt-1"><img class="w-7 h-7" src="/img/Streak.png" alt="  STREAKKK"></span>
                    </p>
                </div>
                <div
                    class="w-14 h-14 bg-gradient-to-br from-orange-50 to-orange-100 rounded-[1.2rem] flex items-center justify-center text-3xl shadow-sm border border-orange-200/50 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                    <img width="50" height="50" src="/img/onboard2.png" alt="  STREAKKK">
                </div>
            </div>

            {{-- Completed Card --}}
            <div
                class="bg-white rounded-[2rem] p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white hover:border-[#EAE1FF] hover:-translate-y-1 hover:shadow-lg transition-all duration-300 flex items-center justify-between group cursor-default">
                <div>
                    <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1">Total Completed</p>
                    <p class="text-3xl font-black text-slate-800">{{ $totalCompleted }}</p>
                </div>
                <div
                    class="w-14 h-14 bg-gradient-to-br from-[#F3EFFF] to-[#EAE1FF] rounded-[1.2rem] flex items-center justify-center text-3xl shadow-sm border border-[#B28CFF]/20 group-hover:scale-110 group-hover:-rotate-6 transition-all duration-300">
                    <img src="/img/image_home_banner.png" alt="  tag">
                </div>
            </div>

            {{-- Longest Streak Card --}}
            <div
                class="bg-white rounded-[2rem] p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white hover:border-amber-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 flex items-center justify-between group cursor-default">
                <div>
                    <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1">Longest Streak</p>
                    <p class="text-3xl font-black text-slate-800">{{ $user->longest_streak ?? 0 }}</p>
                </div>
                <div
                    class="w-14 h-14 bg-gradient-to-br from-amber-50 to-amber-100 rounded-[1.2rem] flex items-center justify-center text-3xl shadow-sm border border-amber-200/50 group-hover:scale-110 group-hover:rotate-12 transition-all duration-300">
                    <img src="/img/img_challenge_mascot.png" alt="  crown">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left column --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Weekly tracker --}}
                <div
                    class="bg-white rounded-[2.5rem] p-6 md:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white relative overflow-hidden">
                    <h3 class="font-extrabold text-slate-800 mb-6 text-lg tracking-tight">Your Progress This Week</h3>
                    <div class="flex justify-between gap-2">
                        @foreach ($weeklyTracker as $day)
                            @php
                                $isCompleted = $day['is_completed'];
                                $isToday = $day['date'] === now()->toDateString();
                                $isPast = $day['date'] < now()->toDateString();
                            @endphp

                            <div class="flex-1 flex flex-col items-center gap-3">
                                <div
                                    class="w-full max-w-[4rem] text-black aspect-[1/1.4] rounded-[1.5rem] flex flex-col items-center justify-center transition-all duration-300 {{ $isCompleted
                                        ? 'bg-gradient-to-b from-[#C7AFFF] to-[#B28CFF] text-white shadow-md shadow-[#B28CFF]/30 hover:-translate-y-1'
                                        : ($isToday
                                            ? 'bg-white border-[3px] border-[#B28CFF] text-black shadow-sm hover:-translate-y-1'
                                            : ($isPast
                                                ? 'bg-[#ffffff] border-[3px] border-[#ff4800] text-black shadow-sm opacity-80'
                                                : 'bg-slate-50 text-slate-300 border border-slate-100')) }}">
                                    <span class="text-xl md:text-2xl mb-1 drop-shadow-sm">
                                        @if ($isCompleted)
                                            <img class="w-7 h-7" src="/img/Streak.png" alt="STREAK">
                                        @elseif ($isPast)
                                            <img src="/img/ic_day_missed.png" alt="missed day">
                                        @elseif ($isToday)
                                            <img src="/img/ic_day_done.png" alt="done day">
                                        @else
                                            <div class="w-2.5 h-2.5 rounded-full bg-slate-200"></div>
                                        @endif
                                    </span>
                                    <span class="text-[10px] md:text-xs font-black uppercase tracking-wider">
                                        {{ substr($day['day'], 0, 3) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Today Challenge Card --}}
                <div
                    class="bg-white rounded-[2.5rem] p-6 md:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white relative overflow-hidden group">
                    {{-- Dekorasi Floating Blobs Pastel --}}
                    <div
                        class="absolute -top-10 -right-10 w-40 h-40 bg-orange-50 rounded-full blur-2xl opacity-70 group-hover:scale-110 transition-transform duration-700">
                    </div>
                    <div
                        class="absolute -bottom-10 right-20 w-32 h-32 bg-[#F3EFFF] rounded-full blur-xl opacity-80 group-hover:translate-x-4 transition-transform duration-700">
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-6">
                            <div class="bg-slate-100 px-3 py-1.5 rounded-xl flex items-center gap-2">
                                <span class="text-lg">✨</span>
                                <span class="text-sm font-extrabold text-slate-700 uppercase tracking-widest">Today's
                                    Mission</span>
                            </div>
                        </div>

                        @if ($todayChallenge && $todayChallenge->challenge)
                            <div class="flex flex-col gap-4">
                                <div>
                                    <h2 class="text-3xl font-black text-slate-800 mb-3 leading-tight tracking-tight">
                                        {{ $todayChallenge->challenge->title }}
                                    </h2>

                                    <div class="flex flex-wrap gap-2 mb-4">
                                        <span
                                            class="bg-[#F3EFFF] text-[#9261F3] text-[11px] font-extrabold px-3 py-1.5 rounded-full uppercase tracking-wider border border-[#EAE1FF]">
                                            {{ implode(' · ', array_map('ucfirst', $todayChallenge->challenge->tags ?? [])) }}
                                        </span>
                                        <span
                                            class="bg-orange-50 text-orange-500 text-[11px] font-extrabold px-3 py-1.5 rounded-full uppercase tracking-wider border border-orange-100">
                                            ⏱ {{ $todayChallenge->challenge->estimated_minutes }} mins
                                        </span>
                                    </div>

                                    <div
                                        class="bg-slate-50/80 backdrop-blur-sm border border-slate-100 rounded-[1.5rem] p-6 mb-6">
                                        <p class="text-sm font-medium text-slate-600 leading-relaxed">
                                            {{ $todayChallenge->challenge->content }}
                                        </p>
                                    </div>

                                    @if ($todayChallenge->is_completed)
                                        <div
                                            class="bg-emerald-50 border border-emerald-100 rounded-[1.5rem] p-5 flex items-center justify-center gap-3">
                                            <span class="text-3xl animate-bounce">🎉</span>
                                            <p class="text-emerald-600 font-black text-lg tracking-tight">Mission
                                                Accomplished!</p>
                                        </div>
                                    @else
                                        <a href="{{ route('web.challenge') }}"
                                            class="w-full md:w-max inline-flex justify-center items-center gap-2 bg-slate-800 text-white px-8 py-4 rounded-full font-extrabold text-base hover:bg-slate-700 hover:shadow-lg hover:shadow-slate-800/20 active:scale-95 transition-all">
                                            Start Challenge <span class="text-xl">🚀</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-center py-10">
                                <div class="flex justify-center mb-6 animate-[pulse_3s_ease-in-out_infinite]">
                                    <img src="/img/img_challenge_mascot.png" alt="Challenge Mascot"
                                        class="w-32 md:w-40 h-auto drop-shadow-lg">
                                </div>

                                <h3 class="text-2xl font-black text-slate-800 mb-2 tracking-tight">Ready for a challenge?
                                </h3>
                                <p class="text-slate-500 text-sm font-medium mb-8">Generate your task today and keep the
                                    streak alive!</p>

                                <a href="{{ route('web.challenge') }}"
                                    class="inline-flex items-center gap-2 bg-gradient-to-r from-slate-800 to-slate-700 text-white px-8 py-4 rounded-[1.5rem] font-black text-base shadow-lg shadow-slate-800/20 hover:shadow-slate-800/40 hover:-translate-y-1 active:scale-95 transition-all duration-300">
                                    Generate Task ✨
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right column — Leaderboard (Glassmorphism Box) --}}
            <div class="space-y-6">
                <div
                    class="bg-gradient-to-br from-[#B28CFF] to-[#9261F3] rounded-[2.5rem] p-6 text-white shadow-lg shadow-[#B28CFF]/20 relative overflow-hidden flex flex-col h-full">
                    {{-- Aksen pattern/bintang transparan --}}
                    <div
                        class="absolute top-0 right-0 w-48 h-48 bg-white opacity-10 rounded-full blur-3xl translate-x-10 -translate-y-10">
                    </div>
                    <div class="absolute bottom-10 -left-10 text-6xl opacity-10 rotate-12">👑</div>

                    <h3 class="font-black text-2xl mb-6 flex items-center gap-2 relative z-10 tracking-tight">
                        Top 10 Players
                    </h3>

                    <div class="space-y-3 relative z-10 flex-1">
                        @foreach ($leaderboard as $i => $u)
                            <div
                                class="flex items-center gap-3 bg-white/20 backdrop-blur-md rounded-[1.5rem] p-3 border border-white/30 hover:bg-white/30 transition-all hover:scale-[1.02] cursor-default">

                                {{-- Rank / Mahkota --}}
                                <div class="w-8 text-center font-black flex justify-center">
                                    @if ($i == 0)
                                        <span class="text-2xl drop-shadow-md"><img src="/img/1st.png" alt="1st"></span>
                                    @elseif($i == 1)
                                        <span class="text-2xl drop-shadow-md"><img src="/img/2nd.png" alt="2nd"></span>
                                    @elseif($i == 2)
                                        <span class="text-2xl drop-shadow-md"><img src="/img/3rd.png" alt="3rd"></span>
                                    @else
                                        <span class="text-white/80 text-lg">{{ $i + 1 }}</span>
                                    @endif
                                </div>

                                {{-- Avatar Profil --}}
                                <div
                                    class="w-10 h-10 rounded-full bg-white text-[#9261F3] flex items-center justify-center text-sm font-black shadow-sm shrink-0">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>

                                <span
                                    class="flex-1 text-sm font-extrabold truncate drop-shadow-sm">{{ $u->name }}</span>

                                <div
                                    class="flex items-center gap-1.5 bg-white/30 px-3 py-1.5 rounded-full backdrop-blur-sm border border-white/20">
                                    <span class="text-xs"><img class="w-7 h-7" src="/img/Streak.png" alt="  STREAKKK"></span>
                                    <span class="text-sm font-black drop-shadow-sm">{{ $u->current_streak ?? 0 }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <a href="{{ route('web.leaderboard') }}"
                        class="mt-6 block text-center bg-white text-[#9261F3] py-4 rounded-full font-black text-sm uppercase tracking-widest hover:bg-slate-50 hover:shadow-lg hover:-translate-y-1 active:scale-95 transition-all shadow-sm relative z-10">
                        See All Rankings
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection
