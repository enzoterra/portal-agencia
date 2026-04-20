<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Página Não Encontrada</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --brand: #BD1613;
            --brand-hover: #A01210;
            --page: #09090B;
            --surface: #131416;
            --surface-acc: #1A1B1E;
            --ink: #FAFBFC;
            --ink-muted: #A3A3A3;
            --ink-subtle: #555;
            --border: rgba(255, 255, 255, 0.07);
            --blue: #3B82F6;
        }

        html {
            -webkit-font-smoothing: antialiased;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--page);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow: hidden;
            position: relative;
        }

        /* ── Floating particles ── */
        .particles {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(189, 22, 19, 0.12);
            animation: float linear infinite;
        }

        .particle:nth-child(1) {
            width: 4px;
            height: 4px;
            left: 10%;
            animation-duration: 18s;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            width: 3px;
            height: 3px;
            left: 25%;
            animation-duration: 22s;
            animation-delay: -5s;
        }

        .particle:nth-child(3) {
            width: 5px;
            height: 5px;
            left: 40%;
            animation-duration: 16s;
            animation-delay: -8s;
            background: rgba(59, 130, 246, 0.1);
        }

        .particle:nth-child(4) {
            width: 3px;
            height: 3px;
            left: 60%;
            animation-duration: 20s;
            animation-delay: -2s;
        }

        .particle:nth-child(5) {
            width: 4px;
            height: 4px;
            left: 75%;
            animation-duration: 14s;
            animation-delay: -11s;
            background: rgba(59, 130, 246, 0.1);
        }

        .particle:nth-child(6) {
            width: 2px;
            height: 2px;
            left: 88%;
            animation-duration: 25s;
            animation-delay: -6s;
        }

        @keyframes float {
            0% {
                transform: translateY(110vh) scale(0);
                opacity: 0;
            }

            10% {
                opacity: 1;
                transform: translateY(90vh) scale(1);
            }

            90% {
                opacity: 0.6;
            }

            100% {
                transform: translateY(-10vh) scale(0.5);
                opacity: 0;
            }
        }

        /* ── Grid ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 0;
        }

        .wrapper {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            max-width: 560px;
            width: 100%;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 3.5rem;
            text-decoration: none;
            animation: fadeDown 0.5s ease both;
        }

        @keyframes fadeDown {
            from {
                opacity: 0;
                transform: translateY(-16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-mark {
            width: 100px;
            height: auto;
            object-fit: contain;
        }

        .logo-text {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.3px;
            color: var(--ink);
        }

        /* ── 404 large display ── */
        .four-oh-four {
            position: relative;
            margin-bottom: 1rem;
            animation: popIn 0.7s 0.1s cubic-bezier(.34, 1.56, .64, 1) both;
        }

        @keyframes popIn {
            from {
                opacity: 0;
                transform: scale(0.75);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .four-oh-four-text {
            font-size: clamp(90px, 20vw, 130px);
            font-weight: 800;
            letter-spacing: -8px;
            line-height: 1;
            color: var(--ink);
            opacity: 0.06;
            user-select: none;
        }

        .four-oh-four-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-icon-wrap {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 0 60px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.04);
        }

        .error-icon-wrap svg {
            width: 28px;
            height: 28px;
            stroke: var(--brand);
            flex-shrink: 0;
        }

        .error-code-inline {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -2px;
            background: linear-gradient(135deg, #BD1613, #FF4441);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .error-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 0.75rem;
            letter-spacing: -0.3px;
            animation: fadeUp 0.5s 0.3s ease both;
        }

        .divider {
            width: 40px;
            height: 2px;
            background: linear-gradient(90deg, var(--brand), transparent);
            margin: 0 auto 1.5rem;
            border-radius: 99px;
            animation: fadeUp 0.5s 0.35s ease both;
        }

        .error-desc {
            font-size: 0.875rem;
            color: var(--ink-muted);
            line-height: 1.7;
            margin-bottom: 2rem;
            animation: fadeUp 0.5s 0.4s ease both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ── Search suggestion ── */
        .suggestions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
            animation: fadeUp 0.5s 0.45s ease both;
        }

        .suggestion-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.375rem 0.875rem;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border);
            border-radius: 99px;
            font-size: 0.75rem;
            color: var(--ink-muted);
            text-decoration: none;
            transition: background 0.15s, color 0.15s, border-color 0.15s;
        }

        .suggestion-chip:hover {
            background: rgba(189, 22, 19, 0.08);
            border-color: rgba(189, 22, 19, 0.3);
            color: var(--ink);
        }

        .suggestion-chip svg {
            width: 12px;
            height: 12px;
        }

        .actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            animation: fadeUp 0.5s 0.5s ease both;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.625rem 1.25rem;
            background: var(--brand);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.8125rem;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
        }

        .btn-primary:hover {
            background: var(--brand-hover);
        }

        .btn-primary:active {
            transform: scale(0.97);
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.625rem 1.25rem;
            background: transparent;
            color: var(--ink-muted);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 0.8125rem;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
        }

        .btn-ghost:hover {
            background: rgba(255, 255, 255, 0.04);
            color: var(--ink);
        }

        .footer {
            position: fixed;
            bottom: 1.5rem;
            font-size: 0.75rem;
            color: var(--ink-subtle);
        }
    </style>
</head>

<body>

    <!-- Particles -->
    <div class="particles" aria-hidden="true">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="wrapper">

        <a href="{{ url('/') }}" class="logo">
            <div class="logo-mark"><img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-mark"></div>
        </a>

        <!-- Large ghost 404 -->
        <div class="four-oh-four">
            <div class="four-oh-four-text">404</div>
            <div class="four-oh-four-overlay">
                <div class="error-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                        <path d="M11 8v3M11 14h.01" />
                    </svg>
                    <span class="error-code-inline">404</span>
                </div>
            </div>
        </div>

        <h1 class="error-title">Página Não Encontrada</h1>

        <div class="divider"></div>

        <p class="error-desc">
            O endereço que você tentou acessar não existe ou foi movido.<br>
            Verifique a URL ou navegue para uma das páginas abaixo.
        </p>

        <!-- Quick nav chips -->
        <div class="suggestions">
            <a href="{{ url('/') }}" class="suggestion-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
                Dashboard
            </a>
            <a href="{{ route('cliente.relatorios.index') }}" class="suggestion-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                </svg>
                Relatórios
            </a>
            <a href="{{ route('cliente.financeiro.index') }}" class="suggestion-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2" />
                    <line x1="1" y1="10" x2="23" y2="10" />
                </svg>
                Pagamentos
            </a>
            <a href="{{ route('cliente.midias.index') }}" class="suggestion-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2l5 3 3-3 3 3 5-3a2 2 0 0 1 2 2z" />
                </svg>
                Mídias
            </a>
        </div>

        <div class="actions">
            <a href="javascript:history.back()" class="btn-ghost">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 5l-7 7 7 7" />
                </svg>
                Voltar
            </a>
            <a href="{{ url('/') }}" class="btn-primary">
                Ir para o Dashboard
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14M12 5l7 7-7 7" />
                </svg>
            </a>
        </div>

    </div>

    <p class="footer">© {{ date('Y') }} Agência · Todos os direitos reservados</p>

</body>

</html>