CREATE TABLE IF NOT EXISTS erp_projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    client_id INT,
    -- This actually refers to erp_crm_customers
    start_date DATE,
    end_date DATE,
    status ENUM(
        'not_started',
        'in_progress',
        'on_hold',
        'completed',
        'cancelled'
    ) DEFAULT 'not_started',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES erp_crm_customers(id) ON DELETE
    SET NULL
);
CREATE TABLE IF NOT EXISTS erp_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    project_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    assigned_to INT,
    due_date DATE,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('todo', 'in_progress', 'review', 'done') DEFAULT 'todo',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES erp_projects(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES erp_employees(id) ON DELETE
    SET NULL
);