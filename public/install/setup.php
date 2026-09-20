<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?step=2');
    exit;
}

$host = $_POST['db_host'] ?? 'localhost';
$dbname = $_POST['db_name'] ?? '';
$user = $_POST['db_user'] ?? '';
$pass = $_POST['db_pass'] ?? '';

try {
    // 1. Test Connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Write Config File
    $configContent = "<?php
return [
    'host' => '$host',
    'dbname' => '$dbname',
    'username' => '$user',
    'password' => '$pass',
    'charset' => 'utf8mb4'
];";

    $configPath = __DIR__ . '/../../config/database.php';
    if (!is_dir(dirname($configPath))) mkdir(dirname($configPath), 0777, true);
    file_put_contents($configPath, $configContent);

    // 3. Run Migrations
    // Run core tables
    $sqlFiles = [
        __DIR__ . '/../../app/Core/sql/system_settings_install.sql'
    ];

    foreach ($sqlFiles as $file) {
        if (file_exists($file)) {
            $sql = file_get_contents($file);
            $pdo->exec($sql);
        }
    }

    $_SESSION['db_host'] = $host;
    $_SESSION['db_name'] = $dbname;
    $_SESSION['db_user'] = $user;
    $_SESSION['db_pass'] = $pass;

    header('Location: index.php?step=3');
} catch (PDOException $e) {
    die("Installation Failed: Connection Error - " . $e->getMessage());
}
