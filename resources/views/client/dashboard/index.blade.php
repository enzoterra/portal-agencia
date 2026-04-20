@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Olá, ' . (auth()->user()->client?->trade_name ?? auth()->user()->client?->company_name ?? 'Cliente') . '! Aqui está o resumo da sua conta.')

@section('topbar-actions')
    <span class="text-xs text-ink-muted max-lg:hidden">
        {{ now()->translatedFormat('l, d \d\e F \d\e Y') }}
    </span>
@endsection

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

    {{-- ── STAT CARDS ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 {{ $showRoi ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }} gap-4 mb-6">

        {{-- ROI do Mês (só exibe se show_roi estiver ativo) --}}
        @if($showRoi)
            <x-stat-card label="ROI do Mês" value="{{ $latestReport ? number_format($latestReport->roi, 0) . '%' : '—' }}"
                color="purple" :change="$latestReport ? '+' . number_format($latestReport->roi - 200, 0) . '%' : null"
                change-label="vs. meta">
                <x-slot:icon>
                    <x-solar-graph-up-linear class="w-5 h-5" />
                </x-slot:icon>
            </x-stat-card>
        @endif

        {{-- Investimento --}}
        <x-stat-card label="Investimento"
            value="R$ {{ $latestReport ? number_format($latestReport->investment, 0, ',', '.') : '—' }}" color="blue"
            :change="null">
            <x-slot:icon>
                <x-iconsax-bro-money-send class="w-5 h-5" />
            </x-slot:icon>
            <span class="text-xs text-ink-subtle">
                {{ $latestReport ? $latestReport->reference_month->translatedFormat('F Y') : 'Sem dados' }}
            </span>
        </x-stat-card>

        {{-- Receita Gerada --}}
        <x-stat-card label="Receita Gerada"
            value="R$ {{ $latestReport ? number_format($latestReport->revenue, 0, ',', '.') : '—' }}" color="green"
            :change="null">
            <x-slot:icon>
                <x-iconsax-bro-money-recive class="w-5 h-5" />
            </x-slot:icon>
            <span class="text-xs text-ink-subtle">
                {{ $latestReport ? $latestReport->reference_month->translatedFormat('F Y') : 'Sem dados' }}
            </span>
        </x-stat-card>

        {{-- Próximo Pagamento --}}
        <x-stat-card label="Próximo Pagamento"
            value="{{ $nextPayment ? 'R$ ' . number_format($nextPayment->amount, 0, ',', '.') : 'Em dia' }}"
            :color="$nextPayment && $nextPayment->status === 'overdue' ? 'brand' : 'gray'" :change="null">
            <x-slot:icon>
                <x-heroicon-o-credit-card class="w-5 h-5" />
            </x-slot:icon>

            @if($nextPayment)
                <span class="text-xs {{ $nextPayment->status === 'overdue' ? 'text-brand' : 'text-ink-subtle' }}">
                    {{ $nextPayment->status === 'overdue' ? '⚠ Vencido' : 'Vence ' . $nextPayment->due_date->diffForHumans() }}
                </span>
            @else
                <span class="text-xs text-green-400 flex gap-1"><x-heroicon-o-check class="w-4 h-4" />Nenhum pendente</span>
            @endif
        </x-stat-card>

    </div>

    {{-- ── GRÁFICOS ────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 {{ $showRoi ? 'lg:grid-cols-5' : 'lg:grid-cols-1' }} gap-4 mb-6">

        {{-- Gráfico investimento vs receita --}}
        <div class="card p-5 {{ $showRoi ? 'lg:col-span-3' : '' }}">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-sm font-semibold text-ink font-title">Investimento vs. Receita</h3>
                    <p class="text-xs text-ink-muted mt-0.5">Últimos 6 meses</p>
                </div>
                <div class="flex items-center gap-4 text-xs text-ink-muted">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span> Investimento
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500 inline-block"></span> Receita
                    </span>
                </div>
            </div>
            <canvas id="barChart" height="180"></canvas>
        </div>

        {{-- ROI do mês em destaque (só exibe se show_roi estiver ativo) --}}
        @if($showRoi)
            <div class="card p-5 lg:col-span-2 flex flex-col">
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-ink font-title">ROI Acumulado</h3>
                    <p class="text-xs text-ink-muted mt-0.5">Evolução mensal</p>
                </div>

                <div class="flex-1 flex flex-col items-center justify-center text-center py-2">
                    <div
                        class="text-5xl font-impact @if($latestReport->roi > 0) text-green-400 @else text-sky-400 @endif leading-none mb-1">
                        {{ $latestReport ? number_format($latestReport->roi, 0) . '%' : '—' }}
                    </div>
                    <p class="text-xs text-ink-muted mb-4">
                        {{ $latestReport ? $latestReport->reference_month->translatedFormat('F \d\e Y') : 'Sem dados' }}
                    </p>

                    @if($latestReport)
                        <div class="w-full bg-surface-accent rounded-lg px-4 py-2.5 text-xs text-ink-subtle font-mono mb-4">
                            ({{ number_format($latestReport->revenue, 0, ',', '.') }} −
                            {{ number_format($latestReport->investment, 0, ',', '.') }})
                            / {{ number_format($latestReport->investment, 0, ',', '.') }} × 100
                        </div>

                        <div class="grid grid-cols-3 gap-3 w-full">
                            <div class="text-center">
                                <div class="text-sm font-bold text-blue-400">
                                    R$ {{ number_format($latestReport->investment, 0, ',', '.') }}
                                </div>
                                <div class="text-[10px] text-ink-subtle mt-0.5">Investido</div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm font-bold text-amber-400">
                                    R$ {{ number_format($latestReport->revenue, 0, ',', '.') }}
                                </div>
                                <div class="text-[10px] text-ink-subtle mt-0.5">Receita</div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm font-bold text-green-400">
                                    R$ {{ number_format($latestReport->revenue - $latestReport->investment, 0, ',', '.') }}
                                </div>
                                <div class="text-[10px] text-ink-subtle mt-0.5">Lucro</div>
                            </div>
                        </div>
                    @endif
                </div>

                <canvas id="roiLine" height="70" class="mt-3"></canvas>
            </div>
        @endif

    </div>

    {{-- ── LINHA INFERIOR: Pagamentos + Relatórios + PIX ─────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- Pagamentos recentes --}}
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-ink font-title">Pagamentos</h3>
                <a href="{{ route('cliente.financeiro.index') }}"
                    class="text-xs text-ink hover:text-brand transition-colors flex gap-1">
                    Ver todos <x-heroicon-m-arrow-small-right class="w-4 h-4" />
                </a>
            </div>

            @forelse($recentPayments ?? [] as $payment)
                    <div class="flex items-center justify-between py-3 border-b border-subtle last:border-0 last:pb-0">
                        <div>
                            <div class="text-sm font-medium text-ink">
                                {{ $payment->reference_month?->translatedFormat('M/Y') ?? $payment->due_date->translatedFormat('M/Y') }}
                            </div>
                            <div class="text-xs text-ink-muted mt-0.5">
                                {{ $payment->status === 'paid' ? 'Pago em ' . $payment->paid_at?->format('d/m/Y') : 'Vence ' . $payment->due_date->format('d/m/Y') }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-ink">
                                R$ {{ number_format($payment->amount, 0, ',', '.') }}
                            </div>
                            <div class="mt-1">
                                <span class="badge-{{ match ($payment->status) {
                    'paid' => 'green',
                    'pending' => 'amber',
                    'overdue' => 'red',
                    'cancelled' => 'gray',
                    default => 'gray',
                } }}">
                                    {{ match ($payment->status) {
                    'paid' => 'Pago',
                    'pending' => 'Pendente',
                    'overdue' => '⚠ Vencido',
                    'cancelled' => '✕ Cancelado',
                    default => $payment->status,
                } }}
                                </span>
                            </div>
                        </div>
                    </div>
            @empty
                <div class="empty-state py-8">
                    <div class="empty-state-icon"><x-heroicon-s-credit-card class="w-8 h-8" /></div>
                    <p class="empty-state-desc">Nenhum pagamento encontrado.</p>
                </div>
            @endforelse
        </div>

        {{-- Relatórios recentes --}}
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-ink font-title">Relatórios</h3>
                <a href="{{ route('cliente.relatorios.index') }}"
                    class="text-xs text-ink hover:text-brand transition-colors flex gap-1">
                    Ver todos <x-heroicon-m-arrow-small-right class="w-4 h-4" />
                </a>
            </div>

            @forelse($recentReports ?? [] as $report)
                <a href="{{ route('cliente.relatorios.show', $report) }}"
                    class="flex items-center gap-3 py-3 border-b border-subtle last:border-0 last:pb-0 group cursor-pointer">
                    <div
                        class="w-9 h-9 rounded-xl bg-blue-500/10 flex items-center justify-center text-base flex-shrink-0 group-hover:bg-blue-500/20 transition-colors">
                        <x-heroicon-s-document-chart-bar class="w-4 h-4 text-blue-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-ink truncate group-hover:text-brand transition-colors">
                            {{ $report->reference_month->translatedFormat('F/Y') }}
                        </div>
                        <div class="text-xs text-ink-muted mt-0.5">
                            Publicado {{ $report->published_at?->diffForHumans() }}
                        </div>
                    </div>
                    @if($showRoi)
                        <div class="text-sm font-bold text-green-400 flex-shrink-0">
                            {{ number_format($report->roi, 0) }}%
                        </div>
                    @endif
                </a>
            @empty
                <div class="empty-state py-8">
                    <div class="empty-state-icon"><x-heroicon-s-document-chart-bar class="w-8 h-8" /></div>
                    <p class="empty-state-desc">Nenhum relatório publicado ainda.</p>
                </div>
            @endforelse
        </div>

        {{-- QR Code PIX --}}
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-ink font-title flex gap-1 mb-4"><x-heroicon-o-qr-code class="w-4 h-4" />
                Pagar via PIX</h3>

            @php
                $settings = app(\App\Domain\Setting\Services\SettingService::class);
                $pixKey = $settings->get('pix_key', '');
                $pixName = $settings->get('pix_name', '');
                $pixCity = $settings->get('pix_city', '');
                $isPixConfigured = $pixKey && $pixName && $pixCity;

                $nextPaymentPayload = '';
                if ($nextPayment && $isPixConfigured) {
                    $nextPaymentPayload = app(\App\Domain\Financial\Services\PixService::class)->generatePayload($pixKey, $pixName, $pixCity, $nextPayment->amount, 'PGTO' . $nextPayment->uuid);
                }
            @endphp
            @if($nextPayment && $isPixConfigured)
                <div class="card-brand rounded-xl p-4 text-center">
                    <div class="text-2xl font-impact text-ink mb-1">
                        R$ {{ number_format($nextPayment->amount, 2, ',', '.') }}
                    </div>
                    <p class="text-xs text-ink-muted mb-4">
                        Vence {{ $nextPayment->due_date->format('d/m/Y') }}
                    </p>

                    {{-- QR Code --}}
                    <div class="w-32 h-32 mx-auto bg-white rounded-xl mb-4">
                        <img src="{{ app(\App\Domain\Financial\Services\PixService::class)->generateQrCodeBase64($nextPaymentPayload) }}"
                            alt="QR Code PIX" class="w-full h-full object-contain">
                    </div>

                    <button onclick="copyPix('{{ $nextPaymentPayload }}')" class="btn-primary w-full btn-sm flex gap-1">
                        <x-ri-file-copy-2-fill class="h-4 w-4" /> Copiar código PIX
                    </button>
                </div>
            @elseif($nextPayment)
                <div class="card-accent rounded-xl p-4 text-center">
                    <div class="text-2xl font-impact text-ink mb-1">
                        R$ {{ number_format($nextPayment->amount, 2, ',', '.') }}
                    </div>
                    <p class="text-xs text-ink-muted mb-3">
                        Vence {{ $nextPayment->due_date->format('d/m/Y') }}
                    </p>
                    <p class="text-xs text-ink-subtle">PIX Indisponível. Entre em contato com a agência.</p>
                </div>
            @else
                <div class="empty-state py-8">
                    <div class="empty-state-icon"><x-heroicon-c-check-circle class="w-8 h-8 text-green-400" /></div>
                    <p class="empty-state-title text-green-400">Pagamentos em dia!</p>
                    <p class="empty-state-desc">Nenhuma cobrança pendente.</p>
                </div>
            @endif
        </div>

    </div>

    {{-- ── NAVEGAÇÃO RÁPIDA ────────────────────────────────────────── --}}
    <div class="mb-2">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-ink-muted mb-3">Acesso Rápido</h3>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

        <x-nav-card href="{{ route('cliente.relatorios.index') }}" title="Relatórios"
            description="{{ $totalReports ?? 0 }} relatórios disponíveis">
            <x-slot:icon>
                <x-heroicon-s-document-chart-bar class="w-5 h-5" />
            </x-slot:icon>
        </x-nav-card>

        <x-nav-card href="{{ route('cliente.financeiro.index') }}" title="Pagamentos" :description="($pendingPayments ?? 0) > 0 ? ($pendingPayments . ' pagamento(s) pendente(s)') : 'Todos em dia'">
            <x-slot:icon>
                <x-heroicon-s-credit-card class="w-5 h-5" />
            </x-slot:icon>
        </x-nav-card>

        <x-nav-card href="{{ route('cliente.midias.index') }}" title="Mídias"
            description="Links e arquivos do Google Drive">
            <x-slot:icon>
                <x-heroicon-s-folder class="w-5 h-5" />
            </x-slot:icon>
        </x-nav-card>

        <x-nav-card href="{{ route('cliente.calendario.index') }}" title="Calendário" description="Agenda da agência">
            <x-slot:icon>
                <x-heroicon-s-calendar-date-range class="w-5 h-5" />
            </x-slot:icon>
        </x-nav-card>

    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const months = @json($roiHistory->pluck('reference_month')->map(fn($d) => \Carbon\Carbon::parse($d)->translatedFormat('M/Y')));
            const investment = @json($roiHistory->pluck('investment')->map(fn($v) => (float) $v));
            const revenue = @json($roiHistory->pluck('revenue')->map(fn($v) => (float) $v));
            @if($showRoi)
                const roi = @json($roiHistory->pluck('roi')->map(fn($v) => (float) $v));
            @endif

                    const gridColor = 'rgba(255,255,255,0.05)';
            const textColor = '#6B6B6B';

            Chart.defaults.color = textColor;
            Chart.defaults.font.family = 'Montserrat';
            Chart.defaults.font.size = 11;

            // Bar chart — Investimento vs Receita
            new Chart(document.getElementById('barChart'), {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Investimento',
                            data: investment,
                            backgroundColor: 'rgba(59,130,246,0.7)',
                            borderRadius: 6,
                            borderSkipped: false,
                        },
                        {
                            label: 'Receita',
                            data: revenue,
                            backgroundColor: 'rgba(16, 207, 112, 0.7)',
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

            @if($showRoi)
                // Line chart — ROI histórico
                new Chart(document.getElementById('roiLine'), {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            data: roi,
                            borderColor: '#0572caff',
                            backgroundColor: 'rgba(189,22,19,0.08)',
                            fill: true,
                            tension: 0.45,
                            pointRadius: 3,
                            pointBackgroundColor: '#0067bbff',
                            pointBorderColor: '#000',
                            pointBorderWidth: 2,
                        }]
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
                                ticks: { callback: v => v + '%' }
                            }
                        }
                    }
                });
            @endif

                });

        function copyPix(key) {
            if (!key) return;
            navigator.clipboard.writeText(key).then(() => {
                const btn = event.target.closest('button');
                const original = btn.innerHTML;
                btn.innerHTML = '✓ Copiado!';
                btn.classList.add('opacity-75');
                setTimeout(() => { btn.innerHTML = original; btn.classList.remove('opacity-75'); }, 2000);
            });
        }
    </script>
@endpush