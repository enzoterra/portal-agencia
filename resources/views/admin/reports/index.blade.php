@extends('layouts.admin')

@section('title', 'Relatórios')
@section('page-title', 'Relatórios')
@section('page-subtitle', $reports->total() . ' relatórios cadastrados')

@section('topbar-actions')
    <a href="{{ route('admin.relatorios.create') }}" class="btn-primary btn-sm">
        <x-heroicon-m-plus-circle class="w-5 h-5" /> Novo Relatório
    </a>
@endsection

@section('content')
    <div>

        {{-- Filtros --}}
        <form method="GET" class="flex items-center gap-3 mb-4 flex-wrap">
            <div class="relative flex-1 max-w-xs">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-ink-subtle" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por cliente..."
                    class="input pl-9">
            </div>
            <select name="status" class="select w-36">
                <option value="">Todos</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Rascunhos</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publicados</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Arquivados</option>
            </select>
            <button type="submit" class="btn-secondary btn-sm">Filtrar</button>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.relatorios.index') }}" class="btn-ghost btn-sm">Limpar</a>
            @endif
        </form>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Mês</th>
                        <th>Investimento</th>
                        <th>Receita</th>
                        <th>ROI</th>
                        <th>Status</th>
                        <th>Publicado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-brand-icon flex items-center justify-center text-xs font-bold text-brand flex-shrink-0">
                                        {{ strtoupper(substr($report->client?->company_name ?? 'X', 0, 2)) }}
                                    </div>
                                    <span class="font-medium text-ink">
                                        {{ $report->client?->trade_name ?? $report->client?->company_name ?? 'Cliente Removido' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="font-medium text-ink capitalize">
                                    {{ $report->reference_month->translatedFormat('F/Y') }}
                                </span>
                            </td>
                            <td>R$ {{ number_format($report->investment, 2, ',', '.') }}</td>
                            <td>R$ {{ number_format($report->revenue, 2, ',', '.') }}</td>
                            <td>
                                <span class="font-bold {{ $report->roi >= 0 ? 'text-green-400' : 'text-brand' }}">
                                    {{ number_format($report->roi, 0) }}%
                                </span>
                            </td>
                            <td><span class="{{ $report->status_color }}">{{ $report->status_label }}</span></td>
                            <td class="text-ink-muted text-xs">
                                {{ $report->published_at?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td>
                                <div class="flex items-center gap-1 justify-end">
                                    <a href="{{ route('admin.relatorios.edit', $report) }}" class="btn-icon" title="Ver">
                                        <x-heroicon-o-eye class="w-4 h-4" />
                                    </a>
                                    @if($report->status !== 'published')
                                        <a href="{{ route('admin.relatorios.edit', $report) }}" class="btn-icon" title="Editar">
                                            <x-heroicon-o-pencil class="w-4 h-4" />
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('admin.relatorios.destroy', $report) }}"
                                        onsubmit="return confirm('Remover este relatório?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon hover:bg-brand-icon hover:text-brand"
                                            title="Remover">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state py-12">
                                    <x-heroicon-o-chart-bar class="w-10 h-10 text-ink-subtle mx-auto mb-3" />
                                    <p class="empty-state-title">Nenhum relatório encontrado</p>
                                    <p class="empty-state-desc mb-4">Crie o primeiro relatório para um cliente.</p>
                                    <a href="{{ route('admin.relatorios.create') }}" class="btn-primary btn-sm">+ Novo
                                        Relatório</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="flex items-center justify-between mt-4">
                <p class="text-xs text-ink-muted">Mostrando {{ $reports->firstItem() }}–{{ $reports->lastItem() }} de
                    {{ $reports->total() }}
                </p>
                {{ $reports->links('vendor.pagination.tailwind') }}
            </div>
        @endif

    </div>
@endsection