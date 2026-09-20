<?php

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();

try {
    // 1. Tracks Table (The "Mini MBA" Bundle)
    $db->query("CREATE TABLE IF NOT EXISTS intro_tracks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL, 
        title VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) DEFAULT 0.00,
        thumbnail VARCHAR(255),
        is_published BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (tenant_id)
    )");

    // 2. Track Courses (Linking Courses to Tracks)
    $db->query("CREATE TABLE IF NOT EXISTS intro_track_courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        track_id INT NOT NULL,
        course_id INT NOT NULL,
        sort_order INT DEFAULT 0,
        FOREIGN KEY (track_id) REFERENCES intro_tracks(id) ON DELETE CASCADE,
        FOREIGN KEY (course_id) REFERENCES academy_courses(id) ON DELETE CASCADE,
        INDEX (track_id),
        INDEX (course_id)
    )");

    echo "Schema updated: intro_tracks and intro_track_courses tables created.\n";

} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage() . "\n";
}
