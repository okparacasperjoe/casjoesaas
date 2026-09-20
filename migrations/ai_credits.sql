-- ============================================================
-- CORI AI Credit System Migration
-- Compatible with MySQL 5.7+
-- Run this once on the production database
-- ============================================================

-- 1a. Add ai_tokens_limit column (ignore error if already exists)
ALTER TABLE subscriptions ADD COLUMN ai_tokens_limit INT UNSIGNED NOT NULL DEFAULT 500 COMMENT 'Monthly token allowance from plan';

-- 1b. Add ai_tokens_used column
ALTER TABLE subscriptions ADD COLUMN ai_tokens_used INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Tokens consumed this billing cycle';

-- 1c. Add ai_credits column
ALTER TABLE subscriptions ADD COLUMN ai_credits INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Extra purchased tokens (roll-over)';

-- 1d. Add ai_cycle_reset_at column
ALTER TABLE subscriptions ADD COLUMN ai_cycle_reset_at DATETIME NULL COMMENT 'When ai_tokens_used last reset';

-- 2. AI credit packs / purchase log
CREATE TABLE IF NOT EXISTS ai_credit_purchases (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id      INT UNSIGNED NOT NULL,
    user_id        INT UNSIGNED NOT NULL,
    pack_name      VARCHAR(100) NOT NULL,
    tokens_granted INT UNSIGNED NOT NULL,
    amount_paid    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    currency       VARCHAR(10)  NOT NULL DEFAULT 'NGN',
    reference      VARCHAR(100) NULL,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tenant (tenant_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. AI usage log — full audit trail of every AI call
CREATE TABLE IF NOT EXISTS ai_usage_log (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id     INT UNSIGNED NOT NULL,
    user_id       INT UNSIGNED NOT NULL DEFAULT 0,
    action        VARCHAR(100) NOT NULL COMMENT 'e.g. chat_reply, create_invoice, email_sequence',
    tokens_used   INT UNSIGNED NOT NULL DEFAULT 0,
    prompt_length INT UNSIGNED NOT NULL DEFAULT 0,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tenant_date (tenant_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. AI scheduled tasks (invoice reminders, email sequences)
CREATE TABLE IF NOT EXISTS ai_scheduled_tasks (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id     INT UNSIGNED NOT NULL,
    user_id       INT UNSIGNED NOT NULL,
    task_type     ENUM('invoice_reminder','email_sequence','custom') NOT NULL,
    task_label    VARCHAR(255) NOT NULL,
    payload       JSON         NOT NULL COMMENT 'Stores invoice ID, recipient, email content, etc.',
    interval_type ENUM('once','daily','weekly','every_x_days') NOT NULL DEFAULT 'once',
    interval_days TINYINT UNSIGNED NOT NULL DEFAULT 0,
    next_run_at   DATETIME NOT NULL,
    last_run_at   DATETIME NULL,
    status        ENUM('pending','active','paused','completed','failed') NOT NULL DEFAULT 'pending',
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tenant_status (tenant_id, status),
    INDEX idx_next_run (next_run_at, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Set default monthly token limits per existing subscriptions
-- (Simple UPDATE — no WHERE on the new column to avoid MySQL 5.7 issues)
UPDATE subscriptions SET ai_tokens_limit = 5000 WHERE status = 'active';
UPDATE subscriptions SET ai_tokens_limit = 500  WHERE status = 'trial';
