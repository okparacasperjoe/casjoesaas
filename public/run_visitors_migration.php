<?php
// Run the site_visitors migration
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/Core/bootstrap.php';

use App\Core\Database;

echo "<pre>";
try {
    $pdo = Database::getInstance()->getConnection();
    $sql = file_get_contents(BASE_PATH . '/database/migrations/site_visitors.sql');
    $pdo->exec($sql);
    echo "✅ site_visitors table created successfully!\n";
    
    // Verify
    $stmt = $pdo->query("DESCRIBE site_visitors");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\nTable columns:\n";
    foreach ($cols as $col) {
        echo "  - {$col['Field']} ({$col['Type']})\n";
    }
    
    // Reset opcache
    if (function_exists('opcache_reset')) {
        opcache_reset();
        echo "\n✅ opcache reset\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
echo "</pre>";
