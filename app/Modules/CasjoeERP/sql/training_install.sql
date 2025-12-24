CREATE TABLE IF NOT EXISTS erp_training_programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    instructor VARCHAR(255),
    start_date DATE,
    end_date DATE,
    status ENUM(
        'scheduled',
        'in_progress',
        'completed',
        'cancelled'
    ) DEFAULT 'scheduled',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS erp_training_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    program_id INT NOT NULL,
    employee_id INT NOT NULL,
    status ENUM('enrolled', 'completed', 'dropped') DEFAULT 'enrolled',
    FOREIGN KEY (program_id) REFERENCES erp_training_programs(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES erp_employees(id) ON DELETE CASCADE
);