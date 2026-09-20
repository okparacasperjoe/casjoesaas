CREATE TABLE IF NOT EXISTS cms_pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content LONGTEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS cms_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    excerpt TEXT,
    content LONGTEXT,
    image_url VARCHAR(255),
    is_published BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Seed Default Pages if not exist
INSERT IGNORE INTO cms_pages (title, slug, content)
VALUES (
        'About Us',
        'about-us',
        '<h1>About Us</h1><p>Welcome to Casjoe...</p>'
    ),
    (
        'Privacy Policy',
        'privacy-policy',
        '<h1>Privacy Policy</h1><p>Your privacy is important...</p>'
    ),
    (
        'Terms of Service',
        'terms-of-service',
        '<h1>Terms of Service</h1><p>Rules and regulations...</p>'
    );