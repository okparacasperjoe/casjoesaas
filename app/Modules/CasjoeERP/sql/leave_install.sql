CREATE TABLE IF NOT EXISTS erp_leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    employee_id INT NOT NULL,
    leave_type ENUM(
        'annual',
        'sick',
        'unpaid',
        'maternity',
        'paternity',
        'other'
    ) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    reason TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES erp_employees(id) ON DELETE CASCADE
);