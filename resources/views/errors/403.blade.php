<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Acesso Negado</title>
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
            --amber: #F59E0B;
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

        /* ── Background subtle hexagons ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.04) 1px, transparent 0);
            background-size: 32px 32px;
            pointer-events: none;
            z-index: 0;
        }

        /* ── Glow amber ── */
        body::after {
            content: '';
            position: fixed;
            bottom: -10%;
            right: -5%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.06) 0%, transparent 65%);
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
            max-width: 520px;
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

        /* ── Lock animation ── */
        .lock-wrap {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .error-icon {
            width: 60px;
            height: 60px;
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            animation: shakeOnce 0.8s 0.5s cubic-bezier(.36, .07, .19, .97) both;
        }

        @keyframes shakeOnce {

            0%,
            100% {
                transform: translateX(0) rotate(0deg);
            }

            15% {
                transform: translateX(-4px) rotate(-2deg);
            }

            30% {
                transform: translateX(4px) rotate(2deg);
            }

            45% {
                transform: translateX(-3px) rotate(-1deg);
            }

            60% {
                transform: translateX(3px) rotate(1deg);
            }

            75% {
                transform: translateX(-1px);
            }
        }

        .error-icon svg {
            width: 28px;
            height: 28px;
            stroke: var(--amber);
        }

        .error-code {
            font-size: clamp(80px, 18vw, 120px);
            font-weight: 800;
            letter-spacing: -6px;
            line-height: 1;
            color: var(--amber);
            opacity: 0.9;
            margin-bottom: 0.5rem;
            text-shadow: 0 0 80px rgba(245, 158, 11, 0.2);
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
            background: linear-gradient(90deg, var(--amber), transparent);
            margin: 0 auto 1.5rem;
            border-radius: 99px;
        }

        .error-desc {
            font-size: 0.875rem;
            color: var(--ink-muted);
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        /* ── Info box ── */
        .info-box {
            background: rgba(245, 158, 11, 0.06);
            border: 1px solid rgba(245, 158, 11, 0.15);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 2rem;
            text-align: left;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .info-box svg {
            width: 16px;
            height: 16px;
            stroke: var(--amber);
            flex-shrink: 0;
            margin-top: 1px;
        }

        .info-box p {
            font-size: 0.8125rem;
            color: rgba(245, 158, 11, 0.9);
            line-height: 1.6;
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

        .footer {
            position: fixed;
            bottom: 1.5rem;
            font-size: 0.75rem;
            color: var(--ink-subtle);
        }
    </style>
</head>

<body>

    <div class="wrapper">

        <a href="{{ url('/') }}" class="logo">
            <div class="logo-mark"><img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-mark"></div>
        </a>

        <div class="lock-wrap">
            <div class="error-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
            </div>
        </div>

        <div class="error-code">403</div>
        <h1 class="error-title">Acesso Negado</h1>

        <div class="divider"></div>

        <p class="error-desc">
            Você não tem permissão para acessar este recurso. Se acredita
            que isso é um erro, entre em contato com a equipe da Conti.
        </p>

        <div class="info-box">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 8v4M12 16h.01" />
            </svg>
            <p>Esta ação foi registrada por motivos de segurança. Se precisar de acesso, entre em contato com um
                administrador da agência.</p>
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