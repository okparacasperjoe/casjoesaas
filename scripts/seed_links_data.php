<?php
require_once 'app/core/bootstrap.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();
$tenantId = 1;
$userId = 1;

echo "Seeding Links Data...\n";

try {
    // 1. Bio Page
    $slug = 'casjoe';
    $stmt = $pdo->prepare("SELECT id FROM links_bio_pages WHERE slug = ?");
    $stmt->execute([$slug]);
    if (!$stmt->fetch()) {
        $blocks = json_encode([
            ['type' => 'link', 'title' => 'Visit Website', 'url' => 'https://casjoe.com'],
            ['type' => 'text', 'content' => 'Welcome to my Bio Page!']
        ]);
        $pdo->prepare("INSERT INTO links_bio_pages (tenant_id, user_id, slug, title, blocks) VALUES (?, ?, ?, 'My Official Links', ?)")
            ->execute([$tenantId, $userId, $slug, $blocks]);
        echo "[OK] Created Bio Page: $slug\n";
    }

    // 2. Short URL
    $code = 'google';
    $stmt = $pdo->prepare("SELECT id FROM links_short_urls WHERE short_code = ?");
    $stmt->execute([$code]);
    if (!$stmt->fetch()) {
        $pdo->prepare("INSERT INTO links_short_urls (tenant_id, user_id, long_url, short_code, type) VALUES (?, ?, 'https://google.com', ?, 'direct')")
            ->execute([$tenantId, $userId, $code]);
        echo "[OK] Created Short URL: $code\n";
    }

    // 3. QR Code
    $pdo->prepare("INSERT INTO links_qr_codes (tenant_id, user_id, name, type, content) VALUES (?, ?, 'My WiFi', 'wifi', 'WIFI:S:MyNetwork;T:WPA;P:secret;;')")
        ->execute([$tenantId, $userId]);
    echo "[OK] Created QR Code.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
