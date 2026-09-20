<?php
require 'app/Core/bootstrap.php';
$pdo = \App\Core\Database::getInstance()->getConnection();

$sql = "
CREATE TABLE IF NOT EXISTS erp_payment_integrations (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT(11) NOT NULL,
    provider VARCHAR(50) NOT NULL DEFAULT 'moniepoint',
    client_id VARCHAR(255) NULL,
    client_secret VARCHAR(255) NULL,
    terminal_serial VARCHAR(100) NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY tenant_provider (tenant_id, provider)
);
";

try {
    $pdo->exec($sql);
    echo "Table erp_payment_integrations created successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
