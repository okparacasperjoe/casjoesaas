CREATE TABLE IF NOT EXISTS crm_workflows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    trigger_type ENUM('lead_created', 'score_threshold', 'form_submitted', 'stage_changed', 'inbox_message_received', 'booking_scheduled') NOT NULL,
    trigger_config JSON NULL,
    is_active TINYINT(1) DEFAULT 1,
    runs_count INT DEFAULT 0,
    last_run_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_wf_tenant (tenant_id),
    INDEX idx_wf_trigger (tenant_id, trigger_type, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS crm_workflow_steps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    workflow_id INT NOT NULL,
    tenant_id INT NOT NULL,
    step_order INT NOT NULL DEFAULT 1,
    action_type ENUM('send_email', 'send_whatsapp', 'change_stage', 'add_score', 'notify_user') NOT NULL,
    action_config JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_wfs_workflow (workflow_id, step_order),
    FOREIGN KEY (workflow_id) REFERENCES crm_workflows(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS crm_workflow_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    workflow_id INT NOT NULL,
    tenant_id INT NOT NULL,
    lead_id INT NULL,
    trigger_event VARCHAR(100) NOT NULL,
    step_id INT NULL,
    action_taken VARCHAR(100) NOT NULL,
    status ENUM('success', 'failed', 'skipped') DEFAULT 'success',
    output_message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_wfl_tenant (tenant_id),
    INDEX idx_wfl_workflow (workflow_id),
    FOREIGN KEY (workflow_id) REFERENCES crm_workflows(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
