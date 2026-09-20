<?php
/**
 * Support Attachments Table Migration
 * Access: /casper-joe/migrate-support
 */

// Security check
if (!isset($_SESSION['user_id'])) {
    die("<h1>Access Denied</h1><p>Please <a href='/login'>login</a> first.</p>");
}

$pdo = App\Core\Database::getInstance()->getConnection();

// Check if user is admin
$stmt = $pdo->prepare("SELECT role, tenant_id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !in_array($user['role'], ['admin', 'super_admin']) || $user['tenant_id'] != 1) {
    die("<h1>Access Denied</h1><p>Only super admin can run migrations.</p>");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Attachments Migration</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body { padding: 40px; font-family: Arial, sans-serif; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .error { background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .info { background: #dbeafe; color: #1e40af; padding: 15px; border-radius: 8px; margin: 20px 0; }
        pre { background: #f3f4f6; padding: 15px; border-radius: 8px; overflow-x: auto; }
        .btn { display: inline-block; padding: 12px 24px; background: #000066; color: white; text-decoration: none; border-radius: 8px; border: none; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #000044; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Support Attachments Table Migration</h1>
            
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
                try {
                    // Check if table already exists
                    $stmt = $pdo->query("SHOW TABLES LIKE 'support_attachments'");
                    if ($stmt->rowCount() > 0) {
                        echo '<div class="info">⚠️ Table <code>support_attachments</code> already exists. No action taken.</div>';
                    } else {
                        // Create the table
                        $sql = "
                        CREATE TABLE IF NOT EXISTS `support_attachments` (
                            `id` INT AUTO_INCREMENT PRIMARY KEY,
                            `ticket_id` INT NOT NULL,
                            `message_id` INT NULL,
                            `file_id` INT NOT NULL COMMENT 'References cloud_files.id',
                            `uploaded_by` INT NOT NULL,
                            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            KEY `ticket_id` (`ticket_id`),
                            KEY `file_id` (`file_id`),
                            FOREIGN KEY (`file_id`) REFERENCES `cloud_files`(`id`) ON DELETE CASCADE
                        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4
                        ";
                        
                        $pdo->exec($sql);
                        
                        echo '<div class="success">✅ <strong>Success!</strong> Table <code>support_attachments</code> has been created successfully.</div>';
                        echo '<p><a href="/casper-joe/support" class="btn">Go to Support Tickets</a></p>';
                    }
                } catch (PDOException $e) {
                    echo '<div class="error">❌ <strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            } else {
                ?>
                <div class="info">
                    <strong>📋 What this migration does:</strong>
                    <p>Creates the <code>support_attachments</code> table to store file attachments for support tickets.</p>
                </div>
                
                <h3>SQL to be executed:</h3>
                <pre>CREATE TABLE IF NOT EXISTS `support_attachments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ticket_id` INT NOT NULL,
    `message_id` INT NULL,
    `file_id` INT NOT NULL COMMENT 'References cloud_files.id',
    `uploaded_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `ticket_id` (`ticket_id`),
    KEY `file_id` (`file_id`),
    FOREIGN KEY (`file_id`) REFERENCES `cloud_files`(`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4</pre>

                <form method="POST">
                    <button type="submit" name="confirm" value="1" class="btn">Run Migration</button>
                    <a href="/casper-joe" class="btn" style="background: #6b7280; margin-left: 10px;">Cancel</a>
                </form>
                <?php
            }
            ?>
        </div>
    </div>
</body>
</html>
