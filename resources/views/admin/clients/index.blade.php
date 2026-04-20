@extends('layouts.admin')

@section('title', 'Clientes')
@section('page-title', 'Clientes')
@section('page-subtitle', $clients->total() . ' clientes cadastrados')

@section('topbar-actions')
    <a href="{{ route('admin.clientes.create') }}" class="btn-primary btn-sm">
        <x-heroicon-m-plus-circle class="w-5 h-5" /> Novo Cliente
    </a>
@endsection

@section('content')
    <div class="mt-3">

        {{-- Filtros --}}
        <div class="flex items-center gap-3 mb-4">
            <form method="GET" class="flex items-center gap-3 flex-1">
                <div class="relative flex-1 max-w-xs">
                    <x-heroicon-o-magnifying-glass
                        class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-ink-subtle" />
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar cliente..."
                        class="input pl-9">
                </div>
                <select name="status" class="select w-36">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspensos</option>
                </select>
                <button type="submit" class="btn-secondary btn-sm">Filtrar</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.clientes.index') }}" class="btn-ghost btn-sm">Limpar</a>
                @endif
            </form>
        </div>

        {{-- Tabela --}}
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Contato</th>
                        <th>Mensalidade</th>
                        <th>Status</th>
                        <th>Relatórios</th>
                        <th>Pagamentos</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                                    <tr>
                                        {{-- Nome --}}
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-9 h-9 rounded-xl bg-brand-icon flex items-center justify-center text-sm font-bold text-brand flex-shrink-0">
                                                    {{ strtoupper(substr($client->company_name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="font-medium text-ink">
                                                        {{ $client->trade_name ?? $client->company_name }}
                                                    </div>
                                                    @if($client->trade_name)
                                                        <div class="text-xs text-ink-muted">{{ $client->company_name }}</div>
                                                    @endif
                                                    @if($client->cnpj)
                                                        <div class="text-xs text-ink-subtle font-mono">{{ $client->cnpj }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Contato --}}
                                        <td>
                                            <div class="text-sm text-ink">{{ $client->email }}</div>
                                            @if($client->phone)
                                                <div class="text-xs text-ink-muted mt-0.5">{{ $client->phone }}</div>
                                            @endif
                                        </td>

                                        {{-- Mensalidade --}}
                                        <td>
                                            <span class="font-semibold text-ink">
                                                R$ {{ number_format($client->monthly_fee, 2, ',', '.') }}
                                            </span>
                                        </td>

                                        {{-- Status --}}
                                        <td>
                                            <span class="badge {{ match ($client->status) {
                            'active' => 'badge-green',
                            'inactive' => 'badge-gray',
                            'suspended' => 'badge-red',
                            default => 'badge-gray',
                        } }}">
                                                {{ match ($client->status) {
                            'active' => 'Ativo',
                            'inactive' => 'Inativo',
                            'suspended' => 'Suspenso',
                            default => $client->status,
                        } }}
                                            </span>
                                        </td>

                                        {{-- Relatórios --}}
                                        <td>
                                            <span class="text-sm text-ink">{{ $client->reports_count }}</span>
                                        </td>

                                        {{-- Pagamentos --}}
                                        <td>
                                            <span class="text-sm text-ink">{{ $client->payments_count }}</span>
                                        </td>

                                        {{-- Ações --}}
                                        <td>
                                            <div class="flex items-center gap-1 justify-end">
                                                <a href="{{ route('admin.clientes.show', $client) }}" class="btn-icon" title="Visualizar">
                                                    <x-heroicon-o-eye class="w-4 h-4" />
                                                </a>
                                                <a href="{{ route('admin.clientes.edit', $client) }}" class="btn-icon" title="Editar">
                                                    <x-heroicon-o-pencil class="w-4 h-4" />
                                                </a>
                                                <form method="POST" action="{{ route('admin.clientes.destroy', $client) }}"
                                                    onsubmit="return confirm('Remover {{ addslashes($client->company_name) }}?')">
                                                    @csrf
                                                    @method('DELETE')
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
                            <td colspan="7">
                                <div class="empty-state py-12">
                                    <x-heroicon-o-users class="w-10 h-10 text-ink-subtle mx-auto mb-3" />
                                    <p class="empty-state-title">Nenhum cliente encontrado</p>
                                    <p class="empty-state-desc mb-4">Comece cadastrando o primeiro cliente.</p>
                                    <a href="{{ route('admin.clientes.create') }}" class="btn-primary btn-sm">
                                        + Novo Cliente
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        @if($clients->hasPages())
            <div class="flex items-center justify-between mt-4">
                <p class="text-xs text-ink-muted">
                    Mostrando {{ $clients->firstItem() }}–{{ $clients->lastItem() }} de {{ $clients->total() }}
                </p>
                <div class="pagination">
                    {{ $clients->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        @endif

    </div>
@endsection