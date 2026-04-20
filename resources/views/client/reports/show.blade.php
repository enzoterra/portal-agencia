@extends('layouts.app')

@section('title', $report->title ?: $report->reference_month->translatedFormat('F/Y'))
@section('page-title', $report->title ?: 'Relatório de ' . $report->reference_month->translatedFormat('F/Y'))
@section('page-subtitle', $report->reference_month->translatedFormat('F \d\e Y') . ' · Publicado em ' . $report->published_at?->format('d/m/Y'))

@section('topbar-actions')
    <div class="flex items-center gap-3">
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <button @click="open = !open" class="btn-secondary btn-sm flex items-center gap-2">
                <x-heroicon-o-calendar class="w-4 h-4" />
                <span>{{ $report->reference_month->translatedFormat('F/Y') }}</span>
                <x-heroicon-o-chevron-down class="w-3 h-3 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
            </button>
            
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-48 card shadow-xl z-50 overflow-hidden py-1 border border-white/10"
                 style="display: none;">
                <div class="max-h-60 overflow-y-auto">
                    @foreach($availableReports as $avReport)
                        <a href="{{ route('cliente.relatorios.show', $avReport->uuid) }}" 
                           class="block px-4 py-2 text-sm {{ $report->uuid === $avReport->uuid ? 'text-brand bg-brand/10 font-medium' : 'text-ink-muted hover:bg-white/5 hover:text-ink' }}">
                            {{ $avReport->reference_month->translatedFormat('F/Y') }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <a href="{{ route('cliente.relatorios.index') }}" class="btn-secondary btn-sm">
            <x-heroicon-o-arrow-left class="w-4 h-4" /> Voltar
        </a>
    </div>
@endsection

@section('content')
@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

    @php
        // Os dados estão em colunas separadas no modelo — monta o array $m para compatibilidade com a view
        $m = [
            'paid_conversations'  => $report->paid_conversations,
            'cpc'                 => $report->cpc,
            'ig_publications'     => $report->ig_publications,
            'ig_interactions'     => $report->ig_interactions,
            'ig_reach'            => $report->ig_reach,
            'ig_views'            => $report->ig_views,
            'ig_profile_visits'   => $report->ig_profile_visits,
            'ig_new_followers'    => $report->ig_new_followers,
            'top_contents'        => $report->top_contents ?? [],
            'audience_locations'  => $report->audience_locations ?? [],
            'audience_age'        => $report->audience_age ?? [],
            'audience_gender'     => $report->audience_gender ?? [],
            'next_month_goals'    => $report->next_month_goals,
        ];
    @endphp

    <div class="space-y-6">

        {{-- =============================================
        CARDS PRINCIPAIS — ROI, Investimento, Receita
        ============================================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-{{ auth()->user()->client?->show_roi ? '3' : '2' }} gap-4">

            {{-- ROI (só exibe se show_roi estiver ativo) --}}
            @if(auth()->user()->client?->show_roi)
                <div class="metric-card {{ $report->roi >= 0 ? 'metric-card-green' : 'metric-card-brand' }}">
                    <div class="flex items-start justify-between mb-3">
                        <div
                            class="w-9 h-9 rounded-xl {{ $report->roi >= 0 ? 'bg-green-500/10' : 'bg-brand/10' }} flex items-center justify-center">
                            <x-heroicon-o-arrow-trending-up
                                class="w-4 h-4 {{ $report->roi >= 0 ? 'text-green-400' : 'text-brand' }}" />
                        </div>
                        <span class="badge {{ $report->roi >= 0 ? 'badge-green' : 'badge-red' }}">
                            {{ $report->roi >= 0 ? '▲' : '▼' }} ROI
                        </span>
                    </div>
                    <p class="text-2xl font-bold {{ $report->roi >= 0 ? 'text-green-400' : 'text-brand' }} mb-0.5">
                        {{ $report->roi !== null ? ($report->roi >= 0 ? '+' : '') . number_format($report->roi, 0) . '%' : '—' }}
                    </p>
                    <p class="text-xs text-ink-muted">Retorno sobre investimento</p>
                </div>
            @endif

            {{-- Investimento --}}
            <div class="metric-card metric-card-amber">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 flex items-center justify-center">
                        <x-heroicon-o-banknotes class="w-4 h-4 text-amber-400" />
                    </div>
                </div>
                <p class="text-2xl font-bold text-ink mb-0.5">
                    R$ {{ number_format($report->investment, 2, ',', '.') }}
                </p>
                <p class="text-xs text-ink-muted">Valor investido em tráfego pago</p>
            </div>

            {{-- Receita --}}
            <div class="metric-card metric-card-blue">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-500/10 flex items-center justify-center">
                        <x-heroicon-o-currency-dollar class="w-4 h-4 text-blue-400" />
                    </div>
                </div>
                <p class="text-2xl font-bold text-ink mb-0.5">
                    R$ {{ number_format($report->revenue, 2, ',', '.') }}
                </p>
                <p class="text-xs text-ink-muted">Receita gerada no período</p>
            </div>

        </div>

        {{-- =============================================
        TRÁFEGO PAGO
        ============================================= --}}
        @if(data_get($m, 'paid_conversations') || data_get($m, 'cpc'))
            <div class="card p-6">
                <h2 class="text-sm font-semibold text-ink mb-5 flex items-center gap-2">
                    <x-heroicon-o-currency-dollar class="w-4 h-4 text-brand" /> Tráfego Pago
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">

                    @if(data_get($m, 'paid_conversations') !== null)
                        <div class="card-accent rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold text-ink">
                                {{ number_format(data_get($m, 'paid_conversations')) }}
                            </p>
                            <p class="text-xs text-ink-muted mt-1">Conversas</p>
                        </div>
                    @endif

                    @if(data_get($m, 'cpc') !== null)
                        <div class="card-accent rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold text-ink">
                                R$ {{ number_format(data_get($m, 'cpc'), 2, ',', '.') }}
                            </p>
                            <p class="text-xs text-ink-muted mt-1">Custo por Clique (CPC)</p>
                        </div>
                    @endif

                </div>
            </div>
        @endif

        {{-- =============================================
        INSTAGRAM
        ============================================= --}}
        @php
            $igFields = [
                'ig_publications' => ['label' => 'Publicações', 'icon' => 'heroicon-o-squares-plus'],
                'ig_interactions' => ['label' => 'Interações', 'icon' => 'heroicon-o-heart'],
                'ig_reach' => ['label' => 'Alcance', 'icon' => 'heroicon-o-signal'],
                'ig_views' => ['label' => 'Visualizações', 'icon' => 'heroicon-o-play'],
                'ig_profile_visits' => ['label' => 'Visitas ao Perfil', 'icon' => 'heroicon-o-eye'],
                'ig_new_followers' => ['label' => 'Novos Seguidores', 'icon' => 'heroicon-o-user-plus'],
            ];
            $hasIg = collect($igFields)->keys()->some(fn($k) => data_get($m, $k) !== null);
        @endphp

        @if($hasIg)
            <div class="card p-6">
                <h2 class="text-sm font-semibold text-ink mb-5 flex items-center gap-2">
                    <x-ri-instagram-line class="w-4 h-4 text-brand" /> Resultados Instagram
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    @foreach($igFields as $key => $meta)
                        @if(data_get($m, $key) !== null)
                            <div class="card-accent rounded-xl p-4 text-center">
                                <p class="text-xl font-bold text-ink">
                                    {{ is_numeric(data_get($m, $key))
                                ? number_format(data_get($m, $key))
                                : data_get($m, $key) }}
                                </p>
                                <p class="text-xs text-ink-muted mt-1">{{ $meta['label'] }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Gráfico de barras Instagram --}}
                {{--
                @php
                    $chartIgLabels = [];
                    $chartIgValues = [];
                    foreach ($igFields as $key => $meta) {
                        $val = data_get($m, $key);
                        if ($val !== null && is_numeric($val)) {
                            $chartIgLabels[] = $meta['label'];
                            $chartIgValues[] = (float) $val;
                        }
                    }
                @endphp
                @if(count($chartIgValues) >= 1)
                    <div class="mt-5">
                        <canvas id="igChart" height="80"></canvas>
                    </div>
                @endif
                --}}
            </div>
        @endif

        {{-- =============================================
        TOP CONTEÚDOS
        ============================================= --}}
        @php $topContents = array_filter(data_get($m, 'top_contents', []), fn($c) => !empty($c['url']) || !empty($c['title'])); @endphp

        @if(count($topContents) > 0)
            <div class="card p-6">
                <h2 class="text-sm font-semibold text-ink mb-5 flex items-center gap-2">
                    <x-heroicon-o-star class="w-4 h-4 text-brand" /> Top Conteúdos do Mês
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach($topContents as $i => $content)
                        <div class="card-accent rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <div
                                    class="w-6 h-6 rounded-lg bg-brand flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                                    {{ $i + 1 }}
                                </div>
                                <span class="text-sm font-medium text-ink">{{ $i + 1 }}º Conteúdo</span>
                            </div>

                            @if(!empty($content['title']))
                                <p class="text-sm font-medium text-ink mb-3">{{ $content['title'] }}</p>
                            @endif

                            @if(!empty($content['description']))
                                <p class="text-xs text-ink-muted mb-3">{{ $content['description'] }}</p>
                            @endif

                            @if(!empty($content['url']))
                                <a href="{{ $content['url'] }}" target="_blank" rel="noopener noreferrer"
                                    class="btn-secondary btn-sm text-xs w-full justify-center">
                                    <x-ri-instagram-line class="w-3.5 h-3.5" />
                                    Ver no Instagram
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- =============================================
        NOSSO PÚBLICO
        ============================================= --}}
        @php
            $locations = array_filter(data_get($m, 'audience_locations', []), fn($l) => !empty($l['city']));
            $ageData = array_filter(data_get($m, 'audience_age', []), fn($v) => $v !== null && $v !== '');
            $genderData = array_filter(data_get($m, 'audience_gender', []), fn($v) => $v !== null && $v !== '');
            $hasAudience = count($locations) || count($ageData) || count($genderData);
        @endphp

        @if($hasAudience)
            <div class="card p-6">
                <h2 class="text-sm font-semibold text-ink mb-5 flex items-center gap-2">
                    <x-heroicon-o-users class="w-4 h-4 text-brand" /> Nosso Público
                </h2>

                <div class="grid grid-cols-1 lg:grid-cols-3" style="gap: 3rem;">

                    {{-- Localizações --}}
                    @if(count($locations))
                        <div>
                            <p class="text-xs font-semibold text-ink-muted uppercase tracking-wider mb-3">
                                Principais Localizações
                            </p>
                            <div class="space-y-2.5">
                                @foreach($locations as $loc)
                                    <div class="flex items-center gap-3">
                                        <div class="w-5 h-5 rounded-md bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                                            <x-heroicon-o-map-pin class="w-3 h-3 text-blue-400" />
                                        </div>
                                        <span class="text-sm text-ink flex-1">{{ $loc['city'] }}</span>
                                        @if(!empty($loc['percentage']))
                                            <div class="flex items-center gap-2">
                                                <div class="w-20 h-1.5 rounded-full bg-white/10 overflow-hidden">
                                                    <div class="h-full rounded-full bg-blue-400 transition-all"
                                                        style="width: {{ min($loc['percentage'], 100) }}%"></div>
                                                </div>
                                                <span
                                                    class="text-xs text-ink-muted font-mono w-10 text-right">{{ $loc['percentage'] }}%</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Faixa etária --}}
                    @if(count($ageData))
                        <div>
                            <p class="text-xs font-semibold text-ink-muted uppercase tracking-wider mb-3">
                                Faixa Etária
                            </p>
                            <div class="space-y-2.5">
                                @foreach($ageData as $range => $pct)
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs text-ink-muted font-mono w-12 flex-shrink-0">{{ $range }}</span>
                                        <div class="flex-1 h-1.5 rounded-full bg-white/10 overflow-hidden">
                                            <div class="h-full rounded-full bg-brand transition-all"
                                                style="width: {{ min($pct, 100) }}%"></div>
                                        </div>
                                        <span class="text-xs text-ink-muted font-mono w-10 text-right">{{ $pct }}%</span>
                                    </div>
                                @endforeach
                            </div>
                            {{-- Gráfico de pizza faixa etária
                            <div class="mt-4">
                                <canvas id="ageChart" height="120"></canvas>
                            </div>
                            --}}
                        </div>
                    @endif

                    {{-- Gênero --}}
                    @if(count($genderData))
                        <div>
                            <p class="text-xs font-semibold text-ink-muted uppercase tracking-wider mb-3">
                                Gênero
                            </p>
                            @php
                                $male = (float) ($genderData['male'] ?? 0);
                                $female = (float) ($genderData['female'] ?? 0);
                                $total = $male + $female ?: 100;
                            @endphp
                            <div class="space-y-3 mb-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-ink-muted w-20 flex-shrink-0">Masculino</span>
                                    <div class="flex-1 h-2 rounded-full bg-white/10 overflow-hidden">
                                        <div class="h-full rounded-full bg-blue-400" style="width: {{ ($male / $total) * 100 }}%">
                                        </div>
                                    </div>
                                    <span class="text-xs text-ink-muted font-mono w-10 text-right">{{ $male }}%</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-ink-muted w-20 flex-shrink-0">Feminino</span>
                                    <div class="flex-1 h-2 rounded-full bg-white/10 overflow-hidden">
                                        <div class="h-full rounded-full bg-brand" style="width: {{ ($female / $total) * 100 }}%">
                                        </div>
                                    </div>
                                    <span class="text-xs text-ink-muted font-mono w-10 text-right">{{ $female }}%</span>
                                </div>
                            </div>
                            {{-- 
                            <canvas id="genderChart" height="120"></canvas>
                            --}}
                        </div>
                    @endif

                </div>
            </div>
        @endif

        {{-- =============================================
        RESUMO & METAS
        ============================================= --}}
        @if($report->summary || data_get($m, 'next_month_goals'))
            <div class="card p-6">
                <h2 class="text-sm font-semibold text-ink mb-5 flex items-center gap-2">
                    <x-heroicon-o-document-text class="w-4 h-4 text-brand" /> Resumo & Metas
                </h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @if($report->summary)
                        <div>
                            <p class="text-xs font-semibold text-ink-muted uppercase tracking-wider mb-3">Resumo do Mês</p>
                            <p class="text-sm text-ink-muted leading-relaxed whitespace-pre-line">{{ $report->summary }}</p>
                        </div>
                    @endif
                    @if(data_get($m, 'next_month_goals'))
                        <div>
                            <p class="text-xs font-semibold text-ink-muted uppercase tracking-wider mb-3">Metas para o Próximo Mês
                            </p>
                            <p class="text-sm text-ink-muted leading-relaxed">
                                {{ data_get($m, 'next_month_goals') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- =============================================
        RODAPÉ — navegação entre relatórios
        ============================================= --}}
        <div class="flex items-center justify-between pt-2">
            @if($prev)
                <a href="{{ route('cliente.relatorios.show', $prev->uuid) }}" class="btn-secondary btn-sm">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                    {{ $prev->reference_month->translatedFormat('M/Y') }}
                </a>
            @else
                <span></span>
            @endif

            <a href="{{ route('cliente.relatorios.index') }}" class="btn-ghost btn-sm text-xs">
                Ver todos os relatórios
            </a>

            @if($next)
                <a href="{{ route('cliente.relatorios.show', $next->uuid) }}" class="btn-secondary btn-sm">
                    {{ $next->reference_month->translatedFormat('M/Y') }}
                    <x-heroicon-o-arrow-right class="w-4 h-4" />
                </a>
            @else
                <span></span>
            @endif
        </div>

    </div>
