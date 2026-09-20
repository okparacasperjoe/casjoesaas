<?php
require_once __DIR__ . '/../app/core/Database.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();
$tenantId = 1;

echo "Seeding Performance Reviews...\n";

// 1. Create Table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS erp_performance_reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        employee_id INT NOT NULL,
        reviewer_id INT,
        review_date DATE NOT NULL,
        rating INT NOT NULL,
        comments TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES erp_employees(id) ON DELETE CASCADE
    );
");

// 2. Clear old data (optional)
// $pdo->exec("DELETE FROM erp_performance_reviews");

// 3. Seed Reviews
$stmt = $pdo->prepare("SELECT id FROM erp_employees WHERE tenant_id = ? LIMIT 2");
$stmt->execute([$tenantId]);
$employees = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (count($employees) >= 2) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO erp_performance_reviews (tenant_id, employee_id, reviewer_id, review_date, rating, comments) VALUES 
    (?, ?, 1, '2025-12-01', 5, 'Outstanding performance this quarter. Alice has shown great leadership.'),
    (?, ?, 1, '2025-11-15', 4, 'Bob has met all his sales targets and exceeded expectations in client relationship management.')
    ");
    $stmt->execute([$tenantId, $employees[0], $tenantId, $employees[1]]);
}

echo "Performance Reviews Seeded.\n";
