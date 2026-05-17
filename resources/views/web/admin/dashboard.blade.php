@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')
@section('page-subtitle', 'Overview of OnePercent platform')

@section('content')
    <div class="space-y-6">

        {{-- Stats cards --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach ([['label' => 'Total Users', 'value' => $stats['total_users'], 'icon' => '👥', 'color' => 'bg-blue-50 text-blue-600'], ['label' => 'Completed Today', 'value' => $stats['active_today'], 'icon' => '⚡', 'color' => 'bg-green-50 text-green-600'], ['label' => 'Total Completions', 'value' => $stats['total_challenges'], 'icon' => '✅', 'color' => 'bg-purple-50 text-purple-600'], ['label' => 'Total Pokes', 'value' => $stats['total_pokes'], 'icon' => '👋', 'color' => 'bg-yellow-50 text-yellow-600'], ['label' => 'Avg Streak', 'value' => $stats['avg_streak'], 'icon' => '🔥', 'color' => 'bg-orange-50 text-orange-600'], ['label' => 'Top Streak', 'value' => $stats['top_streak'], 'icon' => '👑', 'color' => 'bg-red-50 text-red-600']] as $stat)
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs text-gray-500">{{ $stat['label'] }}</span>
                        <span class="text-lg">{{ $stat['icon'] }}</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Daily activity --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Daily Completions (7 days)</h3>
                <div class="flex items-end gap-2 h-32">
                    @php $maxCount = collect($dailyActivity)->max('count') ?: 1; @endphp
                    @foreach ($dailyActivity as $day)
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <span class="text-xs text-gray-500">{{ $day['count'] }}</span>
                            <div class="w-full bg-primary rounded-t-lg transition-all"
                                style="height: {{ max(4, ($day['count'] / $maxCount) * 100) }}px">
                            </div>
                            <span class="text-xs text-gray-400">{{ $day['day'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Top users --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Top 10 by Streak</h3>
                <div class="space-y-2">
                    @foreach ($topUsers as $i => $u)
                        <div class="flex items-center gap-3 py-2 border-b border-gray-50">
                            <span
                                class="w-6 text-sm font-bold
                        {{ $i < 3 ? 'text-primary' : 'text-gray-400' }}">
                                {{ $i + 1 }}
                            </span>
                            <div
                                class="w-8 h-8 rounded-full bg-primary-light flex items-center justify-center text-primary text-sm font-bold">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $u->name }}</p>
                                <p class="text-xs text-gray-400">{{ $u->email }}</p>
                            </div>
                            <div class="text-sm font-bold text-orange-500">
                                🔥 {{ $u->current_streak ?? 0 }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Recent users --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800">Recent Users</h3>
                <a href="{{ route('admin.users') }}" class="text-sm text-primary hover:text-primary-dark">View all →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-400 border-b border-gray-100">
                            <th class="pb-3 font-medium">Name</th>
                            <th class="pb-3 font-medium">Email</th>
                            <th class="pb-3 font-medium">Streak</th>
                            <th class="pb-3 font-medium">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($recentUsers as $u)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 font-medium text-gray-800">{{ $u->name }}</td>
                                <td class="py-3 text-gray-500">{{ $u->email }}</td>
                                <td class="py-3">
                                    <span class="text-orange-500 font-semibold">
                                        🔥 {{ $u->current_streak ?? 0 }}
                                    </span>
                                </td>
                                <td class="py-3 text-gray-400">
                                    {{ $u->created_at?->format('d M Y') ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
