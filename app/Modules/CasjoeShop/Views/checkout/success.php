<?php 
$title = "Order Compelte";
require __DIR__ . '/../layout/store_header.php';

// In a real app, pass $orderId to view and fetch items again to display links
// Here we are reusing the logic from Controller to pass data or relying on session flash?
// For MVP simplicity, we will query the latest order for this user to show links directly here.
// Fetch Order Details using Session ID (works for Guest & User)
$orderId = $_SESSION['last_order_id'] ?? null;
$db = \App\Core\Database::getInstance();
$order = null;

if ($orderId) {
    $stmt = $db->prepare("SELECT * FROM shop_orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
} else if (isset($_SESSION['user_id'])) {
    // Fallback for logged in users if session lost?
    $userId = $_SESSION['user_id'];
    $stmt = $db->query("SELECT * FROM shop_orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 1", [$userId]);
    $order = $stmt->fetch();
}

// Get Digital Downloads
$downloads = [];
if ($order) {
    $stmt = $db->query("
        SELECT da.token, p.name 
        FROM shop_digital_access da
        JOIN shop_order_items oi ON da.order_item_id = oi.id
        JOIN shop_products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ", [$order['id']]);
    $downloads = $stmt->fetchAll();
}
?>

<div class="container mt-5 text-center">
    <div class="mb-4">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
    </div>
    <h1 class="fw-bold mb-3">Payment Successful!</h1>
    <p class="lead text-muted mb-5">Thank you for your purchase. Your order #<?= $order['id'] ?? '' ?> has been confirmed.</p>

    <?php if(!empty($downloads)): ?>
    <div class="card border-0 shadow-sm mx-auto mb-5" style="max-width: 600px;">
        <div class="card-header bg-info text-white fw-bold">
            <i class="bi bi-cloud-download"></i> Your Digital Downloads
        </div>
        <div class="list-group list-group-flush">
            <?php foreach($downloads as $dl): ?>
            <a href="/shop/download/<?= $dl['token'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                <span><i class="bi bi-file-earmark-music"></i> <?= htmlspecialchars($dl['name']) ?></span>
                <span class="btn btn-sm btn-primary">Download</span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <a href="/shop" class="btn btn-outline-primary">Continue Shopping</a>
</div>

<?php require __DIR__ . '/../../../../Core/Views/global_footer.php'; ?>

