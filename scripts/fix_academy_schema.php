<?php
require_once 'app/core/bootstrap.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();
$tenantId = 1;

echo "Patching Academy Schema...\n";

try {
    // 1. Ensure Tables Exist (Run Install SQL Logic if missing)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `academy_courses` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `tenant_id` int(11) NOT NULL,
      `title` varchar(255) NOT NULL,
      `description` text,
      `thumbnail` varchar(255) DEFAULT '/assets/course_placeholder.jpg',
      `price` decimal(10, 2) DEFAULT 0.00,
      `status` enum('draft', 'published') DEFAULT 'draft',
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4");
    
    // 2. Add Missing Columns
    $alters = [
        "ALTER TABLE academy_courses ADD COLUMN category VARCHAR(100) DEFAULT 'General'",
        "ALTER TABLE academy_courses ADD COLUMN price_per_seat DECIMAL(10,2) DEFAULT 0.00",
        "ALTER TABLE academy_courses ADD COLUMN is_system_course TINYINT(1) DEFAULT 0",
        "ALTER TABLE academy_courses ADD COLUMN instructor_id INT(11) DEFAULT NULL" // Safety for future
    ];

    foreach ($alters as $sql) {
        try {
            $pdo->exec($sql);
            echo "[FIXED] Executed: $sql\n";
        } catch (PDOException $e) {
            // echo "[NOTE] " . $e->getMessage() . "\n"; // Ignore "Duplicate column"
        }
    }

    // 3. Ensure other tables exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS `academy_sections` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `course_id` int(11) NOT NULL,
      `title` varchar(255) NOT NULL,
      `sort_order` int(11) DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `academy_lessons` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `section_id` int(11) NOT NULL,
      `title` varchar(255) NOT NULL,
      `content` text,
      `video_url` varchar(255),
      `duration_minutes` int(11) DEFAULT 0,
      `sort_order` int(11) DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4");
    
    echo "[OK] Schema Checked.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
