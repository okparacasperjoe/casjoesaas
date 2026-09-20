<?php

namespace App\Modules\CasjoeAcademy\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class CourseController
{
    public function index()
    {
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        $stmt = $db->query("SELECT * FROM academy_courses WHERE tenant_id = ?", [$tenantId]);
        $courses = $stmt->fetchAll();

        require __DIR__ . '/../Views/index.php';
    }

    public function create()
    {
        require __DIR__ . '/../Views/create.php';
    }

    public function store()
    {
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['title'])));

        $db->query(
            "INSERT INTO academy_courses (tenant_id, title, slug, description, price) VALUES (?, ?, ?, ?, ?)",
            [$tenantId, $_POST['title'], $slug, $_POST['description'], $_POST['price']]
        );

        header('Location: /academy');
    }

    public function show($id)
    {
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        $stmt = $db->query("SELECT * FROM academy_courses WHERE id = ? AND tenant_id = ?", [$id, $tenantId]);
        $course = $stmt->fetch();

        if (!$course)
            die("Course not found");

        // Get Lessons
        $stmt = $db->query("SELECT * FROM academy_lessons WHERE course_id = ? ORDER BY sort_order ASC", [$course['id']]);
        $lessons = $stmt->fetchAll();

        require __DIR__ . '/../Views/course.php';
    }
}
