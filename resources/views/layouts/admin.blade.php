<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OnePercent Admin - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#BA9FFE',
                        'primary-dark': '#fff',
                        'primary-light': '#fff',
                    },
                    boxShadow: {
                        'premium': '0 10px 30px -10px rgba(186, 159, 254, 0.2)',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-[#F8FAFC] antialiased text-slate-700">
    <div class="flex h-screen overflow-hidden">

        <aside class="w-64 bg-slate-950 flex flex-col fixed h-full border-r border-slate-900 z-30">
            <div class="p-6 border-b border-slate-900/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-primary-dark to-primary flex items-center justify-center shadow-lg shadow-primary/20 overflow-hidden relative group">
                        <img src="{{ asset('img/onboard1.png') }}" alt="" class="w-full h-full object-cover absolute  group-hover:scale-110 transition-transform">
                    </div>
                    <div>
                        <p class="font-black text-white tracking-tight text-sm uppercase">OnePercent</p>
                        <p class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">Admin Panel</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Main Menu</p>

                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm font-semibold group
                      {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-slate-950 shadow-premium font-bold' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="w-5 h-5 transition-transform group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.users') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm font-semibold group
                      {{ request()->routeIs('admin.users') ? 'bg-primary text-slate-950 shadow-premium font-bold' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="w-5 h-5 transition-transform group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Users
                </a>

                <a href="{{ route('admin.challenges') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm font-semibold group
                      {{ request()->routeIs('admin.challenges') ? 'bg-primary text-slate-950 shadow-premium font-bold' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="w-5 h-5 transition-transform group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Challenges
                </a>

                <a href="{{ route('admin.achievements') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm font-semibold group
                      {{ request()->routeIs('admin.achievements') ? 'bg-primary text-slate-950 shadow-premium font-bold' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="w-5 h-5 transition-transform group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    Achievements
                </a>
            </nav>

            <div class="p-4 border-t border-slate-900/60 bg-slate-950/40 backdrop-blur-md">
                @php
                    $adminName = session('web_user.name', 'Admin User');
                    $adminAvatar = session('web_user.avatar');
                @endphp

                <div class="flex items-center gap-3 p-2 bg-slate-900/40 rounded-xl border border-slate-900/30 mb-2">
                    {{-- Avatar Handling untuk Admin --}}
                    <div class="w-9 h-9 rounded-lg overflow-hidden bg-gradient-to-br from-primary/20 to-primary/40 border border-primary/30 flex items-center justify-center flex-shrink-0 text-primary font-black text-sm shadow-inner">
                        @if($adminAvatar && str_starts_with($adminAvatar, 'http'))
                            <img src="{{ $adminAvatar }}" referrerpolicy="no-referrer" class="w-full h-full object-cover">
                        @elseif($adminAvatar && str_starts_with($adminAvatar, 'data:image'))
                            <img src="{{ $adminAvatar }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($adminName, 0, 1)) }}
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-white truncate">{{ $adminName }}</p>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Administrator</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('web.logout') }}">
                    @csrf
                    <button class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg text-xs font-bold text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 border border-transparent hover:border-rose-500/20 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout System
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 ml-64 flex flex-col min-h-screen">

            <header class="bg-white/80 backdrop-blur-md border-b border-slate-100 px-8 py-5 flex items-center justify-between sticky top-0 z-20">
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-xs font-medium text-slate-400 mt-0.5">@yield('page-subtitle', 'Welcome back to manage your platform.')</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 border border-rose-100 text-rose-600 rounded-full text-xs font-bold tracking-wide uppercase">
                        <span class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-pulse"></span>
                        Live Admin
                    </span>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-8 content-fade">
                @if (session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl flex items-start gap-3 shadow-sm animate-[fadeIn_0.2s_ease-out]">
                        <span class="text-emerald-500 text-lg leading-none">✨</span>
                        <div class="text-sm font-semibold">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-[0_4px_20px_rgba(0,0,0,0.01)] min-h-[calc(100vh-12rem)]">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>

</html>
