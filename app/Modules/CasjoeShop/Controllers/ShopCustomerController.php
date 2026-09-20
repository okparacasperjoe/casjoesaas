<?php

namespace App\Modules\CasjoeShop\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class ShopCustomerController
{
    private $db;
    private $tenantId;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->db = Database::getInstance();
        $this->tenantId = TenantContext::getTenantId();
    }

    public function dashboard()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /shop/login?redirect=/shop/account');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Fetch Orders
        $stmt = $this->db->query("
            SELECT o.*, COUNT(oi.id) as item_count 
            FROM shop_orders o
            LEFT JOIN shop_order_items oi ON o.id = oi.order_id
            WHERE o.user_id = ? AND o.tenant_id = ?
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ", [$userId, $this->tenantId]);
        $orders = $stmt->fetchAll();

        require_once __DIR__ . '/../Views/customer/dashboard.php';
    }

    public function order($params)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /shop/login');
            exit;
        }

        $id = is_array($params) ? $params['id'] : $params;
        $userId = $_SESSION['user_id'];

        // Fetch Order (Secure Check)
        $stmt = $this->db->query("SELECT * FROM shop_orders WHERE id = ? AND user_id = ?", [$id, $userId]);
        $order = $stmt->fetch();

        if (!$order) { die("Order not found or access denied."); }

        // Fetch Items
        $stmt = $this->db->query("
            SELECT oi.*, p.name, p.image_path, p.type, da.token, da.download_count, da.max_downloads
            FROM shop_order_items oi
            JOIN shop_products p ON oi.product_id = p.id
            LEFT JOIN shop_digital_access da ON oi.id = da.order_item_id
            WHERE oi.order_id = ?
        ", [$order['id']]);
        $items = $stmt->fetchAll();

        require_once __DIR__ . '/../Views/customer/order.php';
    }
    public function wishlist()
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /shop/login'); exit; }
        $userId = $_SESSION['user_id'];
        
        $stmt = $this->db->query("
            SELECT p.*, w.id as wishlist_id, w.created_at as added_at 
            FROM shop_wishlists w
            JOIN shop_products p ON w.product_id = p.id
            WHERE w.user_id = ?
            ORDER BY w.created_at DESC
        ", [$userId]);
        $items = $stmt->fetchAll();

        require_once __DIR__ . '/../Views/customer/wishlist.php';
    }

    public function toggleWishlist()
    {
        if (!isset($_SESSION['user_id'])) { 
            header('Location: /shop/login?msg=Login to manage wishlist'); 
            exit; 
        }

        $userId = $_SESSION['user_id'];
        $productId = $_POST['product_id'];

        // Check exists
        $stmt = $this->db->query("SELECT id FROM shop_wishlists WHERE user_id = ? AND product_id = ?", [$userId, $productId]);
        $exists = $stmt->fetch();

        if ($exists) {
            $this->db->query("DELETE FROM shop_wishlists WHERE id = ?", [$exists['id']]);
        } else {
            $this->db->query("INSERT INTO shop_wishlists (tenant_id, user_id, product_id) VALUES (?, ?, ?)", [$this->tenantId, $userId, $productId]);
        }

        // Return to previous page
        $redirect = $_SERVER['HTTP_REFERER'] ?? '/shop';
        header("Location: $redirect");
        exit;
    }

    public function removeWishlist($params)
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /shop/login'); exit; }
        $id = is_array($params) ? $params['id'] : $params;
        $userId = $_SESSION['user_id'];

        $this->db->query("DELETE FROM shop_wishlists WHERE id = ? AND user_id = ?", [$id, $userId]);
        header('Location: /shop/account/wishlist');
        exit;
    }

    public function orders()
    {
        $this->dashboard();
    }
}

