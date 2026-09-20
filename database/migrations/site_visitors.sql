CREATE TABLE IF NOT EXISTS site_visitors (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    url VARCHAR(500) NOT NULL,
    user_agent VARCHAR(500) DEFAULT NULL,
    referrer VARCHAR(500) DEFAULT NULL,
    user_id INT DEFAULT NULL,
    is_bot TINYINT(1) DEFAULT 0,
    country VARCHAR(100) DEFAULT NULL,
    visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_visited_at (visited_at),
    INDEX idx_ip (ip_address),
    INDEX idx_url (url(191)),
    INDEX idx_is_bot (is_bot)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
