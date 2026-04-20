@extends('layouts.admin')

@section('title', 'Novo Cliente')
@section('page-title', 'Novo Cliente')
@section('page-subtitle', 'Preencha os dados para cadastrar um novo cliente.')

@section('topbar-actions')
    <a href="{{ route('admin.clientes.index') }}" class="btn-secondary btn-sm">
        <x-heroicon-o-arrow-left class="w-4 h-4" /> Voltar
    </a>
@endsection

@section('content')
    <div class="mt-6 max-w-3xl">
        <form method="POST" action="{{ route('admin.clientes.store') }}" class="space-y-6">
            @csrf

            {{-- Dados da empresa --}}
            <div class="card p-6">
                <h3 class="text-sm font-semibold text-ink font-title mb-5 flex items-center gap-2">
                    <x-heroicon-o-building-office class="w-4 h-4 text-brand" />
                    Dados da Empresa
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group sm:col-span-2">
                        <label class="label">Razão Social <span class="text-brand">*</span></label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}"
                            class="input @error('company_name') input-error @enderror" placeholder="Ex: Fazenda Araújo Ltda"
                            required>
                        @error('company_name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="label">Nome Fantasia</label>
                        <input type="text" name="trade_name" value="{{ old('trade_name') }}" class="input"
                            placeholder="Ex: Fazenda Araújo">
                    </div>

                    <div class="form-group">
                        <label class="label">CNPJ</label>
                        <input type="text" name="cnpj" value="{{ old('cnpj') }}" class="input"
                            placeholder="00.000.000/0000-00" maxlength="18">
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
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="input @error('email') input-error @enderror" placeholder="contato@empresa.com.br"
                            required>
                        @error('email') <p class="form-error">
                            {{ $message == 'validation.unique' ? 'Este e-mail já está cadastrado' : $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="label">Telefone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="input"
                            placeholder="(00) 00000-0000">
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
                        <input type="number" name="monthly_fee" value="{{ old('monthly_fee', '0.00') }}"
                            class="input @error('monthly_fee') input-error @enderror" step="0.01" min="0" required>
                        @error('monthly_fee') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="label">Início do Contrato</label>
                        <input type="date" name="contract_start" value="{{ old('contract_start') }}" class="input">
                    </div>

                    <div class="form-group">
                        <label class="label">Fim do Contrato</label>
                        <input type="date" name="contract_end" value="{{ old('contract_end') }}" class="input">
                    </div>

                    <div class="form-group">
                        <label class="label">Status <span class="text-brand">*</span></label>
                        <select name="status" class="select">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                            <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspenso</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Acesso ao portal --}}
            <div class="card p-6">
                <h3 class="text-sm font-semibold text-ink font-title mb-1 flex items-center gap-2">
                    <x-heroicon-o-lock-closed class="w-4 h-4 text-brand" />
                    Acesso ao Portal
                </h3>
                <p class="text-xs text-ink-muted mb-5">Cria automaticamente um usuário de acesso para este cliente.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="label">Nome do Usuário <span class="text-brand">*</span></label>
                        <input type="text" name="user_name" value="{{ old('user_name') }}"
                            class="input @error('user_name') input-error @enderror" placeholder="Ex: João Silva" required>
                        @error('user_name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="label">E-mail de Acesso <span class="text-brand">*</span></label>
                        <input type="email" name="user_email" value="{{ old('user_email') }}"
                            class="input @error('user_email') input-error @enderror" placeholder="acesso@empresa.com.br"
                            required>
                        @error('user_email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="label">Senha <span class="text-brand">*</span></label>
                        <input type="password" name="user_password"
                            class="input @error('user_password') input-error @enderror" placeholder="Mínimo 8 caracteres"
                            required>
                        @error('user_password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="label">Confirmar Senha <span class="text-brand">*</span></label>
                        <input type="password" name="user_password_confirmation" class="input" placeholder="Repita a senha"
                            required>
                    </div>
                </div>

                {{-- Toggle Mostrar ROI --}}
                <div class="flex items-start gap-3 pt-2 mt-2 border-t border-white/[0.07]">
                    <div class="flex-1">
                        <label for="show_roi" class="text-sm font-medium text-ink cursor-pointer">Mostrar ROI</label>
                        <p class="text-xs text-ink-muted mt-0.5">Exibe cards e dados de ROI no dashboard, financeiro e relatórios do cliente.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer mt-0.5">
                        <input type="hidden" name="show_roi" value="0">
                        <input type="checkbox" id="show_roi" name="show_roi" value="1"
                            class="sr-only peer" {{ old('show_roi') ? 'checked' : '' }}>
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
                <div class="form-group">
                    <textarea name="notes" rows="3" class="textarea"
                        placeholder="Anotações internas sobre o cliente...">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Ações --}}
            <div class="flex items-center gap-3 justify-end">
                <a href="{{ route('admin.clientes.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">
                    <x-heroicon-o-check class="w-4 h-4" /> Cadastrar Cliente
                </button>
            </div>

        </form>
    </div>
@endsection