-- ============================================================
-- USERS (autenticação e controle de acesso)
-- ============================================================
CREATE TABLE users (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id       BIGINT UNSIGNED NULL,           -- NULL = admin/staff
    name            VARCHAR(255) NOT NULL,
    email           VARCHAR(255) NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL,
    role            ENUM('super_admin','admin','client') NOT NULL DEFAULT 'client',
    avatar          VARCHAR(255) NULL,
    is_active       BOOLEAN NOT NULL DEFAULT TRUE,
    last_login_at   TIMESTAMP NULL,
    email_verified_at TIMESTAMP NULL,
    remember_token  VARCHAR(100) NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      TIMESTAMP NULL,                -- Soft delete

    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
    INDEX idx_users_client_id (client_id),
    INDEX idx_users_role (role),
    INDEX idx_users_email (email),
    INDEX idx_users_deleted_at (deleted_at)
);

-- ============================================================
-- CLIENTS (empresas/clientes da agência)
-- ============================================================
CREATE TABLE clients (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid            CHAR(36) NOT NULL UNIQUE,       -- UUID para URLs públicas (anti-IDOR)
    company_name    VARCHAR(255) NOT NULL,
    trade_name      VARCHAR(255) NULL,
    cnpj            VARCHAR(18) NULL UNIQUE,
    email           VARCHAR(255) NOT NULL,
    phone           VARCHAR(20) NULL,
    address         JSON NULL,                      -- Endereço completo como JSON
    contract_start  DATE NULL,
    contract_end    DATE NULL,
    monthly_fee     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status          ENUM('active','inactive','suspended','trial') NOT NULL DEFAULT 'active',
    notes           TEXT NULL,
    settings        JSON NULL,                      -- Configurações por cliente
    show_roi        BOOLEAN NOT NULL DEFAULT TRUE,  -- Mostrar ROI no dashboard
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      TIMESTAMP NULL,

    INDEX idx_clients_uuid (uuid),
    INDEX idx_clients_status (status),
    INDEX idx_clients_cnpj (cnpj),
    INDEX idx_clients_deleted_at (deleted_at)
);

-- ============================================================
-- PAYMENTS (pagamentos mensais)
-- ============================================================
CREATE TABLE payments (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid            CHAR(36) NOT NULL UNIQUE,
    client_id       BIGINT UNSIGNED NOT NULL,
    invoice_id      BIGINT UNSIGNED NULL,
    amount          DECIMAL(10,2) NOT NULL,
    due_date        DATE NOT NULL,
    paid_at         TIMESTAMP NULL,
    payment_method  ENUM('pix','bank_transfer','credit_card','other') NULL,
    status          ENUM('pending','paid','overdue','cancelled') NOT NULL DEFAULT 'pending',
    pix_qr_code     TEXT NULL,                      -- QR Code base64 ou URL
    pix_key         VARCHAR(255) NULL,
    reference       VARCHAR(255) NULL,              -- Referência interna
    notes           TEXT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      TIMESTAMP NULL,

    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL,
    INDEX idx_payments_client_id (client_id),
    INDEX idx_payments_status (status),
    INDEX idx_payments_due_date (due_date),
    INDEX idx_payments_uuid (uuid),
    INDEX idx_payments_client_status (client_id, status),   -- Índice composto para queries de dashboard
    INDEX idx_payments_deleted_at (deleted_at)
);

-- ============================================================
-- INVOICES (notas fiscais)
-- ============================================================
CREATE TABLE invoices (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid            CHAR(36) NOT NULL UNIQUE,
    client_id       BIGINT UNSIGNED NOT NULL,
    invoice_number  VARCHAR(50) NOT NULL,
    amount          DECIMAL(10,2) NOT NULL,
    issue_date      DATE NOT NULL,
    due_date        DATE NULL,
    file_path       VARCHAR(500) NULL,              -- Caminho no storage (fora de public/)
    file_disk       VARCHAR(50) NOT NULL DEFAULT 'local', -- local, s3, etc.
    description     TEXT NULL,
    reference_month DATE NULL,                      -- Mês de referência (primeiro dia do mês)
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      TIMESTAMP NULL,

    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    UNIQUE INDEX idx_invoices_number_client (invoice_number, client_id),
    INDEX idx_invoices_client_id (client_id),
    INDEX idx_invoices_uuid (uuid),
    INDEX idx_invoices_reference_month (reference_month),
    INDEX idx_invoices_deleted_at (deleted_at)
);

