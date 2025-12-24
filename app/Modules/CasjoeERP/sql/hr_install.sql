DROP TABLE IF EXISTS erp_employees;
DROP TABLE IF EXISTS erp_departments;
CREATE TABLE erp_departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE erp_employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    user_id INT NULL,
    department_id INT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    job_title VARCHAR(100),
    hire_date DATE,
    salary DECIMAL(10, 2) DEFAULT 0.00,
    status ENUM('active', 'on_leave', 'terminated', 'resigned') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES erp_departments(id) ON DELETE
    SET NULL
);