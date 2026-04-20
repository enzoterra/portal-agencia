<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portal Agência</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-page font-sans flex">

    {{-- ── LADO ESQUERDO — Branding ──────────────────────── --}}
    <div class="hidden lg:flex lg:w-5/12 relative overflow-hidden bg-surface flex-col justify-between p-14 ps-16">

        {{-- Gradiente de fundo --}}
        <div class="absolute inset-0 bg-brand-subtle pointer-events-none"></div>
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-brand/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-brand/5 rounded-full blur-3xl pointer-events-none"></div>

        {{-- Logo --}}
        <div class="relative z-10">
            <img src="{{ asset('images/logo.png') }}" alt="Agência" class="h-10 w-auto">
        </div>

        {{-- Texto central --}}
        <div class="relative z-10">
            <p class="text-brand text-sm font-semibold uppercase tracking-widest mb-4">Portal do Cliente</p>
            <h2 class="font-title text-4xl font-bold text-ink leading-tight mb-4">
                Acompanhe seus<br>resultados em<br>
                <span class="text-gradient-brand">tempo real.</span>
            </h2>
            <p class="text-ink-muted text-sm leading-relaxed max-w-xs">
                Acesse relatórios, métricas, mídias, pagamentos e todo o histórico da operação de marketing da sua
                empresa.
            </p>
        </div>

        {{-- Rodapé do branding --}}
        <div class="relative z-10 flex items-center gap-2">
            <div class="w-1.5 h-1.5 rounded-full bg-brand animate-pulse-slow"></div>
            <span class="text-xs text-ink-subtle">Atualizado em tempo real</span>
        </div>

    </div>

    {{-- ── LADO DIREITO — Formulário ───────────────────────── --}}
    <div class="w-full lg:w-7/12 flex flex-col items-center justify-center px-6 py-12 min-h-screen mt-0">
        <div class="w-full max-w-sm">

            {{-- Logo mobile --}}
            <div class="flex items-center justify-center mb-12 lg:hidden">
                <img src="{{ asset('images/logo.png') }}" alt="Agência" class="h-10 w-auto">
            </div>

            {{-- Cabeçalho --}}
            <div class="mb-8">
                <h1 class="font-title text-2xl font-bold text-ink">Bem-vindo de volta</h1>
                <p class="text-ink-muted text-sm mt-1">Entre com suas credenciais para continuar.</p>
            </div>

            {{-- Alertas --}}
            @if ($errors->any())
                <div class="alert-error mb-6 animate-slide-up">
                    ✕ {{ $errors->first() }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert-success mb-6 animate-slide-up">
                    ✓ {{ session('success') }}
                </div>
            @endif

            {{-- Formulário --}}
            <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <label for="email" class="label">E-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="email" class="input @error('email') input-error @enderror"
                        placeholder="seu@email.com.br">
                </div>

                {{-- Senha --}}
                <div class="form-group">
                    <label for="password" class="label">Senha</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="input pr-11 @error('password') input-error @enderror" placeholder="••••••••">
                        {{-- Toggle senha visível --}}
                        <button type="button" onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-ink-subtle hover:text-ink transition-colors"
                            tabindex="-1">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Lembrar --}}
                <div class="flex items-center gap-2.5">
                    <input id="remember" type="checkbox" name="remember" class="checkbox">
                    <label for="remember" class="text-sm text-ink-muted cursor-pointer select-none">
                        Manter conectado
                    </label>
                </div>

                {{-- Botão --}}
                <button type="submit" class="btn-primary w-full btn-lg">
                    Entrar na conta
                </button>

            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-3 mt-6 mb-5">
                <div class="flex-1 h-px bg-white/[0.07]"></div>
                <span class="text-xs text-ink-subtle">ou</span>
                <div class="flex-1 h-px bg-white/[0.07]"></div>
            </div>

            {{-- Esqueci senha + WhatsApp --}}
            <div class="space-y-3 text-center">
                <div class="flex flex-col gap-1">
                    <p class="text-sm text-ink-subtle">
                        Esqueceu sua senha?
                    </p>
                    <span class="text-sm text-ink-muted font-medium">Entre em contato com a agência.</span>
                </div>

                {{-- Link WhatsApp — troque pelo número real --}}
                <a href="https://wa.me/[numero]?text=Olá,%20preciso%20de%20ajuda%20com%20meu%20acesso%20ao%20portal."
                    target="_blank" rel="noopener noreferrer" class="btn-whatsapp w-full">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                    </svg>
                    Falar com a agência
                </a>

                {{-- Quer ser cliente --}}
                <p class="text-sm text-ink-subtle pt-3">
                    Ainda não é cliente?
                    <a href="https://site.com.br/contato" target="_blank" rel="noopener noreferrer"
                        class="text-brand hover:text-brand-hover font-medium transition-colors">
                        Fale conosco
                    </a>
                </p>
            </div>

            <p class="text-center text-xs text-ink-subtle mt-10">
                © {{ date('Y') }} Agência Marketing. Todos os direitos reservados.
            </p>

            {{-- Link para página de privacidade --}}
            <p class="text-center text-xs text-ink-subtle mt-1">
                <a href="{{ route('privacy') }}"
                    class="text-brand hover:text-brand-hover font-medium transition-colors">
                    Política de Privacidade
                </a>
            </p>

        </div>
    </div>

</body>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>

</html>