<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Política de Privacidade — Portal Agência Conti</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --brand:        #BD1613;
            --brand-hover:  #9e100e;
            --page:         #0A0A0B;
            --surface:      #131416;
            --surface-acc:  #1A1B1E;
            --border:       rgba(255,255,255,0.07);
            --ink:          #FAFBFC;
            --ink-muted:    #A3A3A3;
            --ink-subtle:   #6B6B6B;
        }

        html { scroll-behavior: smooth; -webkit-font-smoothing: antialiased; }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--page);
            color: var(--ink);
            min-height: 100vh;
            line-height: 1.7;
        }

        ::selection { background: rgba(189,22,19,.28); color: #FAFBFC; }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #000; }
        ::-webkit-scrollbar-thumb { background: #272525; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--brand); }

        /* ── Header ────────────────────────────────────── */
        header {
            position: sticky;
            top: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            height: 64px;
            background: rgba(10,10,11,.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
        }

        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .logo-mark {
            width: 65px;
            height: auto;
            object-fit: contain;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-size: .75rem;
            font-weight: 600;
            color: var(--ink-muted);
            text-decoration: none;
            padding: .5rem .875rem;
            border: 1px solid var(--border);
            border-radius: .5rem;
            background: var(--surface-acc);
            transition: color .15s, border-color .15s, background .15s;
        }

        .back-btn:hover { color: var(--ink); border-color: rgba(255,255,255,.15); background: var(--surface); }
        .back-btn svg { width: 14px; height: 14px; }

        /* ── Hero ───────────────────────────────────────── */
        .hero {
            position: relative;
            overflow: hidden;
            padding: 5rem 2rem 4rem;
            text-align: center;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 50% -10%, rgba(189,22,19,.18) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-size: .6875rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--brand);
            background: rgba(189,22,19,.1);
            border: 1px solid rgba(189,22,19,.25);
            border-radius: 99px;
            padding: .3rem .875rem;
            margin-bottom: 1.5rem;
        }

        .hero-eyebrow::before {
            content: '';
            width: 6px;
            height: 6px;
            background: var(--brand);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%,100% { opacity: 1; transform: scale(1); }
            50%      { opacity: .4; transform: scale(.7); }
        }

        .hero h1 {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 800;
            letter-spacing: -.04em;
            line-height: 1.1;
            color: var(--ink);
            margin-bottom: 1rem;
        }

        .hero h1 em {
            font-style: normal;
            background: linear-gradient(135deg, #BD1613, #FF4441);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-meta {
            font-size: .8125rem;
            color: var(--ink-subtle);
            font-weight: 500;
        }

        .hero-meta strong { color: var(--ink-muted); font-weight: 600; }

        /* ── Layout ─────────────────────────────────────── */
        .layout {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 2rem 6rem;
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 3rem;
            align-items: start;
        }

        @media (max-width: 768px) {
            .layout { grid-template-columns: 1fr; gap: 2rem; }
            .toc { display: none; }
        }

        /* ── ToC ────────────────────────────────────────── */
        .toc {
            position: sticky;
            top: 84px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1.25rem;
        }

        .toc-title {
            font-size: .6875rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--ink-subtle);
            margin-bottom: 1rem;
            padding-bottom: .75rem;
            border-bottom: 1px solid var(--border);
        }

        .toc ul { list-style: none; display: flex; flex-direction: column; gap: .125rem; }

        .toc a {
            display: block;
            font-size: .75rem;
            font-weight: 500;
            color: var(--ink-muted);
            text-decoration: none;
            padding: .375rem .625rem;
            border-radius: .5rem;
            transition: color .15s, background .15s;
            line-height: 1.4;
        }

        .toc a:hover { color: var(--ink); background: rgba(255,255,255,.04); }
        .toc a.active { color: var(--brand); background: rgba(189,22,19,.08); }

        /* ── Content ────────────────────────────────────── */
        .content { min-width: 0; }

        .section {
            margin-bottom: 3rem;
            padding-bottom: 3rem;
            border-bottom: 1px solid var(--border);
        }

        .section:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }

        .section-number {
            display: inline-block;
            font-size: .6875rem;
            font-weight: 700;
            letter-spacing: .06em;
            color: var(--brand);
            text-transform: uppercase;
            margin-bottom: .5rem;
        }

        .section h2 {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -.025em;
            color: var(--ink);
            margin-bottom: 1rem;
            scroll-margin-top: 88px;
        }

        .section p {
            font-size: .875rem;
            color: var(--ink-muted);
            margin-bottom: .875rem;
            font-weight: 400;
        }

        .section p:last-child { margin-bottom: 0; }

        .section strong { color: var(--ink); font-weight: 600; }

        /* listas */
        .section ul, .section ol {
            margin: .75rem 0 .875rem 0;
            padding-left: 0;
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }

        .section ul li, .section ol li {
            font-size: .875rem;
            color: var(--ink-muted);
            padding-left: 1.25rem;
            position: relative;
        }

        .section ul li::before {
            content: '';
            position: absolute;
            left: 0;
            top: .6em;
            width: 5px;
            height: 5px;
            background: var(--brand);
            border-radius: 50%;
        }

        .section ol { counter-reset: item; }
        .section ol li { counter-increment: item; }
        .section ol li::before {
            content: counter(item) ".";
            position: absolute;
            left: 0;
            top: 0;
            font-size: .75rem;
            font-weight: 700;
            color: var(--brand);
        }

        /* callout */
        .callout {
            background: rgba(189,22,19,.06);
            border: 1px solid rgba(189,22,19,.2);
            border-left: 3px solid var(--brand);
            border-radius: .75rem;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
        }

        .callout p {
            color: rgba(250,251,252,.7) !important;
            font-size: .8125rem !important;
            margin-bottom: 0 !important;
        }

        .callout p strong { color: var(--ink) !important; }

        /* info box */
        .info-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: .875rem;
            padding: 1.25rem;
            margin: 1.25rem 0;
            display: flex;
            gap: .875rem;
        }

        .info-box-icon {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            background: rgba(189,22,19,.12);
            border-radius: .5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--brand);
            font-size: 1rem;
        }

        .info-box-body { flex: 1; min-width: 0; }

        .info-box-title {
            font-size: .8125rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: .25rem;
        }

        .info-box-desc {
            font-size: .8125rem;
            color: var(--ink-muted);
        }

        /* contact card */
        .contact-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-top: 1.25rem;
        }

        .contact-card-label {
            font-size: .6875rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--ink-subtle);
            margin-bottom: 1rem;
        }

        .contact-row {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .625rem 0;
            border-bottom: 1px solid var(--border);
        }

        .contact-row:last-child { border-bottom: none; padding-bottom: 0; }

        .contact-row-icon {
            width: 32px;
            height: 32px;
            background: var(--surface-acc);
            border-radius: .5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ink-muted);
            font-size: .875rem;
            flex-shrink: 0;
        }

        .contact-row-icon .contact-icon, .info-box-icon .info-icon {
            width: 24px;
            height: 24px;
        }

        .contact-row-label {
            font-size: .6875rem;
            color: var(--ink-subtle);
            font-weight: 500;
        }

        .contact-row-value {
            font-size: .8125rem;
            color: var(--ink);
            font-weight: 600;
        }

        /* ── Footer ─────────────────────────────────────── */
        footer {
            border-top: 1px solid var(--border);
            padding: 2rem;
            text-align: center;
        }

        .footer-inner {
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .75rem;
        }

        footer p {
            font-size: .75rem;
            color: var(--ink-subtle);
        }

        footer a { color: var(--ink-muted); text-decoration: none; }
        footer a:hover { color: var(--brand); }

        /* ── Divider ─────────────────────────────────────── */
        .divider { border: none; border-top: 1px solid var(--border); margin: 2rem 0; }

        /* ── Scroll progress ──────────────────────────────── */
        #progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 2px;
            background: linear-gradient(90deg, #BD1613, #FF4441);
            z-index: 100;
            width: 0%;
            transition: width .1s linear;
        }
    </style>
