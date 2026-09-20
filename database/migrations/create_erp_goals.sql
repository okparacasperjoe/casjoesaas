CREATE TABLE IF NOT EXISTS erp_goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    target_value DECIMAL(15, 2) DEFAULT 0.00,
    current_value DECIMAL(15, 2) DEFAULT 0.00,
    unit VARCHAR(10) DEFAULT '',
    deadline DATE NULL,
    status ENUM('active', 'completed', 'archived') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);