@endsection

{{--
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ── Configuração base Chart.js ──────────────────────────
        const chartDefaults = {
            color: '#A3A3A3',
            font: { family: 'Montserrat', size: 11 },
        };
        Chart.defaults.color = chartDefaults.color;
        Chart.defaults.font.family = chartDefaults.font.family;
        Chart.defaults.font.size = chartDefaults.font.size;
        Chart.defaults.plugins.legend.labels.boxWidth = 10;
        Chart.defaults.plugins.legend.labels.padding = 12;

        // ── Gráfico Instagram ───────────────────────────────────
        @if(isset($chartIgLabels) && count($chartIgValues ?? []) >= 1)
            new Chart(document.getElementById('igChart'), {
                type: 'bar',
                data: {
                    labels: @json($chartIgLabels),
                    datasets: [{
                        data: @json($chartIgValues),
                        backgroundColor: 'rgba(146, 13, 11, 0.69)',
                        borderColor: 'rgba(216, 40, 37, 0.8)',
                        borderWidth: 1,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#6B7280' } },
                        y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#6B7280' }, beginAtZero: true },
                    }
                }
            });
        @endif

        // ── Gráfico faixa etária ────────────────────────────────
        @if(count($ageData ?? []) > 0)
            new Chart(document.getElementById('ageChart'), {
                type: 'doughnut',
                data: {
                    labels: @json(array_keys($ageData)),
                    datasets: [{
                        data: @json(array_values($ageData)),
                        backgroundColor: ['#BD1613', '#E84340', '#FF7A78', '#3B82F6', '#60A5FA', '#93C5FD'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '60%',
                    plugins: {
                        legend: { position: 'right', labels: { color: '#A3A3A3' } }
                    }
                }
            });
        @endif

        // ── Gráfico gênero ──────────────────────────────────────
        @if(count($genderData ?? []) > 0)
            new Chart(document.getElementById('genderChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Masculino', 'Feminino'],
                    datasets: [{
                        data: [{{ $male ?? 0 }}, {{ $female ?? 0 }}],
                        backgroundColor: ['#3B82F6', '#BD1613'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '60%',
                    plugins: {
                        legend: { position: 'right', labels: { color: '#A3A3A3' } }
                    }
                }
            });
        @endif
        });
    </script>
@endpush
--}}