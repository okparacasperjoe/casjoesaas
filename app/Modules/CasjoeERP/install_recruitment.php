<?php
require_once __DIR__ . '/../../core/Database.php';

use App\Core\Database;

$db = Database::getInstance()->getConnection();

$sql = file_get_contents(__DIR__ . '/sql/recruitment_install.sql');

try {
    // Basic split for multiple statements if PDO doesn't handle them at once (though MySQL usually does)
    // For safety with simple drivers:
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $db->exec($stmt);
        }
    }
    echo "Recruitment tables installed successfully.\n";
} catch (PDOException $e) {
    echo "Error installing recruitment tables: " . $e->getMessage() . "\n";
}
