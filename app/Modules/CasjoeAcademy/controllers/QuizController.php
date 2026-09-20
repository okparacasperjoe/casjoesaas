<?php

namespace App\Modules\CasjoeAcademy\Controllers;

use App\Core\Auth;
use App\Core\Database;

class QuizController
{
    // Instructor: Show create form
    public function create($params)
    {
        $courseId = $params['courseId'];
        if (!Auth::check() || Auth::user()['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }

        $db = Database::getInstance();
        
        // Verify course ownership
        $stmt = $db->query("SELECT * FROM academy_courses WHERE id = ? AND tenant_id = ?", [$courseId, Auth::user()['tenant_id']]);
        $course = $stmt->fetch();

        if (!$course) {
            die('Course not found');
        }

        require __DIR__ . '/../Views/admin/quizzes/create.php';
    }

    // Instructor: Store quiz and questions
    public function store($params)
    {
        $courseId = $params['courseId'];
        if (!Auth::check() || Auth::user()['role'] !== 'admin') {
            die('Unauthorized');
        }

        $title = $_POST['title'];
        $passingScore = $_POST['passing_score'] ?? 70;
        $questions = $_POST['questions'] ?? []; // Array of questions

        $db = Database::getInstance();

        // 1. Create Quiz
        $db->query("INSERT INTO academy_quizzes (course_id, title, passing_score) VALUES (?, ?, ?)", 
            [$courseId, $title, $passingScore]
        );
        $quizId = $db->lastInsertId();

        // 2. Add Questions
        foreach ($questions as $q) {
            $text = $q['text'];
            $options = json_encode($q['options']); // ['Option A', 'Option B', ...]
            $correctIndex = $q['correct_index'];

            $db->query("INSERT INTO academy_quiz_questions (quiz_id, question_text, options, correct_option_index) VALUES (?, ?, ?, ?)", 
                [$quizId, $text, $options, $correctIndex]
            );
        }

        header("Location: /academy/instructor/edit/" . $courseId);
    }

    // Learner: Take Quiz
    public function take($params)
    {
        $id = $params['id'];
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $db = Database::getInstance();
        
        // Fetch Quiz
        $stmt = $db->query("SELECT * FROM academy_quizzes WHERE id = ?", [$id]);
        $quiz = $stmt->fetch();

        // Fetch Questions
        $stmt = $db->query("SELECT * FROM academy_quiz_questions WHERE quiz_id = ?", [$id]);
        $questions = $stmt->fetchAll();

        require __DIR__ . '/../Views/learner/quiz/take.php';
    }

    // Learner: Submit Quiz
    public function submit($params)
    {
        $id = $params['id'];
        if (!Auth::check()) {
            die('Unauthorized');
        }

        $db = Database::getInstance();
        $answers = $_POST['answers'] ?? []; // [question_id => selected_index]
        
        // Fetch Questions to grade
        $stmt = $db->query("SELECT * FROM academy_quiz_questions WHERE quiz_id = ?", [$id]);
        $questions = $stmt->fetchAll();
        
        $totalQuestions = count($questions);
        $correctCount = 0;

        foreach ($questions as $q) {
            if (isset($answers[$q['id']]) && (int)$answers[$q['id']] === (int)$q['correct_option_index']) {
                $correctCount++;
            }
        }

        $score = ($totalQuestions > 0) ? round(($correctCount / $totalQuestions) * 100) : 0;
        
        // Check passing score
        $stmt = $db->query("SELECT passing_score FROM academy_quizzes WHERE id = ?", [$id]);
        $quiz = $stmt->fetch();
        $passed = $score >= $quiz['passing_score'];

        // Record Attempt
        $db->query("INSERT INTO academy_quiz_attempts (user_id, quiz_id, score, passed) VALUES (?, ?, ?, ?)", 
            [Auth::user()['id'], $id, $score, $passed ? 1 : 0]
        );

        header("Location: /academy/quiz/result/$id?score=$score&passed=" . ($passed ? 1 : 0));
    }
    
    // Result View
    public function result($params) {
        $id = $params['id'];
         if (!Auth::check()) {
            header('Location: /login');
            exit;
        }
        $score = $_GET['score'];
        $passed = $_GET['passed'];
        $quizId = $id;
        
        require __DIR__ . '/../Views/learner/quiz/result.php';
    }
}

