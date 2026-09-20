CREATE TABLE IF NOT EXISTS lead_score_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    lead_id INT NOT NULL,
    reason VARCHAR(255) NOT NULL,
    points INT NOT NULL,
    source_module VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_lead_score_tenant (tenant_id, lead_id),
    INDEX idx_lead_score_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add updated_at and score columns to track lead scores and decay calculation
ALTER TABLE erp_crm_leads ADD COLUMN IF NOT EXISTS ai_score INT DEFAULT 0;
ALTER TABLE erp_crm_leads ADD COLUMN IF NOT EXISTS lead_score INT DEFAULT 0;
ALTER TABLE erp_crm_leads ADD COLUMN IF NOT EXISTS lead_score_updated_at DATETIME NULL;
