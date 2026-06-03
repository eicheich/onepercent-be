<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OnePercent - @yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: '#B28CFF',
                        /* Sedikit diterangkan agar lebih pastel */
                        'primary-dark': '#9261F3',
                        'primary-light': '#F3EFFF',
                        background: '#F4F2F8' /* Background sedikit keunguan */
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #F4F2F8;
        }

        /* Animasi Transisi Halus */
        .sidebar-link {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-link.active {
            background: #B28CFF;
            color: #ffffff;
            font-weight: 700;
            transform: scale(1.02);
            box-shadow: 0 4px 15px rgba(178, 140, 255, 0.3);
        }

        .sidebar-link:not(.active):hover {
            background: #ffffff;
            transform: translateX(6px);
            color: #9261F3;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
        }

        .sidebar-link:not(.active) {
            color: #64748b;
            font-weight: 600;
        }

        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: slideUpFade 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Custom Scrollbar yang lebih rapi */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>

<body class="font-sans text-slate-700 antialiased selection:bg-primary selection:text-white">

    <div class="flex h-screen overflow-hidden p-4 gap-4">

        <aside
            class="w-[260px] bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] flex flex-col h-full z-20 shrink-0 relative overflow-hidden">

            <div
                class="absolute top-0 right-0 w-32 h-32 bg-primary-light rounded-full blur-3xl -z-10 opacity-60 transform translate-x-1/2 -translate-y-1/2">
            </div>

            <div class="p-6 pb-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 rounded-[1.2rem] bg-gradient-to-br from-[#d2c2f1] to-[#f9f6ff] flex items-center justify-center shadow-lg shadow-primary/20 transform rotate-3 hover:rotate-0 transition-transform cursor-pointer">
                        <img src="/img/onboard1.png" class="text-white font-extrabold text-xl" /></span>
                    </div>
                    <div>
                        <p class="font-extrabold text-slate-800 text-lg leading-tight">OnePercent</p>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-primary-dark opacity-80">Better
                            Every Day</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <a href="{{ route('web.dashboard') }}"
                    class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-[1.25rem] {{ request()->routeIs('web.dashboard') ? 'active' : '' }}">
                    <img class="w-5 h-5" src="/img/ic_home.png" />
                    <span>Home</span>
                </a>

                <a href="{{ route('web.challenge') }}"
                    class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-[1.25rem] {{ request()->routeIs('web.challenge') ? 'active' : '' }}">
                    <img class="w-5 h-5" src="/img/ic_grid.png" />
                    <span>Challenge</span>
                </a>

                <a href="{{ route('web.leaderboard') }}"
                    class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-[1.25rem] {{ request()->routeIs('web.leaderboard') ? 'active' : '' }}">
                    <img class="w-5 h-5" src="/img/ic_crown.png" />
                    <span>Leaderboard</span>
                </a>

                <a href="{{ route('web.friends') }}"
                    class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-[1.25rem] {{ request()->routeIs('web.friends*') ? 'active' : '' }}">
                    <img class="w-6 h-5" src="/img/ic_laugh.png" />
                    <span>Friends</span>
                </a>

                <a href="{{ route('web.profile') }}"
                    class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-[1.25rem] {{ request()->routeIs('web.profile') ? 'active' : '' }}">
                    <img class="w-5 h-5" src="/img/person.png" />
                    <span>Profile</span>
                </a>
            </nav>

            <div class="p-4 m-4 mt-0 bg-background/50 rounded-[1.5rem] border border-white">
                <div class="flex items-center gap-3 mb-3">
                    @if (session('web_user.avatar'))
                        <img src="{{ session('web_user.avatar') }}"
                            class="w-10 h-10 rounded-[1rem] object-cover shadow-sm bg-white">
                    @else
                        <div
                            class="w-10 h-10 rounded-[1rem] bg-gradient-to-tr from-primary to-primary-light flex items-center justify-center shadow-sm">
                            <span class="text-primary-dark font-extrabold text-sm">
                                {{ strtoupper(substr(session('web_user.name', 'U'), 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="font-extrabold text-slate-800 text-sm truncate">
                            {{ session('web_user.name', 'Sobat 1%') }}
                        </p>
                        <p class="text-[11px] font-medium text-slate-500 truncate">
                            {{ session('web_user.email', 'user@onepercent.com') }}
                        </p>
                    </div>
                </div>
                <a class="w-full text-center px-3 py-2.5 text-sm font-bold text-amber-500 hover:bg-amber-500 hover:text-white rounded-[1rem] transition-all flex items-center justify-center gap-2 group"
                    href="{{ route('web.settings') }}">Settings</a>
            </div>
        </aside>

        <div class="flex-1 flex flex-col relative h-full overflow-hidden rounded-[2rem]">

            <header
                class="bg-white/70 backdrop-blur-xl border border-white/50 rounded-[1.5rem] px-6 py-4 flex items-center justify-between z-10 shadow-[0_4px_20px_rgb(0,0,0,0.02)] mb-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-sm font-medium text-slate-500 mt-0.5">@yield('page-subtitle', 'Let\'s get 1% better today! 🚀')</p>
                </div>

                <div class="flex items-center gap-4">
                    <div
                        class="flex items-center gap-2 bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-100 px-4 py-2 rounded-full shadow-sm hover:scale-105 transition-transform cursor-default">
                        <span class="text-orange-500 animate-bounce">🔥</span>
                        <span class="text-sm font-extrabold text-orange-600">
                            {{ session('web_streak', 0) }} Days
                        </span>
                    </div>
                    <a href="{{ route('web.notifications') }}"
                        class="w-11 h-11 bg-white rounded-full flex items-center justify-center hover:bg-primary-light hover:text-primary transition-colors relative shadow-sm border border-slate-100 group">
                        <svg fill="none" stroke="currentColor"
                            class="w-5 h-5 text-slate-500 group-hover:text-primary transition-colors"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>

                        {{-- Unread badge diperbaiki --}}
                        @php
                            $unread = \App\Models\Notification::where('user_id', session('web_user.id'))
                                ->whereNull('read_at')
                                ->count();
                        @endphp

                        @if ($unread > 0)
                            <span
                                class="absolute top-0 right-0 w-5 h-5 bg-red-500 border-2 border-white rounded-full text-white text-[10px] flex items-center justify-center font-extrabold shadow-sm">
                                {{ $unread > 9 ? '9+' : $unread }}
                            </span>
                        @endif
                    </a>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 md:p-8">

                @if (session('success'))
                    <div
                        class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-[1.2rem] flex items-center gap-3 fade-in shadow-sm">
                        <div
                            class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-emerald-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="font-bold text-emerald-700 text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                @yield('content')

            </main>
        </div>
    </div>

</body>

</html>
