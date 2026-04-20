<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Erro Interno</title>
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
            --red-glow: rgba(189, 22, 19, 0.15);
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

        /* ── Animated scanlines ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: repeating-linear-gradient(0deg,
                    transparent,
                    transparent 2px,
                    rgba(255, 255, 255, 0.008) 2px,
                    rgba(255, 255, 255, 0.008) 4px);
            pointer-events: none;
            z-index: 0;
        }

        /* ── Red pulse glow center ── */
        .glow-bg {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(189, 22, 19, 0.08) 0%, transparent 65%);
            animation: pulseGlow 3s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes pulseGlow {

            0%,
            100% {
                opacity: 0.6;
                transform: translate(-50%, -50%) scale(1);
            }

            50% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.08);
            }
        }

        .wrapper {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            max-width: 540px;
            width: 100%;
            animation: fadeUp 0.6s cubic-bezier(.22, .68, 0, 1.2) both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 3rem;
            text-decoration: none;
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

        /* ── Terminal-style error box ── */
        .terminal {
            background: #0D0D0E;
            border: 1px solid rgba(189, 22, 19, 0.3);
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
            width: 100%;
            box-shadow: 0 0 40px rgba(189, 22, 19, 0.06), inset 0 1px 0 rgba(255, 255, 255, 0.04);
            position: relative;
            overflow: hidden;
        }

        .terminal::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, var(--brand), transparent 60%);
        }

        .terminal-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .terminal-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .terminal-dot-red {
            background: #FF5F57;
        }

        .terminal-dot-amber {
            background: #FEBC2E;
        }

        .terminal-dot-green {
            background: #2AC643;
        }

        .terminal-title {
            font-size: 0.6875rem;
            color: var(--ink-subtle);
            margin-left: 0.25rem;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .terminal-body {
            font-family: 'SF Mono', 'Fira Code', 'Consolas', monospace;
            font-size: 0.75rem;
            line-height: 1.7;
        }

        .terminal-line {
            display: flex;
            gap: 0.75rem;
        }

        .terminal-prompt {
            color: var(--brand);
            user-select: none;
        }

        .terminal-text {
            color: rgba(250, 251, 252, 0.7);
        }

        .terminal-text.error {
            color: #FF6B6B;
        }

        .terminal-text.dim {
            color: var(--ink-subtle);
        }

        .terminal-cursor {
            display: inline-block;
            width: 7px;
            height: 13px;
            background: var(--brand);
            margin-left: 2px;
            vertical-align: middle;
            animation: blink 1s step-end infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }
        }

        /* ── Code & title ── */
        .error-code {
            font-size: clamp(80px, 18vw, 118px);
            font-weight: 800;
            letter-spacing: -6px;
            line-height: 1;
            background: linear-gradient(135deg, #BD1613 0%, #FF4441 50%, #BD1613 100%);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 4s ease infinite;
            margin-bottom: 0.5rem;
        }

        @keyframes gradientShift {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        .error-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 0.75rem;
            letter-spacing: -0.3px;
        }

        .divider {
            width: 40px;
            height: 2px;
            background: linear-gradient(90deg, var(--brand), transparent);
            margin: 0 auto 1.5rem;
            border-radius: 99px;
        }

        .error-desc {
            font-size: 0.875rem;
            color: var(--ink-muted);
            line-height: 1.7;
            margin-bottom: 1.75rem;
        }

        .actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            flex-wrap: wrap;
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

        /* ── Status ── */
        .status-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.75rem;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--brand);
            animation: statusPulse 2s ease-in-out infinite;
        }

        @keyframes statusPulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(189, 22, 19, 0.6);
            }

            50% {
                box-shadow: 0 0 0 5px rgba(189, 22, 19, 0);
            }
        }

        .status-text {
            font-size: 0.75rem;
            color: var(--brand);
            font-weight: 600;
            letter-spacing: 0.3px;
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

    <div class="glow-bg" aria-hidden="true"></div>

    <div class="wrapper">

        <a href="{{ url('/') }}" class="logo">
            <div class="logo-mark"><img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-mark"></div>
        </a>

        <div class="error-code">500</div>
        <h1 class="error-title">Erro Interno do Servidor</h1>
        <div class="divider"></div>

        <div class="status-row">
            <div class="status-dot"></div>
            <span class="status-text">Equipe técnica notificada</span>
        </div>

        <!-- Terminal visual -->
        <div class="terminal" role="region" aria-label="Detalhes do erro">
            <div class="terminal-header">
                <span class="terminal-dot terminal-dot-red"></span>
                <span class="terminal-dot terminal-dot-amber"></span>
                <span class="terminal-dot terminal-dot-green"></span>
                <span class="terminal-title">portal.conti.com.br — erro do sistema</span>
            </div>
            <div class="terminal-body">
                <div class="terminal-line">
                    <span class="terminal-prompt">$</span>
                    <span class="terminal-text">GET /portal — HTTP 500</span>
                </div>
                <div class="terminal-line">
                    <span class="terminal-prompt">→</span>
                    <span class="terminal-text error">InternalServerError: unexpected condition</span>
                </div>
                <div class="terminal-line">
                    <span class="terminal-prompt"> </span>
                    <span class="terminal-text dim">at {{ now()->format('d/m/Y H:i:s') }}</span>
                </div>
                <div class="terminal-line" style="margin-top:0.5rem">
                    <span class="terminal-prompt">$</span>
                    <span class="terminal-text dim">Aguardando resolução... <span class="terminal-cursor"></span></span>
                </div>
            </div>
        </div>

        <p class="error-desc">
            Algo inesperado aconteceu no nosso servidor. Nossa equipe já foi
            informada e está trabalhando para resolver. Tente novamente
            em alguns instantes.
        </p>

        <div class="actions">
            <a href="javascript:location.reload()" class="btn-ghost">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="1 4 1 10 7 10" />
                    <path d="M3.51 15a9 9 0 1 0 .49-4" />
                </svg>
                Tentar novamente
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