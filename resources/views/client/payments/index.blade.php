@extends('layouts.app')

@section('title', 'Financeiro')
@section('page-title', 'Financeiro')
@section('page-subtitle', 'Pagamentos, notas fiscais e dados de retorno')

@section('content')
    <div class="space-y-6" x-data="{ tab: 'pagamentos' }">

        {{-- =============================================
        RESUMO — cards de status rápido
        ============================================= --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Próximo vencimento --}}
            <div class="metric-card {{ $nextPayment?->isOverdue() ? 'metric-card-brand' : 'metric-card-green' }}">
                <div class="flex items-start justify-between mb-3">
                    <div
                        class="w-9 h-9 rounded-xl {{ $nextPayment?->isOverdue() ? 'bg-brand/10' : 'bg-green-500/10' }} flex items-center justify-center">
                        <x-heroicon-o-calendar-days
                            class="w-4 h-4 {{ $nextPayment?->isOverdue() ? 'text-brand' : 'text-green-400' }}" />
                    </div>
                    @if($nextPayment?->isOverdue())
                        <span class="badge badge-red">Vencido</span>
                    @elseif($nextPayment?->isUnderReview())
                        <span class="badge badge-purple">Em Análise</span>
                    @elseif($nextPayment)
                        <span class="badge badge-green">Pendente</span>
                    @endif
                </div>
                <p class="text-xl sm:text-2xl font-bold text-ink mb-0.5">
                    @if($nextPayment)
                        {{ $nextPayment->due_date->format('d/m/Y') }}
                    @else
                        Em dia
                    @endif
                </p>
                <p class="text-xs text-ink-muted">Próximo vencimento</p>
            </div>

            {{-- Mensalidade --}}
            <div class="metric-card metric-card-blue">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-500/10 flex items-center justify-center">
                        <x-heroicon-o-banknotes class="w-4 h-4 text-blue-400" />
                    </div>
                </div>
                <p class="text-xl sm:text-2xl font-bold text-ink mb-0.5">
                    R$ {{ number_format(auth()->user()->client?->monthly_fee, 2, ',', '.') }}
                </p>
                <p class="text-xs text-ink-muted">Mensalidade</p>
            </div>

            {{-- ROI último mês (opcional por cliente) --}}
            @if(auth()->user()->client?->show_roi && $lastMonthRoi !== null)
                <div class="metric-card {{ $lastMonthRoi >= 0 ? 'metric-card-purple' : 'metric-card-brand' }}">
                    <div class="flex items-start justify-between mb-3">
                        <div
                            class="w-9 h-9 rounded-xl {{ $lastMonthRoi >= 0 ? 'bg-purple-500/10' : 'bg-brand/10' }} flex items-center justify-center">
                            <x-heroicon-o-arrow-trending-up
                                class="w-4 h-4 {{ $lastMonthRoi >= 0 ? 'text-purple-400' : 'text-brand' }}" />
                        </div>
                        <span class="badge badge-gray">{{ now()->subMonth()->translatedFormat('M/Y') }}</span>
                    </div>
                    <p class="text-xl sm:text-2xl font-bold {{ $lastMonthRoi >= 0 ? 'text-purple-400' : 'text-brand' }} mb-0.5">
                        {{ $lastMonthRoi >= 0 ? '+' : '' }}{{ number_format($lastMonthRoi, 0) }}%
                    </p>
                    <p class="text-xs text-ink-muted">ROI no último mês</p>
                </div>
            @endif

            {{-- Pagamentos em dia no ano --}}
            <div class="metric-card metric-card-gray">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-gray-500/10 flex items-center justify-center">
                        <x-heroicon-o-check-badge class="w-4 h-4 text-gray-400" />
                    </div>
                </div>
                <p class="text-xl sm:text-2xl font-bold text-ink mb-0.5">
                    {{ $paidThisYear }}
                </p>
                <p class="text-xs text-ink-muted">Pagamentos em dia em {{ now()->year }}</p>
            </div>

        </div>

        {{-- =============================================
        LINHA INFERIOR — PIX Card + Tabs (Pagamentos / NFs)
        ============================================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

            {{-- ── CARD PIX ─────────────────────────────────── --}}
            @php $isPixConfigured = $pixKey && $pixName && $pixCity; @endphp
            @if($isPixConfigured)
                <div class="card p-5 flex flex-col items-center text-center gap-4">
                    <div class="flex items-center gap-2 w-full">
                        <div class="w-8 h-8 rounded-xl bg-brand/10 flex items-center justify-center shrink-0">
                            <x-heroicon-o-qr-code class="w-4 h-4 text-brand" />
                        </div>
                        <div class="text-left">
                            <p class="text-sm font-semibold text-ink">Pagar via PIX</p>
                            <p class="text-xs text-ink-muted">Escaneie ou copie a chave</p>
                        </div>
                    </div>

                    {{-- QR Code --}}
                    @php
                        $mensalidade = auth()->user()->client?->monthly_fee ?? 0;
                        $mensalidadeTxid = 'MENSALIDADE' . now()->format('Ym');
                        $mensalidadePayload = app(\App\Domain\Financial\Services\PixService::class)->generatePayload($pixKey, $pixName, $pixCity, $mensalidade, $mensalidadeTxid);
                    @endphp
                    <div class="w-44 h-44 rounded-2xl bg-white flex items-center justify-center shadow-card">
                        <img src="{{ app(\App\Domain\Financial\Services\PixService::class)->generateQrCodeBase64($mensalidadePayload) }}"
                            alt="QR Code PIX" class="w-full h-full object-contain rounded-lg">
                    </div>

                    {{-- Mensalidade destaque --}}
                    <div class="w-full bg-surface-accent rounded-xl px-4 py-3 border border-white/[0.07]">
                        <p class="text-xs text-ink-muted">Valor da mensalidade</p>
                        <p class="text-xl font-bold text-ink mt-0.5">
                            R$ {{ number_format($mensalidade, 2, ',', '.') }}
                        </p>
                    </div>

                    {{-- Chave PIX copiável --}}
                    <div class="w-full">
                        <p class="text-xs text-ink-muted mb-2 text-left">Código Copia e Cola:</p>
                        <div class="flex items-center gap-2">
                            <code
                                class="flex-1 bg-black border border-white/10 rounded-lg px-3 py-2 text-xs font-mono text-ink truncate">{{ $mensalidadePayload }}</code>
                            <button type="button" x-data="{ copied: false }"
                                @click="navigator.clipboard.writeText('{{ $mensalidadePayload }}').then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                                class="btn-icon shrink-0" title="Copiar código">
                                <x-heroicon-o-check class="w-4 h-4 text-green-400" x-show="copied" />
                                <x-heroicon-o-clipboard-document class="w-4 h-4" x-show="!copied" />
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── TABS: Pagamentos / Notas Fiscais ─────────── --}}
            <div class="{{ $isPixConfigured ? 'lg:col-span-2' : 'lg:col-span-3' }} card overflow-hidden">

                {{-- Tab Bar --}}
                <div class="flex items-center gap-1 border-b border-white/[0.07] px-5">
                    <button @click="tab = 'pagamentos'"
                        :class="tab === 'pagamentos' ? 'border-brand text-ink' : 'border-transparent text-ink-muted hover:text-ink'"
                        class="flex items-center gap-2 px-3 py-3.5 text-sm font-medium border-b-2 -mb-px transition-colors">
                        <x-heroicon-o-clock class="w-4 h-4" />
                        Pagamentos
                        @if($pendingPayments->count())
                            <span class="ml-1 badge badge-red text-[10px] px-1.5 py-0">{{ $pendingPayments->count() }}</span>
                        @endif
                    </button>
                    <button @click="tab = 'notas'"
                        :class="tab === 'notas' ? 'border-brand text-ink' : 'border-transparent text-ink-muted hover:text-ink'"
                        class="flex items-center gap-2 px-3 py-3.5 text-sm font-medium border-b-2 -mb-px transition-colors">
                        <x-heroicon-o-document-text class="w-4 h-4" />
                        Notas Fiscais
                        @if($invoices->count())
                            <span class="ml-1 badge badge-gray text-[10px] px-1.5 py-0">{{ $invoices->count() }}</span>
                        @endif
                    </button>
                </div>

                {{-- ── TAB: Pagamentos ──────────────────────── --}}
                <div x-show="tab === 'pagamentos'" x-transition.opacity>

                    {{-- Pendentes / Vencidos --}}
                    @if($pendingPayments->count())
                        <div class="px-5 pt-4 pb-2">
                             <p class="text-xs font-semibold uppercase tracking-widest text-ink-subtle mb-3">Pendentes</p>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Referência</th>
                                    <th>Vencimento</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th class="text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingPayments as $payment)
                                    <tr>
                                        <td>
                                            <span class="text-sm text-ink">
                                                {{ $payment->reference ?? $payment->due_date->translatedFormat('F \d\e Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-sm {{ $payment->isOverdue() ? 'text-brand font-semibold' : 'text-ink' }}">
                                                {{ $payment->due_date->format('d/m/Y') }}
                                            </span>
                                            @if($payment->isOverdue())
                                                <span class="text-xs text-brand ml-1">
                                                    ({{ $payment->due_date->diffForHumans() }})
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-sm font-semibold text-ink font-mono">
                                                R$ {{ number_format($payment->amount, 2, ',', '.') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ match ($payment->status) {
                                                'pending' => 'badge-amber',
                                                'overdue' => 'badge-red',
                                                'under_review' => 'badge-purple font-semibold',
                                                default => 'badge-gray',
                                            } }}">
                                                {{ match ($payment->status) {
                                                    'pending' => 'Pendente',
                                                    'overdue' => 'Vencido',
                                                    'under_review' => 'Em Análise',
                                                    default => $payment->status,
                                                } }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                @if($payment->status === 'pending' || $payment->status === 'overdue')
                                                    <form method="POST" action="{{ route('cliente.financeiro.notificar', $payment->uuid) }}"
                                                        onsubmit="return confirm('Confirmar que você já realizou o pagamento?')">
                                                        @csrf
                                                        <button type="submit" class="btn-primary btn-sm px-3">
                                                            <x-heroicon-o-check class="w-3.5 h-3.5" />
                                                            Já paguei
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($isPixConfigured)
                                                    @php
                                                        $paymentPayload = app(\App\Domain\Financial\Services\PixService::class)->generatePayload($pixKey, $pixName, $pixCity, $payment->amount, 'PGTO' . $payment->uuid);
                                                        $paymentQrBase64 = app(\App\Domain\Financial\Services\PixService::class)->generateQrCodeBase64($paymentPayload);
                                                    @endphp
                                                    <button type="button" @click="$dispatch('open-pix', {
                                                                             amount: '{{ number_format($payment->amount, 2, ',', '.') }}',
                                                                             reference: '{{ $payment->reference ?? $payment->due_date->translatedFormat('F/Y') }}',
                                                                             payload: '{{ $paymentPayload }}',
                                                                             qrcodeSrc: '{{ $paymentQrBase64 }}'
                                                                         })"
                                                        class="btn-secondary btn-sm">
                                                        <x-heroicon-o-qr-code class="w-3.5 h-3.5" />
                                                        Ver PIX
                                                    </button>
                                                @else
                                                    <span class="text-xs text-ink-subtle">PIX Indisponível</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                {{-- ── TAB: Historico ──────────────────────── --}}
                <div x-show="tab === 'pagamentos'">
                    {{-- Histórico --}}
                    <div class="px-5 pt-5 pb-2 flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-widest text-ink-subtle">Histórico</p>
                        {{-- Filtro de ano --}}
                        <form method="GET" action="{{ route('cliente.financeiro.index') }}" class="flex items-center gap-2">
                            <select name="year" class="select w-24 text-xs py-1.5" onchange="this.form.submit()">
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ request('year', now()->year) == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Referência</th>
                                <th>Vencimento</th>
                                <th>Pago em</th>
                                <th>Valor</th>
                                <th>Método</th>
                                <th>Status</th>
                                @if(auth()->user()->client?->show_roi)
                                    <th>ROI</th>
                                @endif
                                <th class="text-right">NF</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $payment)
                                <tr>
                                    <td>
                                        <span class="text-sm text-ink">
                                            {{ $payment->reference ?? $payment->due_date->translatedFormat('M/Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-sm text-ink-muted">{{ $payment->due_date->format('d/m/Y') }}</span>
                                    </td>
                                    <td>
                                        <span class="text-sm text-ink-muted">
                                            {{ $payment->paid_at?->format('d/m/Y') ?? '—' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-sm font-semibold text-ink font-mono">
                                            R$ {{ number_format($payment->amount, 2, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($payment->payment_method)
                                            <span class="badge badge-gray capitalize">
                                                {{ match ($payment->payment_method) {
                                                    'pix' => 'PIX',
                                                    'bank_transfer' => 'TED/DOC',
                                                    'credit_card' => 'Cartão',
                                                    default => 'Outro',
                                                } }}
                                            </span>
                                        @else
                                            <span class="text-ink-subtle text-xs">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ match ($payment->status) {
                                            'paid' => 'badge-green',
                                            'pending' => 'badge-amber',
                                            'overdue' => 'badge-red',
                                            'under_review' => 'badge-purple',
                                            'cancelled' => 'badge-gray',
                                            default => 'badge-gray',
                                        } }}">
                                            {{ match ($payment->status) {
                                                'paid' => 'Pago',
                                                'pending' => 'Pendente',
                                                'overdue' => 'Vencido',
                                                'under_review' => 'Em Análise',
                                                'cancelled' => 'Cancelado',
                                                default => $payment->status,
                                            } }}
                                        </span>
                                    </td>
                                    @if(auth()->user()->client?->show_roi)
                                        <td>
                                            @php $roi = $roiByMonth[$payment->due_date->format('Y-m')] ?? null; @endphp
                                            @if($roi !== null)
                                                <span class="text-sm font-semibold {{ $roi >= 0 ? 'text-green-400' : 'text-brand' }}">
                                                    {{ $roi >= 0 ? '+' : '' }}{{ number_format($roi, 0) }}%
                                                </span>
                                            @else
                                                <span class="text-ink-subtle text-xs">—</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="text-right">
                                        @php $invoice = $invoiceByMonth[$payment->due_date->format('Y-m')] ?? null; @endphp
                                        @if($invoice)
                                            <a href="{{ route('cliente.financeiro.nota.baixar', $invoice->uuid) }}" class="btn-icon"
                                                title="Baixar nota fiscal">
                                                <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                                            </a>
                                        @else
                                            <span class="text-ink-subtle text-xs px-3">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-state py-10">
                                            <x-heroicon-o-banknotes class="w-8 h-8 text-ink-subtle mx-auto mb-2" />
                                            <p class="empty-state-title">Nenhum pagamento em {{ request('year', now()->year) }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ── TAB: Notas Fiscais ───────────────────── --}}
                <div x-show="tab === 'notas'" x-transition.opacity>
                    @if($invoices->count())
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Mês de referência</th>
                                    <th>Emissão</th>
                                    <th>Valor</th>
                                    <th class="text-right">Download</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $invoice)
                                    <tr>
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
                                            <span class="text-sm font-semibold text-ink font-mono">
                                                R$ {{ number_format($invoice->amount, 2, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('cliente.financeiro.nota.baixar', $invoice->uuid) }}"
                                                class="btn-secondary btn-sm">
                                                <x-heroicon-o-arrow-down-tray class="w-3.5 h-3.5" />
                                                PDF
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-state py-16">
                            <x-heroicon-o-document-text class="w-8 h-8 text-ink-subtle mx-auto mb-2" />
                            <p class="empty-state-title">Nenhuma nota fiscal emitida</p>
                            <p class="empty-state-desc">As notas fiscais emitidas aparecerão aqui.</p>
                        </div>
                    @endif
                </div>

            </div>{{-- fim .card tabs --}}

        </div>{{-- fim grid --}}

    </div>

    {{-- =============================================
    MODAL — QR Code PIX (disparado pelo botão "Ver PIX" na tabela)
    ============================================= --}}
    <div x-data="pixModal()" @open-pix.window="open($event.detail)">
        <dialog x-ref="dialog" class="card w-full max-w-xs p-0 backdrop:bg-black/60 open:animate-fade-in"
            @click.self="$refs.dialog.close()">
            <div class="flex items-center justify-between px-5 py-4 border-b border-white/[0.07]">
                <h2 class="text-sm font-semibold text-ink">Pagar com PIX</h2>
                <button @click="$refs.dialog.close()" class="btn-ghost btn-sm p-1">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </div>

            <div class="px-5 py-5 flex flex-col items-center gap-4 text-center">

                {{-- QR Code gerado pelo servidor --}}
                <div class="w-48 h-48 rounded-xl bg-white p-2 flex items-center justify-center">
                    <img :src="qrcodeSrc" alt="QR Code PIX" class="w-full h-full object-contain">
                </div>

                <div>
                    <p class="text-xs text-ink-muted mb-1" x-text="`Referência: ${reference}`"></p>
                    <p class="text-lg font-bold text-ink" x-text="`R$ ${amount}`"></p>
                </div>

                {{-- Chave PIX copiável --}}
                <div class="w-full">
                    <p class="text-xs text-ink-muted mb-2">Copia e Cola:</p>
                    <div class="flex items-center gap-2">
                        <code
                            class="flex-1 bg-black border border-white/10 rounded-lg px-3 py-2 text-xs font-mono text-ink truncate"
                            x-text="payload"></code>
                        <button type="button" x-data="{ copied: false }"
                            @click="navigator.clipboard.writeText(payload).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                            class="btn-icon shrink-0" title="Copiar código">
                            <x-heroicon-o-check class="w-4 h-4 text-green-400" x-show="copied" />
                            <x-heroicon-o-clipboard-document class="w-4 h-4" x-show="!copied" />
                        </button>
                    </div>
                </div>

            </div>
        </dialog>
    </div>

@endsection

@push('scripts')
    <script>
        function pixModal() {
            return {
                amount: '',
                reference: '',
                payload: '',
                qrcodeSrc: '',
                open(detail) {
                    this.amount = detail.amount;
                    this.reference = detail.reference;
                    this.payload = detail.payload;
                    this.qrcodeSrc = detail.qrcodeSrc;
                    this.$refs.dialog.showModal();
                }
            }
        }
    </script>
@endpush