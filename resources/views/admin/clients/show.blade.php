@extends('layouts.admin')

@section('title', $client->trade_name ?? $client->company_name)
@section('page-title', $client->trade_name ?? $client->company_name)
@section('page-subtitle', $client->company_name . ($client->cnpj ? ' · ' . $client->cnpj : ''))

@section('topbar-actions')
    <a href="{{ route('admin.clientes.index') }}" class="btn-secondary btn-sm">
        <x-heroicon-o-arrow-left class="w-4 h-4" /> Clientes
    </a>
    <a href="{{ route('admin.clientes.edit', $client) }}" class="btn-primary btn-sm">
        <x-heroicon-o-pencil class="w-4 h-4" /> Editar
    </a>
@endsection

@section('content')
<div class="mt-6 space-y-6">

    {{-- Topo: status + dados rápidos --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card label="Mensalidade" color="blue"
            value="R$ {{ number_format($client->monthly_fee, 2, ',', '.') }}"
            icon="">
            <span class="text-xs text-ink-subtle">
                {{ $client->contract_start ? 'Desde ' . $client->contract_start->format('m/Y') : 'Sem data' }}
            </span>
        </x-stat-card>

        <x-stat-card label="Relatórios" color="purple"
            value="{{ $client->reports->count() }}"
            icon="">
            <span class="text-xs text-ink-subtle">publicados</span>
        </x-stat-card>

        <x-stat-card label="Status" color="{{ $client->status === 'active' ? 'green' : 'brand' }}"
            value="{{ match($client->status) { 'active' => 'Ativo', 'inactive' => 'Inativo', 'suspended' => 'Suspenso', default => $client->status } }}"
            icon="" />

        <x-stat-card label="Último ROI" color="amber"
            value="{{ $client->reports->first()?->roi ? number_format($client->reports->first()->roi, 0) . '%' : '—' }}"
            icon="">
            <span class="text-xs text-ink-subtle">
                {{ $client->reports->first()?->reference_month?->translatedFormat('M/Y') ?? 'Sem relatório' }}
            </span>
        </x-stat-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Dados do cliente --}}
        <div class="card p-6 space-y-4">
            <h3 class="text-sm font-semibold font-title text-ink flex items-center gap-2">
                <x-heroicon-o-building-office class="w-4 h-4 text-brand" /> Dados
            </h3>
            <div class="divider"></div>

            @foreach([
                ['label' => 'Razão Social',  'value' => $client->company_name],
                ['label' => 'Nome Fantasia', 'value' => $client->trade_name],
                ['label' => 'CNPJ',          'value' => $client->cnpj],
                ['label' => 'E-mail',        'value' => $client->email],
                ['label' => 'Telefone',      'value' => $client->phone],
                ['label' => 'Início',        'value' => $client->contract_start?->format('d/m/Y')],
                ['label' => 'Fim',           'value' => $client->contract_end?->format('d/m/Y')],
            ] as $row)
                @if($row['value'])
                <div class="flex justify-between gap-4">
                    <span class="text-xs text-ink-muted">{{ $row['label'] }}</span>
                    <span class="text-xs text-ink font-medium text-right">{{ $row['value'] }}</span>
                </div>
                @endif
            @endforeach

            @if($client->notes)
                <div class="divider"></div>
                <p class="text-xs text-ink-muted">{{ $client->notes }}</p>
            @endif
        </div>

        {{-- Últimos relatórios --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold font-title text-ink flex items-center gap-2">
                    <x-heroicon-o-chart-bar class="w-4 h-4 text-brand" /> Relatórios
                </h3>
                <a href="{{ route('admin.relatorios.create') }}" class="btn-ghost btn-sm text-brand">+ Novo</a>
            </div>

            @forelse($client->reports as $report)
                <a href="{{ route('admin.relatorios.show', $report) }}"
                   class="flex items-center justify-between py-2.5 border-b border-subtle last:border-0 group">
                    <div>
                        <div class="text-sm font-medium text-ink group-hover:text-brand transition-colors">
                            {{ $report->reference_month->translatedFormat('F/Y') }}
                        </div>
                        <div class="text-xs text-ink-muted mt-0.5">
                            <span class="badge-{{ $report->status }} badge">{{ match($report->status) {
                                'published' => 'Publicado', 'draft' => 'Rascunho', 'archived' => 'Arquivado', default => $report->status
                            } }}</span>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-green-400">
                        {{ number_format($report->roi, 0) }}%
                    </span>
                </a>
            @empty
                <div class="empty-state py-6">
                    <p class="empty-state-desc">Nenhum relatório ainda.</p>
                </div>
            @endforelse
        </div>

        {{-- Últimos pagamentos --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold font-title text-ink flex items-center gap-2">
                    <x-heroicon-o-credit-card class="w-4 h-4 text-brand" /> Pagamentos
                </h3>
                <a href="{{ route('admin.financeiro.index') }}" class="btn-ghost btn-sm text-brand">Ver todos</a>
            </div>

            @forelse($client->payments as $payment)
                <div class="flex items-center justify-between py-2.5 border-b border-subtle last:border-0">
                    <div>
                        <div class="text-sm font-medium text-ink">
                            R$ {{ number_format($payment->amount, 2, ',', '.') }}
                        </div>
                        <div class="text-xs text-ink-muted mt-0.5">
                            Vence {{ $payment->due_date->format('d/m/Y') }}
                        </div>
                    </div>
                    <span class="badge-{{ $payment->status }}">
                        {{ match($payment->status) {
                            'paid' => '✓ Pago', 'pending' => 'Pendente',
                            'overdue' => 'Vencido', 'cancelled' => 'Cancelado', default => $payment->status
                        } }}
                    </span>
                </div>
            @empty
                <div class="empty-state py-6">
                    <p class="empty-state-desc">Nenhum pagamento ainda.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection