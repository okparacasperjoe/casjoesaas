CREATE TABLE IF NOT EXISTS erp_crm_integrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    provider ENUM('facebook', 'whatsapp', 'generic') DEFAULT 'generic',
    webhook_secret VARCHAR(255) NOT NULL UNIQUE,
    config JSON DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS erp_crm_webhook_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    integration_id INT NULL,
    payload TEXT,
    status ENUM('processed', 'failed', 'ignored') DEFAULT 'processed',
    error_message TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (integration_id) REFERENCES erp_crm_integrations(id) ON DELETE
    SET NULL
);