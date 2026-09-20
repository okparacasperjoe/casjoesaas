<?php

namespace App\Modules\CasjoeShop\Controllers;

use App\Core\Auth;
use App\Core\Database;

class PosController
{
    // POS Terminal Interface
    public function terminal()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();

        // Check if user is a vendor
        $stmt = $db->query("SELECT id FROM shop_vendors WHERE user_id = ?", [$user['id']]);
        $vendor = $stmt->fetch();

        if ($vendor) {
            // Vendor: Show own products
            $stmt = $db->query("SELECT * FROM shop_products WHERE vendor_id = ? AND status = 'active' ORDER BY name ASC", [$vendor['id']]);
        } else {
            // Admin/Tenant: Show all tenant products via Join
            $stmt = $db->query("
                SELECT p.* 
                FROM shop_products p
                JOIN shop_vendors v ON p.vendor_id = v.id
                WHERE v.tenant_id = ? AND p.status = 'active' 
                ORDER BY p.name ASC
            ", [$user['tenant_id']]);
        }
        $products = $stmt->fetchAll();

        // Fetch Customers (Simple list for now, ideally AJAX search for scalability)
        $stmt = $db->query("SELECT id, name, email FROM users WHERE tenant_id = ? ORDER BY name ASC LIMIT 100", [$user['tenant_id']]);
        $customers = $stmt->fetchAll();

        require __DIR__ . '/../Views/pos/terminal.php';
    }

    // Process Sales (AJAX)
    public function checkout()
    {
        if (!Auth::check()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $cart = $input['cart'] ?? [];
        $total = $input['total'] ?? 0;
        $discount = $input['discount'] ?? 0;
        $paymentMethod = $input['payment_method'] ?? 'cash';
        $customerId = $input['customer_id'] ?? null;
        
        if (empty($cart)) {
            echo json_encode(['error' => 'Cart is empty']);
            exit;
        }

        $db = Database::getInstance();
        $user = Auth::user(); // Cashier

        // Use selected customer, or fallback to Cashier (or designated Walk-in user if configured)
        // For accurate records, we should ideally have a NULL user_id for guest, but let's use Cashier as fallback to satisfy FK for now
        $buyerId = $customerId ?: $user['id']; 

        try {
            // 1. Create Order
            $orderRef = 'POS-' . strtoupper(uniqid());
            $db->query(
                "INSERT INTO shop_orders (tenant_id, user_id, total_amount, discount_amount, status, payment_status, payment_method, order_ref) 
                 VALUES (?, ?, ?, ?, 'completed', 'paid', ?, ?)",
                [$user['tenant_id'], $buyerId, $total, $discount, $paymentMethod, $orderRef]
            );
            $orderId = $db->lastInsertId();

            // 2. Add Items & Update Stock
            foreach ($cart as $item) {
                // Insert Item
                $db->query(
                    "INSERT INTO shop_order_items (order_id, product_id, vendor_id, quantity, price, product_name) 
                     VALUES (?, ?, ?, ?, ?, ?)",
                    [$orderId, $item['id'], $item['vendor_id'], $item['qty'], $item['price'], $item['name']]
                );

                // Update Stock
                $db->query("UPDATE shop_products SET stock_quantity = stock_quantity - ? WHERE id = ?", [$item['qty'], $item['id']]);
            }

            echo json_encode(['success' => true, 'order_id' => $orderId]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
}

