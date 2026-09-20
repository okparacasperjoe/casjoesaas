CREATE TABLE IF NOT EXISTS erp_sops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_tenant_sops (tenant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS erp_sop_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    sop_id INT NOT NULL,
    employee_id INT NOT NULL,
    status ENUM('assigned', 'signed') DEFAULT 'assigned',
    signed_name VARCHAR(255) NULL,
    signature LONGTEXT NULL, 
    signed_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sop_id) REFERENCES erp_sops(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES erp_employees(id) ON DELETE CASCADE,
    UNIQUE KEY unique_assignment (sop_id, employee_id),
    KEY idx_tenant_assignments (tenant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
