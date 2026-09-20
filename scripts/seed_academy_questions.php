<?php
require_once __DIR__ . '/../app/Core/Database.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();
echo "Seeding Questions...\n";

// Get all quizzes
$stmt = $pdo->query("SELECT id, title FROM academy_quizzes");
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($quizzes as $q) {
    // Check if questions exist
    $count = $pdo->query("SELECT COUNT(*) FROM academy_questions WHERE quiz_id = {$q['id']}")->fetchColumn();
    if ($count > 0) continue;

    echo "Adding questions to: {$q['title']}\n";

    $questions = [
        [
            'text' => 'What is the primary goal of this module?',
            'options' => json_encode(['Increase complexity', 'Streamline operations (Correct)', 'Reduce efficiency', 'Ignore customers']),
            'correct' => 1
        ],
        [
            'text' => 'Which tool is best recommended for this task?',
            'options' => json_encode(['A Hammer', 'Casjoe ERP (Correct)', 'Pen and Paper', 'Excel 95']),
            'correct' => 1
        ],
        [
            'text' => 'True or False: Automation replaces all human oversight.',
            'options' => json_encode(['True', 'False (Correct)']),
            'correct' => 1
        ]
    ];

    foreach ($questions as $qn) {
        $stmt = $pdo->prepare("INSERT INTO academy_questions (quiz_id, question_text, options, correct_option, points) VALUES (?, ?, ?, ?, 10)");
        $stmt->execute([$q['id'], $qn['text'], $qn['options'], $qn['correct']]);
    }
}
echo "Questions Seeded!\n";
