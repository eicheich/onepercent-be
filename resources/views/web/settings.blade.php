@extends('layouts.app')
@section('title', 'Settings')
@section('page-title', 'Settings')
@section('page-subtitle', 'Manage your account settings')

@section('content')
    <div class="max-w-2xl space-y-6 fade-in pb-10">

        {{-- Alert Success --}}
        @if (session('success'))
            <div class="bg-white border-2 border-green-100 rounded-[1.5rem] p-4 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow duration-300 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-16 h-16 bg-green-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500 pointer-events-none"></div>
                <div class="w-12 h-12 rounded-[1rem] bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="flex-1 relative z-10">
                    <h4 class="text-sm font-black text-slate-800">Berhasil! 🎉</h4>
                    <p class="text-xs font-bold text-slate-500 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Change Password Card --}}
        <div class="bg-white rounded-[1.5rem] shadow-sm border border-slate-100 p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-2">
                <span class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-lg">🔑</span>
                <h3 class="font-black text-xl text-slate-800 tracking-tight">Change Password</h3>
            </div>
            <p class="text-sm font-medium text-slate-500 mb-6 ml-13">
                Update your password regularly to keep your account secure.
            </p>

            @if ($errors->has('current_password'))
                <div class="p-4 mb-6 bg-red-50 border-2 border-red-100 rounded-[1rem] flex items-center gap-3">
                    <span class="text-red-500 animate-pulse">🚨</span>
                    <span class="text-sm font-bold text-red-600">{{ $errors->first('current_password') }}</span>
                </div>
            @endif

            @if ($user->google_id && !$user->password)
                <div class="bg-amber-50 border-2 border-amber-100 rounded-[1.2rem] p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-amber-200/50 flex items-center justify-center shrink-0">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google">
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-amber-800">Google Account</h4>
                        <p class="text-xs font-bold text-amber-600 mt-0.5">Password change is not available.</p>
                    </div>
                </div>
            @else
                <form method="POST" action="{{ route('web.account.password') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5 ml-1">Current Password</label>
                        <input type="password" name="current_password"
                            class="w-full border-2 border-slate-100 bg-slate-50 rounded-[1rem] px-4 py-3.5 text-sm font-medium text-slate-700 focus:bg-white focus:outline-none focus:border-slate-800 transition-colors"
                            required>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5 ml-1">New Password</label>
                            <input type="password" name="password" placeholder="Min. 6 characters"
                                class="w-full border-2 border-slate-100 bg-slate-50 rounded-[1rem] px-4 py-3.5 text-sm font-medium text-slate-700 focus:bg-white focus:outline-none focus:border-slate-800 transition-colors"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5 ml-1">Confirm Password</label>
                            <input type="password" name="password_confirmation"
                                class="w-full border-2 border-slate-100 bg-slate-50 rounded-[1rem] px-4 py-3.5 text-sm font-medium text-slate-700 focus:bg-white focus:outline-none focus:border-slate-800 transition-colors"
                                required>
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full mt-2 bg-slate-800 text-white py-3.5 rounded-[1rem] font-black text-sm hover:bg-slate-700 hover:shadow-lg hover:shadow-slate-800/20 hover:-translate-y-0.5 active:scale-95 transition-all duration-300">
                        Update Password
                    </button>
                </form>
            @endif
        </div>

        {{-- Edit Tags Card --}}
        <div class="bg-white rounded-[1.5rem] shadow-sm border border-slate-100 p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-lg">🏷️</span>
                        <h3 class="font-black text-xl text-slate-800 tracking-tight">My Interests</h3>
                    </div>
                    <p class="text-sm font-medium text-slate-500 sm:ml-13">
                        Update tags to get better challenge recommendations.
                    </p>
                </div>
                <a href="{{ route('web.tags.edit') }}"
                    class="inline-flex items-center justify-center bg-slate-100 text-slate-800 px-5 py-2.5 rounded-full text-sm font-black hover:bg-slate-800 hover:text-white transition-colors duration-300 shrink-0">
                    Edit Tags
                </a>
            </div>

            @php
                $myTags = \App\Models\UserPersonalization::where('user_id', session('web_user.id'))->first()?->tags ?? [];
            @endphp

            <div class="mt-5 sm:ml-13">
                @if (count($myTags) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach ($myTags as $tag)
                            <span class="px-3.5 py-1.5 bg-gradient-to-r from-slate-100 to-slate-50 border border-slate-200 text-slate-600 rounded-full text-xs font-black uppercase tracking-wider shadow-sm">
                                {{ ucfirst($tag) }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-50 border border-slate-100 border-dashed rounded-[1rem]">
                        <span class="text-slate-400">👻</span>
                        <span class="text-sm font-bold text-slate-400">No tags selected yet</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- App Info Card --}}
        <div class="bg-white rounded-[1.5rem] shadow-sm border border-slate-100 p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-5">
                <span class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-lg">📱</span>
                <h3 class="font-black text-xl text-slate-800 tracking-tight">App Information</h3>
            </div>

            <div class="space-y-2 text-sm sm:ml-13">
                <div class="flex justify-between items-center py-3 border-b border-slate-50">
                    <span class="font-bold text-slate-500">App Version</span>
                    <span class="px-3 py-1 bg-slate-100 rounded-full font-black text-slate-800 text-xs">1.0.0</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-slate-50">
                    <span class="font-bold text-slate-500">Platform</span>
                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full font-black text-xs">Web Version</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-slate-50 group cursor-pointer">
                    <span class="font-bold text-slate-500 group-hover:text-slate-800 transition-colors">Terms & Conditions</span>
                    <a href="{{ route('web.terms') }}" class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center group-hover:bg-slate-800 group-hover:text-white transition-colors">
                        →
                    </a>
                </div>
                <div class="flex justify-between items-center py-3 group cursor-pointer">
                    <span class="font-bold text-slate-500 group-hover:text-slate-800 transition-colors">About OnePercent</span>
                    <a href="{{ route('web.about') }}" class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center group-hover:bg-slate-800 group-hover:text-white transition-colors">
                        →
                    </a>
                </div>
            </div>
        </div>

        {{-- Danger Zone & Logout --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <button onclick="document.getElementById('deleteModal').classList.remove('hidden')"
                class="flex items-center justify-center gap-2 w-full bg-white border-2 border-red-100 text-red-500 py-4 rounded-[1.5rem] font-black text-sm hover:bg-red-50 hover:border-red-200 transition-colors group">
                <span class="group-hover:scale-110 transition-transform">⚠️</span> Delete Account
            </button>

            <form method="POST" action="{{ route('web.logout') }}" class="w-full">
                @csrf
                <button class="flex items-center justify-center gap-2 w-full bg-slate-100 text-slate-600 py-4 rounded-[1.5rem] font-black text-sm hover:bg-slate-200 hover:text-slate-800 transition-colors group">
                    <span class="group-hover:-translate-x-1 transition-transform">👋</span> Logout
                </button>
            </form>
        </div>
    </div>

    {{-- Delete Modal (Redesigned) --}}
    <div id="deleteModal" class="hidden fixed inset-0 backdrop-blur-sm bg-slate-900/40 flex items-center justify-center z-50 p-4 transition-all">
        <div class="bg-white rounded-[2rem] max-w-md w-full p-8 shadow-2xl scale-100 animate-[bounce_0.3s_ease-out]">
            <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center text-3xl mb-4 mx-auto animate-pulse">
                🚨
            </div>
            <h3 class="text-2xl font-black text-center text-slate-800 mb-2">Are you sure?</h3>
            <p class="text-slate-500 font-medium text-sm text-center mb-6">
                This action is <strong class="text-red-500">permanent</strong>. All your challenges, streaks, and achievements will be lost forever.
            </p>

            <form method="POST" action="{{ route('web.account.delete') }}">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2 text-center">
                        Type <strong class="text-red-500 bg-red-50 px-2 py-0.5 rounded">DELETE</strong> to confirm
                    </label>
                    <input type="text" name="confirm_text" placeholder="DELETE"
                        class="w-full border-2 border-slate-200 rounded-[1rem] px-4 py-3.5 text-center font-black tracking-widest text-slate-800 focus:outline-none focus:border-red-400 focus:bg-red-50 transition-colors"
                        oninput="document.getElementById('confirmDeleteBtn').disabled = this.value !== 'DELETE'"
                        required>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')"
                        class="flex-1 bg-slate-100 text-slate-700 py-3.5 rounded-[1rem] font-black hover:bg-slate-200 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="confirmDeleteBtn" disabled
                        class="flex-1 bg-red-500 text-white py-3.5 rounded-[1rem] font-black shadow-lg shadow-red-500/30 hover:bg-red-600 hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:shadow-none">
                        Delete Forever
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
