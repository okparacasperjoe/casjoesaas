<?php
require_once 'app/core/bootstrap.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();
$tenantId = 1;

echo "Seeding Shop Data...\n";

try {
    // 1. Ensure User 1 exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = 1");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $hash = password_hash('password', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (id, tenant_id, name, email, password, role, is_verified) VALUES (1, $tenantId, 'Admin User', 'admin@casjoe.com', '$hash', 'admin', 1)");
        echo "[OK] Created User 1.\n";
    }

    // 2. Create Vendor
    $stmt = $pdo->prepare("SELECT id FROM shop_vendors WHERE user_id = 1");
    $stmt->execute();
    $vendor = $stmt->fetch();
    if (!$vendor) {
        $pdo->exec("INSERT INTO shop_vendors (tenant_id, user_id, store_name, slug, status) VALUES ($tenantId, 1, 'Casjoe Official Store', 'casjoe-official', 'active')");
        $vendorId = $pdo->lastInsertId();
        echo "[OK] Created Vendor 'Casjoe Official Store'.\n";
    } else {
        $vendorId = $vendor['id'];
        echo "[SKIP] Vendor already exists.\n";
    }

    // 3. Create Products
    $products = [
        ['Casjoe T-Shirt', 'casjoe-tshirt', 'Premium Cotton T-Shirt', 25.00, 'physical'],
        ['PHP Masterclass Ebook', 'php-masterclass', 'Learn PHP in 7 days.', 49.99, 'digital', '/uploads/shop/digital/sample.pdf'],
        ['Wireless Mouse', 'wireless-mouse', 'Ergonomic mouse.', 15.00, 'physical']
    ];

    $stmt = $pdo->prepare("INSERT INTO shop_products (vendor_id, name, slug, description, price, type, file_path, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW())");

    foreach ($products as $p) {
        // Check duplication by slug
        $chk = $pdo->prepare("SELECT id FROM shop_products WHERE slug = ?");
        $chk->execute([$p[1]]);
        if (!$chk->fetch()) {
            $filePath = $p[5] ?? null;
            $stmt->execute([$vendorId, $p[0], $p[1], $p[2], $p[3], $p[4], $filePath]);
            echo "[OK] Added product: {$p[0]}\n";
        }
    }

    // 4. Create Dummy Order
    $chk = $pdo->query("SELECT id FROM shop_orders LIMIT 1");
    if (!$chk->fetch()) {
        $pdo->exec("INSERT INTO shop_orders (user_id, total_amount, status, created_at) VALUES (1, 40.00, 'completed', NOW())");
        $orderId = $pdo->lastInsertId();
        
        // Add items (assuming first two products exist now)
        $prod1 = $pdo->query("SELECT id FROM shop_products WHERE slug='casjoe-tshirt'")->fetchColumn();
        $prod2 = $pdo->query("SELECT id FROM shop_products WHERE slug='wireless-mouse'")->fetchColumn();
        
        if ($prod1) $pdo->exec("INSERT INTO shop_order_items (order_id, product_id, vendor_id, price, quantity) VALUES ($orderId, $prod1, $vendorId, 25.00, 1)");
        if ($prod2) $pdo->exec("INSERT INTO shop_order_items (order_id, product_id, vendor_id, price, quantity) VALUES ($orderId, $prod2, $vendorId, 15.00, 1)");
        
        echo "[OK] Created dummy order #$orderId.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
