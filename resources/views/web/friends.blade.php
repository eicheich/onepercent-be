@extends('layouts.app')
@section('title', 'Friends')
@section('page-title', 'Friends')
@section('page-subtitle', 'Search and manage your friends')

@section('content')
    <div class="space-y-6 md:space-y-8 fade-in pb-10">
           @if (session('successfol') || session('successfol'))
            <div
                class="mb-6 bg-white border-2 border-green-100 rounded-[1.5rem] p-4 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow duration-300 relative overflow-hidden group">
                {{-- Aksen Latar (opsional untuk efek manis) --}}
                <div
                    class="absolute -right-4 -top-4 w-16 h-16 bg-green-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500 pointer-events-none">
                </div>

                {{-- Icon Box --}}
                <div
                    class="w-12 h-12 rounded-[1rem] bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                {{-- Text Content --}}
                <div class="flex-1 relative z-10">
                    <h4 class="text-sm font-black text-slate-800">Success! </h4>
                    <p class="text-xs font-bold text-slate-500 mt-0.5">
                        {{ session('successfol') ?? session('successfol') }}
                    </p>
                </div>
            </div>
        @endif
        {{-- Alert Success --}}
        @if (session('successpoke') || session('successpoke'))
            <div
                class="mb-6 bg-white border-2 border-green-100 rounded-[1.5rem] p-4 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow duration-300 relative overflow-hidden group">
                {{-- Aksen Latar (opsional untuk efek manis) --}}
                <div
                    class="absolute -right-4 -top-4 w-16 h-16 bg-green-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500 pointer-events-none">
                </div>

                {{-- Icon Box --}}
                <div
                    class="w-12 h-12 rounded-[1rem] bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                {{-- Text Content --}}
                <div class="flex-1 relative z-10">
                    <h4 class="text-sm font-black text-slate-800">Success! </h4>
                    <p class="text-xs font-bold text-slate-500 mt-0.5">
                        {{ session('successpoke') ?? session('successpoke') }}
                    </p>
                </div>
            </div>
        @endif

        {{-- Alert Error --}}
        @if (session('errorpoke') || session('errorpoke'))
            <div
                class="mb-6 bg-white border-2 border-red-100 rounded-[1.5rem] p-4 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow duration-300 relative overflow-hidden group">
                {{-- Aksen Latar --}}
                <div
                    class="absolute -right-4 -top-4 w-16 h-16 bg-red-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500 pointer-events-none">
                </div>

                {{-- Icon Box --}}
                <div
                    class="w-12 h-12 rounded-[1rem] bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center shrink-0 animate-pulse">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>

                {{-- Text Content --}}
                <div class="flex-1 relative z-10">
                    <h4 class="text-sm font-black text-slate-800">Oops!, there was a problem</h4>
                    <p class="text-xs font-bold text-slate-500 mt-0.5">
                        {{ session('errorpoke') ?? session('errorpoke') }}
                    </p>
                </div>
            </div>
        @endif
        {{-- Search bar --}}
        <div
            class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-[#EAE1FF] p-4 md:p-6 relative overflow-hidden group">
            {{-- Aksen Blob Background --}}
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[#F3EFFF] to-transparent rounded-full -mr-10 -mt-10 opacity-50 pointer-events-none">
            </div>

            <form method="GET" action="{{ route('web.friends.search') }}"
                class="flex flex-col md:flex-row gap-3 relative z-10">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" name="q" value="{{ $query ?? '' }}"
                        placeholder="Search by name or email..."
                        class="w-full border-2 border-slate-100 rounded-[1.2rem] pl-11 pr-4 py-3.5 text-slate-700 placeholder-slate-400 font-medium
                               focus:outline-none focus:border-[#B28CFF] focus:ring-4 focus:ring-[#B28CFF]/10 transition-all">
                </div>
                <button type="submit"
                    class="bg-gradient-to-r from-[#B28CFF] to-[#9261F3] text-white px-8 py-3.5 rounded-[1.2rem]
                           font-bold shadow-lg shadow-[#9261F3]/20 hover:shadow-[#9261F3]/40 hover:-translate-y-0.5 transition-all">
                    Search
                </button>
            </form>
        </div>

        {{-- Search results --}}
        @if (isset($query) && $query)
            <div
                class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-[#EAE1FF] overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-black text-slate-800 text-xl tracking-tight">
                        Results for "<span class="text-[#9261F3]">{{ $query }}</span>"
                    </h3>
                    <span class="bg-[#F3EFFF] text-[#9261F3] px-3 py-1 rounded-full text-xs font-bold">
                        {{ $users->count() }} found
                    </span>
                </div>

                @if ($users->isEmpty())
                    <div class="p-16 text-center flex flex-col items-center justify-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                            <span class="text-4xl opacity-50">🔍</span>
                        </div>
                        <p class="text-slate-500 font-medium text-lg">No users found for "{{ $query }}"</p>
                        <p class="text-slate-400 text-sm mt-1">Try searching with a different name or email.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-50">
                        @foreach ($users as $u)
                            <div class="flex items-center gap-4 px-6 py-4 hover:bg-[#F3EFFF]/30 transition-colors">
                                {{-- Avatar --}}
                                <div
                                    class="w-12 h-12 rounded-[1rem] flex items-center justify-center font-black text-base flex-shrink-0 shadow-inner
                                    {{ in_array((string) $u->getKey(), $following ?? [])
                                        ? 'bg-gradient-to-br from-[#C7AFFF] to-[#B28CFF] text-white shadow-[#B28CFF]/20'
                                        : 'bg-white text-slate-400 border border-slate-200' }}">
                                    @if ($u->avatar && str_starts_with($u->avatar, 'http'))
                                        <img src="{{ $u->avatar }}" class="w-full h-full rounded-[1rem] object-cover">
                                    @else
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="font-black text-slate-800 text-base truncate tracking-tight">
                                        {{ $u->name }}</p>
                                    <p class="text-sm text-slate-400 truncate font-medium">{{ $u->email }}</p>
                                </div>

                                {{-- Streak --}}
                                <div
                                    class="hidden md:flex items-center gap-1.5 shrink-0 bg-white border border-slate-100 px-3 py-1.5 rounded-full shadow-sm">
                                    <span class="text-sm">🔥</span>
                                    <span class="font-black text-sm text-[#9261F3]">
                                        {{ $u->current_streak ?? 0 }}
                                    </span>
                                </div>

                                {{-- Follow/Unfollow --}}
                                <div class="shrink-0 ml-2">
                                    @if (in_array((string) $u->getKey(), $following ?? []))
                                        <form method="POST" action="{{ route('web.friends.unfollow', $u->getKey()) }}">
                                            @csrf
                                            <button
                                                class="px-5 py-2.5 border-2 border-[#EAE1FF] bg-slate-50 text-slate-500 rounded-[1rem] text-sm font-bold hover:border-red-200 hover:bg-red-50 hover:text-red-500 transition-all">
                                                Following
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('web.friends.follow', $u->getKey()) }}">
                                            @csrf
                                            <button
                                                class="px-5 py-2.5 bg-gradient-to-r from-[#B28CFF] to-[#9261F3] text-white rounded-[1rem] text-sm font-bold shadow-md shadow-[#9261F3]/20 hover:shadow-lg hover:-translate-y-0.5 transition-all">
                                                Follow
                                            </button>
                                        </form>
                                    @endif
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            {{-- Following / Followers tabs --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">

                {{-- Following Card --}}
                <div
                    class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-[#EAE1FF] overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                        <h3 class="font-black text-slate-800 text-xl tracking-tight">Following</h3>
                        <span
                            class="bg-[#F3EFFF] text-[#9261F3] px-3 py-1 rounded-full text-xs font-bold">{{ $followingUsers->count() }}</span>
                    </div>

                    @if ($followingUsers->isEmpty())
                        <div class="p-12 flex-1 flex flex-col items-center justify-center text-center">
                            <div
                                class="w-16 h-16 bg-[#F3EFFF] rounded-[1.5rem] flex items-center justify-center mb-4 rotate-3">
                                <span class="text-3xl"> <img src="/img/friend.png" alt=""></span>
                            </div>
                            <p class="text-slate-800 font-bold">Not following anyone</p>
                            <p class="text-slate-400 text-sm mt-1">Search for friends above to build your network.</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-50 flex-1 overflow-y-auto max-h-[500px] custom-scrollbar">
                            @foreach ($followingUsers as $u)
                                <div
                                    class="flex items-center gap-4 px-6 py-4 hover:bg-[#F3EFFF]/30 transition-colors group">
                                    <div
                                        class="w-11 h-11 rounded-[1rem] bg-gradient-to-br from-[#C7AFFF] to-[#B28CFF] flex items-center justify-center text-white font-black text-sm shadow-inner shrink-0">
                                        @if ($u->avatar && str_starts_with($u->avatar, 'http'))
                                            <img src="{{ $u->avatar }}"
                                                class="w-full h-full rounded-[1rem] object-cover">
                                        @else
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-slate-800 text-base truncate">{{ $u->name }}</p>
                                        <p class="text-xs font-bold text-[#9261F3] mt-0.5 flex items-center gap-1">
                                            🔥 {{ $u->current_streak ?? 0 }} days
                                        </p>
                                    </div>
                                    <form method="POST" action="{{ route('web.friends.unfollow', $u->getKey()) }}">
                                        @csrf
                                        <button
                                            class="w-9 h-9 flex items-center justify-center rounded-full border border-slate-200 text-slate-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-all opacity-100 lg:opacity-0 group-hover:opacity-100"
                                            title="Unfollow">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('web.challenge.poke', $u->getKey()) }}"
                                        class="ml-2">
                                        @csrf
                                        <button
                                            class="text-xs bg-orange-100 text-orange-500 px-3 py-1.5
                                                  rounded-lg hover:bg-orange-500 hover:text-white transition"
                                            title="Poke friend">
                                            👋 Poke
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Followers Card --}}
                <div
                    class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-[#EAE1FF] overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                        <h3 class="font-black text-slate-800 text-xl tracking-tight">Followers</h3>
                        <span
                            class="bg-[#F3EFFF] text-[#9261F3] px-3 py-1 rounded-full text-xs font-bold">{{ $followerUsers->count() }}</span>
                    </div>

                    @if ($followerUsers->isEmpty())
                        <div class="p-12 flex-1 flex flex-col items-center justify-center text-center">
                            <div
                                class="w-16 h-16 bg-[#F3EFFF] rounded-[1.5rem] flex items-center justify-center mb-4 -rotate-3">
                                <span class="text-3xl"><img src="/img/mutual.png"  alt=""></span>
                            </div>
                            <p class="text-slate-800 font-bold">No followers yet</p>
                            <p class="text-slate-400 text-sm mt-1">Keep up your streak to get noticed!</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-50 flex-1 overflow-y-auto max-h-[500px] custom-scrollbar">
                            @foreach ($followerUsers as $u)
                                <div class="flex items-center gap-4 px-6 py-4 hover:bg-[#F3EFFF]/30 transition-colors">
                                    <div
                                        class="w-11 h-11 rounded-[1rem] bg-slate-100 border border-slate-200 flex items-center justify-center text-[#9261F3] font-black text-sm shrink-0">
                                        @if ($u->avatar && str_starts_with($u->avatar, 'http'))
                                            <img src="{{ $u->avatar }}"
                                                class="w-full h-full rounded-[1rem] object-cover">
                                        @else
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-slate-800 text-base truncate">{{ $u->name }}</p>
                                        <p class="text-xs font-bold text-[#9261F3] mt-0.5 flex items-center gap-1">
                                            🔥 {{ $u->current_streak ?? 0 }} days
                                        </p>
                                    </div>

                                    {{-- Status / Action --}}
                                    <div class="shrink-0 ml-2">
                                        @if (!in_array((string) $u->getKey(), $following ?? []))
                                            <form method="POST"
                                                action="{{ route('web.friends.follow', $u->getKey()) }}">
                                                @csrf
                                                <button
                                                    class="text-xs bg-[#F3EFFF] text-[#9261F3] px-4 py-2 rounded-[0.8rem] font-bold hover:bg-[#9261F3] hover:text-white transition-all shadow-sm">
                                                    Follow back
                                                </button>
                                            </form>
                                        @else
                                            <span
                                                class="bg-green-50 text-green-500 border border-green-200 px-3 py-1.5 rounded-[0.8rem] text-xs font-bold flex items-center gap-1.5">

                                                Mutual
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        @endif

    </div>

    {{-- Tambahan CSS untuk custom scrollbar yang estetik (Bisa diletakkan di app.css) --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #EAE1FF;
            border-radius: 20px;
        }

        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background-color: #C7AFFF;
        }
    </style>
@endsection
