@extends('layouts.app')

@section('title', 'Calendário')
@section('page-title', 'Calendário')
@section('page-subtitle', 'Confira os eventos agendados')

@section('content')
    <div>

        <div class="grid grid-cols-1 xl:grid-cols-[1fr_300px] gap-5">

            {{-- =============================================
            COLUNA ESQUERDA — Grade mensal
            ============================================= --}}
            <div>

                {{-- Navegação de mês --}}
                <div class="flex items-center rounded-lg justify-between mb-4 bg-surface-accent border border-white/10">
                    <a href="{{ route('cliente.calendario.index', ['month' => $prevMonth->format('Y-m')]) }}"
                        class="btn-icon btn-icon-bar-left" title="Mês anterior">
                        <x-heroicon-o-chevron-left class="w-4 h-4" />
                    </a>

                    <h2 class="text-sm font-semibold text-ink capitalize">
                        {{ $currentMonth->translatedFormat('F Y') }}
                    </h2>

                    <a href="{{ route('cliente.calendario.index', ['month' => $nextMonth->format('Y-m')]) }}"
                        class="btn-icon btn-icon-bar-right" title="Próximo mês">
                        <x-heroicon-o-chevron-right class="w-4 h-4" />
                    </a>
                </div>

                {{-- Grade --}}
                <div class="card overflow-hidden">

                    {{-- Cabeçalho dias da semana --}}
                    <div class="grid grid-cols-7 border-b border-white/[0.07]">
                        @foreach(['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'] as $day)
                            <div class="py-2.5 text-center text-[10px] font-semibold uppercase tracking-wider text-ink-muted">
                                {{ $day }}
                            </div>
                        @endforeach
                    </div>

                    {{-- Células --}}
                    <div class="grid grid-cols-7">
                        @php
                            // Dias em branco antes do dia 1
                            $startDow = (int) $currentMonth->copy()->startOfMonth()->dayOfWeek; // 0=Dom
                        @endphp

                        {{-- Células vazias do início --}}
                        @for($i = 0; $i < $startDow; $i++)
                            <div class="min-h-[88px] p-2 border-b border-r border-white/[0.04] bg-black/20"></div>
                        @endfor

                        {{-- Dias do mês --}}
                        @for($day = 1; $day <= $currentMonth->daysInMonth; $day++)
                            @php
                                $date = $currentMonth->copy()->setDay($day);
                                $dateStr = $date->toDateString();
                                $isToday = $date->isToday();
                                $isPast = $date->isPast() && !$isToday;
                                $isWeekend = $date->isWeekend();
                                $dayEvents = $eventsByDay[$dateStr] ?? collect();

                                // Última célula da linha não tem border-r
                                $col = ($startDow + $day - 1) % 7;
                            @endphp

                            <div class="min-h-[88px] p-2 border-b border-r border-white/[0.04] flex flex-col gap-1 transition-colors
                                                {{ $isToday ? 'bg-sky-600/[0.04]' : '' }}
                                                {{ $isPast ? 'opacity-60' : '' }}
                                                {{ $isWeekend && !$isToday ? 'bg-white/[0.01]' : '' }}
                                                {{ $col === 6 ? 'border-r-0' : '' }}" x-data @if($dayEvents->count() > 0)
                                                    @click="$dispatch('show-day-events', { date: '{{ $date->translatedFormat('d \d\e F') }}', events: {{ $dayEvents->map(fn($e) => ['title' => $e->title, 'time' => $e->all_day ? 'Dia todo' : $e->starts_at->format('H:i'), 'color' => $e->color, 'is_client' => (bool) $e->client_id, 'description' => $e->description])->values()->toJson() }} })"
                                                class="cursor-pointer" @endif>
                                {{-- Número do dia --}}
                                <div class="flex justify-end mb-1">
                                    <span class="w-6 h-6 flex items-center justify-center rounded-full text-xs font-medium
                                                    {{ $isToday ? 'bg-sky-600 text-white font-bold' : 'text-ink-muted' }}">
                                        {{ $day }}
                                    </span>
                                </div>

                                {{-- Eventos do dia (máx 3, resto vira "+N") --}}
                                @foreach($dayEvents->take(3) as $event)
                                    <div class="w-full text-left px-1.5 py-0.5 rounded text-[10px] font-medium truncate leading-tight bg-brand/20 text-brand"
                                        title="{{ $event->title }}">
                                        @if(!$event->all_day)
                                            <span class="opacity-70">{{ $event->starts_at->format('H:i') }}</span>
                                        @endif
                                        {{ Str::limit($event->title, 22) }}
                                    </div>
                                @endforeach

                                @if($dayEvents->count() > 3)
                                    <div class="text-[10px] text-ink-subtle pl-1">
                                        +{{ $dayEvents->count() - 3 }} mais
                                    </div>
                                @endif

                            </div>
                        @endfor

                        {{-- Células vazias do fim para completar a última linha --}}
                        @php
                            $totalCells = $startDow + $currentMonth->daysInMonth;
                            $remaining = (7 - ($totalCells % 7)) % 7;
                        @endphp
                        @for($i = 0; $i < $remaining; $i++)
                            <div class="min-h-[88px] p-2 border-b border-white/[0.04] bg-black/20 last:border-r-0"></div>
                        @endfor

                    </div>
                </div>

                {{-- Legenda --}}
                <div class="flex items-center gap-4 mt-3 px-1">
                    <div class="flex items-center gap-1.5">
                        <div class="w-2.5 h-2.5 rounded-sm bg-brand/20 border border-brand/40"></div>
                        <span class="text-xs text-ink-muted">Evento seu</span>
                    </div>
                </div>

            </div>

            {{-- =============================================
            COLUNA DIREITA — Próximos eventos
            ============================================= --}}
            <div class="flex flex-col gap-4">

                {{-- Hoje --}}
                <div class="card p-4">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-2 h-2 rounded-full bg-brand animate-pulse"></div>
                        <span class="text-xs font-semibold text-ink-muted uppercase tracking-wider">Hoje</span>
                    </div>
                    <p class="text-sm font-semibold text-ink capitalize">
                        {{ now()->translatedFormat('l, d \d\e F') }}
                    </p>
                </div>

                {{-- Próximos eventos --}}
                <div class="card p-4">
                    <h3 class="text-xs font-semibold text-ink-muted uppercase tracking-wider mb-3">
                        Próximos eventos
                    </h3>

                    @forelse($upcomingEvents as $event)
                                <div class="flex gap-3 py-3 border-b border-white/[0.06] last:border-b-0 last:pb-0 first:pt-0">

                                    <div class="flex flex-col items-center gap-1 pt-0.5 shrink-0">
                                        <div class="w-2 h-2 rounded-full bg-brand"></div>
                                        <div class="w-px flex-1 bg-white/[0.06]"></div>
                                    </div>

                                    <div class="flex-1 min-w-0 pb-1">
                                        <p class="text-sm font-semibold text-ink leading-snug">
                                            {{ $event->title }}
                                        </p>

                                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                                            {{-- Data --}}
                                            <span class="text-xs text-ink-muted capitalize">
                                                {{ $event->starts_at->isToday()
                        ? 'Hoje'
                        : ($event->starts_at->isTomorrow()
                            ? 'Amanhã'
                            : $event->starts_at->translatedFormat('d M')) }}
                                            </span>

                                            {{-- Hora --}}
                                            @if(!$event->all_day)
                                                <span class="text-xs text-ink-subtle font-mono">
                                                    {{ $event->starts_at->format('H:i') }}
                                                </span>
                                            @else
                                                <span class="badge badge-gray" style="font-size:9px;padding:1px 6px;">Dia todo</span>
                                            @endif


                                        </div>

                                        @if($event->description)
                                            <p class="text-xs text-ink-subtle mt-1 line-clamp-2 leading-relaxed">
                                                {{ $event->description }}
                                            </p>
                                        @endif
                                    </div>

                                </div>
                    @empty
                        <div class="py-6 text-center">
                            <x-heroicon-o-calendar class="w-8 h-8 text-ink-subtle mx-auto mb-2" />
                            <p class="text-xs text-ink-muted">Nenhum evento próximo</p>
                        </div>
                    @endforelse
                </div>

            </div>

        </div>

    </div>

    {{-- =============================================
    MODAL — Eventos do dia (ao clicar na célula)
    ============================================= --}}
    <div x-data="dayModal()" x-on:show-day-events.window="open($event.detail)">
        <dialog x-ref="dialog" class="card w-full max-w-sm p-0 backdrop:bg-black/60 open:animate-fade-in"
            @click.self="$refs.dialog.close()">
            <div class="flex items-center justify-between px-5 py-4 border-b border-white/[0.07]">
                <h2 class="text-sm font-semibold text-ink capitalize" x-text="date"></h2>
                <button @click="$refs.dialog.close()" class="btn-ghost btn-sm p-1">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </div>

            <div class="px-5 py-4 space-y-3 max-h-80 overflow-y-auto">
                <template x-for="(event, i) in events" :key="i">
                    <div class="flex gap-3 items-start" style="margin-bottom: 1rem;">
                        <div class="w-1.5 h-1.5 rounded-full mt-1.5 shrink-0 bg-brand"></div>
                        <div>
                            <p class="text-sm font-medium text-ink" x-text="event.title"></p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-ink-muted font-mono" x-text="event.time"></span>
                            </div>
                            <p x-show="event.description" class="text-xs text-ink-muted mt-1 leading-relaxed"
                                x-text="event.description"></p>
                        </div>
                    </div>
                </template>
            </div>
        </dialog>
    </div>

@endsection

@push('scripts')
    <script>
        function dayModal() {
            return {
                date: '',
                events: [],
                open(detail) {
                    this.date = detail.date;
                    this.events = detail.events;
                    this.$refs.dialog.showModal();
                }
            }
        }
    </script>
@endpush