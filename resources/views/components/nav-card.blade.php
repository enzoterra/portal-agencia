{{--
    Props:
    - href: string
    - icon: string
    - title: string
    - description: string
    - badge: string|null  ex: "3 novos"
    - badge-color: 'brand'|'green'|'amber'|'blue'  (default: brand)
--}}
@props([
    'href'        => '#',
    'icon'        => '',
    'title'       => '',
    'description' => '',
    'badge'       => null,
    'badgeColor'  => 'brand',
])

@php
    $badgeClass = match($badgeColor) {
        'green' => 'badge-green',
        'amber' => 'badge-amber',
        'blue'  => 'badge-blue',
        default => 'badge-red',
    };
@endphp

<a href="{{ $href }}" class="card-hover p-5 flex items-center gap-4 group cursor-pointer block">
    <div class="w-11 h-11 rounded-xl bg-brand-icon flex items-center justify-center text-xl flex-shrink-0
                group-hover:bg-brand transition-colors duration-200">
        {{ $icon }}
    </div>
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 mb-0.5">
            <span class="text-sm font-semibold text-ink truncate">{{ $title }}</span>
            @if($badge)
                <span class="{{ $badgeClass }}">{{ $badge }}</span>
            @endif
        </div>
        <p class="text-xs text-ink-muted truncate">{{ $description }}</p>
    </div>
    <svg class="w-4 h-4 text-ink-subtle group-hover:text-brand group-hover:translate-x-0.5 transition-all duration-150 flex-shrink-0"
         fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
</a>