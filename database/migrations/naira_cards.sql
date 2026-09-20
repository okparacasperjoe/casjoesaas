CREATE TABLE IF NOT EXISTS cp_naira_card_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    user_id INT NOT NULL UNIQUE,
    customer_id VARCHAR(100) NOT NULL,
    firstname VARCHAR(100),
    lastname VARCHAR(100),
    email VARCHAR(255),
    phone VARCHAR(20),
    nin VARCHAR(20),
    dob DATE,
    address_line1 VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(10),
    provider VARCHAR(20) DEFAULT 'white',
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_customer (customer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cp_naira_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    user_id INT NOT NULL,
    customer_id VARCHAR(100) NOT NULL,
    card_id VARCHAR(100) DEFAULT NULL,
    card_type ENUM('virtual','physical') NOT NULL DEFAULT 'virtual',
    brand VARCHAR(20) DEFAULT 'AfriGo',
    masked_pan VARCHAR(30) DEFAULT NULL,
    expiry_month VARCHAR(5) DEFAULT NULL,
    expiry_year VARCHAR(5) DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'active',
    provider VARCHAR(20) DEFAULT 'white',
    meta_data LONGTEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_customer (customer_id),
    INDEX idx_card (card_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cp_naira_card_inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pan VARCHAR(30) NOT NULL,
    brand VARCHAR(20) DEFAULT 'AfriGo',
    status ENUM('available','assigned','damaged') DEFAULT 'available',
    assigned_to_user_id INT DEFAULT NULL,
    assigned_card_id VARCHAR(100) DEFAULT NULL,
    added_by_admin_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    assigned_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_status (status),
    INDEX idx_pan (pan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
