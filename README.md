<h1 align="center">
  Portal de Clientes - Agência de Marketing
</h1>

<p align="center">
  Plataforma SaaS white-label para agências de marketing digital gerenciarem clientes, entregas, finanças e resultados, em um único lugar.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/PostgreSQL-16-4169E1?style=for-the-badge&logo=postgresql&logoColor=white" />
  <img src="https://img.shields.io/badge/Google_APIs-Calendar%20%7C%20Drive-4285F4?style=for-the-badge&logo=google&logoColor=white" />
</p>

---

## 📌 Sobre o projeto

O **Portal de Clientes** é um sistema web desenvolvido em Laravel 11 para centralizar a gestão de clientes de uma agência de marketing digital. Ele oferece dois ambientes distintos: um **painel administrativo** para a equipe da agência e um **portal exclusivo** para os clientes.

A plataforma conecta-se à conta Google da agência para sincronizar automaticamente eventos de reuniões e entregas, permite o envio de relatórios mensais de performance com dados reais de campanhas e redes sociais, e oferece uma área financeira completa com geração de cobranças, QR Code PIX e controle de inadimplência.

---

## ✨ Funcionalidades

### 🔐 Acesso e Permissões
- Sistema de autenticação com múltiplos perfis: `super_admin`, `admin` e `client`
- Controle de permissões granular via **Spatie Laravel Permission**
- Suporte a **múltiplos usuários por empresa-cliente**, cada um com acesso controlado à sua conta
- Proteção anti-IDOR com UUIDs em todas as rotas de recursos sensíveis
- Soft delete em usuários, clientes e registros financeiros

### 🗓️ Integração com Google Agenda
- OAuth 2.0 com a conta Google da agência
- **Sincronização automática a cada 5 minutos** via Laravel Scheduler + Cron Job
- Eventos baixados e armazenados localmente para exibição no portal do cliente
- Filtro inteligente de eventos por nome da empresa (case-insensitive)
- Cada cliente vê apenas os eventos relevantes à sua conta

### 📊 Relatórios de Performance
- Relatórios mensais com dados de **tráfego pago, Instagram e metas**
- Campos disponíveis: investimento, faturamento, CPC, conversas, alcance, publicações, novos seguidores, views, visitas ao perfil
- Cálculo automático de **ROI** (coluna gerada no banco)
- Controle de versões do relatório com histórico de alterações
- Fluxo de publicação em etapas: `rascunho → publicado → arquivado`
- Exportação em **PDF via DomPDF**
- Navegação por mês de referência diretamente no portal

### 💰 Gestão Financeira
- Cadastro de notas fiscais (`Invoice`) e cobranças individuais (`Payment`)
- Pagamentos com suporte a **QR Code PIX** gerado dinamicamente
- Status de pagamento: `pending`, `under_review`, `paid`, `overdue`
- Botão **"Já paguei"** no portal do cliente para notificar a agência sem precisar contato
- Marcação automática de pagamentos vencidos diariamente (via Scheduler)
- Painel administrativo com visão consolidada de inadimplência

### 📁 Biblioteca de Mídia (Google Drive)
- Links de entregáveis organizados por cliente, mês e ano
- Suporte a tipos de conteúdo: `video`, `image`, `document`, `link`
- Controle de visibilidade pública/privada por item
- Integrado ao portal do cliente para visualização de entregas

### ⚙️ Painel Administrativo
- CRUD completo de clientes com dados: razão social, CNPJ, e-mail, telefone, endereço, vigência de contrato e mensalidade
- Gerenciamento de usuários e permissões por cliente
- Dashboard com visão geral de clientes ativos, pagamentos e relatórios
- Configurações globais do sistema (aparência, textos, comportamentos)
- Logs de auditoria de ações críticas

---

## 🏗️ Arquitetura

O projeto segue uma arquitetura organizada em **domains**, separando as responsabilidades por contexto de negócio:

```
app/
├── Console/
│   └── Commands/
│       └── SyncCalendarCommand.php   # Comando artisan: calendar:sync
├── Domain/
│   ├── Calendar/     # Integração com Google Agenda
│   ├── Client/       # Gestão de clientes
│   ├── Financial/    # Pagamentos e faturas
│   ├── Media/        # Links de entregáveis
│   ├── Report/       # Relatórios de performance
│   └── Setting/      # Configurações do sistema
├── Http/
│   ├── Controllers/
│   │   ├── Admin/    # Controllers do painel da agência
│   │   └── Client/   # Controllers do portal do cliente
│   └── Middleware/
└── Models/           # User, GoogleOAuthToken, AuditLog
```

---

## 🛠️ Stack Tecnológica

| Camada | Tecnologia |
|---|---|
| Backend | Laravel 11, PHP 8.2 |
| Banco de dados | PostgreSQL |
| Frontend | Blade + Tailwind CSS |
| Permissões | Spatie Laravel Permission |
| Google APIs | google/apiclient (Calendar + Drive) |
| PDF | barryvdh/laravel-dompdf |
| QR Code PIX | chillerlan/php-qrcode |
| Ícones (UI) | Heroicons, Solar Icons, Remix Icons |
| Logs de atividade | Spatie Laravel Activitylog |
| Deploy | Hostinger (shared hosting) |

---

## 🚀 Instalação local

### Pré-requisitos
- PHP 8.2+
- Composer
- PostgreSQL
- Node.js + NPM

### Passos

```bash
# Clone o repositório
git clone https://github.com/enzoterra/portal-agencia.git
cd portal-agencia

# Instale as dependências PHP
composer install

# Instale as dependências JS
npm install

# Configure o ambiente
cp .env.example .env
php artisan key:generate

# Rode as migrations
php artisan migrate --seed

# Inicie o servidor de desenvolvimento
composer run dev
```

### Variáveis de ambiente necessárias

Configure as seguintes entradas no `.env`:

```env
# Banco de dados
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=portal_agencia
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

# Google OAuth
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=
GOOGLE_CALENDAR_ID=
```

---

## ⏰ Tarefas Agendadas

O sistema depende de um Cron Job configurado no servidor para funcionar corretamente. Adicione a seguinte linha ao cron do servidor:

```
* * * * * /usr/bin/php /caminho/para/artisan schedule:run >> /dev/null 2>&1
```

O Laravel gerencia internamente os intervalos de cada tarefa:

| Tarefa | Frequência | Descrição |
|---|---|---|
| `calendar:sync` | A cada 5 minutos | Sincroniza eventos do Google Agenda |
| Marcar vencidos | Diário às 00:05 | Atualiza status de pagamentos vencidos |

---

## 📄 Licença

Este projeto foi desenvolvido para uso interno e fins de portfólio.
