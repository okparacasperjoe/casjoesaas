CREATE TABLE IF NOT EXISTS erp_crm_stages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    color VARCHAR(20) DEFAULT '#cccccc',
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS erp_crm_leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    contact_name VARCHAR(100) NOT NULL,
    company_name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(50),
    value DECIMAL(15, 2) DEFAULT 0.00,
    stage_id INT,
    status ENUM('open', 'won', 'lost') DEFAULT 'open',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (stage_id) REFERENCES erp_crm_stages(id) ON DELETE
    SET NULL
);
-- Seed Default Stages if not exists
INSERT INTO erp_crm_stages (tenant_id, name, color, sort_order)
SELECT 1,
    'New Lead',
    '#3498db',
    1
WHERE NOT EXISTS (
        SELECT 1
        FROM erp_crm_stages
        WHERE name = 'New Lead'
    );
INSERT INTO erp_crm_stages (tenant_id, name, color, sort_order)
SELECT 1,
    'Contacted',
    '#f1c40f',
    2
WHERE NOT EXISTS (
        SELECT 1
        FROM erp_crm_stages
        WHERE name = 'Contacted'
    );
INSERT INTO erp_crm_stages (tenant_id, name, color, sort_order)
SELECT 1,
    'Proposal Sent',
    '#e67e22',
    3
WHERE NOT EXISTS (
        SELECT 1
        FROM erp_crm_stages
        WHERE name = 'Proposal Sent'
    );
INSERT INTO erp_crm_stages (tenant_id, name, color, sort_order)
SELECT 1,
    'Negotiation',
    '#9b59b6',
    4
WHERE NOT EXISTS (
        SELECT 1
        FROM erp_crm_stages
        WHERE name = 'Negotiation'
    );
INSERT INTO erp_crm_stages (tenant_id, name, color, sort_order)
SELECT 1,
    'Won',
    '#27ae60',
    5
WHERE NOT EXISTS (
        SELECT 1
        FROM erp_crm_stages
        WHERE name = 'Won'
    );
INSERT INTO erp_crm_stages (tenant_id, name, color, sort_order)
SELECT 1,
    'Lost',
    '#c0392b',
    6
WHERE NOT EXISTS (
        SELECT 1
        FROM erp_crm_stages
        WHERE name = 'Lost'
    );