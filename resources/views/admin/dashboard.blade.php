@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Visão Geral')
@section('page-subtitle', 'Resumo de todos os clientes e operações da agência.')

@section('topbar-actions')
    <a href="{{ route('admin.clientes.create') }}" class="btn-primary btn-sm">
        <x-heroicon-m-plus-circle class="w-5 h-5" /> Novo Cliente
    </a>
@endsection

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

    {{-- ── STAT CARDS ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <x-stat-card label="Clientes Ativos" value="{{ $activeClients }}" color="blue" :change="null">
            <span class="text-xs text-ink-subtle">{{ $totalClients - $activeClients }} inativos</span>
        </x-stat-card>

        <x-stat-card label="Receita Gerada" value="R$ {{ number_format($totalRevenue / 1000, 0) }}k" color="green"
            :change="null">
            <span class="text-xs text-ink-subtle">Soma dos relatórios</span>
        </x-stat-card>

        <x-stat-card label="Recebido (Mês)" value="R$ {{ number_format($financial->total_paid, 0, ',', '.') }}"
            color="amber" :change="null">
            <span class="text-xs text-ink-subtle">
                R$ {{ number_format($financial->total_pending, 0, ',', '.') }} pendente
            </span>
        </x-stat-card>

        <x-stat-card label="Inadimplentes" value="{{ $financial->overdue_count }}" :color="$financial->overdue_count > 0 ? 'brand' : 'gray'" :change="null">
            @if($financial->overdue_count > 0)
                <span class="text-xs text-brand">
                    R$ {{ number_format($financial->total_overdue, 0, ',', '.') }} em atraso
                </span>
            @else
                <span class="mt-1 text-xs text-green-400 flex gap-1"><x-heroicon-o-check class="w-4 h-4" /> Nenhum em
                    atraso</span>
            @endif
        </x-stat-card>

    </div>

    <div class="mb-2 mt-6 pt-3 lg:hidden">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-ink-muted">Resultados</h3>
    </div>

    {{-- ── GRÁFICO + CLIENTES COM ROI ─────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 mb-6">

        {{-- Receita vs Investimento --}}
        <div class="card p-5 lg:col-span-3">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-sm font-semibold text-ink font-title">Receita vs. Investimento</h3>
                    <p class="text-xs text-ink-muted mt-0.5">Consolidado de todos os clientes — últimos 6 meses</p>
                </div>
                <div class="flex items-center gap-4 text-xs text-ink-muted">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span> Investimento
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> Receita
                    </span>
                </div>
            </div>
            <canvas id="revenueChart" height="180"></canvas>
        </div>

        {{-- ROI por cliente --}}
        <div class="card p-5 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-ink font-title">ROI por Cliente</h3>
                    <p class="text-xs text-ink-muted mt-0.5">Último relatório publicado</p>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($clientsWithRoi as $client)
                    @php $report = $client->reports->first(); @endphp
                    <div class="flex items-center gap-3">
                        <div
                            class="w-7 h-7 rounded-lg bg-brand-icon flex items-center justify-center text-xs font-bold text-brand flex-shrink-0">
                            {{ strtoupper(substr($client->company_name, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-medium text-ink truncate">
                                    {{ $client->trade_name ?? $client->company_name }}
                                </span>
                                <span
                                    class="text-xs font-bold {{ $report && $report->roi > 0 ? 'text-green-400' : 'text-ink-muted' }} ml-2 flex-shrink-0">
                                    {{ $report ? number_format($report->roi, 0) . '%' : '—' }}
                                </span>
                            </div>
                            <div class="w-full bg-surface-accent rounded-full h-1.5">
                                <div class="h-1.5 rounded-full bg-brand transition-all duration-500"
                                    style="width: {{ $report ? min(100, max(0, $report->roi / 3)) : 0 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state py-6">
                        <p class="empty-state-desc">Nenhum relatório publicado ainda.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <div class="mb-2 mt-6 pt-3 lg:hidden">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-ink-muted">Calendário</h3>
    </div>

    {{-- ── LINHA INFERIOR ──────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- Calendário — próximos 7 dias --}}
        <div class="card p-5 lg:col-span-3">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-blue-500/10 flex items-center justify-center">
                        <x-heroicon-o-calendar-days class="w-4 h-4 text-blue-400" />
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-ink font-title">Agenda</h3>
                        <p class="text-xs text-ink-muted">
                            {{ $calendarThisMonth }} evento{{ $calendarThisMonth != 1 ? 's' : '' }} este mês
                        </p>
                    </div>
                </div>
                <a href="{{ route('admin.configuracoes.index') }}?tab=google" class="btn-icon"
                    title="Configurar sincronização Google">
                    <x-heroicon-o-arrow-path class="w-4 h-4" />
                </a>
            </div>

            {{-- Hoje --}}
            @if($calendarToday->count())
                <div class="mb-3">
                    <div class="flex items-center gap-1.5 mb-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-brand animate-pulse"></div>
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-ink-subtle">
                            Hoje · {{ now()->translatedFormat('d \d\e F') }}
                        </p>
                    </div>
                    <div class="space-y-1.5">
                        @foreach($calendarToday as $event)
                            <div
                                class="flex items-start gap-2.5 px-3 py-2 rounded-xl
                                                                    {{ $event->client_id ? 'bg-brand/[0.08] border border-brand/20' : 'bg-surface-accent border border-white/[0.07]' }}">
                                <div class="w-1 h-full min-h-[28px] rounded-full shrink-0 mt-1
                                                                        {{ $event->client_id ? 'bg-brand' : 'bg-white/20' }}">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-ink truncate">{{ $event->title }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] text-ink-muted font-mono">
                                            {{ $event->all_day ? 'Dia todo' : $event->starts_at->format('H:i') }}
                                        </span>
                                        @if($event->client_id && $event->client)
                                            <span class="badge badge-red" style="font-size:9px;padding:1px 6px;">
                                                {{ $event->client?->trade_name ?? $event->client?->company_name ?? 'Cliente Removido' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="divider"></div>
            @endif

            {{-- Próximos 7 dias (exceto hoje) --}}
            @php $futureEvents = $calendarUpcoming->filter(fn($e) => !$e->starts_at->isToday()); @endphp

            @if($futureEvents->count())
                <div class="mb-1">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-ink-subtle mb-2">
                        Próximos 7 dias
                    </p>
                </div>
                <div class="space-y-2">
                    @foreach($futureEvents as $event)
                        <div class="flex gap-3 items-start py-1.5">
                            <div class="flex flex-col items-center gap-0.5 w-8 shrink-0 text-center">
                                <span class="text-[10px] font-semibold text-ink-muted uppercase">
                                    {{ $event->starts_at->translatedFormat('D') }}
                                </span>
                                <span class="text-sm font-bold text-ink leading-none">
                                    {{ $event->starts_at->format('d') }}
                                </span>
                            </div>
                            <div class="w-px self-stretch bg-white/[0.06] shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-ink truncate">{{ $event->title }}</p>
                                <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                    <span class="text-[10px] text-ink-muted font-mono">
                                        {{ $event->all_day ? 'Dia todo' : $event->starts_at->format('H:i') }}
                                    </span>
                                    @if($event->client_id && $event->client)
                                        <span class="badge badge-red" style="font-size:9px;padding:1px 6px;">
                                            {{ $event->client?->trade_name ?? $event->client?->company_name ?? 'Cliente Removido' }}
                                        </span>
                                    @endif
                                    @if($event->location)
                                        <span class="text-[10px] text-ink-subtle truncate">
                                            {{ Str::limit($event->location, 20) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif(!$calendarToday->count())
                <div class="py-8 text-center">
                    <x-heroicon-o-calendar class="w-8 h-8 text-ink-subtle mx-auto mb-2" />
                    <p class="text-sm text-ink-muted">Nenhum evento nos próximos 7 dias</p>
                    <p class="text-xs text-ink-subtle mt-1">
                        Sincronize o Google Calendar nas
                        <a href="{{ route('admin.configuracoes.index') }}" class="text-brand hover:underline">configurações</a>.
                    </p>
                </div>
            @endif
        </div>

        {{-- Coluna lateral vazia para futuro uso / mantém grid 3 colunas --}}
        <div class="space-y-4">
            {{-- Rascunhos pendentes --}}
            @if($draftReports->count())
                <div class="card p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 flex items-center justify-center">
                            <x-heroicon-o-document-text class="w-4 h-4 text-amber-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-ink font-title">Rascunhos</h3>
                            <p class="text-xs text-ink-muted">{{ $draftReports->count() }} aguardando publicação</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        @foreach($draftReports->take(4) as $report)
                            <a href="{{ route('admin.relatorios.edit', $report->uuid) }}"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-surface-accent hover:bg-white/[0.05] border border-white/[0.07] transition-colors group">
                                    <div
                                        class="w-6 h-6 rounded-lg bg-amber-500/10 flex items-center justify-center text-[10px] font-bold text-amber-400 shrink-0">
                                        {{ strtoupper(substr($report->client?->company_name ?? '?', 0, 2)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-ink truncate group-hover:text-brand transition-colors">
                                            {{ $report->title }}
                                        </p>
                                        <p class="text-[10px] text-ink-subtle capitalize">
                                            {{ $report->client?->trade_name ?? $report->client?->company_name ?? 'Cliente Removido' }}
                                        </p>
                                    </div>
                                <x-heroicon-o-pencil class="w-3.5 h-3.5 text-ink-subtle group-hover:text-brand shrink-0" />
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Inadimplentes --}}
            @if($overdueClients->count())
                <div class="card p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-xl bg-brand/10 flex items-center justify-center">
                            <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-brand" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-ink font-title">Inadimplentes</h3>
                            <p class="text-xs text-ink-muted">{{ $overdueClients->count() }} em atraso</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        @foreach($overdueClients->take(3) as $client)
                            <a href="{{ route('admin.clientes.show', $client->uuid) }}"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-brand/[0.05] hover:bg-brand/10 border border-brand/20 transition-colors">
                                <div
                                    class="w-6 h-6 rounded-lg bg-brand-icon flex items-center justify-center text-[10px] font-bold text-brand shrink-0">
                                    {{ strtoupper(substr($client->company_name, 0, 2)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-ink truncate">
                                        {{ $client->trade_name ?? $client->company_name }}
                                    </p>
                                    <p class="text-[10px] text-brand">
                                        R$ {{ number_format($client->payments->sum('amount'), 0, ',', '.') }} em atraso
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    </div>


    {{-- ── ATALHOS ADMIN ───────────────────────────────────────────── --}}
    <div class="mb-2">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-ink-muted mb-3">Ações Rápidas</h3>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

        <x-nav-card href="{{ route('admin.clientes.create') }}" title="Novo Cliente" description="Cadastrar novo cliente">
            <x-slot:icon>
                <x-heroicon-c-user-plus class="w-6 h-6" />
            </x-slot:icon>
        </x-nav-card>

        <x-nav-card href="{{ route('admin.relatorios.create') }}" title="Novo Relatório"
            :description="$draftReports->count() . ' rascunho(s)'" :badge="$draftReports->count() > 0 ? $draftReports->count() : null" badge-color="amber">
            <x-slot:icon>
                <x-heroicon-s-document-plus class="w-6 h-6" />
            </x-slot:icon>
        </x-nav-card>

        <x-nav-card href="{{ route('admin.financeiro.index') }}?modal=upload-invoice" title="Upload Nota Fiscal"
            description="Enviar PDF para cliente">
            <x-slot:icon>
                <x-heroicon-c-document-arrow-up class="w-6 h-6" />
            </x-slot:icon>
        </x-nav-card>

        <x-nav-card href="{{ route('admin.financeiro.index') }}" title="Pagamentos" :description="$financial->pending_count . ' pendente(s)'" :badge="$financial->overdue_count > 0 ? $financial->overdue_count . ' vencido(s)' : null"
            badge-color="brand">
            <x-slot:icon>
                <x-heroicon-c-credit-card class="w-6 h-6" />
            </x-slot:icon>
        </x-nav-card>

    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const months = @json($revenueByMonth->pluck('month')->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('M/Y')));
            const investment = @json($revenueByMonth->pluck('investment')->map(fn($v) => (float) $v));
            const revenue = @json($revenueByMonth->pluck('revenue')->map(fn($v) => (float) $v));

            const gridColor = 'rgba(255,255,255,0.05)';

            Chart.defaults.color = '#6B6B6B';
            Chart.defaults.font.family = 'Montserrat';
            Chart.defaults.font.size = 11;

            new Chart(document.getElementById('revenueChart'), {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Investimento',
                            data: investment,
                            backgroundColor: 'rgba(20, 98, 200, 0.7)',
                            borderRadius: 6,
                            borderSkipped: false,
                        },
                        {
                            label: 'Receita',
                            data: revenue,
                            backgroundColor: 'rgba(12, 197, 71, 0.7)',
                            borderRadius: 6,
                            borderSkipped: false,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { color: gridColor }, border: { display: false } },
                        y: {
                            grid: { color: gridColor },
                            border: { display: false },
                            ticks: { callback: v => 'R$' + (v / 1000).toFixed(0) + 'k' }
                        }
                    }
                }
            });

        });
    </script>
@endpush