@extends('layouts.admin')
@section('title', 'Challenges')
@section('page-title', 'Challenge Pool')
@section('page-subtitle', 'All system generated challenges')

@section('content')
<div class="space-y-6">

    {{-- Stats Bento Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- Total Challenges --}}
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.03)] border border-slate-100 flex items-center gap-4 group hover:border-slate-200 transition-all duration-300">
            <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-500 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-black text-slate-800 tracking-tight">{{ $stats['total'] }}</p>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mt-0.5">Total Pool</p>
            </div>
        </div>

        {{-- Total Completions --}}
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.03)] border border-slate-100 flex items-center gap-4 group hover:border-emerald-100 transition-all duration-300">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-black text-emerald-600 tracking-tight">{{ $stats['completed'] }}</p>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mt-0.5">Total Completions</p>
            </div>
        </div>

        {{-- Active Today --}}
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.03)] border border-slate-100 flex items-center gap-4 group hover:border-[#B28CFF]/30 transition-all duration-300">
            <div class="w-12 h-12 rounded-2xl bg-[#9261F3]/5 flex items-center justify-center text-[#9261F3] group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-black text-[#9261F3] tracking-tight">{{ $stats['today'] }}</p>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mt-0.5">Active Live Today</p>
            </div>
        </div>
    </div>

    {{-- Challenge List Block --}}
    <div class="bg-white rounded-3xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.03)] border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/40 flex items-center justify-between">
            <h3 class="font-black text-slate-800 text-base tracking-tight">Repository Space</h3>
            <span class="text-xs font-bold text-slate-400 bg-white border border-slate-200 px-3 py-1 rounded-xl shadow-sm">
                Showing Paginated Items
            </span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($challenges as $c)
            <div class="p-6 hover:bg-slate-50/60 transition-all duration-200 relative group border-l-4 border-l-transparent hover:border-l-[#9261F3]">
                <div class="flex flex-col sm:flex-row items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-800 text-base tracking-tight group-hover:text-[#9261F3] transition-colors mb-1">
                            {{ $c->title }}
                        </h4>
                        <p class="text-sm font-medium text-slate-500 leading-relaxed max-w-4xl mb-4">
                            {{ $c->content }}
                        </p>

                        {{-- Meta Elements Row --}}
                        <div class="flex flex-wrap items-center gap-4">
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 bg-slate-100/80 px-2.5 py-1 rounded-lg">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $c->estimated_minutes }} mins allocation
                            </span>

                            @if(!empty($c->tags))
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($c->tags as $tag)
                                    <span class="px-2.5 py-0.5 bg-[#9261F3]/5 text-[#9261F3] border border-[#9261F3]/10 rounded-md text-[10px] font-black uppercase tracking-wider">
                                        #{{ ucfirst($tag) }}
                                    </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Timestamp View --}}
                    <div class="text-left sm:text-right shrink-0">
                        <span class="text-xs font-bold text-slate-400 bg-slate-50 border border-slate-100/80 px-2.5 py-1 rounded-lg">
                            {{ $c->created_at ? $c->created_at->format('d M Y') : '-' }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-3.586-3.586a2 2 0 00-2.828 0L16 14m-2-2l-3.414-3.414a2 2 0 00-2.828 0L4 12" />
                    </svg>
                </div>
                <h4 class="text-slate-700 font-bold">No Challenges Discovered</h4>
                <p class="text-sm text-slate-400 mt-1">The main automated challenge repository pool is empty.</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination Block --}}
        @if($challenges->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                {{ $challenges->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection
