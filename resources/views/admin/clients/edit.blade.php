@extends('layouts.admin')

@section('title', 'Editar Cliente')
@section('page-title', $client->trade_name ?? $client->company_name)
@section('page-subtitle', 'Editando dados do cliente.')

@section('topbar-actions')
    <a href="{{ route('admin.clientes.show', $client) }}" class="btn-secondary btn-sm">
        <x-heroicon-o-arrow-left class="w-4 h-4" /> Voltar
    </a>
@endsection

@section('content')
<div class="mt-6 max-w-3xl">
    <form method="POST" action="{{ route('admin.clientes.update', $client) }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Dados da empresa --}}
        <div class="card p-6">
            <h3 class="text-sm font-semibold text-ink font-title mb-5 flex items-center gap-2">
                <x-heroicon-o-building-office class="w-4 h-4 text-brand" />
                Dados da Empresa
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="form-group sm:col-span-2">
                    <label class="label">Razão Social <span class="text-brand">*</span></label>
                    <input type="text" name="company_name" value="{{ old('company_name', $client->company_name) }}"
                           class="input @error('company_name') input-error @enderror" required>
                    @error('company_name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="label">Nome Fantasia</label>
                    <input type="text" name="trade_name" value="{{ old('trade_name', $client->trade_name) }}" class="input">
                </div>
                <div class="form-group">
                    <label class="label">CNPJ</label>
                    <input type="text" name="cnpj" value="{{ old('cnpj', $client->cnpj) }}" class="input" maxlength="18">
                </div>
            </div>
        </div>

        {{-- Contato --}}
        <div class="card p-6">
            <h3 class="text-sm font-semibold text-ink font-title mb-5 flex items-center gap-2">
                <x-heroicon-o-envelope class="w-4 h-4 text-brand" />
                Contato
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="label">E-mail <span class="text-brand">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $client->email) }}"
                           class="input @error('email') input-error @enderror" required>
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="label">Telefone</label>
                    <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" class="input">
                </div>
            </div>
        </div>

        {{-- Contrato --}}
        <div class="card p-6">
            <h3 class="text-sm font-semibold text-ink font-title mb-5 flex items-center gap-2">
                <x-heroicon-o-document-text class="w-4 h-4 text-brand" />
                Contrato & Financeiro
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="form-group">
                    <label class="label">Mensalidade (R$) <span class="text-brand">*</span></label>
                    <input type="number" name="monthly_fee" value="{{ old('monthly_fee', $client->monthly_fee) }}"
                           class="input" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label class="label">Início do Contrato</label>
                    <input type="date" name="contract_start"
                           value="{{ old('contract_start', $client->contract_start?->format('Y-m-d')) }}"
                           class="input">
                </div>
                <div class="form-group">
                    <label class="label">Fim do Contrato</label>
                    <input type="date" name="contract_end"
                           value="{{ old('contract_end', $client->contract_end?->format('Y-m-d')) }}"
                           class="input">
                </div>
                <div class="form-group">
                    <label class="label">Status</label>
                    <select name="status" class="select">
                        <option value="active"    {{ old('status', $client->status) === 'active'    ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive"  {{ old('status', $client->status) === 'inactive'  ? 'selected' : '' }}>Inativo</option>
                        <option value="suspended" {{ old('status', $client->status) === 'suspended' ? 'selected' : '' }}>Suspenso</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Portal -- Mostrar ROI --}}
        <div class="card p-6">
            <h3 class="text-sm font-semibold text-ink font-title mb-1 flex items-center gap-2">
                <x-heroicon-o-chart-bar class="w-4 h-4 text-brand" />
                Configurações do Portal
            </h3>
            <p class="text-xs text-ink-muted mb-5">Controla quais informações são visíveis para este cliente.</p>

            <div class="flex items-start gap-3">
                <div class="flex-1">
                    <label for="show_roi" class="text-sm font-medium text-ink cursor-pointer">Mostrar ROI</label>
                    <p class="text-xs text-ink-muted mt-0.5">Exibe cards e dados de ROI no dashboard, financeiro e relatórios do cliente.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer mt-0.5">
                    <input type="hidden" name="show_roi" value="0">
                    <input type="checkbox" id="show_roi" name="show_roi" value="1"
                        class="sr-only peer" {{ old('show_roi', $client->show_roi) ? 'checked' : '' }}>
                    <div class="w-10 h-5 bg-white/10 peer-focus:outline-none rounded-full peer
                        peer-checked:after:translate-x-5 peer-checked:after:border-white after:content-['']
                        after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300
                        after:border after:rounded-full after:h-4 after:w-4 after:transition-all
                        peer-checked:bg-brand"></div>
                </label>
            </div>
        </div>

        {{-- Observações --}}
        <div class="card p-6">
            <h3 class="text-sm font-semibold text-ink font-title mb-5 flex items-center gap-2">
                <x-heroicon-o-chat-bubble-left-ellipsis class="w-4 h-4 text-brand" />
                Observações
            </h3>
            <textarea name="notes" rows="3" class="textarea"
                      placeholder="Anotações internas...">{{ old('notes', $client->notes) }}</textarea>
        </div>

        <div class="flex items-center gap-3 justify-end">
            <a href="{{ route('admin.clientes.show', $client) }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">
                <x-heroicon-o-check class="w-4 h-4" /> Salvar Alterações
            </button>
        </div>
    </form>
</div>
@endsection