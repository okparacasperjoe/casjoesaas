<?php
require_once 'app/core/bootstrap.php';
use App\Core\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    echo "Patching ERP Schema...\n";

    // 1. Employee Department
    try {
        $pdo->exec("ALTER TABLE erp_employees ADD COLUMN department VARCHAR(100) DEFAULT NULL");
        echo "[FIXED] Added 'department' to erp_employees.\n";
    } catch (PDOException $e) {
        // Likely exists or other error
        echo "[NOTE] 'department' column: " . $e->getMessage() . "\n";
    }

    // 2. Employee Position (Just in case)
    try {
        $pdo->exec("ALTER TABLE erp_employees ADD COLUMN position VARCHAR(100) DEFAULT NULL");
        echo "[FIXED] Added 'position' to erp_employees.\n";
    } catch (PDOException $e) {
        echo "[NOTE] 'position' column: " . $e->getMessage() . "\n";
    }

    // 2b. Hired Date
    try {
        $pdo->exec("ALTER TABLE erp_employees ADD COLUMN hired_date DATE DEFAULT NULL");
        echo "[FIXED] Added 'hired_date' to erp_employees.\n";
    } catch (PDOException $e) {
        echo "[NOTE] 'hired_date' column: " . $e->getMessage() . "\n";
    }

    // 3. Departments table (if missing)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `erp_departments` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `tenant_id` int(11) NOT NULL,
      `name` varchar(100) NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `tenant_id` (`tenant_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // 3b. Add Description
    try {
        $pdo->exec("ALTER TABLE erp_departments ADD COLUMN description TEXT DEFAULT NULL");
        echo "[FIXED] Added 'description' to erp_departments.\n";
    } catch (PDOException $e) {
        echo "[NOTE] 'description' column: " . $e->getMessage() . "\n";
    }
    echo "[CHECK] erp_departments table check complete.\n";

} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage();
}
