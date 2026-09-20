-- Casjoe Scheduler Tables
CREATE TABLE IF NOT EXISTS erp_scheduler_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    user_id INT NOT NULL,
    slug VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL DEFAULT '30-Minute Meeting',
    description TEXT,
    duration INT NOT NULL DEFAULT 30,
    timezone VARCHAR(100) DEFAULT 'Africa/Lagos',
    availability JSON,
    is_active TINYINT(1) DEFAULT 1,
    meeting_link VARCHAR(500) NULL,
    google_access_token TEXT,
    google_refresh_token TEXT,
    google_token_expires_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_slug (slug),
    INDEX idx_tenant_user (tenant_id, user_id)
);
CREATE TABLE IF NOT EXISTS erp_scheduler_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    profile_id INT NOT NULL,
    guest_name VARCHAR(255) NOT NULL,
    guest_email VARCHAR(255) NOT NULL,
    guest_phone VARCHAR(50),
    guest_notes TEXT,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('confirmed', 'cancelled', 'completed') DEFAULT 'confirmed',
    google_event_id VARCHAR(255),
    meet_link VARCHAR(500),
    cancel_token VARCHAR(64),
    lead_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_profile_date (profile_id, booking_date),
    INDEX idx_cancel_token (cancel_token),
    FOREIGN KEY (profile_id) REFERENCES erp_scheduler_profiles(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS erp_scheduler_blocked_dates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    blocked_date DATE NOT NULL,
    reason VARCHAR(255),
    FOREIGN KEY (profile_id) REFERENCES erp_scheduler_profiles(id) ON DELETE CASCADE
);