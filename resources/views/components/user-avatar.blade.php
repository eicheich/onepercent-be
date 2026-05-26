@props([
    'user' => null,
    'name' => '',
    'avatar' => null,
    'size' => 'md',
])

@php
    $avatarSrc = $user?->avatar ?? $avatar;
    $nameStr = $user?->name ?? $name;
    $initial = strtoupper(substr($nameStr, 0, 1)) ?: '?';

    $sizes = [
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-16 h-16 text-xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

@if ($avatarSrc && str_starts_with($avatarSrc, 'http'))
    {{-- Google avatar URL --}}
    <img src="{{ $avatarSrc }}" alt="{{ $nameStr }}" referrerpolicy="no-referrer"
        class="{{ $sizeClass }} rounded-full object-cover flex-shrink-0 {{ $attributes->get('class') }}"
        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
    <div class="{{ $sizeClass }} rounded-full bg-primary flex items-center justify-center
                text-white font-bold flex-shrink-0 {{ $attributes->get('class') }}"
        style="display:none">
        {{ $initial }}
    </div>
@elseif($avatarSrc && str_starts_with($avatarSrc, 'data:image'))
    {{-- Base64 avatar --}}
    <img src="{{ $avatarSrc }}" alt="{{ $nameStr }}"
        class="{{ $sizeClass }} rounded-full object-cover flex-shrink-0 {{ $attributes->get('class') }}"
        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
    <div class="{{ $sizeClass }} rounded-full bg-primary flex items-center justify-center
                text-white font-bold flex-shrink-0 {{ $attributes->get('class') }}"
        style="display:none">
        {{ $initial }}
    </div>
@else
    {{-- No avatar — initials --}}
    <div
        class="{{ $sizeClass }} rounded-full bg-primary-light flex items-center justify-center
                text-primary font-bold flex-shrink-0 {{ $attributes->get('class') }}">
        {{ $initial }}
    </div>
@endif
