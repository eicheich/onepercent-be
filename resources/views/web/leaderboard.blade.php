@extends('layouts.app')
@section('title', 'Leaderboard')
@section('page-title', 'Leaderboard')
@section('page-subtitle', 'Top players this week')

@section('content')
    <div class="space-y-6 fade-in pb-10">

        {{-- Top 3 Podium (Glassmorphism & Gradient Style) --}}
        @if ($global->count() >= 3)
            <div
                class="bg-gradient-to-br from-[#B28CFF] to-[#9261F3] rounded-[2.5rem] pt-12 px-4 md:px-8 mb-2 relative overflow-hidden shadow-lg shadow-[#B28CFF]/20">
                {{-- Aksen Dekoratif & Blobs --}}
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl translate-x-10 -translate-y-10">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl -translate-x-10 translate-y-10">
                </div>

                <div class="absolute top-6 left-8 text-3xl opacity-60 drop-shadow-md">✨</div>
                <div class="absolute top-12 right-12 text-4xl opacity-30 mix-blend-overlay">👑</div>

                <div class="flex items-end justify-center gap-3 md:gap-5 relative z-10 mt-6">

                    {{-- 2nd Place --}}
                    <div class="text-center w-1/3 max-w-[110px] group">
                        <div
                            class="w-16 h-16 md:w-20 md:h-20 rounded-[1.5rem] bg-white/20 backdrop-blur-md flex items-center justify-center text-2xl md:text-3xl font-black text-white mx-auto mb-3 border-2 border-white/30 shadow-lg group-hover:-translate-y-1 transition-transform rotate-[-3deg]">
                            {{ strtoupper(substr($global[1]['name'], 0, 1)) }}
                        </div>
                        <p class="text-white font-black text-xs md:text-sm truncate px-1 drop-shadow-sm">
                            {{ explode(' ', $global[1]['name'])[0] }}</p>
                        <p
                            class="text-white/90 text-[10px] md:text-xs font-bold mb-3 flex items-center justify-center gap-1">
                            <span class="text-xs">🔥</span> {{ $global[1]['current_streak'] }}
                        </p>

                        {{-- Balok Podium --}}
                        <div
                            class="bg-white/10 backdrop-blur-md border-t-2 border-white/30 rounded-t-[1.5rem] h-24 md:h-28 flex items-center justify-center shadow-[inset_0_4px_20px_rgba(255,255,255,0.1)] relative overflow-hidden group-hover:bg-white/20 transition-colors">
                            <div class="absolute top-0 w-full h-3 bg-gradient-to-b from-white/20 to-transparent"></div>
                            <span class="text-white font-black text-3xl md:text-4xl opacity-50 drop-shadow-md">2</span>
                        </div>
                    </div>

                    {{-- 1st Place --}}
                    <div class="text-center w-1/3 max-w-[130px] -mb-2 group z-20">
                        {{-- Wrapper crown dibuat flex justify-center agar tepat di tengah --}}
                        <div class="flex justify-center mb-2 animate-bounce drop-shadow-lg">
                            <img src="/img/ic_crown.png" alt="Crown" class="w-10 md:w-12 h-auto object-contain">
                        </div>

                        <div
                            class="w-20 h-20 md:w-24 md:h-24 rounded-[1.8rem] bg-gradient-to-br from-[#FFD15C] to-[#FFB800] flex items-center justify-center text-3xl md:text-4xl font-black text-white mx-auto mb-3 border-4 border-white shadow-xl shadow-[#FFB800]/30 group-hover:-translate-y-1 transition-transform relative z-20">
                            {{ strtoupper(substr($global[0]['name'], 0, 1)) }}
                        </div>

                        <p class="text-white font-black text-sm md:text-base truncate px-1 drop-shadow-sm">
                            {{ explode(' ', $global[0]['name'])[0] }}</p>

                        <p
                            class="text-[#9261F3] bg-white px-3 py-1 rounded-full inline-flex items-center gap-1 text-[10px] md:text-xs font-black mb-3 shadow-md">
                            <span class="text-xs">🔥</span> {{ $global[0]['current_streak'] }}
                        </p>

                        {{-- Balok Podium --}}
                        <div
                            class="bg-white/20 backdrop-blur-md border-t-2 border-white/50 rounded-t-[2rem] h-32 md:h-40 flex items-start justify-center pt-6 shadow-[inset_0_4px_30px_rgba(255,255,255,0.2)] relative overflow-hidden z-10 group-hover:bg-white/30 transition-colors">
                            <div class="absolute top-0 w-full h-4 bg-gradient-to-b from-white/40 to-transparent"></div>
                            <span class="text-white font-black text-5xl md:text-6xl opacity-90 drop-shadow-lg">1</span>
                        </div>
                    </div>

                    {{-- 3rd Place --}}
                    <div class="text-center w-1/3 max-w-[110px] group">
                        <div
                            class="w-14 h-14 md:w-16 md:h-16 rounded-[1.2rem] bg-white/10 backdrop-blur-md flex items-center justify-center text-xl md:text-2xl font-black text-white mx-auto mb-3 border-2 border-white/20 shadow-lg group-hover:-translate-y-1 transition-transform rotate-[3deg]">
                            {{ strtoupper(substr($global[2]['name'], 0, 1)) }}
                        </div>
                        <p class="text-white font-black text-xs md:text-sm truncate px-1 drop-shadow-sm">
                            {{ explode(' ', $global[2]['name'])[0] }}</p>
                        <p
                            class="text-white/80 text-[10px] md:text-xs font-bold mb-3 flex items-center justify-center gap-1">
                            <span class="text-xs">🔥</span> {{ $global[2]['current_streak'] }}
                        </p>

                        {{-- Balok Podium --}}
                        <div
                            class="bg-white/5 backdrop-blur-sm border-t-2 border-white/10 rounded-t-[1.5rem] h-16 md:h-20 flex items-center justify-center shadow-[inset_0_4px_10px_rgba(255,255,255,0.05)] relative overflow-hidden group-hover:bg-white/10 transition-colors">
                            <div class="absolute top-0 w-full h-2 bg-gradient-to-b from-white/10 to-transparent"></div>
                            <span class="text-white font-black text-2xl md:text-3xl opacity-40">3</span>
                        </div>
                    </div>

                </div>
            </div>
        @endif

        {{-- My Rank Banner (Floating Card style) --}}
        @if ($myRank)
            <div
                class="bg-white border border-[#EAE1FF] rounded-[2rem] p-4 md:p-5 mb-8 flex items-center gap-4 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group hover:border-[#B28CFF] hover:shadow-md transition-all">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[#F3EFFF] to-transparent rounded-full -mr-10 -mt-10 opacity-70 group-hover:scale-110 transition-transform duration-500">
                </div>

                <span
                    class="text-3xl font-black text-[#9261F3] w-14 text-center relative z-10 tracking-tight">#{{ $myRank['rank'] }}</span>

                <div
                    class="w-14 h-14 rounded-[1.2rem] bg-gradient-to-br from-[#C7AFFF] to-[#B28CFF] flex items-center justify-center text-white font-black text-xl relative z-10 shadow-inner shadow-white/20 rotate-3">
                    <img src="{{ $myRank['avatar'] }}" class="w-full h-full rounded-[1rem] object-cover">
                </div>

                <div class="flex-1 relative z-10">
                    <p class="font-extrabold text-slate-800 text-base md:text-lg tracking-tight">Your Standing</p>
                    <p class="text-xs md:text-sm font-bold text-slate-500 mt-0.5 flex items-center gap-1.5">
                        <span class="text-sm">🔥</span> {{ $myRank['current_streak'] }} days streak
                    </p>
                </div>

                <div
                    class="hidden md:flex bg-[#F3EFFF] text-[#9261F3] border border-[#EAE1FF] px-5 py-2 rounded-full text-xs font-black uppercase tracking-wider relative z-10 items-center gap-2">
                    That's you! <span class="text-base animate-pulse">👋</span>
                </div>
            </div>
        @endif

        {{-- Full list --}}
        <div
            class="bg-white rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white p-5 md:p-8 relative overflow-hidden">
            <h3 class="font-black text-slate-800 mb-6 px-2 text-xl tracking-tight">Global Rankings</h3>

            <div class="space-y-3">
                @foreach ($global as $entry)
                    <div
                        class="flex items-center gap-4 px-5 py-4 rounded-[1.5rem] transition-all
                        {{ $entry['is_me'] ? 'bg-gradient-to-r from-[#F3EFFF]/50 to-transparent border border-[#EAE1FF] shadow-sm' : 'bg-slate-50 border border-slate-100 hover:border-slate-200 hover:bg-slate-100/50' }}">

                        {{-- Rank Number/Medal --}}
                        <span
                            class="w-8 text-center font-black text-lg
                            {{ $entry['rank'] == 1
                                ? 'text-[#FFB800] text-2xl drop-shadow-sm'
                                : ($entry['rank'] == 2
                                    ? 'text-slate-400 text-xl'
                                    : ($entry['rank'] == 3
                                        ? 'text-orange-400 text-xl'
                                        : 'text-slate-400')) }}">
                            {{ $entry['rank'] <= 3 ? ['🥇', '🥈', '🥉'][$entry['rank'] - 1] : str_pad($entry['rank'], 2, '0', STR_PAD_LEFT) }}
                        </span>

                        {{-- Avatar --}}
                        <div
                            class="w-12 h-12 rounded-[1rem] flex items-center justify-center font-black text-base shrink-0 shadow-inner
                            {{ $entry['is_me'] ? 'bg-gradient-to-br from-[#C7AFFF] to-[#B28CFF] text-white shadow-[#B28CFF]/20' : 'bg-white text-slate-400 border border-slate-200 shadow-slate-100' }}">
                            @if ($entry['avatar'] && str_starts_with($entry['avatar'], 'http'))
                                <img src="{{ $entry['avatar'] }}" class="w-full h-full rounded-[1rem] object-cover">
                            @else
                                {{ strtoupper(substr($entry['name'], 0, 1)) }}
                            @endif
                        </div>

                        {{-- Name --}}
                        <div class="flex-1 min-w-0">
                            <p
                                class="font-black truncate text-base md:text-lg tracking-tight {{ $entry['is_me'] ? 'text-[#9261F3]' : 'text-slate-700' }}">
                                {{ $entry['name'] }}
                                @if ($entry['is_me'])
                                    <span
                                        class="ml-2 text-[10px] bg-[#9261F3] text-white px-2 py-0.5 rounded-md uppercase tracking-wider align-middle hidden md:inline-block">You</span>
                                @endif
                            </p>
                        </div>

                        {{-- Streak --}}
                        <div
                            class="flex items-center gap-1.5 shrink-0 bg-white border border-slate-100 px-4 py-2 rounded-full shadow-sm">
                            <span class="text-sm">🔥</span>
                            <span class="font-black text-sm {{ $entry['is_me'] ? 'text-[#9261F3]' : 'text-slate-700' }}">
                                {{ $entry['current_streak'] }}
                            </span>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
