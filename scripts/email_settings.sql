-- Email & Social Media Settings Schema
-- Add these settings to system_settings table
INSERT IGNORE INTO system_settings (
        setting_key,
        setting_value,
        setting_group,
        description
    )
VALUES -- SMTP Settings
    (
        'smtp_enabled',
        '0',
        'smtp',
        'Enable SMTP for email sending'
    ),
    (
        'smtp_host',
        'smtp.gmail.com',
        'smtp',
        'SMTP server hostname'
    ),
    ('smtp_port', '587', 'smtp', 'SMTP server port'),
    (
        'smtp_username',
        '',
        'smtp',
        'SMTP username/email'
    ),
    ('smtp_password', '', 'smtp', 'SMTP password'),
    (
        'smtp_encryption',
        'tls',
        'smtp',
        'SMTP encryption method (tls/ssl)'
    ),
    (
        'smtp_from_address',
        'noreply@casjoe.com',
        'smtp',
        'From email address'
    ),
    ('smtp_from_name', 'Casjoe', 'smtp', 'From name'),
    -- Social Media Links
    (
        'social_facebook',
        '',
        'social',
        'Facebook page URL'
    ),
    (
        'social_twitter',
        '',
        'social',
        'Twitter/X profile URL'
    ),
    (
        'social_instagram',
        '',
        'social',
        'Instagram profile URL'
    ),
    (
        'social_linkedin',
        '',
        'social',
        'LinkedIn company page URL'
    ),
    (
        'social_youtube',
        '',
        'social',
        'YouTube channel URL'
    ),
    -- Email Branding
    (
        'email_logo_url',
        'https://app.casjoe.com/assets/casjoe_logo.png',
        'email',
        'Logo URL for email headers'
    ),
    (
        'email_brand_color',
        '#000066',
        'email',
        'Primary brand color for emails'
    ),
    (
        'email_support_email',
        'support@casjoe.com',
        'email',
        'Support email address'
    );