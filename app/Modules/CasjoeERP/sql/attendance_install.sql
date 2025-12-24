CREATE TABLE IF NOT EXISTS erp_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    employee_id INT NOT NULL,
    check_in DATETIME NOT NULL,
    check_out DATETIME NULL,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES erp_employees(id) ON DELETE CASCADE
);