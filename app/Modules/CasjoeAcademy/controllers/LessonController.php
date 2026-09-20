<?php

namespace App\Modules\CasjoeAcademy\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Auth;

class LessonController
{
    public function show($params)
    {
        $courseId = $params['course_id'];
        $lessonId = $params['lesson_id'];

        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        // Get Lesson
        $stmt = $db->query("SELECT * FROM academy_lessons WHERE id = ? AND course_id = ? AND tenant_id = ?", [$lessonId, $courseId, $tenantId]);
        $lesson = $stmt->fetch();

        if (!$lesson)
            die("Lesson not found");

        require __DIR__ . '/../Views/lesson.php';
    }

    public function complete()
    {
        $userId = Auth::user()['id'];
        // Logic to mark lesson complete and update academy_enrollments progress
    }
}
