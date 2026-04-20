@extends('layouts.app')

@section('title', 'Relatórios')
@section('page-title', 'Relatórios')
@section('page-subtitle', 'Acompanhe os resultados mensais da sua campanha')

@section('content')
    <div>

        {{-- =============================================
        FILTROS
        ============================================= --}}
        <form method="GET" action="{{ route('cliente.relatorios.index') }}" class="flex items-center gap-3 mb-4 flex-wrap">
            <label class="text-sm text-ink-muted font-medium">Mês de referência</label>
            <input
                type="month"
                name="reference_month"
                value="{{ $month }}"
                max="{{ now()->format('Y-m') }}"
                class="input w-44"
            >
            <button type="submit" class="btn-secondary btn-sm">Filtrar</button>
            @if(request('reference_month'))
                <a href="{{ route('cliente.relatorios.index') }}" class="btn-ghost btn-sm">Limpar filtro</a>
            @endif
        </form>

        {{-- =============================================
        LISTAGEM
        ============================================= --}}
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Relatório</th>
                        @if(auth()->user()->client?->show_roi)
                            <th>Investimento</th>
                            <th>Receita</th>
                            <th>ROI</th>
                        @endif
                        <th>Status</th>
                        <th>Publicado em</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                                    <tr>

                                        {{-- Título + mês --}}
                                        <td>
                                            <div>
                                                <div class="font-medium text-ink text-sm">
                                                    {{ $report->title ?: $report->reference_month->translatedFormat('F \d\e Y') }}
                                                </div>
                                                <div class="text-xs text-ink-muted mt-0.5">
                                                    {{ $report->reference_month->translatedFormat('F/Y') }}
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Investimento --}}
                                        @if(auth()->user()->client?->show_roi)
                                            <td>
                                                <span class="text-sm text-ink font-mono">
                                                    R$ {{ number_format($report->investment, 2, ',', '.') }}
                                                </span>
                                            </td>

                                            {{-- Receita --}}
                                            <td>
                                                <span class="text-sm text-ink font-mono">
                                                    R$ {{ number_format($report->revenue, 2, ',', '.') }}
                                                </span>
                                            </td>

                                            {{-- ROI --}}
                                            <td>
                                                @if($report->roi !== null)
                                                    <span class="font-semibold text-sm {{ $report->roi >= 0 ? 'text-green-400' : 'text-brand' }}">
                                                        {{ $report->roi >= 0 ? '+' : '' }}{{ number_format($report->roi, 0) }}%
                                                    </span>
                                                @else
                                                    <span class="text-ink-subtle text-sm">—</span>
                                                @endif
                                            </td>
                                        @endif

                                        {{-- Status --}}
                                        <td>
                                            <span class="badge {{ match ($report->status) {
                            'published' => 'badge-green',
                            'draft' => 'badge-gray',
                            'review' => 'badge-amber',
                            'archived' => 'badge-blue',
                            default => 'badge-gray',
                        } }}">
                                                {{ match ($report->status) {
                            'published' => 'Publicado',
                            'draft' => 'Rascunho',
                            'review' => 'Em revisão',
                            'archived' => 'Arquivado',
                            default => $report->status,
                        } }}
                                            </span>
                                        </td>

                                        {{-- Publicado em --}}
                                        <td>
                                            <span class="text-sm text-ink-muted">
                                                {{ $report->published_at?->format('d/m/Y') ?? '—' }}
                                            </span>
                                        </td>

                                        {{-- Ver --}}
                                        <td>
                                            <div class="flex justify-end">
                                                <a href="{{ route('cliente.relatorios.show', $report->uuid) }}" class="btn-icon"
                                                    title="Ver relatório">
                                                    <x-heroicon-o-eye class="w-4 h-4" />
                                                </a>
                                            </div>
                                        </td>

                                    </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state py-12">
                                    <x-heroicon-o-document-chart-bar class="w-10 h-10 text-ink-subtle mx-auto mb-3" />
                                    <p class="empty-state-title">Nenhum relatório disponível</p>
                                    <p class="empty-state-desc">
                                        @if(request('reference_month'))
                                            Nenhum relatório publicado para <strong>{{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F/Y') }}</strong>.
                                        @else
                                            Seus relatórios mensais aparecerão aqui assim que forem publicados.
                                        @endif
                                    </p>
                                    @if(request('reference_month'))
                                        <a href="{{ route('cliente.relatorios.index') }}" class="btn-secondary btn-sm mt-4">Ver todos os meses</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        @if($reports->hasPages())
            <div class="flex items-center justify-between mt-4">
                <p class="text-xs text-ink-muted">
                    Mostrando {{ $reports->firstItem() }}–{{ $reports->lastItem() }} de {{ $reports->total() }}
                </p>
                <div class="pagination">
                    {{ $reports->withQueryString()->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        @endif

    </div>
@endsection