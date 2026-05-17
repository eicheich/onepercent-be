@extends('layouts.app')
@section('title', 'Profile')
@section('page-title', 'My Profile')
@section('page-subtitle', 'Manage your account')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8 fade-in pb-10">

        {{-- Profile card (Kiri) --}}
        <div class="lg:col-span-1 space-y-6 md:space-y-8">
            <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-[#EAE1FF] p-6 md:p-8 text-center relative overflow-hidden flex flex-col">
                {{-- Dekorasi Latar (Blob) --}}
                <div class="absolute top-0 right-0 w-48 h-48 bg-gradient-to-br from-[#F3EFFF] to-transparent rounded-full -mr-16 -mt-16 opacity-70 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-[#F3EFFF] to-transparent rounded-full -ml-10 -mb-10 opacity-50 pointer-events-none"></div>

                <div class="relative z-10 flex-1">
                    {{-- Avatar --}}
                    <div class="w-28 h-28 md:w-32 md:h-32 rounded-[2rem] mx-auto mb-5 overflow-hidden border-4 border-white shadow-xl shadow-[#9261F3]/10 bg-white rotate-3 hover:rotate-0 transition-transform duration-500">
                        @if ($user->avatar && str_starts_with($user->avatar, 'http'))
                            <img src="{{ $user->avatar }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-[#C7AFFF] to-[#B28CFF] flex items-center justify-center text-white text-5xl font-black">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <h2 class="text-2xl font-black text-slate-800 mb-1 tracking-tight">{{ $user->name }}</h2>
                    <p class="text-[#9261F3] font-bold text-sm mb-8 bg-[#F3EFFF] inline-block px-4 py-1.5 rounded-full">{{ $user->email }}</p>

                    {{-- Stats Grid Mini (Bento Style) --}}
                    <div class="grid grid-cols-3 gap-3 mb-8">
                        <div class="text-center p-3 md:p-4 bg-slate-50 border border-slate-100 rounded-[1.2rem] hover:bg-white hover:border-[#EAE1FF] hover:shadow-sm transition-all">
                            <p class="font-black text-slate-800 text-xl">{{ $followersCount }}</p>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-1">Followers</p>
                        </div>
                        <div class="text-center p-3 md:p-4 bg-slate-50 border border-slate-100 rounded-[1.2rem] hover:bg-white hover:border-[#EAE1FF] hover:shadow-sm transition-all">
                            <p class="font-black text-slate-800 text-xl">{{ $followingCount }}</p>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-1">Following</p>
                        </div>
                        <div class="text-center p-3 md:p-4 bg-[#fff6ed] border border-[#ffedd5] rounded-[1.2rem] hover:bg-white hover:shadow-sm transition-all">
                            <p class="font-black text-orange-500 text-xl flex justify-center items-center gap-1">
                                {{ $user->current_streak ?? 0 }}<span class="text-sm">🔥</span>
                            </p>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-orange-400 mt-1">Streak</p>
                        </div>
                    </div>

                    {{-- Detail List --}}
                    <div class="text-left space-y-3">
                        <div class="flex justify-between items-center bg-white border border-slate-100 px-5 py-4 rounded-[1.2rem] shadow-sm hover:border-[#EAE1FF] transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 text-sm">✅</div>
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wide">Total Completed</span>
                            </div>
                            <span class="font-black text-[#9261F3] text-lg">{{ $totalCompleted }}</span>
                        </div>

                        <div class="flex justify-between items-center bg-white border border-slate-100 px-5 py-4 rounded-[1.2rem] shadow-sm hover:border-[#EAE1FF] transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 text-sm">⚡</div>
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wide">Longest Streak</span>
                            </div>
                            <span class="font-black text-slate-700 text-lg">{{ $user->longest_streak ?? 0 }} <span class="text-xs text-slate-400 font-bold ml-1">days</span></span>
                        </div>

                        <div class="flex justify-between items-center bg-white border border-slate-100 px-5 py-4 rounded-[1.2rem] shadow-sm hover:border-[#EAE1FF] transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 text-sm">👤</div>
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wide">Gender</span>
                            </div>
                            <span class="font-bold text-slate-700 text-sm capitalize">{{ $user->gender ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right column --}}
        <div class="lg:col-span-2 space-y-6 md:space-y-8">

            {{-- Edit form --}}
            <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-[#EAE1FF] p-6 md:p-8 relative overflow-hidden">
                <div class="flex items-center gap-3 mb-8 relative z-10">
                    <div class="w-12 h-12 rounded-[1rem] bg-[#F3EFFF] flex items-center justify-center text-2xl text-[#9261F3]">
                        ⚙️
                    </div>
                    <div>
                        <h3 class="font-black text-xl text-slate-800 tracking-tight">Edit Profile</h3>
                        <p class="text-sm text-slate-400 font-medium">Update your personal information</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('web.profile.update') }}" class="relative z-10">
                    @csrf
                    <div class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Full Name</label>
                                <input type="text" name="name" value="{{ $user->name }}"
                                    class="w-full bg-white border-2 border-slate-100 rounded-[1.2rem] px-5 py-3.5 text-sm font-bold text-slate-700 focus:outline-none focus:border-[#B28CFF] focus:ring-4 focus:ring-[#B28CFF]/10 transition-all placeholder:text-slate-300 shadow-sm"
                                    required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Gender</label>
                                <div class="relative">
                                    <select name="gender"
                                        class="w-full bg-white border-2 border-slate-100 rounded-[1.2rem] px-5 py-3.5 text-sm font-bold text-slate-700 appearance-none focus:outline-none focus:border-[#B28CFF] focus:ring-4 focus:ring-[#B28CFF]/10 transition-all cursor-pointer shadow-sm">
                                        <option value="" class="font-medium">Select gender</option>
                                        <option value="male" {{ $user->gender === 'male' ? 'selected' : '' }} class="font-medium">Male 👦</option>
                                        <option value="female" {{ $user->gender === 'female' ? 'selected' : '' }} class="font-medium">Female 👧</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit"
                                class="w-full md:w-auto px-8 bg-gradient-to-r from-[#B28CFF] to-[#9261F3] text-white py-3.5 rounded-[1.2rem] font-bold shadow-lg shadow-[#9261F3]/20 hover:shadow-[#9261F3]/40 hover:-translate-y-0.5 transition-all">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Achievements --}}
            <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-[#EAE1FF] p-6 md:p-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-[1rem] bg-[#F3EFFF] flex items-center justify-center text-2xl text-[#9261F3]">
                            🏆
                        </div>
                        <div>
                            <h3 class="font-black text-xl text-slate-800 tracking-tight">Badges</h3>
                            <p class="text-sm text-slate-400 font-medium">Your learning milestones</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-[#F3EFFF] to-[#EAE1FF] text-[#9261F3] px-5 py-2 rounded-full font-black text-sm border border-[#EAE1FF] shadow-sm">
                        {{ collect($achievements)->where('is_unlocked', true)->count() }} / {{ count($achievements) }} Unlocked
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 md:gap-5">
                    @foreach ($achievements as $ach)
                        @php $isUnlocked = $ach['is_unlocked']; @endphp

                        <div class="relative group text-center p-5 rounded-[1.5rem] border-2 transition-all duration-300 flex flex-col items-center justify-center
                            {{ $isUnlocked
                                ? 'bg-gradient-to-b from-white to-[#F3EFFF]/30 border-[#EAE1FF] hover:border-[#B28CFF] hover:shadow-lg hover:shadow-[#B28CFF]/10 hover:-translate-y-1 cursor-pointer'
                                : 'bg-slate-50 border-slate-100 opacity-60 grayscale hover:opacity-100 hover:grayscale-0' }}"
                            title="{{ $ach['name'] }}: {{ $ach['description'] }}">

                            {{-- Icon Drop Shadow --}}
                            <div class="w-16 h-16 rounded-full {{ $isUnlocked ? 'bg-[#F3EFFF] shadow-inner' : 'bg-slate-200' }} flex items-center justify-center text-3xl md:text-4xl mb-4 drop-shadow-sm transform transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6">
                                {{ $ach['icon'] }}
                            </div>

                            <p class="text-sm font-bold text-slate-800 leading-tight mb-2">{{ $ach['name'] }}</p>

                            {{-- Status Label --}}
                            <div class="mt-auto pt-2">
                                @if ($isUnlocked)
                                    <span class="bg-white border border-[#EAE1FF] text-[#9261F3] px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm">
                                        Unlocked
                                    </span>
                                @else
                                    <span class="bg-white border border-slate-200 text-slate-400 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">
                                        Locked
                                    </span>
                                @endif
                            </div>

                            {{-- Tooltip Hover --}}
                            <div class="absolute inset-x-0 bottom-full mb-3 hidden group-hover:block z-20 pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <div class="bg-slate-800 text-white text-xs font-medium p-3 rounded-[1rem] shadow-xl relative w-max max-w-[180px] mx-auto whitespace-normal text-center leading-snug">
                                    {{ $ach['description'] }}
                                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-3 h-3 bg-slate-800 rotate-45"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
@endsection
