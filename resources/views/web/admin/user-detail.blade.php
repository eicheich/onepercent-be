@extends('layouts.admin')
@section('title', 'User Detail')
@section('page-title', $user->name)
@section('page-subtitle', 'User profile and activity analysis')

@section('content')
    <div class="space-y-6">

        {{-- Sleek Back Button --}}
        <div>
            <a href="{{ route('admin.users') }}" class="inline-flex items-center gap-2 text-xs font-black text-slate-400 hover:text-slate-700 transition-colors uppercase tracking-wider bg-slate-50 hover:bg-slate-100 px-3 py-2 rounded-xl border border-slate-100">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l-7-7m7 7h18"/>
                </svg>
                Back to Users
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- Profile Card (Left Column) --}}
            <div class="lg:col-span-4 bg-white rounded-3xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.03)] border border-slate-100 p-6 text-center flex flex-col justify-between">
                <div>
                    {{-- Avatar Handling Manual --}}
                    <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow-md mx-auto flex items-center justify-center bg-slate-50 mb-4 transition-transform duration-300 hover:scale-105">
                        @if(isset($user->avatar) && $user->avatar && str_starts_with($user->avatar, 'http'))
                            <img src="{{ $user->avatar }}" referrerpolicy="no-referrer" class="w-full h-full object-cover">
                        @elseif(isset($user->avatar) && $user->avatar && str_starts_with($user->avatar, 'data:image'))
                            <img src="{{ $user->avatar }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center font-black text-3xl bg-gradient-to-br from-[#C7AFFF] to-[#B28CFF] text-white">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <h2 class="text-xl font-black text-slate-800 tracking-tight truncate">{{ $user->name }}</h2>
                    <p class="text-slate-400 text-xs font-medium truncate mt-0.5">{{ $user->email }}</p>

                    {{-- Badges Info --}}
                    <div class="mt-3 flex justify-center gap-2">
                        @if ($user->google_id)
                            <span class="px-2.5 py-0.5 bg-blue-50 border border-blue-100 text-blue-600 rounded-md text-[10px] font-bold uppercase tracking-wider">Google</span>
                        @else
                            <span class="px-2.5 py-0.5 bg-slate-100 border border-slate-200 text-slate-600 rounded-md text-[10px] font-bold uppercase tracking-wider">Email</span>
                        @endif
                        <span class="px-2.5 py-0.5 bg-slate-50 border border-slate-100 text-slate-500 rounded-md text-[10px] font-bold uppercase tracking-wider capitalize">
                            {{ $user->gender ?? 'N/A' }}
                        </span>
                    </div>

                    {{-- Quick Stats Matrix --}}
                    <div class="grid grid-cols-3 gap-2.5 mt-6">
                        <div class="bg-slate-50/60 border border-slate-100 rounded-xl p-3 text-center">
                            <p class="text-base font-black text-slate-800">{{ $followersCount }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Followers</p>
                        </div>
                        <div class="bg-slate-50/60 border border-slate-100 rounded-xl p-3 text-center">
                            <p class="text-base font-black text-slate-800">{{ $followingCount }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Following</p>
                        </div>
                        <div class="bg-orange-50/80 border border-orange-100/50 rounded-xl p-3 text-center">
                            <p class="text-base font-black text-orange-600">🔥 {{ $user->current_streak ?? 0 }}</p>
                            <p class="text-[9px] font-bold text-orange-400 uppercase tracking-wider mt-0.5">Streak</p>
                        </div>
                    </div>

                    {{-- Detail Lists --}}
                    <div class="mt-6 text-left text-sm space-y-1">
                        <div class="flex justify-between py-2.5 border-b border-slate-50 items-center">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">Longest Streak</span>
                            <span class="font-black text-slate-700 text-xs bg-slate-100 px-2 py-0.5 rounded">{{ $user->longest_streak ?? 0 }} days</span>
                        </div>
                        <div class="flex justify-between py-2.5 border-b border-slate-50 items-center">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">Last Active</span>
                            <span class="font-bold text-slate-700 text-xs">{{ $user->last_completed_date ?? 'Never' }}</span>
                        </div>
                        <div class="flex justify-between py-2.5 items-center">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">Joined</span>
                            <span class="font-bold text-slate-600 text-xs">
                                {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Danger Action Zone --}}
                <div class="mt-8 border-t border-slate-50 pt-4">
                    <form method="POST" action="{{ route('admin.users.delete', $user->getKey()) }}"
                        onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone!')">
                        @csrf
                        @method('DELETE')
                        <button class="w-full border border-rose-200 hover:bg-rose-500 text-rose-500 hover:text-white py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-300 shadow-sm hover:shadow-rose-100">
                            Delete Account Lifecycle
                        </button>
                    </form>
                </div>
            </div>

            {{-- Main Content Panels (Right Column) --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- Achievements Bento Section --}}
                <div class="bg-white rounded-3xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.03)] border border-slate-100 p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="font-black text-slate-800 text-base tracking-tight">Unlocked Achievements</h3>
                        <span class="text-xs font-black bg-[#9261F3]/10 text-[#9261F3] px-2.5 py-1 rounded-lg">
                            {{ $achievements->count() }} Badges
                        </span>
                    </div>

                    @if ($achievements->isEmpty())
                        <div class="p-6 border border-dashed border-slate-200 rounded-2xl text-center">
                            <p class="text-slate-400 text-xs font-medium">No system achievements unlocked yet.</p>
                        </div>
                    @else
                        <div class="flex flex-wrap gap-2.5">
                            @foreach ($achievements as $ach)
                                <div class="flex items-center gap-2.5 px-3 py-2 bg-slate-50 border border-slate-100/80 rounded-xl hover:border-[#B28CFF]/40 transition-all duration-200"
                                     title="{{ $ach->description }}">
                                    <span class="text-base filter drop-shadow-sm">{{ $ach->icon }}</span>
                                    <span class="font-bold text-slate-700 text-xs tracking-tight">{{ $ach->achievement_name }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Challenge History Bento Section --}}
                <div class="bg-white rounded-3xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.03)] border border-slate-100 p-6">
                    <h3 class="font-black text-slate-800 text-base tracking-tight mb-4">Recent Activity Logs <span class="text-xs font-medium text-slate-400 ml-1">(Last 14 days)</span></h3>

                    @if ($challenges->isEmpty())
                        <div class="p-8 border border-dashed border-slate-200 rounded-2xl text-center">
                            <p class="text-slate-400 text-xs font-medium">No challenge interaction metrics detected.</p>
                        </div>
                    @else
                        <div class="space-y-2.5 max-h-[380px] overflow-y-auto pr-1 custom-scrollbar">
                            @foreach ($challenges as $c)
                                <div class="flex items-center gap-4 p-3 rounded-2xl bg-slate-50/50 border border-slate-100/70 hover:bg-slate-50 transition-colors">
                                    {{-- Custom Rounded Date Box --}}
                                    <div class="text-center min-w-[46px] bg-white border border-slate-100 rounded-xl p-1.5 shadow-sm">
                                        <p class="font-black text-slate-800 text-sm leading-tight">
                                            {{ \Carbon\Carbon::parse($c->challenge_date)->format('d') }}
                                        </p>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-0.5">
                                            {{ \Carbon\Carbon::parse($c->challenge_date)->format('D') }}
                                        </p>
                                    </div>

                                    {{-- Core Info --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-slate-700 truncate tracking-tight">
                                            {{ $c->challenge?->title ?? 'Untitled Challenge Space' }}
                                        </p>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @forelse($c->challenge?->tags ?? [] as $tag)
                                                <span class="text-[9px] font-bold text-slate-400 bg-slate-100/80 px-1.5 py-0.5 rounded">
                                                    #{{ ucfirst($tag) }}
                                                </span>
                                            @empty
                                                <span class="text-[9px] font-medium text-slate-300">No category tags</span>
                                            @endforelse
                                        </div>
                                    </div>

                                    {{-- Metrics Score Badge --}}
                                    @if (isset($c->metadata['proof_score']))
                                        <span class="text-xs text-[#9261F3] font-black bg-[#9261F3]/5 border border-[#9261F3]/10 px-2 py-1 rounded-lg shrink-0">
                                            ⚡ {{ $c->metadata['proof_score'] }}/100
                                        </span>
                                    @endif

                                    {{-- Status Pill --}}
                                    <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-lg border shrink-0 {{ $c->is_completed ? 'bg-emerald-50 text-emerald-600 border-emerald-100/60' : 'bg-rose-50 text-rose-500 border-rose-100/60' }}">
                                        {{ $c->is_completed ? 'Done' : 'Missed' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Custom Micro Scrollbar Styling --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endsection
