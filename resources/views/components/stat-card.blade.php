{{--
    Props:
    - label: string
    - value: string
    - icon: string (emoji ou svg)
    - change: string|null  ex: "+12%" ou "-3%"
    - change-label: string|null  ex: "vs. mês anterior"
    - color: 'brand' | 'green' | 'amber' | 'blue' | 'gray'  (default: gray)
    - size: 'sm' | 'md' (default: md)
--}}
@props([
    'label'       => '',
    'value'       => '',
    'icon'        => '',
    'change'      => null,
    'changeLabel' => 'vs. mês anterior',
    'color'       => 'gray',
    'size'        => 'sm',
])

@php
    $colorMap = [
        'brand' => ['bar' => 'bg-brand',        'icon' => 'bg-brand-icon text-brand',       'value' => 'text-brand',       'change_up' => 'text-brand',       'change_down' => 'text-red-400'],
        'green' => ['bar' => 'bg-green-500',     'icon' => 'bg-green-500/10 text-green-400', 'value' => 'text-green-400',   'change_up' => 'text-green-400',   'change_down' => 'text-red-400'],
        'amber' => ['bar' => 'bg-amber-500',     'icon' => 'bg-amber-500/10 text-amber-400', 'value' => 'text-amber-400',   'change_up' => 'text-green-400',   'change_down' => 'text-red-400'],
        'blue'  => ['bar' => 'bg-blue-500',      'icon' => 'bg-blue-500/10 text-blue-400',   'value' => 'text-blue-400',    'change_up' => 'text-green-400',   'change_down' => 'text-red-400'],
        'gray'  => ['bar' => 'bg-white/20',      'icon' => 'bg-white/5 text-ink-muted',      'value' => 'text-ink',         'change_up' => 'text-green-400',   'change_down' => 'text-red-400'],
        'purple' => ['bar' => 'bg-purple-500',    'icon' => 'bg-purple-500/10 text-purple-400', 'value' => 'text-purple-400',  'change_up' => 'text-green-400',   'change_down' => 'text-red-400'],
    ];
    $c = $colorMap[$color] ?? $colorMap['gray'];

    $isUp   = $change && str_starts_with($change, '+');
    $isDown = $change && str_starts_with($change, '-');
    $changeColor = $isUp ? $c['change_up'] : ($isDown ? $c['change_down'] : 'text-ink-muted');
    $arrow = $isUp ? '↑' : ($isDown ? '↓' : '→');

    $padding   = $size === 'sm' ? 'p-4' : 'p-5';
    $valueSize = $size === 'sm' ? 'text-xl' : 'text-2xl';
    $iconSize  = $size === 'sm' ? 'w-8 h-8 text-sm' : 'w-10 h-10 text-base';
@endphp

<div class="metric-card metric-card-{{ $color }} {{ $padding }}">
    {{-- Barra top colorida --}}
    <div class="absolute top-0 left-0 right-0 h-px {{ $c['bar'] }} opacity-60"></div>

    <div class="flex items-start justify-between mb-3">
        <span class="text-xs font-semibold uppercase tracking-wider text-ink-muted">
            {{ $label }}
        </span>
        @if($icon)
            <div class="rounded-lg {{ $iconSize }} flex items-center justify-center {{ $c['icon'] }} flex-shrink-0">
                {{ $icon }}
            </div>
        @endif
    </div>

    <div class="{{ $valueSize }} font-impact {{ $c['value'] }} leading-none mb-2">
        {{ $value }}
    </div>

    @if($change)
        <div class="flex items-center gap-1.5 text-xs {{ $changeColor }}">
            <span>{{ $arrow }} {{ $change }}</span>
            <span class="text-ink-subtle">{{ $changeLabel }}</span>
        </div>
    @else
        {{ $slot }}
    @endif
</div>