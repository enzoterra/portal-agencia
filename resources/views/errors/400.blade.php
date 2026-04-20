<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>400 — Requisição Inválida</title>
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
        }

        html {
            -webkit-font-smoothing: antialiased;
            scroll-behavior: smooth;
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

        /* ── Background grid ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.025) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
            z-index: 0;
        }

        /* ── Glow radial ── */
        body::after {
            content: '';
            position: fixed;
            top: -20%;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(189, 22, 19, 0.10) 0%, transparent 70%);
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

        /* ── Logo ── */
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

        /* ── Code ── */
        .error-code {
            font-size: clamp(80px, 18vw, 120px);
            font-weight: 800;
            letter-spacing: -6px;
            line-height: 1;
            background: linear-gradient(135deg, #BD1613, #FF4441);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            position: relative;
        }

        .error-code::after {
            content: attr(data-code);
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(189, 22, 19, 0.15), rgba(255, 68, 65, 0.08));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: blur(20px);
            transform: translateY(8px);
            z-index: -1;
        }

        .error-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 0.75rem;
            letter-spacing: -0.3px;
        }

        .error-desc {
            font-size: 0.875rem;
            color: var(--ink-muted);
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        /* ── Divider ── */
        .divider {
            width: 40px;
            height: 2px;
            background: linear-gradient(90deg, var(--brand), transparent);
            margin: 0 auto 2rem;
            border-radius: 99px;
        }

        /* ── Actions ── */
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
            justify-content: center;
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
            justify-content: center;
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

        /* ── Footer ── */
        .footer {
            position: fixed;
            bottom: 1.5rem;
            font-size: 0.75rem;
            color: var(--ink-subtle);
        }

        /* ── Icon ── */
        .error-icon {
            width: 56px;
            height: 56px;
            background: rgba(189, 22, 19, 0.08);
            border: 1px solid rgba(189, 22, 19, 0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .error-icon svg {
            width: 26px;
            height: 26px;
            color: var(--brand);
            stroke: var(--brand);
        }
    </style>
</head>

<body>

    <div class="wrapper">

        <a href="{{ url('/') }}" class="logo">
            <div class="logo-mark"><img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-mark"></div>
        </a>

        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 9v4M12 17h.01" />
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
            </svg>
        </div>

        <div class="error-code" data-code="400">400</div>
        <h1 class="error-title">Requisição Inválida</h1>

        <div class="divider"></div>

        <p class="error-desc">
            O servidor não conseguiu processar esta solicitação porque os dados
            enviados estão malformados ou são inválidos. Verifique as informações
            e tente novamente.
        </p>

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