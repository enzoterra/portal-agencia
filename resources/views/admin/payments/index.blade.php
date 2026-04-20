@extends('layouts.admin')

@section('title', 'Financeiro')
@section('page-title', 'Financeiro')
@section('page-subtitle', 'Gestão de pagamentos e notas fiscais')

@section('topbar-actions')
    <button type="button" x-data @click="$dispatch('open-modal', 'create-payment')" class="btn-primary btn-sm truncate">
        <x-heroicon-m-plus-circle class="w-5 h-5" /> Registrar
    </button>
@endsection

@section('content')

    <div x-data="{ tab: '{{ session('financial_tab', 'payments') }}' }">

        {{-- Tabs --}}
        <div class="flex items-center gap-1 border-b border-white/[0.07] mb-5">
            <button @click="tab = 'payments'"
                :class="tab === 'payments' ? 'border-brand text-ink' : 'border-transparent text-ink-muted hover:text-ink'"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors">
                <x-heroicon-o-banknotes class="w-4 h-4" /> Pagamentos
            </button>
            <button @click="tab = 'invoices'"
                :class="tab === 'invoices' ? 'border-brand text-ink' : 'border-transparent text-ink-muted hover:text-ink'"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors">
                <x-heroicon-o-document-text class="w-4 h-4" /> Notas Fiscais
            </button>
        </div>

        {{-- =============================================
        TAB — Pagamentos
        ============================================= --}}
        <div x-show="tab === 'payments'" x-transition.opacity>

            {{-- Filtros --}}
            <form method="GET" action="{{ route('admin.financeiro.index') }}"
                class="flex items-center gap-3 mb-4 flex-wrap">
                <input type="hidden" name="tab" value="payments">

                <div class="relative flex-1 max-w-xs">
                    <x-heroicon-o-magnifying-glass
                        class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-ink-subtle" />
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar cliente..."
                        class="input pl-9">
                </div>

                <select name="status" class="select w-36">
                    <option value="">Todos</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendentes</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Pagos</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Vencidos</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelados</option>
                </select>

                <select name="client_id" class="select w-44">
                    <option value="">Todos os clientes</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->uuid }}" {{ request('client_id') === $client->uuid ? 'selected' : '' }}>
                            {{ $client->trade_name ?? $client->company_name }}
                        </option>
                    @endforeach
                </select>

                <input type="month" name="month" value="{{ request('month') }}" class="input w-40">

                <button type="submit" class="btn-secondary btn-sm">Filtrar</button>
                @if(request()->hasAny(['search', 'status', 'client_id', 'month']))
                    <a href="{{ route('admin.financeiro.index') }}" class="btn-ghost btn-sm">Limpar</a>
                @endif
            </form>

            {{-- Tabela --}}
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Referência</th>
                            <th>Vencimento</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Pago em</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                                            <tr>
                                                <td>
                                                    <div class="flex items-center gap-2">
                                                        <div
                                                            class="w-6 h-6 rounded-md bg-brand-icon flex items-center justify-center text-[10px] font-bold text-brand shrink-0">
                                                            {{ strtoupper(substr($payment->client?->company_name ?? '?', 0, 2)) }}
                                                        </div>
                                                        <span class="text-sm text-ink">
                                                            {{ $payment->client?->trade_name ?? $payment->client?->company_name ?? 'Cliente Removido' }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-sm text-ink-muted">
                                                        {{ $payment->reference ?? $payment->due_date->translatedFormat('M/Y') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-sm {{ $payment->isOverdue() ? 'text-brand font-semibold' : 'text-ink' }}">
                                                        {{ $payment->due_date->format('d/m/Y') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-sm font-semibold font-mono text-ink">
                                                        R$ {{ number_format($payment->amount, 2, ',', '.') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ match ($payment->status) {
                                                        'paid' => 'badge-green',
                                                        'pending' => 'badge-amber',
                                                        'under_review' => 'badge-purple font-semibold',
                                                        'overdue' => 'badge-red',
                                                        'cancelled' => 'badge-gray',
                                                        default => 'badge-gray',
                                                    } }}">
                                                        {{ match ($payment->status) {
                                                            'paid' => 'Pago',
                                                            'pending' => 'Pendente',
                                                            'under_review' => 'Em Análise',
                                                            'overdue' => 'Vencido',
                                                            'cancelled' => 'Cancelado',
                                                            default => $payment->status,
                                                        } }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-sm text-ink-muted">
                                                        {{ $payment->paid_at?->format('d/m/Y') ?? '—' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-1 justify-end">
                                                        {{-- Marcar como pago --}}
                                                        @if(in_array($payment->status, ['pending', 'overdue', 'under_review']))
                                                            <form method="POST"
                                                                action="{{ route('admin.financeiro.marcar-pago', $payment->uuid) }}">
                                                                @csrf @method('PATCH')
                                                                <button type="submit" class="btn-icon" title="Marcar como pago">
                                                                    <x-heroicon-o-check-circle class="w-4 h-4" />
                                                                </button>
                                                            </form>
                                                        @endif
                                                        {{-- Editar --}}
                                                        <button type="button" x-data
                                                            @click="$dispatch('edit-payment', @json($payment->load('client')))" class="btn-icon"
                                                            title="Editar">
                                                            <x-heroicon-o-pencil class="w-4 h-4" />
                                                        </button>
                                                        {{-- Remover --}}
                                                        <form method="POST" action="{{ route('admin.financeiro.excluir', $payment->uuid) }}"
                                                            onsubmit="return confirm('Remover este pagamento?')">
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
                                <td colspan="7">
                                    <div class="empty-state py-12">
                                        <x-heroicon-o-banknotes class="w-10 h-10 text-ink-subtle mx-auto mb-3" />
                                        <p class="empty-state-title">Nenhum pagamento encontrado</p>
                                        <p class="empty-state-desc mb-4">
                                            @if(request()->hasAny(['search', 'status', 'client_id', 'month']))
                                                Nenhum resultado para os filtros aplicados.
                                            @else
                                                Adicione o primeiro pagamento.
                                            @endif
                                        </p>
                                        @if(request()->hasAny(['search', 'status', 'client_id', 'month']))
                                            <a href="{{ route('admin.financeiro.index') }}" class="btn-secondary btn-sm">Limpar
                                                filtros</a>
                                        @else
                                            <button type="button" x-data @click="$dispatch('open-modal','create-payment')"
                                                class="btn-primary btn-sm">
                                                + Registrar Pagamento
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($payments->hasPages())
                <div class="flex items-center justify-between mt-4">
                    <p class="text-xs text-ink-muted">
                        Mostrando {{ $payments->firstItem() }}–{{ $payments->lastItem() }} de {{ $payments->total() }}
                    </p>
                    {{ $payments->withQueryString()->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>

        {{-- =============================================
        TAB — Notas Fiscais
        ============================================= --}}
        <div x-show="tab === 'invoices'" x-transition.opacity>

            <div class="flex items-center justify-between mb-4">
                <form method="GET" action="{{ route('admin.financeiro.index') }}" class="flex items-center gap-3">
                    <input type="hidden" name="tab" value="invoices">
                    <select name="inv_client_id" class="select w-44">
                        <option value="">Todos os clientes</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->uuid }}" {{ request('inv_client_id') === $client->uuid ? 'selected' : '' }}>
                                {{ $client->trade_name ?? $client->company_name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="month" name="inv_month" value="{{ request('inv_month') }}" class="input w-40">
                    <button type="submit" class="btn-secondary btn-sm">Filtrar</button>
                    @if(request()->hasAny(['inv_client_id', 'inv_month']))
                        <a href="{{ route('admin.financeiro.index', ['tab' => 'invoices']) }}"
                            class="btn-ghost btn-sm">Limpar</a>
                    @endif
                </form>

                <button type="button" x-data @click="$dispatch('open-modal','upload-invoice')" class="btn-primary btn-sm">
                    <x-heroicon-o-arrow-up-tray class="w-4 h-4" /> Upload NF
                </button>
            </div>

            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Número NF</th>
                            <th>Mês de referência</th>
                            <th>Emissão</th>
                            <th>Valor</th>
                            <th class="text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 rounded-md bg-brand-icon flex items-center justify-center text-[10px] font-bold text-brand shrink-0">
                                            {{ strtoupper(substr($invoice->client?->company_name ?? '?', 0, 2)) }}
                                        </div>
                                        <span class="text-sm text-ink">
                                            {{ $invoice->client?->trade_name ?? $invoice->client?->company_name ?? 'Cliente Removido' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-sm font-mono text-ink">{{ $invoice->invoice_number }}</span>
                                </td>
                                <td>
                                    <span class="text-sm text-ink capitalize">
                                        {{ \Carbon\Carbon::parse($invoice->reference_month)->translatedFormat('F \d\e Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-sm text-ink-muted">{{ $invoice->issue_date->format('d/m/Y') }}</span>
                                </td>
                                <td>
                                    <span class="text-sm font-semibold font-mono text-ink">
                                        R$ {{ number_format($invoice->amount, 2, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-1 justify-end">
                                        <a href="{{ route('admin.financeiro.notas.baixar', $invoice->uuid) }}" class="btn-icon"
                                            title="Baixar PDF">
                                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.financeiro.notas.excluir', $invoice->uuid) }}"
                                            onsubmit="return confirm('Remover esta nota fiscal?')">
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
                                <td colspan="6">
                                    <div class="empty-state py-12">
                                        <x-heroicon-o-document-text class="w-10 h-10 text-ink-subtle mx-auto mb-3" />
                                        <p class="empty-state-title">Nenhuma nota fiscal encontrada</p>
                                        <p class="empty-state-desc mb-4">Faça o upload de um PDF para começar.</p>
                                        <button type="button" x-data @click="$dispatch('open-modal','upload-invoice')"
                                            class="btn-primary btn-sm">
                                            + Upload NF
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($invoices->hasPages())
                <div class="flex items-center justify-between mt-4">
                    <p class="text-xs text-ink-muted">
                        Mostrando {{ $invoices->firstItem() }}–{{ $invoices->lastItem() }} de {{ $invoices->total() }}
                    </p>
                    {{ $invoices->withQueryString()->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>

    </div>

    {{-- =============================================
    MODAL — Criar pagamento
    ============================================= --}}
    <div x-data x-on:open-modal.window="if ($event.detail === 'create-payment') $refs.createPayment.showModal()">
        <dialog x-ref="createPayment" class="card w-full max-w-lg p-0 backdrop:bg-black/60 open:animate-fade-in"
            @click.self="$refs.createPayment.close()">

            <div class="flex items-center justify-between px-6 py-4 border-b border-white/[0.07]">
                <h2 class="text-sm font-semibold text-ink">Novo Pagamento</h2>
                <button @click="$refs.createPayment.close()" class="btn-ghost btn-sm p-1">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </div>

            <form method="POST" action="{{ route('admin.financeiro.guardar') }}" class="px-6 py-5 space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-3" style="margin-top: 0 !important">
                    <div class="form-group col-span-2">
                        <label class="label">Cliente <span class="text-brand">*</span></label>
                        <select name="client_id" class="select @error('client_id') input-error @enderror" required>
                            <option value="">Selecione...</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->uuid }}" {{ old('client_id') === $client->uuid ? 'selected' : '' }}>
                                    {{ $client->trade_name ?? $client->company_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="label">Valor (R$) <span class="text-brand">*</span></label>
                        <input type="number" name="amount" step="0.01" min="0" value="{{ old('amount') }}"
                            class="input @error('amount') input-error @enderror" required>
                        @error('amount') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="label">Vencimento <span class="text-brand">*</span></label>
                        <input type="date" name="due_date" value="{{ old('due_date', now()->addMonth()->format('Y-m-d')) }}"
                            class="input @error('due_date') input-error @enderror" required>
                        @error('due_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group col-span-2">
                        <label class="label">Referência</label>
                        <input type="text" name="reference" value="{{ old('reference') }}"
                            placeholder="Ex: Mensalidade Março/2026" class="input">
                    </div>

                    <div class="form-group">
                        <label class="label">Status</label>
                        <select name="status" class="select">
                            <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pendente
                            </option>
                            <option value="paid" {{ old('status') === 'paid' ? 'selected' : '' }}>Pago</option>
                            <option value="overdue" {{ old('status') === 'overdue' ? 'selected' : '' }}>Vencido</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="label">Método de pagamento</label>
                        <select name="payment_method" class="select">
                            <option value="">—</option>
                            <option value="pix" {{ old('payment_method') === 'pix' ? 'selected' : '' }}>PIX</option>
                            <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>
                                TED/DOC</option>
                            <option value="credit_card" {{ old('payment_method') === 'credit_card' ? 'selected' : '' }}>Cartão
                            </option>
                            <option value="other" {{ old('payment_method') === 'other' ? 'selected' : '' }}>Outro</option>
                        </select>
                    </div>

                    <div class="form-group col-span-2">
                        <label class="label">Observações</label>
                        <textarea name="notes" rows="2" class="textarea"
                            placeholder="Informações adicionais...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex justify-between gap-2 pt-2 border-t border-white/[0.07]">
                    <button type="button" @click="$refs.createPayment.close()" class="btn-secondary">Cancelar</button>
                    <button type="submit" class="btn-primary">
                        <x-heroicon-o-check class="w-4 h-4" /> Salvar
                    </button>
                </div>
            </form>
        </dialog>
    </div>

    {{-- =============================================
    MODAL — Upload Nota Fiscal
    ============================================= --}}
    <div x-data x-on:open-modal.window="if ($event.detail === 'upload-invoice') $refs.uploadInvoice.showModal()"
        x-init="
        if (new URLSearchParams(window.location.search).get('modal') === 'upload-invoice') {
            $nextTick(() => $dispatch('open-modal', 'upload-invoice'))
        }
    ">
        <dialog x-ref="uploadInvoice" class="card w-full max-w-md p-0 backdrop:bg-black/60 open:animate-fade-in"
            @click.self="$refs.uploadInvoice.close()">

            <div class="flex items-center justify-between px-6 py-4 border-b border-white/[0.07]">
                <h2 class="text-sm font-semibold text-ink">Upload de Nota Fiscal</h2>
                <button @click="$refs.uploadInvoice.close()" class="btn-ghost btn-sm p-1">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </div>

            <form method="POST" action="{{ route('admin.financeiro.notas.guardar') }}" enctype="multipart/form-data"
                class="px-6 py-5 space-y-4">
                @csrf

                <div class="form-group" style="margin-top: 0 !important">
                    <label class="label">Cliente <span class="text-brand">*</span></label>
                    <select name="client_id" class="select @error('client_id') input-error @enderror" required>
                        <option value="">Selecione...</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->uuid }}">
                                {{ $client->trade_name ?? $client->company_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="form-group">
                        <label class="label">Número da NF <span class="text-brand">*</span></label>
                        <input type="text" name="invoice_number" value="{{ old('invoice_number') }}" placeholder="NF-0001"
                            class="input font-mono @error('invoice_number') input-error @enderror" required>
                        @error('invoice_number') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="label">Mês de referência <span class="text-brand">*</span></label>
                        <input type="month" name="reference_month"
                            value="{{ old('reference_month', now()->format('Y-m')) }}"
                            class="input @error('reference_month') input-error @enderror" required>
                        @error('reference_month') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="label">Valor (R$) <span class="text-brand">*</span></label>
                        <input type="number" name="amount" step="0.01" min="0" value="{{ old('amount') }}"
                            class="input @error('amount') input-error @enderror" required>
                        @error('amount') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="label">Data de emissão <span class="text-brand">*</span></label>
                        <input type="date" name="issue_date" value="{{ old('issue_date', now()->format('Y-m-d')) }}"
                            class="input @error('issue_date') input-error @enderror" required>
                        @error('issue_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Upload PDF --}}
                <div class="form-group">
                    <label class="label">Arquivo PDF <span class="text-brand">*</span></label>
                    <div x-data="{ fileName: '' }" class="relative border-2 border-dashed border-white/[0.12] rounded-xl p-6 text-center
                               hover:border-white/20 transition-colors cursor-pointer" @click="$refs.fileInput.click()">
                        <input x-ref="fileInput" type="file" name="pdf_file" accept="application/pdf" class="hidden"
                            @change="fileName = $event.target.files.length > 0 ? $event.target.files[0].name : ''" required>
                        <x-heroicon-o-document-arrow-up class="w-8 h-8 text-ink-subtle mx-auto mb-2" />
                        <p class="text-sm text-ink-muted" x-text="fileName || 'Clique para selecionar o PDF'"></p>
                        <p class="text-xs text-ink-subtle mt-1">Máximo 15 MB</p>
                    </div>
                    @error('pdf_file') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-between gap-2 pt-2 border-t border-white/[0.07]">
                    <button type="button" @click="$refs.uploadInvoice.close()" class="btn-secondary">Cancelar</button>
                    <button type="submit" class="btn-primary">
                        <x-heroicon-o-arrow-up-tray class="w-4 h-4" /> Enviar
                    </button>
                </div>
            </form>
        </dialog>
    </div>

@endsection