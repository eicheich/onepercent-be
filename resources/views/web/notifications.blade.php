@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('page-subtitle', "{{ $unreadCount }} unread messages")

@section('content')
<div class="max-w-2xl fade-in space-y-6">

    @if($notifications->isEmpty())
    {{-- Empty State (Bento Style) --}}
    <div class="bg-white rounded-3xl p-12 text-center shadow-[0_2px_10px_-4px_rgba(0,0,0,0.03)] border border-slate-100 flex flex-col items-center justify-center min-h-[400px]">
        <div class="w-20 h-20 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center mb-6 shadow-inner-sm">
            <span class="text-4xl filter drop-shadow-sm">🔔</span>
        </div>
        <h3 class="font-black text-slate-800 text-lg tracking-tight mb-2">No notifications yet</h3>
        <p class="text-sm font-medium text-slate-400 max-w-xs">
            Activities from you, system alerts, and your friends' momentum will appear here.
        </p>
    </div>
    @else

    @php
    $today     = now()->toDateString();
    $yesterday = now()->subDay()->toDateString();
    $todayNotifs     = $notifications->filter(fn($n) => $n['created_at']?->toDateString() === $today);
    $yesterdayNotifs = $notifications->filter(fn($n) => $n['created_at']?->toDateString() === $yesterday);
    $olderNotifs     = $notifications->filter(fn($n) => $n['created_at']?->toDateString() < $yesterday);
    @endphp

    @foreach([
        ['label' => 'Today',     'items' => $todayNotifs],
        ['label' => 'Yesterday', 'items' => $yesterdayNotifs],
        ['label' => 'Earlier',   'items' => $olderNotifs],
    ] as $group)

    @if($group['items']->isNotEmpty())
    <div class="space-y-4">
        {{-- Section Label --}}
        <div class="flex items-center gap-3">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-100 px-3 py-1 rounded-md">
                {{ $group['label'] }}
            </h3>
            <div class="flex-1 h-px bg-gradient-to-r from-slate-100 to-transparent"></div>
        </div>

        {{-- Notifications List --}}
        <div class="space-y-3">
            @foreach($group['items'] as $notif)
            <div class="group bg-white rounded-2xl p-4 shadow-sm border transition-all duration-300 hover:shadow-md flex items-start gap-4 relative overflow-hidden
                        {{ is_null($notif['read_at'])
                           ? 'border-[#B28CFF]/40 bg-slate-50/30'
                           : 'border-slate-100 hover:border-slate-200' }}">

                {{-- Left Accent Line for Unread --}}
                @if(is_null($notif['read_at']))
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#9261F3]"></div>
                @endif

                {{-- Icon / Avatar --}}
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0 border border-slate-100 shadow-inner-sm transition-transform duration-300 group-hover:scale-105
                            {{ match($notif['type']) {
                                'follow', 'follow_back' => 'bg-blue-50 text-blue-500',
                                'poke'                  => 'bg-orange-50 text-orange-500',
                                'achievement'           => 'bg-amber-50 text-amber-500',
                                'friend_completed'      => 'bg-emerald-50 text-emerald-500',
                                default                 => 'bg-[#9261F3]/10 text-[#9261F3]'
                            } }}">
                    @php $avatar = $notif['actor_avatar'] ?? null @endphp
                    @if($avatar && str_starts_with($avatar, 'http'))
                        <img src="{{ $avatar }}" referrerpolicy="no-referrer" class="w-full h-full rounded-2xl object-cover p-0.5 bg-white">
                    @elseif($avatar && str_starts_with($avatar, 'data:image'))
                        <img src="{{ $avatar }}" class="w-full h-full rounded-2xl object-cover p-0.5 bg-white">
                    @else
                        {{ $notif['icon'] ?? '🔔' }}
                    @endif
                </div>

                {{-- Content Body --}}
                <div class="flex-1 min-w-0 py-0.5">
                    <p class="font-bold text-slate-800 text-sm tracking-tight {{ is_null($notif['read_at']) ? 'text-[#9261F3]' : '' }}">
                        {{ $notif['title'] }}
                    </p>

                    @if($notif['body'])
                        <p class="text-[13px] font-medium text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                            {{ $notif['body'] }}
                        </p>
                    @endif

                    <p class="text-[11px] font-bold text-slate-400 mt-2 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $notif['created_at']?->diffForHumans() }}
                    </p>
                </div>

                {{-- Unread Dot Indicator (Right Side) --}}
                @if(is_null($notif['read_at']))
                <div class="w-2.5 h-2.5 rounded-full bg-[#9261F3] flex-shrink-0 mt-2 shadow-[0_0_8px_rgba(146,97,243,0.5)]"></div>
                @endif

            </div>
            @endforeach
        </div>
    </div>
    @endif

    @endforeach
    @endif
</div>
@endsection
