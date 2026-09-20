-- Create funnel_mail_triggers table
CREATE TABLE IF NOT EXISTS funnel_mail_triggers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    funnel_id INT NOT NULL,
    event_type VARCHAR(50) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    enabled TINYINT(1) DEFAULT 1,
    delay_minutes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_funnel (funnel_id),
    INDEX idx_event (event_type),
    UNIQUE KEY unique_funnel_event (funnel_id, event_type)
);
-- Create email logging table
CREATE TABLE IF NOT EXISTS funnel_email_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    funnel_id INT NOT NULL,
    recipient VARCHAR(255) NOT NULL,
    event_type VARCHAR(50),
    subject VARCHAR(255),
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_funnel (funnel_id),
    INDEX idx_recipient (recipient),
    INDEX idx_event (event_type)
);