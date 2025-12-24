CREATE TABLE IF NOT EXISTS erp_project_time_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    project_id INT,
    task_id INT,
    employee_id INT,
    description VARCHAR(255),
    start_time DATETIME,
    end_time DATETIME,
    duration_minutes INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);