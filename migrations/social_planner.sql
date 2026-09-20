CREATE TABLE IF NOT EXISTS social_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    platform ENUM('facebook', 'instagram', 'twitter', 'linkedin', 'tiktok') NOT NULL,
    account_name VARCHAR(255) NOT NULL,
    account_handle VARCHAR(255) NULL,
    avatar_url VARCHAR(500) NULL,
    access_token TEXT NULL,
    status ENUM('active', 'disconnected') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_soc_tenant (tenant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS social_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    media_url VARCHAR(500) NULL,
    platforms JSON NOT NULL,
    link_type ENUM('none', 'funnel', 'bio', 'custom') DEFAULT 'none',
    link_url VARCHAR(500) NULL,
    status ENUM('draft', 'scheduled', 'published', 'failed') DEFAULT 'draft',
    scheduled_at DATETIME NULL,
    published_at DATETIME NULL,
    ai_generated TINYINT(1) DEFAULT 0,
    metrics JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sp_tenant (tenant_id, status),
    INDEX idx_sp_scheduled (status, scheduled_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
