<?php
require_once 'app/core/bootstrap.php';
use App\Core\Database;
$pdo = Database::getInstance()->getConnection();
echo "Academy Courses:\n";
try {
    print_r($pdo->query("DESCRIBE academy_courses")->fetchAll(PDO::FETCH_ASSOC));
} catch(Exception $e) { echo "academy_courses missing.\n"; }
