@extends('layouts.app')
@section('title', 'Challenge')
@section('page-title', 'Daily Challenge')
@section('page-subtitle', 'Track your progress')

@section('content')
    <div class="space-y-6 fade-in pb-10">

        {{-- Progress Banner (Glassmorphism & Gradient) --}}
        <div
            class="bg-gradient-to-br from-[#B28CFF] to-[#9261F3] rounded-[2.5rem] p-6 md:p-8 text-white relative overflow-hidden shadow-lg shadow-[#B28CFF]/20 flex flex-col md:flex-row md:items-center justify-between gap-6">
            {{-- Aksen Bintang/Sparkle & Blur --}}
            <div
                class="absolute top-0 right-0 w-48 h-48 bg-white opacity-10 rounded-full blur-3xl translate-x-10 -translate-y-10">
            </div>
            <div class="absolute top-4 right-20 text-3xl opacity-60 drop-shadow-md"><img src="/img/image_home_banner.png"
                    alt="home"></div>
            <div class="absolute bottom-4 left-6 text-xl opacity-40">✦</div>

            <div class="flex items-center gap-4 relative z-10">
                <div
                    class="w-16 h-16 bg-white/20 rounded-[1.5rem] flex items-center justify-center text-3xl backdrop-blur-md border  shadow-inner rotate-3 hover:rotate-6 transition-transform">
                    <img src="/img/trophy.png" alt="">
                </div>
                <div>
                    <h3 class="font-black text-2xl tracking-tight mb-1 drop-shadow-sm">Track your progress</h3>
                    <p class="text-white/90 text-sm font-medium">and complete this week's challenges!</p>
                </div>
            </div>

            {{-- Progress Bar berbentuk Pill --}}
            <div
                class="flex items-center gap-3 relative z-10 bg-white/20 p-2.5 rounded-full backdrop-blur-md border border-white/30 w-full md:w-auto md:min-w-[280px] shadow-sm">
                <div class="flex-1 bg-black/10 rounded-full h-3 mx-2 relative overflow-hidden shadow-inner">
                    <div class="bg-gradient-to-r from-[#FFD15C] to-[#FFB800] h-full rounded-full transition-all duration-1000 relative"
                        style="width: {{ min(100, ($weekCompleted / 7) * 100) }}%">
                        <div class="absolute inset-0 bg-white/30 w-full h-full animate-pulse"></div>
                    </div>
                </div>
                <div class="text-[#9261F3] font-black px-4 py-1.5 bg-white rounded-full text-sm shadow-sm">
                    {{ $weekCompleted }}/7
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Today Challenge --}}
            <div class="lg:col-span-2">
                <div
                    class="bg-white rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white p-6 md:p-8 relative overflow-hidden group">

                    {{-- Aksen grid pastel & blobs di background card --}}
                    <div
                        class="absolute -top-10 -right-10 w-40 h-40 bg-orange-50 rounded-full blur-2xl opacity-70 group-hover:scale-110 transition-transform duration-700">
                    </div>
                    <div
                        class="absolute bottom-10 right-20 w-32 h-32 bg-[#F3EFFF] rounded-full blur-xl opacity-80 group-hover:translate-x-4 transition-transform duration-700">
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-6">
                            <div class="bg-slate-100 px-3 py-1.5 rounded-xl flex items-center gap-2">
                                <span class="text-lg">{{ $todayChallenge?->is_completed ? '✨' : '🔥' }}</span>
                                <span class="text-sm font-extrabold text-slate-700 uppercase tracking-widest">
                                    {{ $todayChallenge?->is_completed ? 'Task Completed' : "Today's Task" }}
                                </span>
                            </div>
                        </div>

                        @if ($todayChallenge && $todayChallenge->challenge)
                            <div class="mb-2">
                                <h2
                                    class="text-3xl md:text-4xl font-black text-slate-800 mb-4 leading-tight tracking-tight">
                                    {{ $todayChallenge->challenge->title }}
                                </h2>

                                <div class="flex flex-wrap gap-2 mb-6">
                                    <span
                                        class="bg-[#F3EFFF] text-[#9261F3] text-[11px] font-extrabold px-3 py-1.5 rounded-full uppercase tracking-wider border border-[#EAE1FF]">
                                        {{ implode(' · ', array_map('ucfirst', $todayChallenge->challenge->tags ?? [])) }}
                                    </span>
                                    <span
                                        class="bg-orange-50 text-orange-500 text-[11px] font-extrabold px-3 py-1.5 rounded-full uppercase tracking-wider border border-orange-100">
                                        ⏱ {{ $todayChallenge->challenge->estimated_minutes }} Mins
                                    </span>
                                </div>

                                <div
                                    class="bg-slate-50/80 backdrop-blur-sm border border-slate-100 rounded-[1.5rem] p-6 mb-8">
                                    <p class="text-slate-600 font-medium leading-relaxed text-sm md:text-base">
                                        {{ $todayChallenge->challenge->content }}
                                    </p>
                                </div>

                                @if ($todayChallenge->is_completed)
                                    <div
                                        class="bg-emerald-50 border border-emerald-100 rounded-[2rem] p-8 text-center shadow-sm">
                                        <div class="text-5xl mb-4 animate-bounce">💡</div>
                                        <p class="text-emerald-600 font-black text-2xl tracking-tight">Challenge Clear!</p>
                                        <p class="text-emerald-600/80 text-sm mt-2 font-medium">You earned points today.
                                            Keep it up!</p>
                                    </div>
                                @else
                                    <div class="space-y-4">
                                        <textarea id="reflection"
                                            class="w-full bg-slate-50 border border-slate-200 rounded-[1.5rem] p-5 text-slate-700 font-medium focus:ring-4 focus:ring-[#B28CFF]/20 focus:border-[#B28CFF] transition-all resize-none shadow-inner"
                                            rows="2" placeholder="Write a quick reflection (optional)..."></textarea>

                                        <button onclick="completeChallenge('{{ $todayChallenge->getKey() }}')"
                                            class="w-full bg-slate-800 text-white py-4 rounded-full font-extrabold text-base hover:bg-slate-700 hover:shadow-lg hover:shadow-slate-800/20 hover:-translate-y-0.5 active:scale-95 transition-all">
                                            Start Challenge <span class="text-xl ml-1">🚀</span>
                                        </button>
                                    </div>
                                    {{-- Tambah setelah button "Start Challenge", sebelum @endif --}}
                                    @if (!$todayChallenge->is_completed)
                                        <div class="flex items-center justify-between mt-2">
                                            <p class="text-xs text-slate-400">
                                                Don't like this challenge?
                                            </p>
                                            @php
                                                $regenCount = $todayChallenge->metadata['regenerate_count'] ?? 0;
                                                $regenRemain = 3 - $regenCount;
                                            @endphp
                                            <button onclick="regenerateChallenge()" id="regenBtn"
                                                {{ $regenRemain <= 0 ? 'disabled' : '' }}
                                                class="text-xs font-bold text-[#9261F3] hover:text-[#7B4FD9]
                   disabled:opacity-40 disabled:cursor-not-allowed
                   flex items-center gap-1 transition">
                                                🔄 Regenerate
                                                <span
                                                    class="bg-[#F3EFFF] text-[#9261F3] px-2 py-0.5 rounded-full text-[10px]">
                                                    {{ $regenRemain }}x left
                                                </span>
                                            </button>
                                        </div>
                                    @endif
                                @endif
                            </div>
                            {{-- Ganti seluruh bagian @else (no challenge) menjadi ini --}}
                        @else
                            {{-- Tidak ada challenge hari ini --}}
                            <div class="text-center py-12">
                                <div class="flex justify-center mb-6 animate-[pulse_3s_ease-in-out_infinite]">
                                    <img src="/img/img_challenge_mascot.png" alt="Challenge Mascot"
                                        class="w-32 md:w-40 h-auto drop-shadow-lg">
                                </div>
                                <h3 class="text-2xl font-black text-slate-800 mb-2 tracking-tight">
                                    Ready for today's challenge?
                                </h3>
                                <p class="text-slate-500 text-sm font-medium mb-8">
                                    Generate your personalized task powered by AI.
                                </p>

                                <button onclick="generateChallenge()"
                                    class="w-full md:w-max mx-auto px-10 inline-flex justify-center
                       items-center gap-2 bg-slate-800 text-white py-4 rounded-full
                       font-extrabold text-base hover:bg-slate-700 hover:shadow-lg
                       hover:-translate-y-0.5 active:scale-95 transition-all">
                                    Generate Task ✨
                                </button>
                                <p id="generateStatus" class="text-[#9261F3] font-bold mt-5 hidden animate-pulse">
                                    Generating your personalized task... 🪄
                                </p>
                            </div>
                        @endif

                        {{-- ===== UPLOAD PROOF — di luar if/else, hanya kalau completed ===== --}}
                        @if ($todayChallenge?->is_completed)
                            <div
                                class="bg-white rounded-[2rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)]
            border border-slate-100 p-6 md:p-8 mt-6 text-left">

                                <h3 class="font-extrabold text-slate-800 mb-1 text-lg flex items-center gap-2">
                                    <span class="text-2xl">📤</span>
                                    Upload Proof
                                    <span class="text-slate-400 text-sm font-medium ml-1">(Optional)</span>
                                </h3>
                                <p class="text-sm text-slate-400 mb-5">
                                    Upload your work and get AI feedback & score
                                </p>

                                {{-- Existing score kalau sudah pernah upload --}}
                                @if (isset($todayChallenge->metadata['proof_score']) && ($todayChallenge->metadata['proof_scored'] ?? false))
                                    <div
                                        class="bg-gradient-to-br from-[#F3EFFF] to-[#EAE1FF]
                border border-[#B28CFF]/20 rounded-2xl p-5 mb-5 shadow-sm">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-16 h-16 bg-white rounded-full flex items-center
                        justify-center text-2xl font-black text-[#9261F3] shadow-sm
                        flex-shrink-0">
                                                {{ $todayChallenge->metadata['proof_score'] }}
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-black text-slate-800 tracking-tight text-sm">
                                                    AI Score & Feedback
                                                </p>
                                                <p class="text-sm text-slate-600 mt-1 font-medium leading-relaxed">
                                                    {{ $todayChallenge->metadata['proof_feedback'] ?? '' }}
                                                </p>
                                                @if (!empty($todayChallenge->metadata['proof_suggestions']))
                                                    <p class="text-xs text-[#9261F3] mt-2 font-semibold">
                                                        💡 {{ $todayChallenge->metadata['proof_suggestions'] }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="text-4xl opacity-60">
                                                @if (($todayChallenge->metadata['proof_score'] ?? 0) >= 80)
                                                    🏆
                                                @elseif(($todayChallenge->metadata['proof_score'] ?? 0) >= 50)
                                                    ⭐
                                                @else
                                                    📝
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Flash result setelah upload --}}
                                @if (session('proof_result'))
                                    <div
                                        class="bg-gradient-to-br from-[#F3EFFF] to-[#EAE1FF]
                border-2 border-[#B28CFF] rounded-2xl p-5 mb-5 shadow-sm">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-16 h-16 bg-white rounded-full flex items-center
                        justify-center text-2xl font-black text-[#9261F3] shadow-sm
                        flex-shrink-0">
                                                {{ session('proof_result.score') }}
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-black text-slate-800 tracking-tight text-sm">
                                                    ✅ AI Feedback
                                                </p>
                                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">
                                                    {{ session('proof_result.feedback') }}
                                                </p>
                                                @if (session('proof_result.suggestions'))
                                                    <p class="text-xs text-[#9261F3] mt-2 font-semibold">
                                                        💡 {{ session('proof_result.suggestions') }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="text-4xl opacity-60">
                                                @if ((session('proof_result.score') ?? 0) >= 80)
                                                    🏆
                                                @elseif((session('proof_result.score') ?? 0) >= 50)
                                                    ⭐
                                                @else
                                                    📝
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($errors->has('error'))
                                    <div
                                        class="bg-red-50 border border-red-100 text-red-600
                rounded-2xl p-4 mb-5 text-sm font-bold flex items-center gap-2">
                                        <span>⚠️</span> {{ $errors->first('error') }}
                                    </div>
                                @endif

                                {{-- Upload form --}}
                                <form method="POST" action="{{ route('web.challenge.upload') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="user_daily_challenge_id"
                                        value="{{ $todayChallenge->getKey() }}">

                                    <div class="border-2 border-dashed border-slate-200 bg-slate-50
                    rounded-[1.5rem] p-8 text-center hover:border-[#B28CFF]
                    hover:bg-[#F3EFFF]/50 transition-colors cursor-pointer mb-5 group"
                                        onclick="document.getElementById('proofFile').click()">
                                        <div
                                            class="w-16 h-16 bg-white rounded-full flex items-center
                        justify-center text-3xl mx-auto mb-4 shadow-sm
                        group-hover:scale-110 transition-transform">
                                            📎
                                        </div>
                                        <p class="font-extrabold text-slate-700">Click to upload your proof</p>
                                        <p class="text-xs text-slate-400 mt-2 font-medium uppercase tracking-wider">
                                            PDF, Image, or any file (max 50MB)
                                        </p>
                                        <p id="selectedFileName"
                                            class="text-sm text-[#9261F3] mt-3 font-bold hidden bg-white
                      inline-block px-4 py-1.5 rounded-full border border-[#EAE1FF]">
                                        </p>
                                    </div>

                                    <input type="file" id="proofFile" name="file" class="hidden"
                                        onchange="
                 const name = this.files[0]?.name;
                 const el = document.getElementById('selectedFileName');
                 const btn = document.getElementById('uploadBtn');
                 if (name) {
                     el.textContent = '📎 ' + name;
                     el.classList.remove('hidden');
                     btn.disabled = false;
                     btn.classList.remove('opacity-50', 'cursor-not-allowed');
                 }">

                                    <button type="submit" id="uploadBtn" disabled
                                        class="w-full bg-[#B28CFF] text-white py-4 rounded-full font-extrabold
                       text-base hover:bg-[#9261F3] hover:shadow-lg hover:-translate-y-0.5
                       active:scale-95 transition-all opacity-50 cursor-not-allowed">
                                        Upload & Get AI Score ✨
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- History sidebar --}}
            <div>
                <div
                    class="bg-white rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white p-6 md:p-8 relative overflow-hidden">
                    <h3 class="font-extrabold text-slate-800 mb-6 text-lg tracking-tight">This Week's History</h3>

                    <div class="space-y-3">
                        @forelse($history as $h)
                            <div
                                class="flex items-center gap-4 p-3 rounded-[1.5rem] transition-colors {{ $h->is_completed ? 'bg-white border border-[#EAE1FF] shadow-sm hover:border-[#B28CFF]' : 'bg-slate-50 border border-slate-100' }}">

                                <div
                                    class="w-12 h-14 rounded-[1.2rem] flex flex-col items-center justify-center shrink-0 transition-transform hover:scale-105 {{ $h->is_completed ? 'bg-white border-[3px] border-[#B28CFF] text-black shadow-sm hover:-translate-y-1' : 'bg-[#ffffff] border-[3px] border-[#ff4800] text-black shadow-sm opacity-80' }}">

                                    <span class="mb-1 drop-shadow-sm flex items-center justify-center">
                                        <img src="{{ $h->is_completed ? '/img/ic_day_done.png' : '/img/ic_day_missed.png' }}"
                                            alt="{{ $h->is_completed ? 'completed day' : 'missed day' }}"
                                            class="w-5 h-5 object-contain">
                                    </span>

                                    <span class="text-[9px] font-black uppercase tracking-widest">
                                        {{ \Carbon\Carbon::parse($h->challenge_date)->format('D') }}
                                    </span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-extrabold text-slate-800 truncate tracking-tight">
                                        {{ $h->challenge?->title ?? 'Daily Task' }}
                                    </p>
                                    <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-wider">
                                        {{ \Carbon\Carbon::parse($h->challenge_date)->format('d M') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div
                                class="text-center py-10 bg-slate-50 rounded-[1.5rem] border border-slate-100 border-dashed">
                                <span class="mb-3 flex justify-center opacity-50">
                                    <img class="w-10 h-10 object-contain " src="/img/task.png" alt="No tasks">
                                </span>
                                <p class="text-slate-400 text-sm font-bold">No tasks this week</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        const token = '{{ session('web_token') }}'
        const baseUrl = '{{ url('/api') }}'

        async function regenerateChallenge() {
            const btn = document.getElementById('regenBtn')
            if (btn) {
                btn.disabled = true
                btn.innerHTML = '🔄 Regenerating...'
            }

            try {
                const res = await fetch(`${baseUrl}/challenge/daily/regenerate`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'ngrok-skip-browser-warning': 'true',
                        'Content-Type': 'application/json',
                    }
                })

                const data = await res.json()

                if (data.status === 'success') {
                    showToast(
                        `New challenge! ${data.data.remaining_regenerates}x regenerate left 🔄`,
                        'success'
                    )
                    setTimeout(() => window.location.reload(), 800)
                } else {
                    showToast(data.message || 'Cannot regenerate', 'error')
                    if (btn) {
                        btn.disabled = false
                        btn.innerHTML =
                            '🔄 Regenerate <span class="bg-[#F3EFFF] text-[#9261F3] px-2 py-0.5 rounded-full text-[10px]">...</span>'
                    }
                }
            } catch (e) {
                showToast('Connection error', 'error')
                if (btn) btn.disabled = false
            }
        }

        async function generateChallenge() {
            const btn = document.querySelector('[onclick="generateChallenge()"]')
            const status = document.getElementById('generateStatus')

            if (btn) {
                btn.disabled = true
                btn.innerHTML = 'Generating... 🪄'
            }
            status?.classList.remove('hidden')

            try {
                const res = await fetch(`${baseUrl}/challenge/daily/generate`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'ngrok-skip-browser-warning': 'true',
                        'Content-Type': 'application/json',
                    }
                })

                const data = await res.json()

                if (data.status === 'success') {
                    if (data.message === 'already_exists') {
                        showToast('You already have a challenge today! 📌', 'info')
                    } else {
                        showToast('Challenge generated! 🎯', 'success')
                    }
                    setTimeout(() => window.location.reload(), 800)
                } else {
                    showToast(data.message || 'Failed to generate', 'error')
                    if (btn) {
                        btn.disabled = false
                        btn.innerHTML = 'Generate Task ✨'
                    }
                }
            } catch (e) {
                showToast('Connection error. Check ngrok/internet.', 'error')
                if (btn) {
                    btn.disabled = false
                    btn.innerHTML = 'Generate Task ✨'
                }
            }

            status?.classList.add('hidden')
        }

        function showToast(message, type = 'info') {
            document.querySelectorAll('.toast-notif').forEach(t => t.remove())

            const colors = {
                success: 'bg-emerald-500',
                error: 'bg-red-500',
                info: 'bg-[#9261F3]',
            }

            const toast = document.createElement('div')
            toast.className =
                `toast-notif fixed top-6 right-6 z-50 px-6 py-4 rounded-2xl text-white font-bold text-sm shadow-2xl flex items-center gap-3 transform translate-x-0 transition-all duration-300 ${colors[type] ?? colors.info}`

            const icons = {
                success: '✅',
                error: '⚠️',
                info: 'ℹ️'
            }
            toast.innerHTML = `<span>${icons[type] ?? 'ℹ️'}</span><span>${message}</span>`

            document.body.appendChild(toast)

            setTimeout(() => {
                toast.style.opacity = '0'
                toast.style.transform = 'translateX(100%)'
                setTimeout(() => toast.remove(), 300)
            }, 3000)
        }

        async function completeChallenge(challengeId) {
            const reflection = document.getElementById('reflection')?.value || ''
            try {
                const res = await fetch(`${baseUrl}/challenge/daily/complete`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'ngrok-skip-browser-warning': 'true',
                    },
                    body: JSON.stringify({
                        user_daily_challenge_id: challengeId,
                        reflection: reflection,
                    })
                })
                const data = await res.json()
                if (data.status === 'success') {
                    window.location.reload()
                } else {
                    alert('Failed: ' + data.message)
                }
            } catch (e) {
                alert('Error: ' + e.message)
            }
        }
    </script>
@endsection
