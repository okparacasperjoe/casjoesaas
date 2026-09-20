-- Add payment tracking table
CREATE TABLE IF NOT EXISTS funnel_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    funnel_id INT NOT NULL,
    session_id INT,
    contact_id INT,
    product_id INT,
    amount DECIMAL(15, 2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'NGN',
    description VARCHAR(255),
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    transaction_ref VARCHAR(100),
    failure_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    INDEX idx_funnel (funnel_id),
    INDEX idx_session (session_id),
    INDEX idx_status (status)
);
-- Add payment_completed flag to funnel_sessions
ALTER TABLE funnel_sessions
ADD COLUMN IF NOT EXISTS payment_completed TINYINT(1) DEFAULT 0;