</head>
<body>

<div id="progress"></div>

<!-- Header -->
<header>
    <a href="{{ route('login') }}" class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="Agência Conti" class="logo-mark">
    </a>
    <a href="{{ route('login') }}" class="back-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Voltar ao Portal
    </a>
</header>

<!-- Hero -->
<div class="hero">
    <h1>Política de Privacidade</h1>
</div>

<!-- Layout -->
<div class="layout">

    <!-- ToC -->
    <aside class="toc" id="toc">
        <div class="toc-title">Sumário</div>
        <ul>
            <li><a href="#s1">1. Quem somos</a></li>
            <li><a href="#s2">2. Dados coletados</a></li>
            <li><a href="#s3">3. Finalidade do tratamento</a></li>
            <li><a href="#s4">4. Google APIs</a></li>
            <li><a href="#s5">5. Base legal</a></li>
            <li><a href="#s6">6. Compartilhamento</a></li>
            <li><a href="#s7">7. Retenção e exclusão</a></li>
            <li><a href="#s8">8. Segurança</a></li>
            <li><a href="#s9">9. Seus direitos (LGPD)</a></li>
            <li><a href="#s10">10. Cookies</a></li>
            <li><a href="#s11">11. Alterações</a></li>
            <li><a href="#s12">12. Contato</a></li>
        </ul>
    </aside>

    <!-- Content -->
    <main class="content">

        <div class="callout">
            <p>
                <strong>Resumo em uma frase:</strong> Coletamos apenas os dados necessários para operar o portal de clientes. Não vendemos, não alugamos e não compartilhamos suas informações com terceiros para fins publicitários.
            </p>
        </div>

        <!-- 1 -->
        <section class="section" id="s1">
            <span class="section-number">Seção 01</span>
            <h2>Quem somos</h2>
            <p>
                A <strong>Agência Conti</strong> é uma empresa especializada em marketing para o agronegócio, responsável pelo tratamento dos dados pessoais descritos nesta política.
            </p>
            <p>
                Este documento descreve como coletamos, usamos, armazenamos e protegemos as informações dos usuários que acessam o <strong>Portal de Clientes</strong> — sistema web restrito a clientes contratantes e colaboradores da agência.
            </p>
        </section>

        <!-- 2 -->
        <section class="section" id="s2">
            <span class="section-number">Seção 02</span>
            <h2>Dados que coletamos</h2>

            <div class="info-box">
                <div class="info-box-icon">
                    <x-heroicon-o-user class="info-icon" />
                </div>
                <div class="info-box-body">
                    <div class="info-box-title">Dados de conta</div>
                    <div class="info-box-desc">Nome, endereço de e-mail e senha (armazenada como hash bcrypt irreversível).</div>
                </div>
            </div>

            <div class="info-box">
                <div class="info-box-icon">
                    <x-heroicon-o-chart-bar class="info-icon" />
                </div>
                <div class="info-box-body">
                    <div class="info-box-title">Dados de uso do portal</div>
                    <div class="info-box-desc">Registros de acesso (IP, data, hora, ação realizada) para fins de auditoria e segurança. Esses logs são mantidos exclusivamente pelo administrador do sistema.</div>
                </div>
            </div>

            <div class="info-box">
                <div class="info-box-icon">
                    <x-heroicon-o-briefcase class="info-icon" />
                </div>
                <div class="info-box-body">
                    <div class="info-box-title">Dados financeiros e operacionais</div>
                    <div class="info-box-desc">Informações relacionadas aos serviços contratados: relatórios de desempenho, valores de investimento, receita atribuída, status de pagamentos e notas fiscais. Esses dados são inseridos pelos administradores da agência e disponibilizados ao cliente titular.</div>
                </div>
            </div>

            <div class="info-box">
                <div class="info-box-icon">
                    <x-heroicon-o-key class="info-icon" />
                </div>
                <div class="info-box-body">
                    <div class="info-box-title">Tokens de autenticação Google (somente admin)</div>
                    <div class="info-box-desc">Tokens de acesso e atualização OAuth 2.0 emitidos pelo Google, necessários para integração com Google Calendar e Google Drive. Armazenados de forma criptografada (AES-256).</div>
                </div>
            </div>

            <p style="margin-top:1rem">
                Não coletamos dados sensíveis (saúde, biometria, origem racial, orientação sexual ou crenças), nem realizamos qualquer tipo de rastreamento comportamental ou publicidade.
            </p>
        </section>

        <!-- 3 -->
        <section class="section" id="s3">
            <span class="section-number">Seção 03</span>
            <h2>Finalidade do tratamento</h2>
            <p>Utilizamos os dados coletados exclusivamente para:</p>
            <ul>
                <li>Autenticar e autorizar o acesso ao portal;</li>
                <li>Exibir ao cliente suas métricas, relatórios, pagamentos, notas fiscais e links de mídia;</li>
                <li>Permitir que administradores da agência gerenciem as informações de cada cliente;</li>
                <li>Sincronizar eventos do Google Calendar da agência para exibição no portal;</li>
                <li>Disponibilizar links de arquivos do Google Drive organizados por período;</li>
                <li>Registrar logs de auditoria para fins de segurança e rastreabilidade de ações administrativas;</li>
                <li>Manter backups periódicos do sistema.</li>
            </ul>
            <p>Nenhum dado é utilizado para fins de marketing, publicidade, perfilamento ou qualquer outra finalidade além das listadas acima.</p>
        </section>

        <!-- 4 -->
        <section class="section" id="s4">
            <span class="section-number">Seção 04</span>
            <h2>Uso das APIs do Google</h2>
            <p>
                O portal utiliza as APIs <strong>Google Calendar API</strong> e <strong>Google Drive API</strong> por meio do protocolo OAuth 2.0, com o objetivo exclusivo de:
            </p>
            <ul>
                <li><strong>Google Calendar:</strong> Ler eventos da agenda da Agência Conti e exibi-los para os clientes no portal, sem criação, edição ou exclusão de eventos;</li>
                <li><strong>Google Drive:</strong> Acessar links de arquivos (mídia de campanhas) organizados por mês/ano e disponibilizá-los ao cliente titular, sem upload, edição ou exclusão de arquivos.</li>
            </ul>

            <div class="callout">
                <p>
                    <strong>Política de Dados da Google API:</strong> O uso das informações recebidas pelas APIs do Google respeita a
                    <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener" style="color:#BD1613;">Google API Services User Data Policy</a>,
                    incluindo os requisitos de Uso Limitado (<em>Limited Use</em>). Os dados obtidos via Google APIs não são transferidos a terceiros, usados para publicidade nem combinados com dados de outras fontes.
                </p>
            </div>

            <p>
                A autenticação OAuth é realizada exclusivamente pela equipe administrativa da Agência Conti. Clientes finais não autorizam nem concedem acesso às suas contas Google por meio deste portal.
            </p>
            <p>
                Tokens de autenticação Google são armazenados de forma criptografada, com acesso restrito ao sistema, e revogados automaticamente quando a conexão for desfeita pelo administrador.
            </p>
        </section>

        <!-- 5 -->
        <section class="section" id="s5">
            <span class="section-number">Seção 05</span>
            <h2>Base legal para o tratamento</h2>
            <p>O tratamento dos dados pessoais descritos nesta política fundamenta-se nas seguintes bases legais previstas na <strong>Lei Geral de Proteção de Dados (LGPD — Lei nº 13.709/2018)</strong>:</p>
            <ul>
                <li><strong>Execução de contrato</strong> (art. 7º, V) — para operação do portal e prestação dos serviços contratados pelo cliente;</li>
                <li><strong>Legítimo interesse</strong> (art. 7º, IX) — para registros de auditoria, segurança do sistema e prevenção a fraudes;</li>
                <li><strong>Cumprimento de obrigação legal</strong> (art. 7º, II) — quando aplicável, como retenção de registros fiscais.</li>
            </ul>
        </section>

        <!-- 6 -->
        <section class="section" id="s6">
            <span class="section-number">Seção 06</span>
            <h2>Compartilhamento de dados</h2>
            <p>Não compartilhamos dados pessoais com terceiros, exceto:</p>
            <ul>
                <li><strong>Infraestrutura de hospedagem</strong> — os dados residem em servidores da Hostinger, sob termos de confidencialidade;</li>
                <li><strong>APIs do Google</strong> — somente os tokens OAuth necessários para autenticação são transmitidos ao Google, conforme descrito na Seção 04;</li>
                <li><strong>Determinação legal</strong> — quando exigido por autoridade competente, ordem judicial ou obrigação regulatória.</li>
            </ul>
            <p>
                <strong>Não há venda, licenciamento ou transferência comercial</strong> de dados pessoais a qualquer terceiro.
            </p>
        </section>

        <!-- 7 -->
        <section class="section" id="s7">
            <span class="section-number">Seção 07</span>
            <h2>Retenção e exclusão de dados</h2>
            <p>Mantemos seus dados pelo tempo necessário para cumprir as finalidades descritas nesta política:</p>
            <ul>
                <li>Dados de conta: enquanto houver contrato ativo. Após rescisão, os dados são anonimizados ou excluídos em até <strong>90 dias</strong>;</li>
                <li>Registros de auditoria: retidos por <strong>12 meses</strong> para fins de segurança e conformidade;</li>
                <li>Notas fiscais: retidas pelo prazo legal mínimo de <strong>5 anos</strong> (obrigação fiscal);</li>
                <li>Tokens Google: excluídos imediatamente quando a conexão for revogada pelo administrador.</li>
            </ul>
        </section>

        <!-- 8 -->
        <section class="section" id="s8">
            <span class="section-number">Seção 08</span>
            <h2>Segurança das informações</h2>
            <p>Adotamos medidas técnicas e organizacionais para proteger seus dados contra acesso não autorizado, perda ou vazamento:</p>
            <ul>
                <li>Senhas armazenadas como hash bcrypt (função de derivação unidirecional);</li>
                <li>Tokens e dados sensíveis criptografados com AES-256 em repouso;</li>
                <li>Comunicações transmitidas exclusivamente via HTTPS (TLS);</li>
                <li>Controle de acesso baseado em perfis (RBAC) com isolamento por cliente;</li>
                <li>Arquivos privados (notas fiscais) armazenados fora do diretório público, inacessíveis diretamente por URL;</li>
                <li>Backups periódicos automáticos do banco de dados;</li>
                <li>Rate limiting em tentativas de login para mitigar ataques de força bruta.</li>
            </ul>
            <p>
                Nenhum sistema é 100% inviolável. Em caso de incidente de segurança que afete dados pessoais, notificaremos os titulares e a Autoridade Nacional de Proteção de Dados (ANPD) conforme exigido pela LGPD.
            </p>
        </section>

        <!-- 9 -->
        <section class="section" id="s9">
            <span class="section-number">Seção 09</span>
            <h2>Seus direitos (LGPD)</h2>
            <p>Como titular de dados pessoais, você tem os seguintes direitos garantidos pela LGPD (art. 18):</p>
            <ol>
                <li>Confirmação da existência de tratamento de seus dados;</li>
                <li>Acesso aos dados pessoais que mantemos sobre você;</li>
                <li>Correção de dados incompletos, inexatos ou desatualizados;</li>
                <li>Anonimização, bloqueio ou eliminação de dados desnecessários ou excessivos;</li>
                <li>Portabilidade dos dados a outro fornecedor de serviço;</li>
                <li>Eliminação dos dados tratados com base no consentimento;</li>
                <li>Informação sobre entidades com quem compartilhamos seus dados;</li>
                <li>Revogação do consentimento, quando aplicável.</li>
            </ol>
            <p>
                Para exercer qualquer desses direitos, entre em contato conosco pelos canais indicados na Seção 12.
            </p>
        </section>

        <!-- 10 -->
        <section class="section" id="s10">
            <span class="section-number">Seção 10</span>
            <h2>Cookies e armazenamento local</h2>
            <p>
                O portal utiliza <strong>apenas cookies essenciais</strong> para o funcionamento do sistema:
            </p>
            <ul>
                <li><strong>Cookie de sessão</strong> — identifica o usuário autenticado durante a navegação. Expira ao fechar o navegador ou após o timeout configurado;</li>
                <li><strong>Token CSRF</strong> — protege formulários contra ataques de falsificação de requisição.</li>
            </ul>
            <p>
                Não utilizamos cookies de rastreamento, analytics, remarketing ou quaisquer tecnologias de perfilamento comportamental.
            </p>
        </section>

        <!-- 11 -->
        <section class="section" id="s11">
            <span class="section-number">Seção 11</span>
            <h2>Alterações nesta política</h2>
            <p>
                Podemos atualizar esta política periodicamente para refletir mudanças nas práticas do sistema, requisitos legais ou integrações. A data de "última atualização" no topo desta página será sempre ajustada.
            </p>
            <p>
                Para alterações relevantes que afetem seus direitos, comunicaremos os usuários diretamente pelo portal no próximo acesso.
            </p>
        </section>

        <!-- 12 -->
        <section class="section" id="s12">
            <span class="section-number">Seção 12</span>
            <h2>Contato e Encarregado de Dados</h2>
            <p>
                Para dúvidas sobre esta política, solicitações relacionadas a dados pessoais ou para exercer seus direitos como titular, entre em contato com a Agência Conti:
            </p>

            <div class="contact-card">
                <div class="contact-card-label">Canais de contato</div>

                <div class="contact-row">
                    <div class="contact-row-icon">
                        <x-heroicon-o-envelope class="contact-icon" />
                    </div>
                    <div>
                        <div class="contact-row-label">E-mail</div>
                        <div class="contact-row-value">contato@agenciaconti.com</div>
                    </div>
                </div>

                <div class="contact-row">
                    <div class="contact-row-icon">
                        <x-ri-whatsapp-line class="contact-icon" />
                    </div>
                    <div>
                        <div class="contact-row-label">WhatsApp</div>
                        <div class="contact-row-value">(67) 99673-9779</div>
                    </div>
                </div>

                <div class="contact-row">
                    <div class="contact-row-icon">
                        <x-heroicon-o-globe-alt class="contact-icon" />
                    </div>
                    <div>
                        <div class="contact-row-label">Website</div>
                        <div class="contact-row-value">www.agenciaconti.com.br</div>
                    </div>
                </div>

                <div class="contact-row">
                    <div class="contact-row-icon">
                        <x-heroicon-o-map-pin class="w-4 h-4" />
                    </div>
                    <div>
                        <div class="contact-row-label">Responsável pelo tratamento</div>
                        <div class="contact-row-value">Agência Conti — CNPJ 42.065.606/0001-55</div>
                    </div>
                </div>
            </div>

            <p style="margin-top:1.25rem">
                Respondemos às solicitações em até <strong>15 dias úteis</strong>, conforme prazo previsto na LGPD.
            </p>
        </section>

    </main>
</div>

<!-- Footer -->
<footer>
    <div class="footer-inner">
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="Agência Conti" class="logo-mark">
        </div>
        <p>© {{ date('Y') }} Agência Conti. Todos os direitos reservados.</p>
        <p>Esta política está em conformidade com a <strong style="color:var(--ink-muted)">LGPD (Lei nº 13.709/2018)</strong> e com a <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener">Google API Services User Data Policy</a>.</p>
    </div>
</footer>

<script>
    // Scroll progress bar
    window.addEventListener('scroll', () => {
        const doc = document.documentElement;
        const scrolled = doc.scrollTop / (doc.scrollHeight - doc.clientHeight);
        document.getElementById('progress').style.width = (scrolled * 100) + '%';
    });

    // ToC active link highlight
    const sections = document.querySelectorAll('.section[id]');
    const tocLinks = document.querySelectorAll('.toc a');

    const obs = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                tocLinks.forEach(a => a.classList.remove('active'));
                const active = document.querySelector('.toc a[href="#' + entry.target.id + '"]');
                if (active) active.classList.add('active');
            }
        });
    }, { rootMargin: '-20% 0px -70% 0px' });

    sections.forEach(s => obs.observe(s));
</script>

</body>
</html>