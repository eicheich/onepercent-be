@extends('layouts.admin')
@section('title', 'Challenges')
@section('page-title', 'Challenge Pool')
@section('page-subtitle', 'All generated challenges')

@section('content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
            <p class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            <p class="text-sm text-gray-400 mt-1">Total Challenges</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
            <p class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</p>
            <p class="text-sm text-gray-400 mt-1">Total Completions</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
            <p class="text-3xl font-bold text-primary">{{ $stats['today'] }}</p>
            <p class="text-sm text-gray-400 mt-1">Active Today</p>
        </div>
    </div>

    {{-- Challenge list --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">All Challenges</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($challenges as $c)
            <div class="p-6 hover:bg-gray-50 transition">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800 mb-1">{{ $c->title }}</h4>
                        <p class="text-sm text-gray-500 mb-3">{{ $c->content }}</p>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-400">⏱ {{ $c->estimated_minutes }} mins</span>
                            <div class="flex gap-1">
                                @foreach($c->tags ?? [] as $tag)
                                <span class="px-2 py-1 bg-primary-light text-primary rounded-lg text-xs font-medium">
                                    {{ ucfirst($tag) }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="text-right text-xs text-gray-400 whitespace-nowrap">
                        {{ $c->created_at?->format('d M Y') ?? '-' }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $challenges->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endsection
