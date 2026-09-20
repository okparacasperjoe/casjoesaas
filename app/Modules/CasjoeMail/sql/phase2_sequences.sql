-- Phase 2: Smart Follow-Up Sequences
-- Migration script to create sequence tables
-- 1. Sequences Table
CREATE TABLE IF NOT EXISTS cm_sequences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    trigger_event VARCHAR(100) DEFAULT 'manual' COMMENT 'manual, form_submit, list_join',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tenant (tenant_id),
    INDEX idx_active (is_active)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- 2. Sequence Steps Table
CREATE TABLE IF NOT EXISTS cm_sequence_steps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sequence_id INT NOT NULL,
    step_order INT NOT NULL,
    delay_days INT NOT NULL DEFAULT 0,
    delay_hours INT NOT NULL DEFAULT 0,
    subject VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    stop_on_reply BOOLEAN DEFAULT TRUE,
    stop_on_click BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sequence_id) REFERENCES cm_sequences(id) ON DELETE CASCADE,
    INDEX idx_sequence (sequence_id),
    INDEX idx_order (step_order)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- 3. Sequence Enrollments Table
CREATE TABLE IF NOT EXISTS cm_sequence_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subscriber_id INT NOT NULL,
    sequence_id INT NOT NULL,
    current_step INT DEFAULT 0,
    status ENUM('active', 'completed', 'paused', 'stopped') DEFAULT 'active',
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_sent_at TIMESTAMP NULL,
    next_send_at TIMESTAMP NULL,
    stop_reason VARCHAR(100) NULL COMMENT 'replied, clicked, manual, unsubscribed',
    FOREIGN KEY (subscriber_id) REFERENCES cm_subscribers(id) ON DELETE CASCADE,
    FOREIGN KEY (sequence_id) REFERENCES cm_sequences(id) ON DELETE CASCADE,
    INDEX idx_subscriber (subscriber_id),
    INDEX idx_sequence (sequence_id),
    INDEX idx_status (status),
    INDEX idx_next_send (next_send_at),
    UNIQUE KEY unique_enrollment (subscriber_id, sequence_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;