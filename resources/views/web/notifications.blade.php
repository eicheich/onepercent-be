@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('page-subtitle', "You have {$unreadCount} unread")

@section('content')
<div class="max-w-2xl mx-auto space-y-8 fade-in pb-10">

    @if($notifications->isEmpty())
        {{-- Empty State --}}
        <div class="bg-white rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-[#EAE1FF] p-12 md:p-16 text-center relative overflow-hidden group flex flex-col items-center justify-center">
            {{-- Aksen Latar --}}
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-[#F3EFFF] to-transparent rounded-full -mr-10 -mt-10 opacity-70 pointer-events-none group-hover:scale-110 transition-transform duration-700"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-[#F3EFFF] to-transparent rounded-full -ml-10 -mb-10 opacity-50 pointer-events-none"></div>

            <div class="w-20 h-20 bg-gradient-to-br from-[#C7AFFF] to-[#B28CFF] rounded-[1.5rem] flex items-center justify-center mb-6 shadow-lg shadow-[#B28CFF]/20 rotate-12 group-hover:rotate-[-5deg] transition-transform duration-500 relative z-10">
                <span class="text-4xl animate-pulse">🔔</span>
            </div>

            <p class="text-slate-800 font-black text-2xl mb-2 tracking-tight relative z-10">All Caught Up!</p>
            <p class="text-slate-500 font-medium relative z-10">No new notifications right now.</p>
            <p class="text-xs text-slate-400 mt-3 font-medium bg-slate-50 px-4 py-2 rounded-full border border-slate-100 relative z-10">
                When friends poke you, it'll show here 👋
            </p>
        </div>
    @else

        @php
            $today     = now()->toDateString();
            $yesterday = now()->subDay()->toDateString();
            $todayNotifs     = $notifications->filter(fn($n) => $n['created_at']?->toDateString() === $today);
            $yesterdayNotifs = $notifications->filter(fn($n) => $n['created_at']?->toDateString() === $yesterday);
            $olderNotifs     = $notifications->filter(fn($n) => $n['created_at']?->toDateString() < $yesterday)
        @endphp

        {{-- Today --}}
        @if($todayNotifs->isNotEmpty())
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-5 px-2">
                    <h3 class="text-xs font-black text-[#9261F3] uppercase tracking-widest bg-[#F3EFFF] px-4 py-2 rounded-[1rem] shadow-sm">
                        Today
                    </h3>
                    <div class="h-px bg-gradient-to-r from-[#EAE1FF] to-transparent flex-1"></div>
                </div>
                <div class="space-y-3">
                    @foreach($todayNotifs as $notif)
                        @include('web.partials.notif-item', ['notif' => $notif])
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Yesterday --}}
        @if($yesterdayNotifs->isNotEmpty())
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-5 px-2">
                    <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest bg-slate-100 px-4 py-2 rounded-[1rem] border border-slate-200">
                        Yesterday
                    </h3>
                    <div class="h-px bg-gradient-to-r from-slate-200 to-transparent flex-1"></div>
                </div>
                <div class="space-y-3">
                    @foreach($yesterdayNotifs as $notif)
                        @include('web.partials.notif-item', ['notif' => $notif])
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Earlier --}}
        @if($olderNotifs->isNotEmpty())
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-5 px-2">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest px-2">
                        Earlier
                    </h3>
                    <div class="h-px bg-gradient-to-r from-slate-200 to-transparent flex-1"></div>
                </div>
                <div class="space-y-3">
                    @foreach($olderNotifs as $notif)
                        @include('web.partials.notif-item', ['notif' => $notif])
                    @endforeach
                </div>
            </div>
        @endif

    @endif
</div>
@endsection
