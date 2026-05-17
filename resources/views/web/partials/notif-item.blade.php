<div
    class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center gap-4
            {{ is_null($notif['read_at']) ? 'border-l-4 border-l-primary' : '' }}">

    {{-- Avatar --}}
    <div
        class="w-12 h-12 rounded-full flex items-center justify-center text-xl flex-shrink-0
                {{ $notif['type'] === 'boast' ? 'bg-green-100' : 'bg-orange-100' }}">
        @if ($notif['sender_avatar'] && str_starts_with($notif['sender_avatar'], 'http'))
            <img src="{{ $notif['sender_avatar'] }}" class="w-12 h-12 rounded-full object-cover">
        @else
            {{ $notif['type'] === 'boast' ? '🎉' : '👋' }}
        @endif
    </div>

    {{-- Content --}}
    <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-gray-800">
            {{ $notif['sender_name'] }}
            <span class="font-normal text-gray-600">
                {{ $notif['type'] === 'boast' ? 'completed a challenge!' : 'poked you to do your challenge!' }}
            </span>
        </p>
        @if ($notif['challenge_title'])
            <p class="text-xs text-gray-400 mt-0.5">
                📌 {{ $notif['challenge_title'] }}
            </p>
        @endif
        <p class="text-xs text-gray-400 mt-1">
            {{ $notif['created_at']?->diffForHumans() }}
        </p>
    </div>

    {{-- Unread dot --}}
    @if (is_null($notif['read_at']))
        <div class="w-2.5 h-2.5 rounded-full bg-primary shrink-0"></div>
    @endif
</div>
