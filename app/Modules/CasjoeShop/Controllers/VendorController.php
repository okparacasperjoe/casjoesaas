<?php

namespace App\Modules\CasjoeShop\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Services\SecurityLogger;

class VendorController
{
    private $db;
    private $tenantId;
    private $userId;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $this->db = Database::getInstance();
        $this->tenantId = TenantContext::getTenantId();
        $this->userId = $_SESSION['user_id'] ?? null;
        $this->ensureShopSchema();
    }

    private function ensureShopSchema()
    {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        try {
            $this->db->query("ALTER TABLE shop_vendors ADD COLUMN currency VARCHAR(10) DEFAULT 'NGN'");
        } catch (\Exception $e) {}

        try {
            $this->db->query("ALTER TABLE shop_vendors ADD COLUMN slug VARCHAR(255) NULL");
        } catch (\Exception $e) {}

        try {
            $this->db->query("ALTER TABLE shop_vendors ADD COLUMN fee_bearer ENUM('merchant', 'customer') DEFAULT 'merchant'");
        } catch (\Exception $e) {}

        try {
            $this->db->query("ALTER TABLE shop_vendors ADD COLUMN facebook_pixel_id VARCHAR(255) NULL");
        } catch (\Exception $e) {}
    }

    private function getVendor()
    {
        if (!$this->userId) return null;
        $stmt = $this->db->query("SELECT * FROM shop_vendors WHERE user_id = ? AND tenant_id = ?", [$this->userId, $this->tenantId]);
        return $stmt->fetch();
    }

    public function register()
    {
        if ($this->getVendor()) {
            header('Location: /shop/vendor/dashboard');
            exit;
        }
        require_once __DIR__ . '/../Views/seller/register.php';
    }

    public function storeVendor()
    {
        if (!$this->userId) { header('Location: /login'); exit; }
        
        $storeName = $_POST['store_name'];
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $storeName)));
        
        $this->db->query(
            "INSERT INTO shop_vendors (tenant_id, user_id, store_name, slug, status) VALUES (?, ?, ?, ?, 'pending')", 
            [$this->tenantId, $this->userId, $storeName, $slug]
        );

        header('Location: /shop/vendor/dashboard?status=registered');
        exit;
    }

    public function dashboard()
    {
        if (!$this->userId) { header('Location: /login'); exit; }

        SecurityLogger::info('module.access', "User accessed Shop Vendor Dashboard", $this->userId);
        
        $vendor = $this->getVendor();
        
        if (!$vendor) {
            header('Location: /shop/vendor/register');
            exit;
        }

        $stmt = $this->db->query("SELECT COUNT(*) FROM shop_products WHERE vendor_id = ?", [$vendor['id']]);
        $totalProducts = $stmt->fetchColumn();

        $stmt = $this->db->query(
            "SELECT COUNT(DISTINCT o.id) FROM shop_orders o 
             JOIN shop_order_items oi ON o.id = oi.order_id 
             WHERE oi.vendor_id = ?", 
            [$vendor['id']]
        );
        $totalOrders = $stmt->fetchColumn();

        $stmt = $this->db->query(
            "SELECT SUM(oi.price * oi.quantity) FROM shop_order_items oi 
             JOIN shop_orders o ON oi.order_id = o.id 
             WHERE oi.vendor_id = ? AND o.status IN ('completed', 'paid', 'shipped')",
            [$vendor['id']]
        );
        $totalRevenue = $stmt->fetchColumn() ?: 0.00;

        $stmt = $this->db->query(
            "SELECT o.id, o.total_amount, o.status, o.created_at, u.email as customer_email 
             FROM shop_orders o
             JOIN shop_order_items oi ON o.id = oi.order_id
             JOIN users u ON o.user_id = u.id
             WHERE oi.vendor_id = ?
             GROUP BY o.id
             ORDER BY o.created_at DESC LIMIT 5",
            [$vendor['id']]
        );
        $recentOrders = $stmt->fetchAll();

        // Chart Data (Last 30 Days)
        $stmt = $this->db->query(
            "SELECT DATE(o.created_at) as date, SUM(oi.price * oi.quantity) as total 
             FROM shop_orders o 
             JOIN shop_order_items oi ON o.id = oi.order_id 
             WHERE oi.vendor_id = ? 
             AND o.status IN ('completed', 'paid', 'shipped')
             AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
             GROUP BY DATE(o.created_at)
             ORDER BY date ASC",
            [$vendor['id']]
        );
        $salesRaw = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR); // ['2023-10-01' => 100.00]

        // Fill gaps with 0
        $salesData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $salesData['labels'][] = date('M j', strtotime($date));
            $salesData['values'][] = $salesRaw[$date] ?? 0;
        }

        require_once __DIR__ . '/../Views/seller/dashboard.php';
    }

    public function products()
    {
        $vendor = $this->getVendor();
        if (!$vendor) { header('Location: /shop/vendor/register'); exit; }

        $stmt = $this->db->query("SELECT * FROM shop_products WHERE vendor_id = ? ORDER BY created_at DESC", [$vendor['id']]);
        $products = $stmt->fetchAll();

        require_once __DIR__ . '/../Views/seller/products.php';
    }

    public function createProduct()
    {
        $vendor = $this->getVendor();
        if (!$vendor) { header('Location: /shop/vendor/register'); exit; }
        
        require_once __DIR__ . '/../Views/seller/create_product.php';
    }

    public function storeProduct()
    {
        $vendor = $this->getVendor();
        if (!$vendor) { header('Location: /shop/vendor/register'); exit; }

        $name = $_POST['name'];
        $price = $_POST['price'];
        $type = $_POST['type']; 
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))) . '-' . uniqid();
        $description = $_POST['description'];
        $longDescription = $_POST['long_description'] ?? null;

        $filePath = null;
        $imagePath = null;

        if (!empty($_FILES['image']['name'])) {
            $uploadDir = __DIR__ . '/../../../../public/uploads/shop/images/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('img_') . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                $imagePath = '/uploads/shop/images/' . $filename;
            }
        }

        if ($type === 'digital' && !empty($_FILES['digital_file']['name'])) {
            $uploadDir = __DIR__ . '/../../../../public/uploads/shop/digital/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $ext = pathinfo($_FILES['digital_file']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('file_') . '.' . $ext;
            if (move_uploaded_file($_FILES['digital_file']['tmp_name'], $uploadDir . $filename)) {
                $filePath = '/uploads/shop/digital/' . $filename;
            }
        }
        
        $category = $_POST['category'] ?? 'Uncategorized';

        $stock = $_POST['stock_quantity'] ?? 0;
        $sku = $_POST['sku'] ?? strtoupper(substr($slug, 0, 8));

        // Use posted file_url if provided, otherwise default to upload path
        $postedUrl = trim($_POST['file_url'] ?? '');
        $finalFileUrl = !empty($postedUrl) ? $postedUrl : $filePath;
        
        // Service metadata
        $deliveryTime = ($type === 'service') ? ($_POST['delivery_time'] ?? null) : null;
        $serviceNotes = ($type === 'service') ? ($_POST['service_notes'] ?? null) : null;
        
        $this->db->query(
            "INSERT INTO shop_products (vendor_id, name, slug, description, long_description, category, price, type, file_path, image_path, stock_quantity, sku, is_digital, file_url, delivery_time, service_notes, status) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')",
            [$vendor['id'], $name, $slug, $description, $longDescription, $category, $price, $type, $filePath, $imagePath, $stock, $sku, ($type === 'digital' ? 1 : 0), $finalFileUrl, $deliveryTime, $serviceNotes]
        );

        $productId = $this->db->lastInsertId();

        // Process Gallery Images
        if (!empty($_FILES['gallery']['name'][0])) {
             $uploadDir = __DIR__ . '/../../../../public/uploads/shop/images/';
             foreach($_FILES['gallery']['name'] as $key => $val) {
                 if ($_FILES['gallery']['error'][$key] === 0) {
                     $ext = pathinfo($_FILES['gallery']['name'][$key], PATHINFO_EXTENSION);
                     $filename = uniqid('gl_') . '.' . $ext;
                     if (move_uploaded_file($_FILES['gallery']['tmp_name'][$key], $uploadDir . $filename)) {
                         $gPath = '/uploads/shop/images/' . $filename;
                         $this->db->query("INSERT INTO shop_product_images (product_id, image_path) VALUES (?, ?)", [$productId, $gPath]);
                     }
                 }
             }
        }

        header('Location: /shop/vendor/products');
        exit;
    }

    public function editProduct($params)
    {
        $vendor = $this->getVendor();
        if (!$vendor) { header('Location: /shop/vendor/register'); exit; }
        
        $id = $params['id'] ?? 0;
        $stmt = $this->db->query("SELECT * FROM shop_products WHERE id = ? AND vendor_id = ?", [$id, $vendor['id']]);
        $product = $stmt->fetch();
        
        if (!$product) {
            header('Location: /shop/vendor/products?error=Product not found');
            exit;
        }

        require_once __DIR__ . '/../Views/seller/edit_product.php';
    }

    public function updateProduct()
    {
        $vendor = $this->getVendor();
        if (!$vendor) { header('Location: /shop/vendor/register'); exit; }

        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'];
        $price = $_POST['price'];
        $type = $_POST['type']; 
        $description = $_POST['description'];
        $longDescription = $_POST['long_description'] ?? null;
        $category = $_POST['category'] ?? 'Uncategorized';
        $stock = $_POST['stock_quantity'] ?? 0;
        $sku = $_POST['sku'] ?? '';
        $isDigital = ($type === 'digital' ? 1 : 0);

        // Ownership Check
        $stmt = $this->db->query("SELECT * FROM shop_products WHERE id = ? AND vendor_id = ?", [$id, $vendor['id']]);
        $product = $stmt->fetch();
        if (!$product) { header('Location: /shop/vendor/products'); exit; }

        $imagePath = $product['image_path'];
        $filePath = $product['file_path'];

        if (!empty($_FILES['image']['name'])) {
            $uploadDir = __DIR__ . '/../../../../public/uploads/shop/images/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('img_') . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                $imagePath = '/uploads/shop/images/' . $filename;
            }
        }

        if ($type === 'digital' && !empty($_FILES['digital_file']['name'])) {
            $uploadDir = __DIR__ . '/../../../../public/uploads/shop/digital/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $ext = pathinfo($_FILES['digital_file']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('file_') . '.' . $ext;
            if (move_uploaded_file($_FILES['digital_file']['tmp_name'], $uploadDir . $filename)) {
                $filePath = '/uploads/shop/digital/' . $filename;
            }
        }
        
        $postedUrl = trim($_POST['file_url'] ?? '');
        $finalFileUrl = !empty($postedUrl) ? $postedUrl : ($filePath ?: $product['file_url']); // Prefer new URL, then new file, then old URL

        // Service metadata
        $deliveryTime = ($type === 'service') ? ($_POST['delivery_time'] ?? null) : null;
        $serviceNotes = ($type === 'service') ? ($_POST['service_notes'] ?? null) : null;

        $this->db->query(
            "UPDATE shop_products SET name=?, description=?, long_description=?, category=?, price=?, type=?, file_path=?, image_path=?, stock_quantity=?, sku=?, is_digital=?, file_url=?, delivery_time=?, service_notes=? WHERE id=? AND vendor_id=?",
            [$name, $description, $longDescription, $category, $price, $type, $filePath, $imagePath, $stock, $sku, $isDigital, $finalFileUrl, $deliveryTime, $serviceNotes, $id, $vendor['id']]
        );

        // Process New Gallery Images
        if (!empty($_FILES['gallery']['name'][0])) {
             $uploadDir = __DIR__ . '/../../../../public/uploads/shop/images/';
             foreach($_FILES['gallery']['name'] as $key => $val) {
                 if ($_FILES['gallery']['error'][$key] === 0) {
                     $ext = pathinfo($_FILES['gallery']['name'][$key], PATHINFO_EXTENSION);
                     $filename = uniqid('gl_') . '.' . $ext;
                     if (move_uploaded_file($_FILES['gallery']['tmp_name'][$key], $uploadDir . $filename)) {
                         $gPath = '/uploads/shop/images/' . $filename;
                         $this->db->query("INSERT INTO shop_product_images (product_id, image_path) VALUES (?, ?)", [$id, $gPath]);
                     }
                 }
             }
        }

        header('Location: /shop/vendor/products?msg=Product updated');
        exit;
    }

    public function deleteProduct($params)
    {
        $vendor = $this->getVendor();
        if (!$vendor) { header('Location: /shop/vendor/register'); exit; }

        $id = is_array($params) ? $params['id'] : $params;

        $this->db->query("DELETE FROM shop_products WHERE id = ? AND vendor_id = ?", [$id, $vendor['id']]);
        $this->db->query("DELETE FROM shop_order_items WHERE product_id = ?", [$id]); 
        
        header('Location: /shop/vendor/products?msg=Product deleted');
        exit;
    }

    public function deleteProductImage($params)
    {
        $vendor = $this->getVendor();
        if (!$vendor) { 
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']); 
            exit; 
        }

        $id = is_array($params) ? $params['id'] : $params;
        
        // Verify ownership via product
        $stmt = $this->db->query("
            SELECT i.* FROM shop_product_images i 
            JOIN shop_products p ON i.product_id = p.id 
            WHERE i.id = ? AND p.vendor_id = ?", 
            [$id, $vendor['id']]
        );
        $image = $stmt->fetch();

        if ($image) {
            $this->db->query("DELETE FROM shop_product_images WHERE id = ?", [$id]);
            // Optional: Unlink file
            // if(file_exists(__DIR__ . '/../../../../public' . $image['image_path'])) unlink(...);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Image not found']);
        }
        exit;
    }

    public function orders()
    {
        $this->dashboard();
    }

    public function settings()
    {
        $vendor = $this->getVendor();
        if (!$vendor) { header('Location: /shop/vendor/register'); exit; }
        
        require_once __DIR__ . '/../Views/seller/settings.php';
    }

    public function updateSettings()
    {
        $vendor = $this->getVendor();
        if (!$vendor) { header('Location: /shop/vendor/register'); exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $feeBearer = $_POST['fee_bearer'] ?? 'merchant';
            if (!in_array($feeBearer, ['merchant', 'customer'])) $feeBearer = 'merchant';

            $pixelId = trim($_POST['facebook_pixel_id'] ?? '');
            
            $currency = $_POST['currency'] ?? 'NGN';
            $validCurrencies = ['USD', 'GBP', 'EUR', 'NGN', 'KES', 'ZAR', 'GHS', 'XOF', 'UGX'];
            if (!in_array($currency, $validCurrencies)) $currency = 'NGN';
            
            $this->db->query("UPDATE shop_vendors SET fee_bearer = ?, facebook_pixel_id = ?, currency = ? WHERE id = ?", [$feeBearer, $pixelId, $currency, $vendor['id']]);
            
            header('Location: /shop/vendor/settings?msg=Settings saved');
            exit;
        }
    }

    public function shipping()
    {
        $vendor = $this->getVendor();
        if (!$vendor) { header('Location: /shop/vendor/register'); exit; }

        $stmt = $this->db->query("SELECT * FROM shop_shipping_zones WHERE vendor_id = ?", [$vendor['id']]);
        $zones = $stmt->fetchAll();

        require_once __DIR__ . '/../Views/seller/shipping.php';
    }

    public function storeShipping()
    {
        $vendor = $this->getVendor();
        if (!$vendor) { header('Location: /shop/vendor/register'); exit; }

        $name = $_POST['zone_name'];
        $cost = $_POST['cost'];

        $this->db->query("INSERT INTO shop_shipping_zones (vendor_id, zone_name, cost) VALUES (?, ?, ?)", [$vendor['id'], $name, $cost]);
        
        header('Location: /shop/vendor/shipping');
        exit;
    }

    public function deleteShipping($params)
    {
        $vendor = $this->getVendor();
        $id = is_array($params) ? $params['id'] : $params;

        $this->db->query("DELETE FROM shop_shipping_zones WHERE id = ? AND vendor_id = ?", [$id, $vendor['id']]);
        
        header('Location: /shop/vendor/shipping');
        exit;
    }

    public function coupons()
    {
        $vendor = $this->getVendor();
        if (!$vendor) { header('Location: /shop/vendor/register'); exit; }

        $stmt = $this->db->query("SELECT * FROM shop_coupons WHERE vendor_id = ? ORDER BY created_at DESC", [$vendor['id']]);
        $coupons = $stmt->fetchAll();

        require_once __DIR__ . '/../Views/seller/coupons.php';
    }

    public function storeCoupon()
    {
        $vendor = $this->getVendor();
        if (!$vendor) { header('Location: /shop/vendor/register'); exit; }

        $code = strtoupper(trim($_POST['code']));
        $type = $_POST['type'];
        $value = $_POST['value'];
        $minSpend = $_POST['min_spend'] ?: 0;
        $usageLimit = $_POST['usage_limit'] ?: 0;
        $expiresAt = $_POST['expires_at'] ?: null;

        try {
            $this->db->query(
                "INSERT INTO shop_coupons (tenant_id, vendor_id, code, type, value, min_spend, usage_limit, expires_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)", 
                [$this->tenantId, $vendor['id'], $code, $type, $value, $minSpend, $usageLimit, $expiresAt]
            );
            header('Location: /shop/vendor/coupons?msg=Coupon created');
        } catch (\Exception $e) {
            header('Location: /shop/vendor/coupons?error=Code already exists');
        }
        exit;
    }

    public function deleteCoupon($params)
    {
        $vendor = $this->getVendor();
        $id = is_array($params) ? $params['id'] : $params;

        $this->db->query("DELETE FROM shop_coupons WHERE id = ? AND vendor_id = ?", [$id, $vendor['id']]);
        
        header('Location: /shop/vendor/coupons');
        exit;
    }
}