-- ============================================================
-- REPORTS (relatórios mensais de marketing)
-- ============================================================
CREATE TABLE reports (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid            CHAR(36) NOT NULL UNIQUE,
    client_id       BIGINT UNSIGNED NOT NULL,
    title           VARCHAR(255) NOT NULL,
    reference_month DATE NOT NULL,                  -- Primeiro dia do mês de referência
    status          ENUM('draft','review','published','archived') NOT NULL DEFAULT 'draft',
    summary         TEXT NULL,
    investment      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    revenue         DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    roi             DECIMAL(8,2) GENERATED ALWAYS AS               -- ROI calculado automaticamente
                    (((revenue - investment) / NULLIF(investment,0)) * 100) STORED,
    metrics         JSON NULL,                      -- KPIs variáveis por cliente
    published_at    TIMESTAMP NULL,
    published_by    BIGINT UNSIGNED NULL,
    current_version INT NOT NULL DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      TIMESTAMP NULL,

    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (published_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE INDEX idx_reports_client_month (client_id, reference_month), -- Um relatório por mês por cliente
    INDEX idx_reports_uuid (uuid),
    INDEX idx_reports_status (status),
    INDEX idx_reports_client_id (client_id),
    INDEX idx_reports_deleted_at (deleted_at)
);

-- ============================================================
-- REPORT_VERSIONS (versionamento de relatórios)
-- ============================================================
CREATE TABLE report_versions (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    report_id       BIGINT UNSIGNED NOT NULL,
    version         INT NOT NULL,
    data_snapshot   JSON NOT NULL,                  -- Snapshot completo dos dados naquela versão
    changed_by      BIGINT UNSIGNED NOT NULL,
    change_reason   VARCHAR(255) NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE RESTRICT,
    UNIQUE INDEX idx_report_versions_unique (report_id, version),
    INDEX idx_report_versions_report_id (report_id)
);

-- ============================================================
-- MEDIA_LINKS (links do Google Drive organizados)
-- ============================================================
CREATE TABLE media_links (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid            CHAR(36) NOT NULL UNIQUE,
    client_id       BIGINT UNSIGNED NOT NULL,
    title           VARCHAR(255) NOT NULL,
    description     TEXT NULL,
    url             VARCHAR(2048) NOT NULL,          -- URL do Google Drive
    type            ENUM('folder','file','video','image','document','other') NOT NULL DEFAULT 'other',
    reference_month DATE NOT NULL,
    thumbnail_url   VARCHAR(2048) NULL,
    is_public       BOOLEAN NOT NULL DEFAULT FALSE,
    sort_order      INT NOT NULL DEFAULT 0,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      TIMESTAMP NULL,

    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    INDEX idx_media_links_client_month (client_id, reference_month),
    INDEX idx_media_links_uuid (uuid),
    INDEX idx_media_links_deleted_at (deleted_at)
);

-- ============================================================
-- CALENDAR_EVENTS (cache local dos eventos do Google Calendar)
-- ============================================================
CREATE TABLE calendar_events (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid                CHAR(36) NOT NULL UNIQUE,
    client_id           BIGINT UNSIGNED NOT NULL,
    google_event_id     VARCHAR(255) NOT NULL,      -- ID do evento no Google Calendar
    google_calendar_id  VARCHAR(255) NOT NULL,
    title               VARCHAR(500) NOT NULL,
    description         TEXT NULL,
    location            VARCHAR(500) NULL,
    starts_at           DATETIME NOT NULL,
    ends_at             DATETIME NOT NULL,
    all_day             BOOLEAN NOT NULL DEFAULT FALSE,
    status              ENUM('confirmed','tentative','cancelled') NOT NULL DEFAULT 'confirmed',
    color               VARCHAR(7) NULL,            -- Hex color
    synced_at           TIMESTAMP NOT NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    UNIQUE INDEX idx_calendar_events_google (client_id, google_event_id),
    INDEX idx_calendar_events_client_date (client_id, starts_at),
    INDEX idx_calendar_events_uuid (uuid)
);

-- ============================================================
-- GOOGLE_OAUTH_TOKENS (tokens criptografados)
-- ============================================================
CREATE TABLE google_oauth_tokens (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id       BIGINT UNSIGNED NOT NULL UNIQUE,
    access_token    TEXT NOT NULL,                  -- Criptografado com AES-256
    refresh_token   TEXT NULL,                      -- Criptografado com AES-256
    token_type      VARCHAR(50) NULL,
    expires_at      TIMESTAMP NULL,
    scopes          JSON NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    INDEX idx_google_tokens_expires (expires_at)
);

-- ============================================================
-- AUDIT_LOGS (trilha de auditoria)
-- ============================================================
CREATE TABLE audit_logs (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NULL,
    client_id       BIGINT UNSIGNED NULL,           -- Contexto do cliente afetado
    action          VARCHAR(100) NOT NULL,          -- create, update, delete, login, etc.
    auditable_type  VARCHAR(255) NULL,              -- Modelo afetado
    auditable_id    BIGINT UNSIGNED NULL,
    old_values      JSON NULL,
    new_values      JSON NULL,
    ip_address      VARCHAR(45) NULL,               -- Suporta IPv6
    user_agent      TEXT NULL,
    url             VARCHAR(2048) NULL,
    tags            JSON NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_audit_user_id (user_id),
    INDEX idx_audit_client_id (client_id),
    INDEX idx_audit_action (action),
    INDEX idx_audit_auditable (auditable_type, auditable_id),
    INDEX idx_audit_created_at (created_at)
);