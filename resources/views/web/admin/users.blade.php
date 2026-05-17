@extends('layouts.admin')
@section('title', 'Manage Users')
@section('page-title', 'Users')
@section('page-subtitle', 'Manage all registered users')

@section('content')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">

        {{-- Search --}}
        <div class="p-6 border-b border-gray-100">
            <form method="GET" action="{{ route('admin.users') }}" class="flex gap-3">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or email..."
                    class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-primary">
                <button type="submit"
                    class="bg-primary text-white px-6 py-2 rounded-xl text-sm font-medium hover:bg-primary-dark transition">
                    Search
                </button>
                @if ($search)
                    <a href="{{ route('admin.users') }}"
                        class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-500 hover:bg-gray-50">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-gray-500">
                        <th class="px-6 py-4 font-medium">User</th>
                        <th class="px-6 py-4 font-medium">Gender</th>
                        <th class="px-6 py-4 font-medium">Streak</th>
                        <th class="px-6 py-4 font-medium">Login Method</th>
                        <th class="px-6 py-4 font-medium">Joined</th>
                        <th class="px-6 py-4 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($users as $u)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-full bg-primary-light flex items-center justify-center font-bold text-primary text-sm overflow-hidden">
                                        @if ($u->avatar && str_starts_with($u->avatar, 'http'))
                                            <img src="{{ $u->avatar }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $u->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500 capitalize">{{ $u->gender ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-orange-500">
                                    🔥 {{ $u->current_streak ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($u->google_id)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-600 rounded-lg text-xs font-medium">
                                        Google
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-medium">
                                        Email
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-xs">
                                {{ $u->created_at?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('admin.users.delete', $u->getKey()) }}"
                                    onsubmit="return confirm('Delete {{ $u->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-xs text-red-500 hover:text-red-700 font-medium transition">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $users->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
