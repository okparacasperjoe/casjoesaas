<?php

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();

try {
    // Certificates Table
    $db->query("CREATE TABLE IF NOT EXISTS intro_certificates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        user_id INT NOT NULL,
        course_id INT NULL, -- Can be null if cert is for a track
        track_id INT NULL, -- Can be null if cert is for a single course
        certificate_code VARCHAR(50) NOT NULL UNIQUE, -- For public verification
        issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (tenant_id),
        INDEX (user_id),
        INDEX (course_id),
        INDEX (track_id)
    )");

    echo "Schema updated: intro_certificates table created.\n";

} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage() . "\n";
}
