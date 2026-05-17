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
                        'primary-dark': '#9B7FE8',
                        'primary-light': '#EDE8FF',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
<div class="flex h-screen overflow-hidden">

    <!-- Admin Sidebar -->
    <aside class="w-64 bg-gray-900 flex flex-col fixed h-full">
        <div class="p-6 border-b border-gray-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-primary flex items-center justify-center">
                    <span class="text-white font-bold">1%</span>
                </div>
                <div>
                    <p class="font-bold text-white">OnePercent</p>
                    <p class="text-xs text-gray-500">Admin Panel</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium
                      {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('admin.users') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium
                      {{ request()->routeIs('admin.users') ? 'bg-primary text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Users
            </a>

            <a href="{{ route('admin.challenges') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium
                      {{ request()->routeIs('admin.challenges') ? 'bg-primary text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Challenges
            </a>
        </nav>

        <div class="p-4 border-t border-gray-800">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-sm font-bold">
                    {{ strtoupper(substr(session('web_user.name', 'A'), 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-white">{{ session('web_user.name') }}</p>
                    <p class="text-xs text-gray-500">Administrator</p>
                </div>
            </div>
            <form method="POST" action="{{ route('web.logout') }}">
                @csrf
                <button class="w-full text-left px-3 py-2 text-xs text-gray-400 hover:text-red-400 transition">
                    → Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Content -->
    <div class="flex-1 ml-64 flex flex-col">
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-800">@yield('page-title')</h1>
                <p class="text-sm text-gray-400">@yield('page-subtitle')</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-semibold">
                    Admin
                </span>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
