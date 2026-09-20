<?php

use App\Core\Router;
use App\Modules\CasjoeShop\Controllers\AdminController;
use App\Modules\CasjoeShop\Controllers\VendorController;
use App\Modules\CasjoeShop\Controllers\ShopController;
use App\Modules\CasjoeShop\Controllers\CheckoutController;
use App\Modules\CasjoeShop\Controllers\ShopAuthController;
use App\Modules\CasjoeShop\Controllers\ShopCustomerController;
use App\Modules\CasjoeShop\Controllers\PosController;

// POS System
Router::get('/shop/pos', [PosController::class, 'terminal']);
Router::post('/shop/pos/checkout', [PosController::class, 'checkout']);

// Public Storefront
Router::get('/shop', [ShopController::class, 'index']);
Router::get('/shop/login', [ShopAuthController::class, 'login']);
Router::post('/shop/login', [ShopAuthController::class, 'attemptLogin']);
Router::get('/shop/register', [ShopAuthController::class, 'register']);
Router::post('/shop/register', [ShopAuthController::class, 'attemptRegister']);
Router::get('/shop/logout', [ShopAuthController::class, 'logout']);
Router::get('/shop/set-currency', function() {
    $currency = $_GET['currency'] ?? 'NGN';
    \App\Modules\CasjoeShop\Services\CurrencyService::setSelectedCurrency($currency);
    $redirect = $_GET['redirect'] ?? '/shop';
    header('Location: ' . $redirect);
    exit;
});
Router::post('/shop/guest-checkout', function() {
    $_SESSION['is_guest'] = true;
    header('Location: /shop/checkout');
    exit;
});

// Customer Account
Router::get('/shop/account', [ShopCustomerController::class, 'dashboard']);
Router::get('/shop/account/orders', [ShopCustomerController::class, 'orders']);
Router::get('/shop/account/orders/{id}', [ShopCustomerController::class, 'order']);
Router::get('/shop/account/wishlist', [ShopCustomerController::class, 'wishlist']);
Router::post('/shop/wishlist/toggle', [ShopCustomerController::class, 'toggleWishlist']);
Router::get('/shop/wishlist/remove/{id}', [ShopCustomerController::class, 'removeWishlist']);
Router::post('/shop/vendor/products/image/delete', [VendorController::class, 'deleteProductImage']);

Router::get('/shop/product/{id}', [ShopController::class, 'product']);
Router::get('/shop/category/{slug}', [ShopController::class, 'category']);
Router::post('/shop/cart/add', [ShopController::class, 'addToCart']);
Router::post('/shop/review', [ShopController::class, 'submitReview']);
Router::get('/shop/cart', [ShopController::class, 'cart']);
Router::get('/shop/checkout', [CheckoutController::class, 'index']);
Router::post('/shop/checkout/process', [CheckoutController::class, 'process']);
Router::get('/shop/checkout/success', [CheckoutController::class, 'success']);
Router::get('/shop/download/{token}', [ShopController::class, 'download']); // Secure Download

// Vendor Portal
Router::get('/shop/vendor/register', [VendorController::class, 'register']);
Router::post('/shop/vendor/register', [VendorController::class, 'storeVendor']);
Router::get('/shop/vendor/dashboard', [VendorController::class, 'dashboard']);
Router::get('/shop/vendor/products', [VendorController::class, 'products']);
Router::get('/shop/vendor/products/create', [VendorController::class, 'createProduct']);
Router::post('/shop/vendor/products/store', [VendorController::class, 'storeProduct']);
Router::get('/shop/vendor/products/edit/{id}', [VendorController::class, 'editProduct']);
Router::post('/shop/vendor/products/update', [VendorController::class, 'updateProduct']);
Router::post('/shop/vendor/products/delete/{id}', [VendorController::class, 'deleteProduct']);
Router::post('/shop/vendor/products/image/delete/{id}', [VendorController::class, 'deleteProductImage']);
Router::get('/shop/vendor/orders', [VendorController::class, 'orders']);
Router::get('/shop/vendor/settings', [VendorController::class, 'settings']);
Router::post('/shop/vendor/settings', [VendorController::class, 'updateSettings']);
Router::get('/shop/vendor/shipping', [VendorController::class, 'shipping']);
Router::post('/shop/vendor/shipping', [VendorController::class, 'storeShipping']);
Router::post('/shop/vendor/shipping/delete/{id}', [VendorController::class, 'deleteShipping']);
Router::get('/shop/vendor/coupons', [VendorController::class, 'coupons']);
Router::post('/shop/vendor/coupons', [VendorController::class, 'storeCoupon']);
Router::post('/shop/vendor/coupons/delete/{id}', [VendorController::class, 'deleteCoupon']);

