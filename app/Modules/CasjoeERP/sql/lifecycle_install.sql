CREATE TABLE IF NOT EXISTS erp_employee_lifecycle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    employee_id INT NOT NULL,
    type ENUM('promotion', 'resignation', 'termination') NOT NULL,
    date DATE NOT NULL,
    reason TEXT,
    new_position VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES erp_employees(id) ON DELETE CASCADE
);