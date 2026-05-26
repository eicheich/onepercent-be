@extends('layouts.app')
@section('title', 'Leaderboard')
@section('page-title', 'Leaderboard')
@section('page-subtitle', 'Top players this week')

@section('content')
    <div class="space-y-6 fade-in pb-10">

        {{-- Top 3 Podium (Glassmorphism & Gradient Style) --}}
        @if ($global->count() >= 3)
            @php
                $first = $global[0];
                $second = $global[1];
                $third = $global[2];
            @endphp
            <div class="bg-gradient-to-br from-[#B28CFF] to-[#9261F3] rounded-[2.5rem] pt-12 px-4 md:px-8 mb-2 relative overflow-hidden shadow-xl shadow-[#9261F3]/15 group">
                {{-- Aksen Dekoratif & Blobs --}}
                <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl translate-x-10 -translate-y-10 transition-transform duration-700 group-hover:scale-110"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl -translate-x-10 translate-y-10 transition-transform duration-700 group-hover:scale-110"></div>

                <div class="absolute bottom-10 left-8 text-3xl opacity-40 drop-shadow-md pointer-events-none"><img src="{{ asset('img/mutual.png') }}" alt="" class=" h-auto"></div>
                <div class="absolute top-12 right-12 text-4xl opacity-20 mix-blend-overlay pointer-events-none"><img src="{{ asset('img/mutual.png') }}" alt="" class=" h-auto"></div>

                <div class="flex items-end justify-center gap-3 md:gap-6 relative z-10 mt-6">

                    {{-- 2nd Place --}}
                    <div class="text-center w-1/3 max-w-[115px] group/podium">
                        {{-- Avatar 2nd Place dengan handler lengkap --}}
                        <div class="w-16 h-16 md:w-20 md:h-20 rounded-full overflow-hidden mx-auto mb-3 border-4 border-white/40 shadow-lg group-hover/podium:-translate-y-1 transition-transform duration-300 rotate-[-3deg] flex items-center justify-center bg-white/20 backdrop-blur-md">
                            @if(isset($second['avatar']) && $second['avatar'] && str_starts_with($second['avatar'], 'http'))
                                <img src="{{ $second['avatar'] }}" referrerpolicy="no-referrer" class="w-full h-full object-cover">
                            @elseif(isset($second['avatar']) && $second['avatar'] && str_starts_with($second['avatar'], 'data:image'))
                                <img src="{{ $second['avatar'] }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-white/10 flex items-center justify-center text-white font-black text-xl md:text-2xl">
                                    {{ strtoupper(substr($second['name'], 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <p class="text-white font-black text-xs md:text-sm truncate px-1 drop-shadow-sm tracking-tight">
                            {{ explode(' ', $second['name'])[0] }}
                        </p>
                        <p class="text-white/90 text-[10px] md:text-xs font-bold mb-3 flex items-center justify-center gap-1 bg-white/10 backdrop-blur-sm rounded-full py-0.5 px-2 w-max mx-auto mt-1">
                            <span>🔥</span> {{ $second['current_streak'] }}
                        </p>

                        {{-- Balok Podium 2 --}}
                        <div class="bg-white/10 backdrop-blur-md border-t-2 border-white/30 rounded-t-[1.5rem] h-24 md:h-28 flex items-center justify-center shadow-[inset_0_4px_20px_rgba(255,255,255,0.1)] relative overflow-hidden group-hover/podium:bg-white/20 transition-all duration-300">
                            <div class="absolute top-0 w-full h-3 bg-gradient-to-b from-white/20 to-transparent"></div>
                            <span class="text-white font-black text-3xl md:text-4xl opacity-50 drop-shadow-md tracking-tight">2</span>
                        </div>
                    </div>

                    {{-- 1st Place --}}
                    <div class="text-center w-1/3 max-w-[135px] -mb-2 group/podium z-20">
                        <div class="flex justify-center mb-2 animate-bounce drop-shadow-lg duration-1000">
                            <img src="{{ asset('img/ic_crown.png') }}" alt="Crown" class="w-10 md:w-12 h-auto object-contain">
                        </div>

                        {{-- Avatar 1st Place dengan handler lengkap --}}
                        <div class="w-20 h-20 md:w-24 md:h-24 rounded-full overflow-hidden mx-auto mb-3 border-4 border-yellow-300 shadow-xl shadow-[#FFB800]/20 group-hover/podium:-translate-y-1 transition-transform duration-300 relative z-20 flex items-center justify-center bg-gradient-to-br from-[#FFD15C] to-[#FFB800]">
                            @if(isset($first['avatar']) && $first['avatar'] && str_starts_with($first['avatar'], 'http'))
                                <img src="{{ $first['avatar'] }}" referrerpolicy="no-referrer" class="w-full h-full object-cover">
                            @elseif(isset($first['avatar']) && $first['avatar'] && str_starts_with($first['avatar'], 'data:image'))
                                <img src="{{ $first['avatar'] }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-black/10 flex items-center justify-center text-white font-black text-2xl md:text-3xl">
                                    {{ strtoupper(substr($first['name'], 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <p class="text-white font-black text-sm md:text-base truncate px-1 drop-shadow-sm tracking-tight">
                            {{ explode(' ', $first['name'])[0] }}
                        </p>

                        <p class="text-[#9261F3] bg-white px-3 py-1 rounded-full inline-flex items-center gap-1 text-[10px] md:text-xs font-black mb-3 shadow-md mt-1">
                            <span>🔥</span> {{ $first['current_streak'] }}
                        </p>

                        {{-- Balok Podium 1 --}}
                        <div class="bg-white/20 backdrop-blur-md border-t-2 border-white/50 rounded-t-[2rem] h-32 md:h-40 flex items-start justify-center pt-6 shadow-[inset_0_4px_30px_rgba(255,255,255,0.2)] relative overflow-hidden z-10 group-hover/podium:bg-white/30 transition-all duration-300">
                            <div class="absolute top-0 w-full h-4 bg-gradient-to-b from-white/40 to-transparent"></div>
                            <span class="text-white font-black text-5xl md:text-6xl opacity-95 drop-shadow-lg tracking-tight">1</span>
                        </div>
                    </div>

                    {{-- 3rd Place --}}
                    <div class="text-center w-1/3 max-w-[115px] group/podium">
                        {{-- Avatar 3rd Place dengan handler lengkap --}}
                        <div class="w-14 h-14 md:w-16 md:h-16 rounded-full overflow-hidden mx-auto mb-3 border-4 border-white/20 shadow-lg group-hover/podium:-translate-y-1 transition-transform duration-300 rotate-[3deg] flex items-center justify-center bg-white/10 backdrop-blur-md">
                            @if(isset($third['avatar']) && $third['avatar'] && str_starts_with($third['avatar'], 'http'))
                                <img src="{{ $third['avatar'] }}" referrerpolicy="no-referrer" class="w-full h-full object-cover">
                            @elseif(isset($third['avatar']) && $third['avatar'] && str_starts_with($third['avatar'], 'data:image'))
                                <img src="{{ $third['avatar'] }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-white/5 flex items-center justify-center text-white font-black text-lg md:text-xl">
                                    {{ strtoupper(substr($third['name'], 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <p class="text-white font-black text-xs md:text-sm truncate px-1 drop-shadow-sm tracking-tight">
                            {{ explode(' ', $third['name'])[0] }}
                        </p>
                        <p class="text-white/80 text-[10px] md:text-xs font-bold mb-3 flex items-center justify-center gap-1 bg-white/5 backdrop-blur-sm rounded-full py-0.5 px-2 w-max mx-auto mt-1">
                            <span>🔥</span> {{ $third['current_streak'] }}
                        </p>

                        {{-- Balok Podium 3 --}}
                        <div class="bg-white/5 backdrop-blur-sm border-t-2 border-white/10 rounded-t-[1.5rem] h-16 md:h-20 flex items-center justify-center shadow-[inset_0_4px_10px_rgba(255,255,255,0.05)] relative overflow-hidden group-hover/podium:bg-white/10 transition-all duration-300">
                            <div class="absolute top-0 w-full h-2 bg-gradient-to-b from-white/10 to-transparent"></div>
                            <span class="text-white font-black text-2xl md:text-3xl opacity-40 tracking-tight">3</span>
                        </div>
                    </div>

                </div>
            </div>
        @endif

        {{-- My Rank Banner (Floating Card style) --}}
        @if ($myRank)
            <div class="bg-white border border-[#EAE1FF] rounded-[2rem] p-4 md:p-5 mb-8 flex items-center gap-4 shadow-[0_8px_30px_rgba(0,0,0,0.03)] relative overflow-hidden group hover:border-[#B28CFF] transition-all duration-300">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[#F3EFFF] to-transparent rounded-full -mr-10 -mt-10 opacity-70 group-hover:scale-110 transition-transform duration-500 pointer-events-none"></div>

                <span class="text-2xl md:text-3xl font-black text-[#9261F3] w-14 text-center relative z-10 tracking-tight">
                    #{{ $myRank['rank'] }}
                </span>

                {{-- Avatar My Rank dengan handler lengkap --}}
                <div class="w-12 h-12 md:w-14 md:h-14 rounded-full overflow-hidden flex-shrink-0 border-2 border-[#B28CFF] shadow-sm flex items-center justify-center relative z-10 bg-white">
                    @if ($myRank['avatar'] && str_starts_with($myRank['avatar'], 'http'))
                        <img src="{{ $myRank['avatar'] }}" referrerpolicy="no-referrer" class="w-full h-full object-cover">
                    @elseif ($myRank['avatar'] && str_starts_with($myRank['avatar'], 'data:image'))
                        <img src="{{ $myRank['avatar'] }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-[#C7AFFF] to-[#B28CFF] text-white flex items-center justify-center font-black text-base md:text-lg">
                            {{ strtoupper(substr($myRank['name'], 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="flex-1 relative z-10">
                    <p class="font-black text-slate-800 text-base md:text-lg tracking-tight">Your Standing</p>
                    <p class="text-xs md:text-sm font-bold text-slate-400 mt-0.5 flex items-center gap-1.5">
                        <span class="text-sm">🔥</span> {{ $myRank['current_streak'] }} days streak
                    </p>
                </div>

                <div class="hidden md:flex bg-[#F3EFFF] text-[#9261F3] border border-[#EAE1FF] px-5 py-2 rounded-xl text-xs font-black uppercase tracking-wider relative z-10 items-center gap-2">
                    That's you! <span class="text-sm animate-pulse">👋</span>
                </div>
            </div>
        @endif

        {{-- Full list Rankings --}}
        <div class="bg-white rounded-[2.5rem] shadow-[0_8px_30px_rgba(0,0,0,0.03)] border border-[#EAE1FF] p-5 md:p-8 relative overflow-hidden">
            <div class="mb-6 px-2">
                <h3 class="font-black text-slate-800 text-xl tracking-tight">Global Rankings</h3>
                <p class="text-xs text-slate-400 font-medium mt-0.5">The most consistent achievers this cycle</p>
            </div>

            <div class="space-y-3">
                @foreach ($global as $entry)
                    <div class="flex items-center gap-4 px-5 py-4 rounded-2xl transition-all duration-300 border
                        {{ $entry['is_me']
                            ? 'bg-gradient-to-r from-[#F3EFFF]/40 to-transparent border-[#B28CFF] shadow-[0_4px_20px_-4px_rgba(146,97,243,0.1)]'
                            : 'bg-white border-slate-100 hover:border-slate-200 hover:bg-slate-50/50 shadow-sm' }}">

                        {{-- Rank Number/Medal --}}
                        <div class="w-8 md:w-10 flex items-center justify-center shrink-0">
                            @if ($entry['rank'] == 1)
                                <img src="{{ asset('img/1st.png') }}" alt="1st Rank" class="w-7 md:w-8 h-auto object-contain drop-shadow-sm animate-[bounce_4s_ease-in-out_infinite]">
                            @elseif ($entry['rank'] == 2)
                                <img src="{{ asset('img/2nd.png') }}" alt="2nd Rank" class="w-6 md:w-7 h-auto object-contain drop-shadow-sm">
                            @elseif ($entry['rank'] == 3)
                                <img src="{{ asset('img/3rd.png') }}" alt="3rd Rank" class="w-6 md:w-7 h-auto object-contain drop-shadow-sm">
                            @else
                                <span class="font-black text-sm md:text-base text-slate-400 tracking-tight">
                                    {{ str_pad($entry['rank'], 2, '0', STR_PAD_LEFT) }}
                                </span>
                            @endif
                        </div>

                        {{-- Avatar di List Ranking dengan handler lengkap & bulat sempurna --}}
                        <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0 border shadow-sm flex items-center justify-center bg-white
                            {{ $entry['is_me'] ? 'border-[#B28CFF]' : 'border-slate-100' }}">
                            @if($entry['avatar'] && str_starts_with($entry['avatar'], 'http'))
                                <img src="{{ $entry['avatar'] }}" referrerpolicy="no-referrer" class="w-full h-full object-cover">
                            @elseif($entry['avatar'] && str_starts_with($entry['avatar'], 'data:image'))
                                <img src="{{ $entry['avatar'] }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center font-black text-xs
                                    {{ $entry['is_me'] ? 'bg-gradient-to-br from-[#C7AFFF] to-[#B28CFF] text-white' : 'bg-slate-100 text-slate-500' }}">
                                    {{ strtoupper(substr($entry['name'], 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        {{-- Name --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-black truncate text-sm md:text-base tracking-tight {{ $entry['is_me'] ? 'text-[#9261F3]' : 'text-slate-700' }}">
                                {{ $entry['name'] }}
                                @if ($entry['is_me'])
                                    <span class="ml-2 text-[9px] bg-[#9261F3] text-white px-2 py-0.5 rounded-md uppercase tracking-wider align-middle hidden md:inline-block font-black">You</span>
                                @endif
                            </p>
                        </div>

                        {{-- Streak Badge --}}
                        <div class="flex items-center gap-1.5 shrink-0 bg-white border border-slate-100 px-3.5 py-1.5 rounded-xl shadow-[0_2px_8px_-3px_rgba(0,0,0,0.05)]">
                            <span class="text-xs">🔥</span>
                            <span class="font-black text-xs md:text-sm {{ $entry['is_me'] ? 'text-[#9261F3]' : 'text-slate-700' }}">
                                {{ $entry['current_streak'] }}
                            </span>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
