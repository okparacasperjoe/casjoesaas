-- AI Manager Tables
-- erp_ai_insights: Stores AI-generated business insights and alerts
-- erp_performance_scores: Stores calculated performance scores over time
CREATE TABLE IF NOT EXISTS erp_ai_insights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    category VARCHAR(50) NOT NULL DEFAULT 'general',
    severity ENUM('info', 'warning', 'critical') NOT NULL DEFAULT 'info',
    title VARCHAR(255) NOT NULL,
    message TEXT,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tenant_unread (tenant_id, is_read),
    INDEX idx_tenant_category (tenant_id, category)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
CREATE TABLE IF NOT EXISTS erp_performance_scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    target_type VARCHAR(50) NOT NULL DEFAULT 'business',
    target_id INT NULL,
    score_type VARCHAR(100) NOT NULL DEFAULT 'overall_health',
    score DECIMAL(5, 1) NOT NULL DEFAULT 0.0,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    metadata JSON NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tenant_type (tenant_id, target_type),
    UNIQUE KEY unique_score (
        tenant_id,
        target_type,
        score_type,
        period_start,
        period_end
    )
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;