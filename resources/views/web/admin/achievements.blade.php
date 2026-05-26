@extends('layouts.admin')
@section('title', 'Achievements')
@section('page-title', 'Achievements')
@section('page-subtitle', 'Achievement statistics across all users')

@section('content')
    <div class="space-y-6">

        {{-- Main Control Block --}}
        <div class="bg-white rounded-3xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.03)] border border-slate-100 p-6">

            {{-- Header Overview --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 bg-slate-50/40 p-4 rounded-2xl border border-slate-100/50">
                <div>
                    <h3 class="font-black text-slate-800 text-base tracking-tight">Achievement Metrics Space</h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Global user gamification distribution indicators.</p>
                </div>
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 bg-white border border-slate-200 px-3 py-2 rounded-xl shadow-sm self-start sm:self-center">
                    Total Unlocked:
                    <strong class="text-[#9261F3] font-black bg-[#9261F3]/5 px-2 py-0.5 rounded-md ml-0.5">
                        {{ $totalUnlocked }}
                    </strong>
                </span>
            </div>

            {{-- Grid Cards Matrix --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @forelse ($stats as $stat)
                    <div class="bg-slate-50/40 border border-slate-100/80 rounded-3xl p-5 text-center transition-all duration-300 hover:bg-white hover:border-[#B28CFF]/30 hover:shadow-lg hover:shadow-[#9261F3]/5 group">

                        {{-- Floating Image Badge Container --}}
                        <div class="w-20 h-20 mx-auto mb-4 p-2 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">
                            <img src="{{ asset('img/' . $stat['image'] . '.png') }}"
                                 alt="{{ $stat['name'] }}"
                                 class="w-full h-full object-contain filter drop-shadow-sm">
                        </div>

                        {{-- Identity Info --}}
                        <h4 class="font-black text-slate-800 text-sm tracking-tight group-hover:text-[#9261F3] transition-colors duration-200 line-clamp-1">
                            {{ $stat['name'] }}
                        </h4>
                        <p class="text-xs font-medium text-slate-400 mt-1 mb-4 leading-normal min-h-[32px] line-clamp-2 px-1">
                            {{ $stat['description'] }}
                        </p>

                        {{-- Counter Info Box --}}
                        <div class="bg-white border border-slate-100/80 rounded-2xl py-3 transition-colors duration-200 group-hover:bg-slate-50/60 shadow-inner-sm">
                            <p class="text-2xl font-black text-[#9261F3] tracking-tight">
                                {{ $stat['count'] }}
                            </p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mt-0.5">
                                Users Unlocked
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-12 text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <h4 class="text-slate-700 font-bold">No Achievement Metrics Available</h4>
                        <p class="text-sm text-slate-400 mt-1">Configure active badges in your system initialization pipeline.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
