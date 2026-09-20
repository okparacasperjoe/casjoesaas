-- Forms Table
CREATE TABLE IF NOT EXISTS smart_forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    user_id INT NOT NULL,
    -- Creator
    title VARCHAR(255) NOT NULL,
    description TEXT,
    logo VARCHAR(255),
    structure JSON NOT NULL,
    -- The form fields definition
    settings JSON,
    -- Payment settings, notifications, etc.
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_tenant (tenant_id)
);
-- Submissions Table
CREATE TABLE IF NOT EXISTS smart_form_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    form_id INT NOT NULL,
    data JSON NOT NULL,
    -- The user submitted data
    payment_status ENUM('pending', 'paid', 'failed', 'none') DEFAULT 'none',
    payment_reference VARCHAR(255),
    amount DECIMAL(10, 2),
    currency VARCHAR(3),
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_form (form_id),
    KEY idx_tenant (tenant_id)
);
-- Analytics Table
CREATE TABLE IF NOT EXISTS smart_form_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    form_id INT NOT NULL,
    event_type ENUM('view', 'click', 'drop_off', 'completion') NOT NULL,
    metadata JSON,
    -- Which field caused drop off, etc.
    ip_address VARCHAR(45),
    device_type VARCHAR(50),
    country VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_form_event (form_id, event_type)
);