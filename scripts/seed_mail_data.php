<?php
require_once 'app/core/bootstrap.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();
$tenantId = 1;

echo "Seeding Mail Data...\n";

try {
    // 1. List
    $stmt = $pdo->prepare("SELECT id FROM cm_lists WHERE name = ?");
    $stmt->execute(['Weekly Newsletter']);
    if (!$list = $stmt->fetch()) {
        $pdo->prepare("INSERT INTO cm_lists (tenant_id, name) VALUES (?, ?)")
            ->execute([$tenantId, 'Weekly Newsletter']);
        $listId = $pdo->lastInsertId();
        echo "[OK] Created List: Weekly Newsletter\n";
    } else {
        $listId = $list['id'];
        echo "[SKIP] List exists.\n";
    }

    // 2. Subscriber
    $email = 'john.doe@example.com';
    $stmt = $pdo->prepare("SELECT id FROM cm_subscribers WHERE email = ? AND list_id = ?");
    $stmt->execute([$email, $listId]);
    if (!$stmt->fetch()) {
        $pdo->prepare("INSERT INTO cm_subscribers (tenant_id, list_id, email, first_name, status) VALUES (?, ?, ?, ?, 'subscribed')")
            ->execute([$tenantId, $listId, $email, 'John']);
        echo "[OK] Added Subscriber: $email\n";
    } else {
        echo "[SKIP] Subscriber exists.\n";
    }

    // 3. Campaign
    $stmt = $pdo->prepare("SELECT id FROM cm_campaigns WHERE name = ?");
    $stmt->execute(['Welcome Email']);
    if (!$stmt->fetch()) {
        $pdo->prepare("INSERT INTO cm_campaigns (tenant_id, name, subject, content, list_id, status) VALUES (?, ?, ?, ?, ?, 'draft')")
            ->execute([$tenantId, 'Welcome Email', 'Welcome to Casjoe!', '<h1>Hello!</h1>', $listId]);
        echo "[OK] Created Campaign: Welcome Email\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