// Admin (Platform Owner)
Router::get('/' . ADMIN_PATH . '/shop', [AdminController::class, 'dashboard']);
Router::get('/' . ADMIN_PATH . '/shop/ads', [AdminController::class, 'ads']);
Router::post('/' . ADMIN_PATH . '/shop/ads/update', [AdminController::class, 'updateAds']);
Router::get('/' . ADMIN_PATH . '/shop/sliders', [AdminController::class, 'sliders']);
Router::post('/' . ADMIN_PATH . '/shop/sliders/store', [AdminController::class, 'storeSlider']);
Router::post('/' . ADMIN_PATH . '/shop/sliders/delete/{id}', [AdminController::class, 'deleteSlider']);

// Checkout Actions
Router::post('/shop/checkout/coupon/apply', [CheckoutController::class, 'applyCoupon']);
Router::get('/shop/checkout/coupon/remove', [CheckoutController::class, 'removeCoupon']);
Router::get('/' . ADMIN_PATH . '/shop/vendors', [AdminController::class, 'vendors']);
Router::post('/' . ADMIN_PATH . '/shop/vendors/{id}/approve', [AdminController::class, 'approveVendor']);

// Temporary Fix Route
Router::get('/' . ADMIN_PATH . '/shop/fix-db-schema', function() {
    $db = \App\Core\Database::getInstance(); 
    echo "<h2>Fixing Database Schema...</h2>";
    
    $columns = [
        'slug' => "VARCHAR(255) NOT NULL AFTER name",
        'image_path' => "VARCHAR(255) NULL AFTER description",
        'file_path' => "VARCHAR(255) NULL AFTER image_path",
        'file_url' => "VARCHAR(255) NULL AFTER file_path", 
        'is_digital' => "TINYINT(1) DEFAULT 0 AFTER price",
        'type' => "VARCHAR(50) DEFAULT 'physical' AFTER is_digital",
        'sku' => "VARCHAR(100) NULL AFTER type",
        'stock_quantity' => "INT DEFAULT 0 AFTER sku",
        'category' => "VARCHAR(100) NULL AFTER description"
    ];

    foreach ($columns as $col => $def) {
        try {
            $db->query("ALTER TABLE shop_products ADD COLUMN $col $def");
            echo "✅ Added <b>$col</b> to shop_products.<br>";
        } catch(\Exception $e) { 
            if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                 echo "ℹ️ Column <b>$col</b> already exists.<br>";
            } else {
                 echo "❌ Error adding <b>$col</b>: " . $e->getMessage() . "<br>";
            }
        }
    }
    
    // Vendor Slug
    try {
        $db->query("ALTER TABLE shop_vendors ADD COLUMN slug VARCHAR(255) NOT NULL AFTER store_name");
        $db->query("CREATE INDEX idx_vendors_slug ON shop_vendors(slug)");
        echo "✅ Added slug to shop_vendors.<br>";
    } catch(\Exception $e) { echo "ℹ️ Vendor slug: " . $e->getMessage() . "<br>"; }

    // Vendor Logo & Currency
    try {
        $db->query("ALTER TABLE shop_vendors ADD COLUMN logo VARCHAR(255) NULL");
    } catch(\Exception $e) {}

    try {
        $db->query("ALTER TABLE shop_vendors ADD COLUMN currency VARCHAR(10) DEFAULT 'NGN'");
        echo "✅ Added currency to shop_vendors.<br>";
    } catch(\Exception $e) { echo "ℹ️ Vendor currency: " . $e->getMessage() . "<br>"; }

    // Vendor Fee Bearer & Facebook Pixel
    try {
        $db->query("ALTER TABLE shop_vendors ADD COLUMN fee_bearer ENUM('merchant', 'customer') DEFAULT 'merchant'");
        echo "✅ Added fee_bearer to shop_vendors.<br>";
    } catch(\Exception $e) { echo "ℹ️ Vendor fee_bearer: " . $e->getMessage() . "<br>"; }

    try {
        $db->query("ALTER TABLE shop_vendors ADD COLUMN facebook_pixel_id VARCHAR(255) NULL AFTER fee_bearer");
        echo "✅ Added facebook_pixel_id to shop_vendors.<br>";
    } catch(\Exception $e) { echo "ℹ️ Facebook Pixel: " . $e->getMessage() . "<br>"; }

    // Shop Orders Columns
    $orderCols = [
        'service_fee' => "DECIMAL(10,2) DEFAULT 0 AFTER total_amount",
        'shipping_cost' => "DECIMAL(10,2) DEFAULT 0 AFTER service_fee",
        'shipping_zone_name' => "VARCHAR(255) NULL AFTER shipping_cost",
        'discount_amount' => "DECIMAL(10,2) DEFAULT 0 AFTER shipping_zone_name",
        'coupon_code' => "VARCHAR(50) NULL AFTER discount_amount",
        'commission_amount' => "DECIMAL(10,2) DEFAULT 0 AFTER coupon_code",
        'payment_ref' => "VARCHAR(100) NULL AFTER status"
    ];

    foreach ($orderCols as $col => $def) {
        try {
            $db->query("ALTER TABLE shop_orders ADD COLUMN $col $def");
            echo "✅ Added <b>$col</b> to shop_orders.<br>";
        } catch(\Exception $e) { 
            // Ignore duplicate column errors
        }
    }

    // Wishlist Tenant ID
    try {
        $db->query("ALTER TABLE shop_wishlists ADD COLUMN tenant_id INT NOT NULL DEFAULT 1 AFTER id");
        $db->query("CREATE INDEX idx_wishlists_tenant ON shop_wishlists(tenant_id)");
        echo "✅ Added tenant_id to shop_wishlists.<br>";
    } catch(\Exception $e) { echo "ℹ️ Wishlist tenant_id: " . $e->getMessage() . "<br>"; }

    // Reviews Tenant ID
    try {
        $db->query("ALTER TABLE shop_product_reviews ADD COLUMN tenant_id INT NOT NULL DEFAULT 1 AFTER id");
        $db->query("CREATE INDEX idx_reviews_tenant ON shop_product_reviews(tenant_id)");
        echo "✅ Added tenant_id to shop_product_reviews.<br>";
    } catch(\Exception $e) { echo "ℹ️ Reviews tenant_id: " . $e->getMessage() . "<br>"; }

    // Fix: Create Chatbot Rules Table
    try {
        $db->exec("CREATE TABLE IF NOT EXISTS system_bot_rules (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL DEFAULT 1,
            keywords TEXT NOT NULL,
            response TEXT NOT NULL,
            matches_count INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        echo "✅ Created system_bot_rules table.<br>";
    } catch (\PDOException $e) {
        echo "ℹ️ Chatbot Rules Table: " . $e->getMessage() . "<br>";
    }

    // Fix: Add guest_id to Chat Messages
    try {
        $db->exec("ALTER TABLE erp_chat_messages ADD COLUMN guest_id VARCHAR(50) NULL AFTER user_id");
        $db->exec("CREATE INDEX idx_chat_guest ON erp_chat_messages(guest_id)");
        echo "✅ Added guest_id to erp_chat_messages.<br>";
    } catch (\PDOException $e) {
        // Ignore if exists
    }

    // Feature: System Billing Coupons
    try {
        $db->exec("CREATE TABLE IF NOT EXISTS system_billing_coupons (
            id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(50) NOT NULL UNIQUE,
            type ENUM('percent', 'fixed') DEFAULT 'percent',
            value DECIMAL(10,2) NOT NULL,
            duration_months INT DEFAULT 1,
            expires_at DATETIME NULL,
            usage_count INT DEFAULT 0,
            tenant_id INT NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        echo "✅ Created system_billing_coupons table.<br>";
    } catch (\PDOException $e) {
        echo "ℹ️ Billing Coupons Table: " . $e->getMessage() . "<br>";
    }

    echo "<br>Done. You can now try updating your settings.";
});

// Migration: Add Service Type and Rich Description Support
Router::get('/' . ADMIN_PATH . '/shop/migrate-service-richtext', function() {
    $db = \App\Core\Database::getInstance();
    echo "<h2>🚀 CasjoeShop Migration: Service Type & Rich Descriptions</h2>";
    echo "<hr>";
    
    // Step 1: Check current type column
    echo "<h3>1. Checking current product type ENUM...</h3>";
    $stmt = $db->query("SHOW COLUMNS FROM shop_products LIKE 'type'");
    $typeColumn = $stmt->fetch();
    
    if ($typeColumn) {
        echo "<p>Current Type: <code>{$typeColumn['Type']}</code></p>";
        
        // Check if 'service' already exists
        if (strpos($typeColumn['Type'], 'service') === false) {
            echo "<h3>2. Adding 'service' to product type ENUM...</h3>";
            try {
                $db->query("ALTER TABLE shop_products MODIFY COLUMN type ENUM('physical', 'digital', 'service') NOT NULL DEFAULT 'physical'");
                echo "<p class='text-success'>✅ Service type added successfully!</p>";
            } catch (\Exception $e) {
                echo "<p class='text-danger'>❌ ERROR: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<h3>2. Service type already exists</h3>";
            echo "<p>⏭️ Skipping...</p>";
        }
    } else {
        echo "<p class='text-danger'>❌ ERROR: type column not found!</p>";
        return;
    }
    
    // Step 2: Check description column
    echo "<h3>3. Checking description column type...</h3>";
    $stmt = $db->query("SHOW COLUMNS FROM shop_products LIKE 'description'");
    $descColumn = $stmt->fetch();
    
    if ($descColumn) {
        echo "<p>Current Type: <code>{$descColumn['Type']}</code></p>";
        echo "<p>✅ Short description field exists (for product listings)</p>";
    } else {
        echo "<p class='text-danger'>❌ ERROR: description column not found!</p>";
        return;
    }
    
    // Step 3: Add long_description field if not exists
    echo "<h3>4. Checking for long_description field...</h3>";
    $stmt = $db->query("SHOW COLUMNS FROM shop_products LIKE 'long_description'");
    $longDescColumn = $stmt->fetch();
    
    if (!$longDescColumn) {
        echo "<h3>5. Adding long_description field for rich HTML content...</h3>";
        try {
            $db->query("ALTER TABLE shop_products ADD COLUMN long_description MEDIUMTEXT NULL AFTER description");
            echo "<p class='text-success'>✅ Long description field added successfully!</p>";
        } catch (\Exception $e) {
            echo "<p class='text-danger'>❌ ERROR: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<h3>5. Long description field already exists</h3>";
        echo "<p>⏭️ Skipping...</p>";
    }
    
    // Step 4: Add service metadata fields
    echo "<h3>6. Checking for service metadata fields...</h3>";
    
    // Delivery time field
    $stmt = $db->query("SHOW COLUMNS FROM shop_products LIKE 'delivery_time'");
    if (!$stmt->fetch()) {
        try {
            $db->query("ALTER TABLE shop_products ADD COLUMN delivery_time VARCHAR(100) NULL AFTER long_description");
            echo "<p class='text-success'>✅ Added delivery_time field</p>";
        } catch (\Exception $e) {
            echo "<p class='text-warning'>⚠️ delivery_time: " . $e->getMessage() . "</p>";
        }
    }
    
    // Service notes field
    $stmt = $db->query("SHOW COLUMNS FROM shop_products LIKE 'service_notes'");
    if (!$stmt->fetch()) {
        try {
            $db->query("ALTER TABLE shop_products ADD COLUMN service_notes TEXT NULL AFTER delivery_time");
            echo "<p class='text-success'>✅ Added service_notes field</p>";
        } catch (\Exception $e) {
            echo "<p class='text-warning'>⚠️ service_notes: " . $e->getMessage() . "</p>";
        }
    }
    
    // Step 5: Add index on type column if not exists
    echo "<h3>5. Checking indexes on type column...</h3>";
    $stmt = $db->query("SHOW INDEX FROM shop_products WHERE Column_name = 'type'");
    $typeIndex = $stmt->fetch();
    
    if (!$typeIndex) {
        echo "<h3>6. Adding index on type column...</h3>";
        try {
            $db->query("CREATE INDEX idx_shop_products_type ON shop_products(type)");
            echo "<p class='text-success'>✅ Index added successfully!</p>";
        } catch (\Exception $e) {
            echo "<p class='text-danger'>❌ ERROR: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<h3>6. Index on type column already exists</h3>";
        echo "<p>⏭️ Skipping...</p>";
    }
    
    echo "<hr>";
    echo "<div class='alert alert-success'>";
    echo "<h3>✅ Migration completed successfully!</h3>";
    echo "<ul>";
    echo "<li>Product types now support: <strong>physical, digital, service</strong></li>";
    echo "<li><strong>Short description</strong> for product listings (plain text)</li>";
    echo "<li><strong>Long description</strong> for product pages (rich HTML with images/videos up to 16MB)</li>";
    echo "<li>Service metadata fields (delivery_time, service_notes)</li>";
    echo "<li>Type column indexed for faster queries</li>";
    echo "</ul>";
    echo "<p><a href='/shop/vendor/products/create' class='btn btn-primary'>Test: Create a Service Product</a></p>";
    echo "</div>";
});

