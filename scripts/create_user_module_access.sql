CREATE TABLE IF NOT EXISTS user_module_access (
    user_id INT NOT NULL,
    module_slug VARCHAR(255) NOT NULL,
    PRIMARY KEY (user_id, module_slug),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);