CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    description VARCHAR(255),
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
-- Seed Default Settings
INSERT IGNORE INTO system_settings (setting_key, setting_value, description)
VALUES (
        'payment_routing_mode',
        'global',
        'Determines if payments use global admin keys or tenant specific keys'
    ),
    (
        'flutterwave_public_key',
        '',
        'Global Flutterwave Public Key'
    ),
    (
        'flutterwave_secret_key',
        '',
        'Global Flutterwave Secret Key'
    ),
    (
        'paystack_public_key',
        '',
        'Global Paystack Public Key'
    ),
    (
        'paystack_secret_key',
        '',
        'Global Paystack Secret Key'
    ),
    ('sudo_api_key', '', 'Global Sudo Card API Key'),
    ('sudo_api_secret', '', 'Global Sudo Card Secret');