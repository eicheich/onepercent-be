@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')
@section('page-subtitle', 'Overview of OnePercent platform')

@section('content')
    <div class="flex flex-col gap-6 pb-10">

        {{-- Stats Cards Section --}}
        @php
            $dashboardStats = [
                [
                    'label' => 'Total Users',
                    'value' => $stats['total_users'],
                    'img' => 'teamwork.png',
                    'color' => 'bg-blue-50 text-blue-600 border-blue-100',
                ],
                [
                    'label' => 'Completed Today',
                    'value' => $stats['active_today'],
                    'img' => 'list.png',
                    'color' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                ],
                [
                    'label' => 'Total Completions',
                    'value' => $stats['total_challenges'],
                    'img' => 'task.png',
                    'color' => 'bg-purple-50 text-purple-600 border-purple-100',
                ],
                [
                    'label' => 'Total Pokes',
                    'value' => $stats['total_pokes'],
                    'img' => 'poke.png',
                    'color' => 'bg-amber-50 text-amber-600 border-amber-100',
                ],
                [
                    'label' => 'Avg Streak',
                    'value' => $stats['avg_streak'],
                    'img' => 'streak.png',
                    'color' => 'bg-orange-50 text-orange-600 border-orange-100',
                ],
                [
                    'label' => 'Top Streak',
                    'value' => $stats['top_streak'],
                    'img' => 'top.png',
                    'color' => 'bg-rose-50 text-rose-600 border-rose-100',
                ],
            ];
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 lg:gap-5">
            @foreach ($dashboardStats as $stat)
                <div
                    class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.03)] hover:shadow-[0_8px_30px_-4px_rgba(146,97,243,0.1)] hover:-translate-y-1 transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <span
                            class="text-[10px] font-black text-slate-400 uppercase tracking-wider">{{ $stat['label'] }}</span>

                        <div
                            class="w-14 h-14 rounded-xl {{ $stat['color'] }} border flex items-center justify-center p-2.5 transition-transform duration-300 group-hover:scale-110">
                            <img src="{{ asset('img/' . $stat['img']) }}" alt="{{ $stat['label'] }}"
                                class="w-full h-full object-contain drop-shadow-sm" onerror="this.style.display='none'">
                        </div>
                    </div>
                    <p class="text-2xl md:text-3xl font-black text-slate-800 tracking-tight">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Analytics Grid Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- Daily activity (Chart) --}}
            <div
                class="lg:col-span-8 bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.03)] border border-slate-100 p-6 flex flex-col justify-between">
                <div class="mb-6">
                    <h3 class="font-black text-slate-800 text-lg tracking-tight">Daily Completions</h3>
                    <p class="text-xs font-medium text-slate-400">Last 7 days performance overview</p>
                </div>

                <div class="flex items-end gap-3 h-48 pt-4 border-t border-slate-50">
                    @php $maxCount = collect($dailyActivity)->max('count') ?: 1; @endphp
                    @foreach ($dailyActivity as $day)
                        <div class="flex-1 flex flex-col items-center gap-2 group">
                            <span
                                class="text-xs font-black text-[#9261F3] opacity-0 group-hover:opacity-100 transition-all duration-200 -translate-y-1 group-hover:translate-y-0">
                                {{ $day['count'] }}
                            </span>
                            <div class="w-full max-w-[2.5rem] bg-gradient-to-t from-[#B28CFF] to-[#9261F3] rounded-t-xl transition-all duration-500 ease-out hover:brightness-105 relative overflow-hidden"
                                style="height: {{ max(8, ($day['count'] / $maxCount) * 100) }}%">
                                <div class="absolute inset-0 bg-white/10 hover:bg-transparent transition-colors"></div>
                            </div>
                            <span
                                class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $day['day'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Top Users Sidebar List --}}
            <div
                class="lg:col-span-4 bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.03)] border border-slate-100 p-6 flex flex-col">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-slate-800 text-lg tracking-tight">Top Streak</h3>
                        <p class="text-xs font-medium text-slate-400">Top 10 active users</p>
                    </div>
                    <div
                        class="w-8 h-8 rounded-full bg-orange-50 border border-orange-100 flex items-center justify-center p-1.5">
                        <img src="{{ asset('img/icon-crown.png') }}" alt="Top" class="w-full h-full object-contain"
                            onerror="this.style.display='none'">
                    </div>
                </div>

                <div class="space-y-1 pr-1 max-h-[260px] overflow-y-auto custom-scrollbar flex-1">
                    @foreach ($topUsers as $i => $u)
                        <div
                            class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100/70">
                            <span
                                class="w-5 text-xs font-black {{ $i < 3 ? 'text-[#9261F3]' : 'text-slate-300' }} tracking-tight">
                                {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                            </span>

                            {{-- Avatar Handling Top Users --}}
                            <div
                                class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0 border border-slate-100 shadow-sm flex items-center justify-center bg-white">
                                @if (isset($u->avatar) && $u->avatar && str_starts_with($u->avatar, 'http'))
                                    <img src="{{ $u->avatar }}" referrerpolicy="no-referrer"
                                        class="w-full h-full object-cover">
                                @elseif(isset($u->avatar) && $u->avatar && str_starts_with($u->avatar, 'data:image'))
                                    <img src="{{ $u->avatar }}" class="w-full h-full object-cover">
                                @else
                                    <div
                                        class="w-full h-full flex items-center justify-center font-black text-xs bg-gradient-to-br from-[#C7AFFF] to-[#B28CFF] text-white">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-700 truncate tracking-tight">{{ $u->name }}</p>
                                <p class="text-[10px] font-medium text-slate-400 truncate">{{ $u->email }}</p>
                            </div>

                            <div
                                class="flex items-center gap-1 px-2 py-1 bg-orange-50 rounded-lg border border-orange-100/50 shrink-0">
                                <span class="text-xs">🔥</span>
                                <span class="text-xs font-black text-orange-500">{{ $u->current_streak ?? 0 }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Recent Users Table Section --}}
        <div class="bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.03)] border border-slate-100 p-6">
            <div class="mb-5">
                <h3 class="font-black text-slate-800 text-lg tracking-tight">Recent Users</h3>
                <p class="text-xs font-medium text-slate-400">Latest platform registration lifecycle</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="border-b border-slate-100 text-slate-400 text-[10px] font-black uppercase tracking-wider">
                            <th class="py-3 px-4">User Details</th>
                            <th class="py-3 px-4">Current Streak</th>
                            <th class="py-3 px-4">Registration Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($recentUsers as $u)
                            <tr class="hover:bg-slate-50/70 transition-colors group">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">

                                        {{-- Avatar Handling Recent Users --}}
                                        <div
                                            class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0 border border-slate-100 shadow-sm flex items-center justify-center bg-white">
                                            @if (isset($u->avatar) && $u->avatar && str_starts_with($u->avatar, 'http'))
                                                <img src="{{ $u->avatar }}" referrerpolicy="no-referrer"
                                                    class="w-full h-full object-cover">
                                            @elseif(isset($u->avatar) && $u->avatar && str_starts_with($u->avatar, 'data:image'))
                                                <img src="{{ $u->avatar }}" class="w-full h-full object-cover">
                                            @else
                                                <div
                                                    class="w-full h-full flex items-center justify-center font-black text-sm bg-gradient-to-br from-[#C7AFFF] to-[#B28CFF] text-white">
                                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-700 text-sm tracking-tight truncate">
                                                {{ $u->name }}</p>
                                            <p class="text-xs text-slate-400 truncate">{{ $u->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-orange-50 border border-orange-100/30 text-orange-600 font-black text-xs">
                                        🔥 {{ $u->current_streak ?? 0 }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-400 font-medium text-xs">
                                    {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
