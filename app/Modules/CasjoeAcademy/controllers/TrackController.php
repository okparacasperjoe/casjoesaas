<?php

namespace App\Modules\CasjoeAcademy\Controllers;

use App\Core\Auth;
use App\Core\Database;

class TrackController
{
    // List all tracks (Admin View)
    public function index()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        if ($user['role'] !== 'admin') {
            die('Unauthorized');
        }

        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM intro_tracks WHERE tenant_id = ? ORDER BY created_at DESC", [$user['tenant_id']]);
        $tracks = $stmt->fetchAll();

        require __DIR__ . '/../Views/admin/tracks/index.php';
    }

    // Show create form
    public function create()
    {
        if (!Auth::check() || Auth::user()['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
        
        // Fetch all courses to populate selection list
        $db = Database::getInstance();
        $stmt = $db->query("SELECT id, title FROM intro_courses WHERE tenant_id = ? ORDER BY title ASC", [Auth::user()['tenant_id']]);
        $courses = $stmt->fetchAll();

        require __DIR__ . '/../Views/admin/tracks/create.php';
    }

    // Store new track
    public function store()
    {
        if (!Auth::check() || Auth::user()['role'] !== 'admin') {
            die('Unauthorized');
        }

        $title = $_POST['title'];
        $description = $_POST['description'];
        $price = $_POST['price'] ?? 0;
        $courseIds = $_POST['courses'] ?? []; // Array of course IDs

        if (empty($title)) {
            header('Location: /academy/tracks/create?error=missing_title');
            exit;
        }

        $db = Database::getInstance();
        
        // 1. Create Track
        $db->query("INSERT INTO intro_tracks (tenant_id, title, description, price, is_published) VALUES (?, ?, ?, ?, 1)", 
            [Auth::user()['tenant_id'], $title, $description, $price]
        );
        $trackId = $db->lastInsertId();

        // 2. Attach Courses
        foreach ($courseIds as $index => $courseId) {
            $db->query("INSERT INTO intro_track_courses (track_id, course_id, sort_order) VALUES (?, ?, ?)", 
                [$trackId, $courseId, $index]
            );
        }

        header('Location: /academy/tracks');
    }
}

