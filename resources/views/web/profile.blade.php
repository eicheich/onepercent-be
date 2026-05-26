@extends('layouts.app')
@section('title', 'Profile')
@section('page-title', 'My Profile')
@section('page-subtitle', 'Manage your account')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8 fade-in pb-10">

        {{-- Profile card (Kiri) --}}
        <div class="lg:col-span-1 space-y-6 md:space-y-8">
            <div
                class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgba(0,0,0,0.03)] border border-[#EAE1FF] p-6 md:p-8 text-center relative overflow-hidden flex flex-col group">
                {{-- Dekorasi Latar (Blob) --}}
                <div
                    class="absolute top-0 right-0 w-48 h-48 bg-gradient-to-br from-[#F3EFFF] to-transparent rounded-full -mr-16 -mt-16 opacity-70 pointer-events-none transition-transform duration-700 group-hover:scale-110">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-[#F3EFFF] to-transparent rounded-full -ml-10 -mb-10 opacity-50 pointer-events-none transition-transform duration-700 group-hover:scale-110">
                </div>

                <div class="relative z-10 flex-1">
                    {{-- Avatar --}}
                    <div
                        class="w-28 h-28 md:w-32 md:h-32 rounded-[2.5rem] mx-auto mb-5 overflow-hidden border-4 border-white shadow-xl shadow-[#9261F3]/10 bg-white rotate-3 hover:rotate-0 transition-transform duration-500">
                        {{-- Ganti bagian avatar --}}
                        <div
                            class="w-24 h-24 rounded-full mx-auto mb-4 overflow-hidden
            border-4 border-primary-light flex-shrink-0">
                            @if ($user->avatar && str_starts_with($user->avatar, 'http'))
                                <img src="{{ $user->avatar }}" referrerpolicy="no-referrer" class="w-full h-full object-cover"
                                    onerror="this.parentElement.innerHTML='<div class=\'w-full h-full bg-primary flex items-center justify-center text-white text-3xl font-bold\'>{{ strtoupper(substr($user->name, 0, 1)) }}</div>'">
                            @elseif($user->avatar && str_starts_with($user->avatar, 'data:image'))
                                <img src="{{ $user->avatar }}" class="w-full h-full object-cover">
                            @else
                                <div
                                    class="w-full h-full bg-primary flex items-center
                    justify-center text-white text-3xl font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <h2 class="text-2xl font-black text-slate-800 mb-1 tracking-tight">{{ $user->name }}</h2>
                    <p
                        class="text-[#9261F3] font-bold text-xs mb-8 bg-[#F3EFFF] inline-block px-4 py-1.5 rounded-full tracking-wide">
                        {{ $user->email }}
                    </p>

                    {{-- Stats Grid Mini (Bento Style) --}}
                    <div class="grid grid-cols-3 gap-3 mb-8">
                        <div
                            class="text-center p-3.5 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-white hover:border-[#EAE1FF] hover:shadow-[0_4px_20px_-4px_rgba(146,97,243,0.12)] transition-all duration-300">
                            <p class="font-black text-slate-800 text-xl tracking-tight">{{ $followersCount }}</p>
                            <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mt-1">Followers</p>
                        </div>
                        <div
                            class="text-center p-3.5 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-white hover:border-[#EAE1FF] hover:shadow-[0_4px_20px_-4px_rgba(146,97,243,0.12)] transition-all duration-300">
                            <p class="font-black text-slate-800 text-xl tracking-tight">{{ $followingCount }}</p>
                            <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mt-1">Following</p>
                        </div>
                        <div
                            class="text-center p-3.5 bg-[#FFF6ED] border border-[#FFEDD5] rounded-2xl hover:bg-white hover:shadow-[0_4px_20px_-4px_rgba(249,115,22,0.15)] transition-all duration-300 group/streak">
                            <p class="font-black text-orange-500 text-xl flex justify-center items-center gap-1.5">
                                {{ $user->current_streak ?? 0 }}
                                <img src="{{ asset('img/icon-fire.png') }}" class="w-4 h-4 object-contain animate-pulse"
                                    onerror="this.style.display='none'">
                            </p>
                            <p class="text-[9px] font-bold uppercase tracking-widest text-orange-400 mt-1">Streak</p>
                        </div>
                    </div>

                    {{-- Detail List --}}
                    <div class="text-left space-y-3">
                        <div
                            class="flex justify-between items-center bg-white border border-slate-100 px-5 py-4 rounded-2xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.02)] hover:border-[#EAE1FF] transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-purple-50 flex items-center justify-center p-1.5">
                                    <img src="{{ asset('img/check.png') }}" alt="check"
                                        class="w-full h-full object-contain">
                                </div>
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total
                                    Completed</span>
                            </div>
                            <span class="font-black text-[#9261F3] text-lg">{{ $totalCompleted }}</span>
                        </div>

                        <div
                            class="flex justify-between items-center bg-white border border-slate-100 px-5 py-4 rounded-2xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.02)] hover:border-[#EAE1FF] transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-orange-50 flex items-center justify-center p-1.5">
                                    <img src="{{ asset('img/Streak.png') }}" alt="streak"
                                        class="w-full h-full object-contain">
                                </div>
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Longest
                                    Streak</span>
                            </div>
                            <span class="font-black text-slate-700 text-lg">
                                {{ $user->longest_streak ?? 0 }} <span
                                    class="text-xs text-slate-400 font-bold ml-0.5">days</span>
                            </span>
                        </div>

                        <div
                            class="flex justify-between items-center bg-white border border-slate-100 px-5 py-4 rounded-2xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.02)] hover:border-[#EAE1FF] transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center p-1.5">
                                    <img src="{{ asset('img/gender.png') }}" alt="gender"
                                        class="w-full h-full object-contain">
                                </div>
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Gender</span>
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
            <div
                class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgba(0,0,0,0.03)] border border-[#EAE1FF] p-6 md:p-8 relative overflow-hidden">
                <div class="flex items-center gap-4 mb-8 relative z-10">
                    <div
                        class="w-12 h-12 rounded-2xl bg-[#F3EFFF] border border-[#EAE1FF] flex items-center justify-center p-3">
                        <img src="{{ asset('img/settings.png') }}" class="w-full h-full object-contain" />
                    </div>
                    <div>
                        <h3 class="font-black text-xl text-slate-800 tracking-tight">Edit Profile</h3>
                        <p class="text-xs text-slate-400 font-medium">Update your personal account settings</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('web.profile.update') }}" class="relative z-10">
                    @csrf
                    <div class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 ml-1">Full
                                    Name</label>
                                <input type="text" name="name" value="{{ $user->name }}"
                                    class="w-full bg-white border-2 border-slate-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-700 focus:outline-none focus:border-[#B28CFF] focus:ring-4 focus:ring-[#B28CFF]/10 transition-all placeholder:text-slate-300 shadow-sm"
                                    required>
                            </div>

                            <div>
                                <label
                                    class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 ml-1">Gender</label>
                                <div class="relative">
                                    <select name="gender"
                                        class="w-full bg-white border-2 border-slate-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-700 appearance-none focus:outline-none focus:border-[#B28CFF] focus:ring-4 focus:ring-[#B28CFF]/10 transition-all cursor-pointer shadow-sm">
                                        <option value="" class="font-medium">Select gender</option>
                                        <option value="male" {{ $user->gender === 'male' ? 'selected' : '' }}
                                            class="font-medium">Male</option>
                                        <option value="female" {{ $user->gender === 'female' ? 'selected' : '' }}
                                            class="font-medium">Female</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-slate-400 stroke-[3]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit"
                                class="w-full md:w-auto px-8 bg-gradient-to-r from-[#B28CFF] to-[#9261F3] text-white py-3.5 rounded-2xl font-bold shadow-lg shadow-[#9261F3]/20 hover:shadow-[#9261F3]/40 hover:-translate-y-0.5 transition-all duration-300">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Achievements Section --}}
            <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgba(0,0,0,0.03)] border border-[#EAE1FF] p-6 md:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="font-black text-xl text-slate-800 tracking-tight">Badges & Achievements</h3>
                        <p class="text-xs text-slate-400 font-medium">Your milestones on OnePercent</p>
                    </div>
                    <span
                        class="text-xs font-bold text-primary bg-primary-light/50 border border-primary-light px-3 py-1.5 rounded-xl tracking-wide">
                        {{ collect($achievements)->where('is_unlocked', true)->count() }} / {{ count($achievements) }}
                        Unlocked
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach ($achievements as $ach)
                        @php $unlocked = $ach['is_unlocked']; @endphp
                        <div class="text-center p-5 rounded-2xl border transition-all duration-300 cursor-pointer relative overflow-hidden group flex flex-col items-center justify-between
                            {{ $unlocked
                                ? 'border-[#EAE1FF] bg-gradient-to-b from-white to-[#FDFBFF] hover:shadow-[0_8px_25px_-6px_rgba(146,97,243,0.15)] hover:-translate-y-1'
                                : 'border-slate-100 bg-slate-50/50 opacity-60 hover:opacity-80' }}"
                            title="{{ $ach['name'] }}: {{ $ach['description'] }}"
                            onclick="showAchievementDetail('{{ $ach['name'] }}', '{{ $ach['description'] }}', {{ $unlocked ? 'true' : 'false' }})">

                            {{-- Achievement Badge Graphic --}}
                            <div class="w-16 h-16 mx-auto mb-4 relative flex items-center justify-center">
                                <img src="{{ asset('img/' . ($ach['image'] ?? 'ic_achievement_1') . '.png') }}"
                                    alt="{{ $ach['name'] }}"
                                    class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-110
                                    {{ $unlocked ? '' : 'grayscale opacity-30' }}">

                                {{-- Sleek Lock Overlay --}}
                                @if (!$unlocked)
                                    <div
                                        class="absolute inset-0 flex items-center justify-center bg-slate-900/5 backdrop-blur-[1px] rounded-full p-4">
                                        <svg class="w-5 h-5 text-slate-400/80" fill="none" stroke="currentColor"
                                            stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v-6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 flex flex-col justify-center">
                                <p class="text-xs font-black text-slate-800 leading-snug tracking-tight mb-1">
                                    {{ $ach['name'] }}</p>
                                <p class="text-[10px] font-medium text-slate-400 leading-tight line-clamp-2 px-1">
                                    {{ $ach['description'] }}</p>
                            </div>

                            {{-- Custom Status Tags --}}
                            <div class="mt-4 w-full">
                                @if ($unlocked)
                                    <span
                                        class="inline-flex items-center gap-1 text-[9px] font-black tracking-widest uppercase text-emerald-600 bg-emerald-50 border border-emerald-100 px-2.5 py-1 rounded-lg w-full justify-center">
                                        <span class="w-1 h-1 rounded-full bg-emerald-500 animate-ping"></span> Unlocked
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center text-[9px] font-bold tracking-widest uppercase text-slate-400 bg-slate-100 border border-slate-200/60 px-2.5 py-1 rounded-lg w-full justify-center">
                                        Locked
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Achievement detail modal --}}
            <div id="achievementModal"
                class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center z-50 p-4 transition-all"
                onclick="this.classList.add('hidden')">
                <div class="bg-white rounded-[2rem] p-8 max-w-sm w-full text-center border border-[#EAE1FF] shadow-2xl transform transition-all scale-100"
                    onclick="event.stopPropagation()">

                    <div class="w-24 h-24 mx-auto mb-5 p-2 bg-slate-50 border border-slate-100 rounded-3xl">
                        <img id="modalAchievementImg" src="" class="w-full h-full object-contain mx-auto">
                    </div>

                    <h3 id="modalAchievementName" class="text-xl font-black text-slate-800 mb-2 tracking-tight"></h3>
                    <p id="modalAchievementDesc" class="text-slate-400 text-xs font-medium mb-6 px-4 leading-relaxed"></p>

                    <div id="modalAchievementStatusBadge"
                        class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-black tracking-wide mb-6">
                        <span id="modalAchievementStatus"></span>
                    </div>

                    <button onclick="document.getElementById('achievementModal').classList.add('hidden')"
                        class="w-full bg-slate-100 border border-slate-200 text-slate-600 hover:bg-slate-200 px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-wider transition-colors">
                        Dismiss
                    </button>
                </div>
            </div>

            <script>
                const achievementImages = @json(collect($achievements)->mapWithKeys(fn($a) => [
                            $a['name'] => asset('img/' . ($a['image'] ?? 'ic_achievement_1') . '.png'),
                        ]));

                function showAchievementDetail(name, desc, isUnlocked) {
                    document.getElementById('modalAchievementImg').src = achievementImages[name] ?? '';
                    document.getElementById('modalAchievementName').textContent = name;
                    document.getElementById('modalAchievementDesc').textContent = desc;

                    const statusText = document.getElementById('modalAchievementStatus');
                    const statusBadge = document.getElementById('modalAchievementStatusBadge');

                    statusText.textContent = isUnlocked ? 'UNLOCKED' : 'LOCKED';

                    if (isUnlocked) {
                        statusBadge.className =
                            'inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black tracking-wide mb-6 bg-emerald-50 text-emerald-600 border border-emerald-100';
                    } else {
                        statusBadge.className =
                            'inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black tracking-wide mb-6 bg-slate-100 text-slate-400 border border-slate-200';
                    }

                    document.getElementById('achievementModal').classList.remove('hidden');
                }
            </script>

        </div>
    </div>
@endsection
