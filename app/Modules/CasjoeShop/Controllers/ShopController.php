<?php

namespace App\Modules\CasjoeShop\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class ShopController
{
    private $db;
    private $tenantId;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->db = Database::getInstance();
        $this->tenantId = TenantContext::getTenantId();
        $this->ensureShopSchema();
    }

    private function ensureShopSchema()
    {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        $pdo = $this->db->getConnection();
        $tables = [
            "shop_sliders" => "CREATE TABLE IF NOT EXISTS `shop_sliders` (`id` INT AUTO_INCREMENT PRIMARY KEY, `tenant_id` INT NOT NULL, `badge_text` VARCHAR(50) NULL, `badge_color` VARCHAR(20) DEFAULT 'primary', `title` VARCHAR(255) NOT NULL, `subtitle` TEXT NULL, `button_text` VARCHAR(50) NULL, `button_link` VARCHAR(255) NULL, `button_color` VARCHAR(20) DEFAULT 'primary', `image_url` VARCHAR(255) NULL, `background_overlay` VARCHAR(100) DEFAULT 'rgba(255,255,255,0.2)', `is_active` TINYINT(1) DEFAULT 1, `sort_order` INT DEFAULT 0, `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX(tenant_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
            "shop_ads_settings" => "CREATE TABLE IF NOT EXISTS `shop_ads_settings` (`id` INT AUTO_INCREMENT PRIMARY KEY, `tenant_id` INT NOT NULL, `banner_location` VARCHAR(50) NOT NULL, `title` VARCHAR(255), `description` TEXT, `button_text` VARCHAR(50), `button_link` VARCHAR(255), `image_url` VARCHAR(255), `bg_gradient_start` VARCHAR(20) DEFAULT '#000066', `bg_gradient_end` VARCHAR(20) DEFAULT '#000044', `is_active` BOOLEAN DEFAULT TRUE, `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
            "shop_product_images" => "CREATE TABLE IF NOT EXISTS `shop_product_images` (`id` INT AUTO_INCREMENT PRIMARY KEY, `product_id` INT NOT NULL, `image_path` VARCHAR(255) NOT NULL, `sort_order` INT DEFAULT 0, `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
            "shop_wishlists" => "CREATE TABLE IF NOT EXISTS `shop_wishlists` (`id` INT AUTO_INCREMENT PRIMARY KEY, `user_id` INT NOT NULL, `product_id` INT NOT NULL, `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY `uk_user_product` (`user_id`, `product_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
            "shop_digital_access" => "CREATE TABLE IF NOT EXISTS `shop_digital_access` (`id` INT AUTO_INCREMENT PRIMARY KEY, `order_item_id` INT NOT NULL, `token` VARCHAR(255) NOT NULL, `max_downloads` INT DEFAULT 5, `download_count` INT DEFAULT 0, `expires_at` TIMESTAMP NULL, `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY `uk_token` (`token`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
        ];

        foreach ($tables as $sql) {
            try { $pdo->exec($sql); } catch (\Exception $e) {}
        }

        try { $pdo->exec("ALTER TABLE shop_vendors ADD COLUMN logo VARCHAR(255) NULL"); } catch (\Exception $e) {}
        try { $pdo->exec("ALTER TABLE shop_vendors ADD COLUMN currency VARCHAR(10) DEFAULT 'NGN'"); } catch (\Exception $e) {}
        try { $pdo->exec("ALTER TABLE shop_vendors ADD COLUMN slug VARCHAR(255) NULL"); } catch (\Exception $e) {}
        try { $pdo->exec("ALTER TABLE shop_vendors ADD COLUMN fee_bearer ENUM('merchant', 'customer') DEFAULT 'merchant'"); } catch (\Exception $e) {}
        try { $pdo->exec("ALTER TABLE shop_vendors ADD COLUMN facebook_pixel_id VARCHAR(255) NULL"); } catch (\Exception $e) {}
    }

    public function index()
    {
        $params = $_GET;
        $sql = "SELECT * FROM shop_products WHERE status = 'active'";
        $bindings = [];

        if (!empty($params['q'])) {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
            $bindings[] = '%' . $params['q'] . '%';
            $bindings[] = '%' . $params['q'] . '%';
        }

        if (!empty($params['category'])) {
            $sql .= " AND category = ?";
            $bindings[] = $params['category'];
        }

        if (!empty($params['min_price'])) {
            $sql .= " AND price >= ?";
            $bindings[] = $params['min_price'];
        }

        if (!empty($params['max_price'])) {
            $sql .= " AND price <= ?";
            $bindings[] = $params['max_price'];
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->db->query($sql, $bindings);
        $products = $stmt->fetchAll();
        
        // Fetch vendor currencies for price conversion
        if (!empty($products)) {
            $vendorIds = array_unique(array_column($products, 'vendor_id'));
            
            // Fix for PDO parameter mismatch in IN clause
            $vendorCurrencies = [];
            if (!empty($vendorIds)) {
                $placeholders = implode(',', array_fill(0, count($vendorIds), '?'));
                $stmt = $this->db->prepare("SELECT id, currency FROM shop_vendors WHERE id IN ($placeholders)");
                // array_values ensures numeric keys for execute
                $stmt->execute(array_values($vendorIds));
                
                foreach ($stmt->fetchAll() as $v) {
                    $vendorCurrencies[$v['id']] = $v['currency'] ?? 'NGN';
                }
            }
            
            // Attach vendor currency to each product
            foreach ($products as &$product) {
                $product['vendor_currency'] = $vendorCurrencies[$product['vendor_id']] ?? 'NGN';
            }
        }

        // Fetch categories for sidebar
        $catStmt = $this->db->query("SELECT DISTINCT category FROM shop_products WHERE status='active' AND category IS NOT NULL AND category != '' ORDER BY category ASC");
        $categories = $catStmt->fetchAll(\PDO::FETCH_COLUMN);

        // Fetch Advertisement Banners
        $adStmt = $this->db->query("SELECT * FROM shop_ads_settings WHERE tenant_id = ? AND is_active = 1", [$this->tenantId]);
        $ads = $adStmt->fetchAll();
        $adsByLocation = [];
        foreach ($ads as $ad) {
            $adsByLocation[$ad['banner_location']] = $ad;
        }

        // Fetch Flash Deals (products with compare_price > price)
        $dealsStmt = $this->db->query("SELECT * FROM shop_products WHERE status = 'active' AND compare_price > price ORDER BY created_at DESC LIMIT 8");
        $deals = $dealsStmt->fetchAll();
        
        // Attach vendor currencies for deals
        if (!empty($deals)) {
            $dealVendorIds = array_unique(array_column($deals, 'vendor_id'));
            $dealCurrencies = [];
            if (!empty($dealVendorIds)) {
                $placeholders = implode(',', array_fill(0, count($dealVendorIds), '?'));
                $stmt = $this->db->prepare("SELECT id, currency FROM shop_vendors WHERE id IN ($placeholders)");
                $stmt->execute(array_values($dealVendorIds));
                foreach ($stmt->fetchAll() as $v) {
                    $dealCurrencies[$v['id']] = $v['currency'] ?? 'NGN';
                }
            }
            foreach ($deals as &$deal) {
                $deal['vendor_currency'] = $dealCurrencies[$deal['vendor_id']] ?? 'NGN';
            }
        }

        // Fetch Sliders
        $sliderStmt = $this->db->query("SELECT * FROM shop_sliders WHERE tenant_id = ? AND is_active = 1 ORDER BY sort_order ASC", [$this->tenantId]);
        $sliders = $sliderStmt->fetchAll();

        require_once __DIR__ . '/../Views/store/index.php';
    }

    public function product($params)
    {
        $id = is_array($params) ? $params['id'] : $params;
        $stmt = $this->db->query("SELECT * FROM shop_products WHERE id = ?", [$id]);
        $product = $stmt->fetch();

        if (!$product) { echo "Product not found"; return; }

        // Get Gallery Images
        $stmt = $this->db->query("SELECT * FROM shop_product_images WHERE product_id = ? ORDER BY sort_order ASC", [$id]);
        $product['gallery'] = $stmt->fetchAll();

        // Get Vendor info with currency
        $stmt = $this->db->query("SELECT store_name, currency FROM shop_vendors WHERE id = ?", [$product['vendor_id']]);
        $vendor = $stmt->fetch();
        $product['vendor_currency'] = $vendor['currency'] ?? 'NGN';

        // Get Reviews
        $stmt = $this->db->query("
            SELECT r.*, u.name as user_name 
            FROM shop_product_reviews r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.product_id = ? 
            ORDER BY r.created_at DESC
        ", [$id]);
        $reviews = $stmt->fetchAll();

        // Calculate Average Rating
        $avgRating = 0;
        if (count($reviews) > 0) {
            $sum = 0;
            foreach ($reviews as $r) $sum += $r['rating'];
            $avgRating = round($sum / count($reviews), 1);
        }

        // Check Wishlist Status
        $isWishlisted = false;
        if (isset($_SESSION['user_id'])) {
            $stmt = $this->db->query("SELECT id FROM shop_wishlists WHERE user_id = ? AND product_id = ?", [$_SESSION['user_id'], $id]);
            if ($stmt->fetch()) $isWishlisted = true;
        }

        require_once __DIR__ . '/../Views/store/product.php';
    }

    public function submitReview()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /shop/login?msg=Login to review');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $productId = $_POST['product_id'];
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);

        // Check if user bought product (Verified Purchase) logic could go here
        // For now, allow all logged in users 

        $this->db->query(
            "INSERT INTO shop_product_reviews (tenant_id, product_id, user_id, rating, comment) VALUES (?, ?, ?, ?, ?)",
            [$this->tenantId, $productId, $userId, $rating, $comment]
        );

        header("Location: /shop/product/$productId?review_submitted=1");
        exit;
    }

    // --- CART Logic (Session Based) ---
    public function addToCart()
    {
        $productId = $_POST['product_id'];
        $quantity = (int)($_POST['quantity'] ?? 1);

        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }

        header('Location: /shop/cart');
        exit;
    }

    public function cart()
    {
        $cartItems = [];
        $total = 0;

        if (!empty($_SESSION['cart'])) {
            $ids = implode(',', array_keys($_SESSION['cart']));
            // Safe as keys are ints from session logic ideally, but better to prepare
            $inQuery = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
            $stmt = $this->db->prepare("SELECT p.*, v.currency as vendor_currency FROM shop_products p JOIN shop_vendors v ON p.vendor_id = v.id WHERE p.id IN ($inQuery)");
            $stmt->execute(array_keys($_SESSION['cart']));
            $products = $stmt->fetchAll();

            // Include currency service for conversion
            require_once __DIR__ . '/../Views/helpers/price_helper.php';
            $customerCurrency = \App\Modules\CasjoeShop\Services\CurrencyService::getSelectedCurrency();

            foreach ($products as $p) {
                $qty = $_SESSION['cart'][$p['id']];
                // Convert price to customer currency
                $convertedPrice = convertPrice($p['price'], $p['vendor_currency'], $customerCurrency);
                $subtotal = $convertedPrice * $qty;
                $total += $subtotal;
                $p['qty'] = $qty;
                $p['subtotal'] = $subtotal;
                $p['display_price'] = $convertedPrice; // Converted price for display
                $cartItems[] = $p;
            }
        }

        require_once __DIR__ . '/../Views/store/cart.php';
    }

    // --- SECURE DOWNLOAD ---
    public function download($params)
    {
        $token = is_array($params) ? $params['token'] : $params;
        
        // Verify Token
        $stmt = $this->db->query("SELECT * FROM shop_digital_access WHERE token = ?", [$token]);
        $access = $stmt->fetch();

        if (!$access) { die("Invalid or expired download link."); }
        if ($access['max_downloads'] > 0 && $access['download_count'] >= $access['max_downloads']) {
            die("Download limit reached.");
        }

        // Get File Path
        // Get File Path & URL
        $stmt = $this->db->query("
            SELECT p.file_path, p.file_url, p.name 
            FROM shop_order_items oi
            JOIN shop_products p ON oi.product_id = p.id
            WHERE oi.id = ?
        ", [$access['order_item_id']]);
        $product = $stmt->fetch();

        if (!$product) { die("Product not found."); }

        // Increment count
        $this->db->query("UPDATE shop_digital_access SET download_count = download_count + 1 WHERE id = ?", [$access['id']]);

        // 1. Check External URL (Prioritize URL if set)
        if (!empty($product['file_url'])) {
            header("Location: " . $product['file_url']);
            exit;
        }

        // 2. Check Local File
        if (empty($product['file_path'])) { die("File not configured for this product."); }

        // Construct real path
        $fullPath = __DIR__ . '/../../../../public' . $product['file_path']; // Assumption based on upload logic

        if (!file_exists($fullPath)) { die("File missing on disk."); }

        // Increment count
        $this->db->query("UPDATE shop_digital_access SET download_count = download_count + 1 WHERE id = ?", [$access['id']]);

        // Serve File
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($fullPath).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fullPath));
        readfile($fullPath);
        exit;
    }

    public function category($params = null)
    {
        $category = is_array($params) ? ($params['slug'] ?? ($params['category'] ?? '')) : ($params ?: ($_GET['category'] ?? ''));
        $_GET['category'] = $category;
        $this->index();
    }
}

