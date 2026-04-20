@extends('layouts.admin')

@section('title', 'Configurações')
@section('page-title', 'Configurações')
@section('page-subtitle', 'Credenciais, segurança e manutenção do sistema')

@section('content')

    <div x-data="{ tab: '{{ session('settings_tab', 'google') }}' }">

        {{-- =============================================
        TABS
        ============================================= --}}
        <div class="flex items-center gap-1 border-b border-white/[0.07] mb-6">
            <button @click="tab = 'google'"
                :class="tab === 'google' ? 'border-brand text-ink' : 'border-transparent text-ink-muted hover:text-ink'"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors">
                <x-heroicon-o-key class="w-4 h-4" />
                Google
            </button>
            <button @click="tab = 'security'"
                :class="tab === 'security' ? 'border-brand text-ink' : 'border-transparent text-ink-muted hover:text-ink'"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors">
                <x-heroicon-o-shield-check class="w-4 h-4" />
                Segurança
            </button>
            <button @click="tab = 'maintenance'"
                :class="tab === 'maintenance' ? 'border-brand text-ink' : 'border-transparent text-ink-muted hover:text-ink'"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors">
                <x-heroicon-o-wrench-screwdriver class="w-4 h-4" />
                Manutenção
            </button>
            <button @click="tab = 'finance'"
                :class="tab === 'finance' ? 'border-brand text-ink' : 'border-transparent text-ink-muted hover:text-ink'"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors">
                <x-heroicon-o-banknotes class="w-4 h-4" />
                Financeiro
            </button>
        </div>

        {{-- =============================================
        TAB — Google
        ============================================= --}}
        <div x-show="tab === 'google'" x-transition.opacity>
            <form method="POST" action="{{ route('admin.configuracoes.atualizar') }}" class="space-y-5">
                @csrf
                @method('PUT')
                <input type="hidden" name="_tab" value="google">

                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-ink mb-1 flex items-center gap-2">
                        <x-heroicon-o-key class="w-4 h-4 text-brand" />
                        OAuth 2.0 — Google Calendar &amp; Drive
                    </h3>
                    <p class="text-xs text-ink-muted mb-5">
                        Credenciais do projeto no
                        <a href="https://console.cloud.google.com" target="_blank" rel="noopener"
                            class="text-brand hover:underline">Google Cloud Console</a>.
                        Valores salvos são criptografados no banco.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div class="form-group">
                            <label class="label">Client ID</label>
                            <input type="text" name="google_client_id"
                                value="{{ old('google_client_id', $settings->get('google_client_id')) }}"
                                placeholder="xxxxx.apps.googleusercontent.com"
                                class="input font-mono text-xs @error('google_client_id') input-error @enderror"
                                style="margin-top: 0 !important;"
                                autocomplete="off">
                            @error('google_client_id')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="label">Client Secret</label>
                            <div class="relative" x-data="{ show: false }">
                                <input :type="show ? 'text' : 'password'" name="google_client_secret"
                                    value="{{ old('google_client_secret', $settings->get('google_client_secret')) }}"
                                    placeholder="GOCSPX-..."
                                    class="input font-mono text-xs pr-10 @error('google_client_secret') input-error @enderror"
                                    autocomplete="off">
                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 px-3 text-ink-subtle hover:text-ink transition-colors">
                                    <x-heroicon-o-eye class="w-4 h-4" x-show="!show" />
                                    <x-heroicon-o-eye-slash class="w-4 h-4" x-show="show" />
                                </button>
                            </div>
                            @error('google_client_secret')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group sm:col-span-2">
                            <label class="label">Redirect URI</label>
                            <div class="flex gap-2">
                                <input type="text" name="google_redirect_uri"
                                    value="{{ old('google_redirect_uri', $settings->get('google_redirect_uri', url('/admin/google/callback'))) }}"
                                    placeholder="{{ url('/admin/google/callback') }}"
                                    class="input font-mono text-xs @error('google_redirect_uri') input-error @enderror">
                                <button type="button" x-data
                                    @click="navigator.clipboard.writeText($el.previousElementSibling.value)"
                                    class="btn-icon shrink-0" title="Copiar">
                                    <x-heroicon-o-clipboard-document class="w-4 h-4" />
                                </button>
                            </div>
                            <p class="text-xs text-ink-subtle mt-1">
                                Cadastre exatamente esta URI nas origens autorizadas do seu projeto Google.
                            </p>
                            @error('google_redirect_uri')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Status da conexão OAuth --}}
                <div class="card p-5 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-xl {{ $oauthConnected ? 'bg-green-500/10' : 'bg-white/5' }} flex items-center justify-center shrink-0">
                            @if ($oauthConnected)
                                <x-heroicon-o-check-circle class="w-4 h-4 text-green-400" />
                            @else
                                <x-heroicon-o-x-circle class="w-4 h-4 text-ink-muted" />
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-medium text-ink">
                                {{ $oauthConnected ? 'Conta Google conectada' : 'Nenhuma conta conectada' }}
                            </p>
                            <p class="text-xs text-ink-muted mt-0.5">
                                @if ($oauthConnected)
                                    Token válido · Conectado {{ $oauthExpiresAt?->diffForHumans() ?? '—' }}
                                @else
                                    Salve as credenciais acima e clique em Conectar.
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        @if ($oauthConnected)
                            <a href="{{ route('admin.google.redirecionar') }}"
                                class="btn-secondary btn-sm"
                                onclick="return confirm('Reconectar substituirá o token atual. Continuar?')">
                                <x-heroicon-o-arrow-path class="w-4 h-4" />
                                Reconectar
                            </a>
                        @else
                            <a href="{{ route('admin.google.redirecionar') }}" class="btn-primary btn-sm">
                                <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                                Conectar Google
                            </a>
                        @endif
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="btn-primary">
                        <x-heroicon-o-check class="w-4 h-4" />
                        Salvar credenciais
                    </button>
                </div>
            </form>
        </div>

        {{-- =============================================
        TAB — Segurança
        ============================================= --}}
        <div x-show="tab === 'security'" x-transition.opacity>
            <form method="POST" action="{{ route('admin.configuracoes.atualizar') }}" class="space-y-5">
                @csrf
                @method('PUT')
                <input type="hidden" name="_tab" value="security">

                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-ink mb-1 flex items-center gap-2">
                        <x-heroicon-o-lock-closed class="w-4 h-4 text-brand" />
                        Rate Limiting — Login
                    </h3>
                    <p class="text-xs text-ink-muted mb-5">
                        Controla quantas tentativas de login são permitidas antes do bloqueio temporário.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div class="form-group">
                            <label class="label">Máximo de tentativas por minuto</label>
                            <input type="number" name="login_max_attempts"
                                value="{{ old('login_max_attempts', $settings->get('login_max_attempts', 5)) }}" min="1"
                                max="20" class="input @error('login_max_attempts') input-error @enderror">
                            <p class="text-xs text-ink-subtle mt-1">Padrão recomendado: 5</p>
                            @error('login_max_attempts')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="label">Tempo de bloqueio (minutos)</label>
                            <input type="number" name="login_decay_minutes"
                                value="{{ old('login_decay_minutes', $settings->get('login_decay_minutes', 1)) }}" min="1"
                                max="60" class="input @error('login_decay_minutes') input-error @enderror">
                            <p class="text-xs text-ink-subtle mt-1">Padrão recomendado: 1</p>
                            @error('login_decay_minutes')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-ink mb-1 flex items-center gap-2">
                        <x-heroicon-o-clock class="w-4 h-4 text-brand" />
                        Sessão
                    </h3>
                    <p class="text-xs text-ink-muted mb-5">
                        Tempo de inatividade antes da sessão expirar automaticamente.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div class="form-group">
                            <label class="label">Expiração da sessão (minutos)</label>
                            <input type="number" name="session_lifetime"
                                value="{{ old('session_lifetime', $settings->get('session_lifetime', 120)) }}" min="15"
                                max="1440" class="input @error('session_lifetime') input-error @enderror">
                            <p class="text-xs text-ink-subtle mt-1">
                                120 min = 2h · 480 min = 8h · 1440 min = 24h
                            </p>
                            @error('session_lifetime')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="label">Forçar HTTPS</label>
                            <label class="flex items-center gap-2.5 h-[42px] cursor-pointer">
                                <input type="checkbox" name="force_https" value="1" 
                                    {{ old('force_https', $settings->get('force_https', false)) ? 'checked' : '' }}
                                    class="checkbox">
                                <span class="text-sm text-ink-muted">Redirecionar HTTP → HTTPS</span>
                            </label>
                            <p class="text-xs text-ink-subtle mt-1">
                                Recomendado manter ativo em produção.
                            </p>
                        </div>

                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">
                        <x-heroicon-o-check class="w-4 h-4" />
                        Salvar configurações
                    </button>
                </div>
            </form>
        </div>

        {{-- =============================================
        TAB — Manutenção
        ============================================= --}}
        <div x-show="tab === 'maintenance'" x-transition.opacity>
            <div class="space-y-5">

                {{-- Ações rápidas --}}
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-ink mb-1 flex items-center gap-2">
                        <x-heroicon-o-bolt class="w-4 h-4 text-brand" />
                        Ações rápidas
                    </h3>
                    <p class="text-xs text-ink-muted mb-5">
                        Operações que afetam o sistema imediatamente.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                        <form method="POST" action="{{ route('admin.configuracoes.manutencao') }}">
                            @csrf
                            <input type="hidden" name="action" value="clear_cache">
                            <button type="submit"
                                class="w-full card-accent rounded-xl p-4 text-left hover:border-white/[0.12] transition-all group">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center mb-3">
                                    <x-heroicon-o-arrow-path class="w-4 h-4 text-blue-400" />
                                </div>
                                <p class="text-sm font-semibold text-ink group-hover:text-brand transition-colors">Limpar
                                    Cache</p>
                                <p class="text-xs text-ink-muted mt-0.5">config, route, view, application</p>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.configuracoes.manutencao') }}">
                            @csrf
                            <input type="hidden" name="action" value="sync_calendar">
                            <button type="submit"
                                class="w-full card-accent rounded-xl p-4 text-left hover:border-white/[0.12] transition-all group">
                                <div class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center mb-3">
                                    <x-heroicon-o-calendar-days class="w-4 h-4 text-green-400" />
                                </div>
                                <p class="text-sm font-semibold text-ink group-hover:text-brand transition-colors">
                                    Sincronizar Calendário</p>
                                <p class="text-xs text-ink-muted mt-0.5">Força sync imediato com Google Calendar</p>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.configuracoes.manutencao') }}">
                            @csrf
                            <input type="hidden" name="action" value="mark_overdue">
                            <button type="submit"
                                class="w-full card-accent rounded-xl p-4 text-left hover:border-white/[0.12] transition-all group">
                                <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center mb-3">
                                    <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-amber-400" />
                                </div>
                                <p class="text-sm font-semibold text-ink group-hover:text-brand transition-colors">
                                    Verificar Vencimentos</p>
                                <p class="text-xs text-ink-muted mt-0.5">Marca pagamentos vencidos manualmente</p>
                            </button>
                        </form>    
                    </div>
                </div>

                {{-- Informações do sistema --}}
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-ink mb-4 flex items-center gap-2">
                        <x-heroicon-o-information-circle class="w-4 h-4 text-brand" />
                        Informações do sistema
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach ([['label' => 'Laravel', 'value' => app()->version()], ['label' => 'PHP', 'value' => PHP_VERSION], ['label' => 'Ambiente', 'value' => app()->environment()], ['label' => 'Debug', 'value' => config('app.debug') ? 'Ativo' : 'Desativado'], ['label' => 'Cache', 'value' => config('cache.default')], ['label' => 'Sessão', 'value' => config('session.driver')], ['label' => 'Fila', 'value' => config('queue.default')], ['label' => 'Timezone', 'value' => config('app.timezone')]] as $info)
                            <div class="card-accent rounded-xl p-3">
                                <p class="text-[10px] text-ink-subtle uppercase tracking-wider mb-1">{{ $info['label'] }}
                                </p>
                                <p class="text-xs font-semibold text-ink font-mono">{{ $info['value'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                {{-- Logs recentes (Atividades) --}}
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-ink flex items-center gap-2">
                                <x-heroicon-o-document-text class="w-4 h-4 text-brand" />
                                Atividades Recentes
                            </h3>
                            <span class="text-xs text-ink-subtle">Audit Log · últimos 30 registros</span>
                        </div>
                        
                        <div class="overflow-hidden border border-white/[0.07] rounded-xl">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-white/[0.02] border-b border-white/[0.07]">
                                        <th class="px-4 py-2 text-[10px] font-semibold text-ink-subtle uppercase tracking-wider">Usuário</th>
                                        <th class="px-4 py-2 text-[10px] font-semibold text-ink-subtle uppercase tracking-wider">Ação</th>
                                        <th class="px-4 py-2 text-[10px] font-semibold text-ink-subtle uppercase tracking-wider">Data</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/[0.05]">
                                    @forelse($auditLogs as $log)
                                        <tr class="hover:bg-white/[0.01] transition-colors">
                                            <td class="px-4 py-3">
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-medium text-ink">{{ $log->user?->name ?? 'Sistema' }}</span>
                                                    <span class="text-[10px] text-ink-subtle">{{ $log->ip_address }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="text-xs text-ink-muted">{{ $log->action }}</span>
                                                @if($log->client)
                                                    <span class="text-[10px] text-brand ml-1">· {{ $log->client->trade_name ?? $log->client->company_name }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-xs text-ink-subtle whitespace-nowrap">
                                                {{ $log->created_at->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-8 text-center text-xs text-ink-subtle">
                                                Nenhum registro de atividade encontrado.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
            </div>
        </div>

        {{-- =============================================
        TAB — Financeiro
        ============================================= --}}
        <div x-show="tab === 'finance'" x-transition.opacity>
            <div class="card p-6">
                <h3 class="text-sm font-semibold text-ink mb-4 flex items-center gap-2">
                    <x-heroicon-o-banknotes class="w-4 h-4 text-brand" />
                    Configurações financeiras
                </h3>

                <form method="POST" action="{{ route('admin.configuracoes.atualizar') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_tab" value="finance">

                    <div class="space-y-4">
                        <div class="form-group">
                            <label class="label">Chave PIX (geral)</label>
                            <input type="text" name="pix_key" value="{{ old('pix_key', $settings->get('pix_key')) }}"
                                placeholder="CPF/CNPJ, e-mail ou telefone" class="input">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="label">Nome do Titular / Empresa</label>
                                <input type="text" name="pix_name" value="{{ old('pix_name', $settings->get('pix_name')) }}"
                                    placeholder="Ex: Agencia Conti" class="input">
                            </div>
                            <div class="form-group">
                                <label class="label">Cidade do Titular</label>
                                <input type="text" name="pix_city" value="{{ old('pix_city', $settings->get('pix_city')) }}"
                                    placeholder="Ex: Sao Paulo" class="input">
                            </div>
                        </div>

                        <p class="text-xs text-ink-muted mt-1">
                            Esses dados serão usados para gerar o QR Code (Copia e Cola) do PIX pelo sistema.
                        </p>
                    </div>

                    <div class="mt-6 pt-6 border-t border-white/[0.07] flex justify-end">
                        <button type="submit" class="btn-primary">
                            <x-heroicon-o-check class="w-4 h-4" />
                            Salvar configurações financeiras
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection