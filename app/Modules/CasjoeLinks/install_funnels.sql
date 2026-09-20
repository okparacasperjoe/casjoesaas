-- Sales Funnels Database Schema
-- Phase 1A: Foundation Tables
-- Main funnels table
CREATE TABLE IF NOT EXISTS sales_funnels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT NOT NULL,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('lead', 'service', 'product', 'event') NOT NULL,
    status ENUM('draft', 'active', 'paused', 'archived') DEFAULT 'draft',
    -- ERP Binding (mandatory)
    default_stage_id INT,
    -- Changed from NOT NULL
    lead_source VARCHAR(100),
    -- Changed from VARCHAR(255) NOT NULL
    owner_id INT NOT NULL,
    -- Links Integration
    casjoe_link_id INT,
    link_attachment_type ENUM('page', 'button'),
    -- Product/Payment (optional)
    product_id INT,
    amount DECIMAL(10, 2),
    currency VARCHAR(3) DEFAULT 'NGN',
    is_free BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tenant (tenant_id),
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_type (type)
);
-- Funnel steps
CREATE TABLE IF NOT EXISTS funnel_steps (
    id INT PRIMARY KEY AUTO_INCREMENT,
    funnel_id INT NOT NULL,
    step_type ENUM('landing', 'form', 'payment', 'thankyou') NOT NULL,
    step_order INT NOT NULL,
    config JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (funnel_id) REFERENCES sales_funnels(id) ON DELETE CASCADE,
    INDEX idx_funnel (funnel_id),
    INDEX idx_order (step_order)
);
-- Funnel sessions (visitor tracking)
CREATE TABLE IF NOT EXISTS funnel_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    funnel_id INT NOT NULL,
    session_token VARCHAR(255) UNIQUE,
    contact_id INT,
    deal_id INT,
    current_step_id INT,
    entry_url VARCHAR(500),
    ip_address VARCHAR(45),
    user_agent TEXT,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (funnel_id) REFERENCES sales_funnels(id),
    INDEX idx_funnel (funnel_id),
    INDEX idx_token (session_token),
    INDEX idx_contact (contact_id)
);
-- Analytics events
CREATE TABLE IF NOT EXISTS funnel_analytics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    funnel_id INT NOT NULL,
    session_id INT,
    event_type VARCHAR(50),
    step_id INT,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (funnel_id) REFERENCES sales_funnels(id),
    FOREIGN KEY (session_id) REFERENCES funnel_sessions(id),
    INDEX idx_funnel (funnel_id),
    INDEX idx_event (event_type),
    INDEX idx_created (created_at)
);
-- Mail automation triggers
CREATE TABLE IF NOT EXISTS funnel_mail_triggers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    funnel_id INT NOT NULL,
    erp_event VARCHAR(100),
    mail_automation_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (funnel_id) REFERENCES sales_funnels(id),
    INDEX idx_funnel (funnel_id),
    INDEX idx_event (erp_event)
);