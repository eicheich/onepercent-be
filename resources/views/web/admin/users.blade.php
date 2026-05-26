@extends('layouts.admin')
@section('title', 'Manage Users')
@section('page-title', 'Users')
@section('page-subtitle', 'Manage all registered users')

@section('content')
    <div class="bg-white rounded-3xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.03)] border border-slate-100 overflow-hidden flex flex-col">

        {{-- Search Section --}}
        <div class="p-6 border-b border-slate-100 bg-slate-50/30">
            <form method="GET" action="{{ route('admin.users') }}" class="flex flex-wrap gap-3">
                <div class="relative flex-1 min-w-[250px]">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or email..."
                        class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm font-medium text-slate-700 focus:outline-none focus:border-[#9261F3] focus:ring-2 focus:ring-[#9261F3]/20 transition-all placeholder:text-slate-400 shadow-sm">
                </div>
                <button type="submit"
                    class="bg-gradient-to-r from-[#B28CFF] to-[#9261F3] text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-[#9261F3]/20 hover:shadow-[#9261F3]/40 hover:-translate-y-0.5 transition-all duration-300">
                    Search
                </button>
                @if ($search)
                    <a href="{{ route('admin.users') }}"
                        class="flex items-center px-4 py-2.5 border border-slate-200 bg-white rounded-xl text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Table Section --}}
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4">User Details</th>
                        <th class="px-6 py-4">Gender</th>
                        <th class="px-6 py-4">Streak</th>
                        <th class="px-6 py-4">Login Method</th>
                        <th class="px-6 py-4">Joined Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($users as $u)
                        <tr class="hover:bg-slate-50/70 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    {{-- Custom Avatar Handler --}}
                                    <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0 border border-slate-100 shadow-sm flex items-center justify-center bg-white transition-transform duration-300 group-hover:scale-105">
                                        @if(isset($u->avatar) && $u->avatar && str_starts_with($u->avatar, 'http'))
                                            <img src="{{ $u->avatar }}" referrerpolicy="no-referrer" class="w-full h-full object-cover">
                                        @elseif(isset($u->avatar) && $u->avatar && str_starts_with($u->avatar, 'data:image'))
                                            <img src="{{ $u->avatar }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center font-black text-sm bg-gradient-to-br from-[#C7AFFF] to-[#B28CFF] text-white">
                                                {{ strtoupper(substr($u->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <a href="{{ route('admin.users.show', $u->getKey()) }}"
                                            class="font-bold text-slate-800 hover:text-[#9261F3] transition-colors text-sm block truncate tracking-tight">
                                            {{ $u->name }}
                                        </a>
                                        <p class="text-xs font-medium text-slate-400 truncate">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-semibold text-slate-500 capitalize bg-slate-100 px-2.5 py-1 rounded-md">
                                    {{ $u->gender ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-orange-50 border border-orange-100/30 text-orange-600 font-black text-xs">
                                    🔥 {{ $u->current_streak ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($u->google_id)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-600 border border-blue-100 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                        </svg>
                                        Google
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-600 border border-slate-200 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        Email
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-400 font-medium text-xs">
                                {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin.users.delete', $u->getKey()) }}"
                                    onsubmit="return confirm('Are you sure you want to delete {{ addslashes($u->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center justify-center p-2 text-rose-400 hover:text-white hover:bg-rose-500 rounded-lg transition-all duration-200 group-hover:opacity-100 sm:opacity-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-slate-700 font-bold">No Users Found</h4>
                                    <p class="text-sm text-slate-400 mt-1">Try adjusting your search query.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Section --}}
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                {{ $users->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
@endsection
