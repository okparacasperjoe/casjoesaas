<?php

namespace App\Modules\CasjoeShop\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class CheckoutController
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

    public function index()
    {
        // Allow if user is logged in OR if guest checkout session is active
        if (!isset($_SESSION['user_id']) && empty($_SESSION['is_guest'])) { 
            header('Location: /shop/login?redirect=checkout'); 
            exit; 
        }

        $userId = $_SESSION['user_id'] ?? null;
        $isGuest = empty($userId);

        // Re-calculate total
        $subtotal = 0;
        $serviceFee = 0;
        $shippingZones = [];
        $hasPhysicalItems = false;
        
        if (!empty($_SESSION['cart'])) {
            $ids = implode(',', array_keys($_SESSION['cart']));
            $inQuery = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
            
            // Join shop_vendors to get fee_bearer
            $stmt = $this->db->prepare("SELECT p.price, p.id, p.vendor_id, p.type, v.fee_bearer 
                                      FROM shop_products p 
                                      JOIN shop_vendors v ON p.vendor_id = v.id 
                                      WHERE p.id IN ($inQuery)");
            $stmt->execute(array_keys($_SESSION['cart']));
            $products = $stmt->fetchAll();
            
            $vendorIds = [];

            foreach ($products as $p) {
                $qty = $_SESSION['cart'][$p['id']];
                $lineTotal = $p['price'] * $qty;
                $subtotal += $lineTotal;
                
                if ($p['fee_bearer'] === 'customer') {
                    $serviceFee += $lineTotal * 0.05; // 5% Service Fee
                }

                if ($p['type'] !== 'digital' && $p['type'] !== 'service') {
                    $hasPhysicalItems = true;
                    if (!in_array($p['vendor_id'], $vendorIds)) {
                        $vendorIds[] = $p['vendor_id'];
                    }
                }
            }

            // Fetch Shipping Zones for relevant vendors (Merge them for now, assuming single vendor checkout or max cost)
            // MVP Simplification: If multi-vendor, we just list all unique zones and user picks one "Region".
            // In reality, each vendor needs shipping. For this MVP, we'll assume the user picks a delivery location 
            // and we match that name across vendors, or just show a consolidated list?
            // Let's do: Fetch ALL zones for these vendors.
            if ($hasPhysicalItems && !empty($vendorIds)) {
                $vIds = implode(',', $vendorIds);
                $stmt = $this->db->query("SELECT * FROM shop_shipping_zones WHERE vendor_id IN ($vIds)");
                $shippingZones = $stmt->fetchAll();
            }
        }
        
        // Apply Coupon (If exists in session)
        $discountAmount = 0;
        $couponError = null;
        if (!empty($_SESSION['coupon'])) {
            $c = $_SESSION['coupon'];
            if ($subtotal >= $c['min_spend']) {
                if ($c['type'] == 'percent') {
                    $discountAmount = $subtotal * ($c['value'] / 100);
                } else {
                    $discountAmount = $c['value'];
                }
                if ($discountAmount > $subtotal) $discountAmount = $subtotal;
            } else {
                $couponError = "Minimum spend of $" . number_format($c['min_spend'], 2) . " required.";
                unset($_SESSION['coupon']);
            }
        }
        
        $total = $subtotal + $serviceFee - $discountAmount;
        if ($total < 0) $total = 0;
        
        if ($subtotal == 0 && empty($_SESSION['cart'])) { header('Location: /shop/cart'); exit; }

        require_once __DIR__ . '/../Views/checkout/index.php';
    }

    public function process()
    {
        // Allow if user is logged in OR if guest checkout session is active
        if (!isset($_SESSION['user_id']) && empty($_SESSION['is_guest'])) { 
            header('Location: /shop/login?redirect=checkout'); 
            exit; 
        }
        
        $userId = $_SESSION['user_id'] ?? null;
        $isGuest = empty($userId);
        
        // RE-CALCULATE SERVER SIDE (Security Best Practice)
        $subtotal = 0;
        $serviceFee = 0;
        $shippingCost = 0;
        $shippingCost = 0;
        $shippingZoneName = null;
        $discountAmount = 0;
        $couponCode = null;
        $couponId = null;
        
        if (!empty($_SESSION['cart'])) {
             $ids = implode(',', array_keys($_SESSION['cart']));
             $inQuery = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
             
             // Join shop_vendors to get fee_bearer
             $stmt = $this->db->prepare("SELECT p.price, p.id, p.vendor_id, p.type, v.fee_bearer 
                                       FROM shop_products p 
                                       JOIN shop_vendors v ON p.vendor_id = v.id 
                                       WHERE p.id IN ($inQuery)");
             $stmt->execute(array_keys($_SESSION['cart']));
             $products = $stmt->fetchAll();
             
             foreach ($products as $p) {
                 $qty = $_SESSION['cart'][$p['id']];
                 $lineTotal = $p['price'] * $qty;
                 $subtotal += $lineTotal;
                 
                 if ($p['fee_bearer'] === 'customer') {
                     $serviceFee += $lineTotal * 0.05; // 5% Service Fee
                 }
             }

             // Handle Shipping
             if (isset($_POST['shipping_zone_id'])) {
                 $zoneId = $_POST['shipping_zone_id'];
                 $stmt = $this->db->prepare("SELECT * FROM shop_shipping_zones WHERE id = ?");
                 $stmt->execute([$zoneId]);
                 $zone = $stmt->fetch();
                 if ($zone) {
                     $shippingCost = $zone['cost'];
                     $shippingZoneName = $zone['zone_name'];
                 }
             }

             // Handle Coupon
             if (!empty($_SESSION['coupon'])) {
                 $code = $_SESSION['coupon']['code'];
                 // Re-verify Validity (race condition check)
                 $stmt = $this->db->prepare("SELECT * FROM shop_coupons WHERE code = ? AND tenant_id = ?");
                 $stmt->execute([$code, $this->tenantId]);
                 $coupon = $stmt->fetch();

                 if ($coupon && (!$coupon['expires_at'] || strtotime($coupon['expires_at']) >= time()) && ($coupon['usage_limit'] == 0 || $coupon['used_count'] < $coupon['usage_limit']) && ($subtotal >= $coupon['min_spend'])) {
                     
                     // Calculate Discount
                     if ($coupon['type'] == 'percent') {
                         $discountAmount = $subtotal * ($coupon['value'] / 100);
                     } else {
                         $discountAmount = $coupon['value'];
                     }
                     // Ensure discount doesn't exceed subtotal
                     if ($discountAmount > $subtotal) $discountAmount = $subtotal;
                     
                     $couponCode = $code;
                     $couponId = $coupon['id'];
                 } else {
                     // Invalid, clear it
                     unset($_SESSION['coupon']);
                 }
             }
        }
        
        // Calculate Total
        $total = $subtotal + $serviceFee + $shippingCost - $discountAmount;
        if ($total < 0) $total = 0;

        // --- PLATFORM COMMISSION LOGIC ---
        // 3% of Subtotal (excluding service fees and shipping)
        $commissionAmount = $subtotal * 0.03;
        // ---------------------------------

        // --- CURRENCY TRACKING ---
        $customerCurrency = \App\Modules\CasjoeShop\Services\CurrencyService::getSelectedCurrency();
        // For simplicity, using NGN as default original currency
        $originalCurrency = 'NGN'; 
        $exchangeRate = 1.0;
        
        if ($customerCurrency !== $originalCurrency) {
            require_once __DIR__ . '/../Services/CurrencyService.php';
            $currencyService = new \App\Modules\CasjoeShop\Services\CurrencyService();
            $exchangeRate = $currencyService->getRate($originalCurrency, $customerCurrency);
        }
        // ---------------------------------

        // Capture Guest Info & Shipping Address
        $guestEmail = $isGuest ? ($_POST['guest_email'] ?? null) : null;
        $guestName = $isGuest ? ($_POST['guest_name'] ?? null) : null;
        
        // Shipping Info (For both Guest & Registered users)
        $shippingAddress = $_POST['shipping_address'] ?? null;
        $shippingCity = $_POST['shipping_city'] ?? null;
        $shippingState = $_POST['shipping_state'] ?? null;
        $shippingPhone = $_POST['shipping_phone'] ?? null;

        // 1. Create Order
        // Prepare SQL with new columns
        $sql = "INSERT INTO shop_orders (
            tenant_id, user_id, total_amount, service_fee, shipping_cost, shipping_zone_name, 
            discount_amount, coupon_code, commission_amount, original_currency, paid_currency, exchange_rate, status,
            guest_email, guest_name, shipping_address, shipping_city, shipping_state, shipping_phone
        ) VALUES (
            ?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, ?, 'pending',
            ?, ?, ?, ?, ?, ?
        )";
        
        $this->db->query($sql, [
            $this->tenantId, $userId, $total, $serviceFee, $shippingCost, $shippingZoneName, 
            $discountAmount, $couponCode, $commissionAmount, $originalCurrency, $customerCurrency, $exchangeRate,
            $guestEmail, $guestName, $shippingAddress, $shippingCity, $shippingState, $shippingPhone
        ]);
        
        $orderId = $this->db->lastInsertId();

        // Increment Coupon Usage
        if ($couponId) {
            $this->db->query("UPDATE shop_coupons SET used_count = used_count + 1 WHERE id = ?", [$couponId]);
        }

        // 2. Create Order Items & Generate Digital Access
        foreach ($_SESSION['cart'] as $prodId => $qty) {
            $stmt = $this->db->query("SELECT * FROM shop_products WHERE id = ?", [$prodId]);
            $product = $stmt->fetch();
            
            // Insert Item
            $this->db->query("INSERT INTO shop_order_items (order_id, vendor_id, product_id, quantity, price) VALUES (?, ?, ?, ?, ?)",
                [$orderId, $product['vendor_id'], $prodId, $qty, $product['price']]);
            $itemId = $this->db->lastInsertId();

            // Link Digital Access if needed
            if ($product['type'] == 'digital') {
                $token = bin2hex(random_bytes(32));
                $this->db->query("INSERT INTO shop_digital_access (order_item_id, token, max_downloads) VALUES (?, ?, 5)", [$itemId, $token]);
            }
        }
        
        // 3. Process Payment (Flutterwave)
        $txRef = 'CJ-SHOP-' . $orderId . '-' . time();
        $callbackUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/shop/checkout/success'; // Ensure correct protocol/host

        // Update Order with Ref
        $this->db->query("UPDATE shop_orders SET payment_ref = ? WHERE id = ?", [$txRef, $orderId]);

        \App\Core\Services\EventBus::publish($this->tenantId, 'Shop', 'order_placed', [
            'order_id' => $orderId,
            'total' => $total,
            'currency' => $customerCurrency
        ]);

        try {
            require_once __DIR__ . '/../../../Core/Services/PaymentGatewayService.php';
            $gateway = new \App\Core\Services\PaymentGatewayService();
            
            // Determine email (User or Guest)
            $payerEmail = $isGuest ? $guestEmail : $_SESSION['user_email'];
            
            $paymentLink = $gateway->initializePayment(
                $payerEmail,
                $total,
                $customerCurrency, // Currency
                $txRef,
                $callbackUrl,
                [
                    'name' => $isGuest ? $guestName : 'Customer',
                    'description' => "Shop Order #$orderId"
                ]
            );

            // Redirect to Payment
            header("Location: " . $paymentLink);
            exit;

        } catch (\Exception $e) {
            // Log error?
            $_SESSION['checkout_error'] = "Payment Error: " . $e->getMessage();
            header('Location: /shop/checkout');
            exit;
        }
    }

    public function success()
    {
        $status = $_GET['status'] ?? '';
        $txRef = $_GET['tx_ref'] ?? '';
        $transactionId = $_GET['transaction_id'] ?? '';

        if ($status === 'successful' || $status === 'completed') {
            
            require_once __DIR__ . '/../../../Core/Services/PaymentGatewayService.php';
            $gateway = new \App\Core\Services\PaymentGatewayService();
            
            // Verify
            $verifyData = $gateway->verifyTransaction($transactionId);
            
            if (isset($verifyData['status']) && $verifyData['status'] === 'success') {
                $amountPaid = $verifyData['data']['amount'];
                $paymentCurrency = $verifyData['data']['currency']; // Should match
                $flwRef = $verifyData['data']['tx_ref']; // Should match our $txRef

                // Find Order by Ref
                $stmt = $this->db->prepare("SELECT * FROM shop_orders WHERE payment_ref = ?");
                $stmt->execute([$flwRef]);
                $order = $stmt->fetch();

                if ($order) {
                    // Update Status
                    if ($order['status'] !== 'paid') {
                        $this->db->query("UPDATE shop_orders SET status = 'paid', paid_currency = ? WHERE id = ?", 
                            [$paymentCurrency, $order['id']]);

                        // Initialize customer variables
                        $customerEmail = null;
                        $customerName = null;
                        $customerPhone = null;

                        if (!empty($order['user_id'])) {
                            $userStmt = $this->db->prepare("SELECT email, name, phone FROM users WHERE id = ?");
                            $userStmt->execute([$order['user_id']]);
                            $userRow = $userStmt->fetch();
                            if ($userRow) {
                                $customerEmail = $userRow['email'];
                                $customerName = $userRow['name'];
                                $customerPhone = $userRow['phone'];
                            }
                        } else {
                            $customerEmail = $order['guest_email'];
                            $customerName = $order['guest_name'];
                            $customerPhone = $order['shipping_phone'];
                        }

                        // --- ERP CRM & CasjoeMail Subscriber Sync ---
                        try {
                            if (filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                                // 1. ERP CRM Customer Sync
                                $checkCrm = $this->db->prepare("SELECT id FROM erp_crm_customers WHERE email = ? AND tenant_id = ?");
                                $checkCrm->execute([$customerEmail, $order['tenant_id']]);
                                if (!$checkCrm->fetch()) {
                                    $crmName = $customerName ?: explode('@', $customerEmail)[0];
                                    $insCrm = $this->db->prepare("INSERT INTO erp_crm_customers (tenant_id, name, email, phone, company) VALUES (?, ?, ?, ?, ?)");
                                    $insCrm->execute([$order['tenant_id'], $crmName, $customerEmail, $customerPhone, 'Shop Order']);
                                }

                                // 2. CasjoeMail Subscriber Sync
                                $checkList = $this->db->prepare("SELECT id FROM cm_lists WHERE tenant_id = ? AND name = ?");
                                $checkList->execute([$order['tenant_id'], 'Shop Customers']);
                                $listRow = $checkList->fetch();
                                if ($listRow) {
                                    $listId = $listRow['id'];
                                } else {
                                    $insList = $this->db->prepare("INSERT INTO cm_lists (tenant_id, name) VALUES (?, ?)");
                                    $insList->execute([$order['tenant_id'], 'Shop Customers']);
                                    $listId = $this->db->lastInsertId();
                                }

                                $checkSub = $this->db->prepare("SELECT id FROM cm_subscribers WHERE list_id = ? AND email = ? AND tenant_id = ?");
                                $checkSub->execute([$listId, $customerEmail, $order['tenant_id']]);
                                $subRow = $checkSub->fetch();
                                if (!$subRow) {
                                    $firstName = '';
                                    $lastName = '';
                                    if ($customerName) {
                                        $parts = explode(' ', $customerName, 2);
                                        $firstName = $parts[0];
                                        $lastName = $parts[1] ?? '';
                                    }
                                    $insSub = $this->db->prepare("INSERT INTO cm_subscribers (tenant_id, list_id, email, first_name, last_name, phone, tags) VALUES (?, ?, ?, ?, ?, ?, ?)");
                                    $insSub->execute([$order['tenant_id'], $listId, $customerEmail, $firstName, $lastName, $customerPhone, 'mart-customer']);
                                    $subId = $this->db->lastInsertId();

                                    $timestamp = date('Y-m-d H:i:s');
                                    $consentProof = json_encode([
                                        'source' => 'shop_checkout',
                                        'order_id' => $order['id'],
                                        'timestamp' => $timestamp
                                    ]);
                                    $updSub = $this->db->prepare("UPDATE cm_subscribers SET consent_method = 'import', consent_proof = ?, consent_date = ? WHERE id = ?");
                                    $updSub->execute([$consentProof, $timestamp, $subId]);
                                }
                            }
                        } catch (\Exception $ex) {
                            // Suppress errors during sync to avoid blocking flow
                        }

                        // --- 1. Wallet Settlement & 2. Invoicing & 3. Stock updates & 4. Notifications & 5. Mail logging & 6. AI Insights ---
                        try {
                            $tenantId = $order['tenant_id'];

                            // Retrieve order items
                            $stmtItems = $this->db->prepare("SELECT product_id, quantity, price, vendor_id FROM shop_order_items WHERE order_id = ?");
                            $stmtItems->execute([$order['id']]);
                            $orderItems = $stmtItems->fetchAll();

                            // Settle funds into vendor user wallets
                            foreach ($orderItems as $item) {
                                // Wallet Settlement
                                $lineTotal = $item['quantity'] * $item['price'];
                                $commission = $lineTotal * 0.03;
                                $vendorEarning = $lineTotal - $commission;

                                // Find vendor's user ID
                                $stmtVendor = $this->db->prepare("SELECT user_id, store_name FROM shop_vendors WHERE id = ?");
                                $stmtVendor->execute([$item['vendor_id']]);
                                $vendorData = $stmtVendor->fetch();

                                if ($vendorData) {
                                    $vendorUserId = $vendorData['user_id'];
                                    
                                    // Ensure wallet exists
                                    $this->db->query("INSERT IGNORE INTO cp_wallets (tenant_id, user_id, currency, balance) VALUES (?, ?, ?, 0.00)", 
                                        [$tenantId, $vendorUserId, $paymentCurrency]);
                                    
                                    // Credit balance
                                    $this->db->query("UPDATE cp_wallets SET balance = balance + ? WHERE user_id = ? AND currency = ?", 
                                        [$vendorEarning, $vendorUserId, $paymentCurrency]);

                                    // Record transaction
                                    $txRef = 'SHOP-SETTLE-' . $order['id'] . '-' . $item['vendor_id'] . '-' . time() . '-' . rand(100, 999);
                                    $desc = "Mart Settle: Order #" . $order['id'] . " - " . $vendorData['store_name'];
                                    $meta = json_encode([
                                        'order_id' => $order['id'],
                                        'vendor_id' => $item['vendor_id'],
                                        'line_total' => $lineTotal,
                                        'commission' => $commission,
                                        'net_earning' => $vendorEarning
                                    ]);
                                    
                                    $this->db->query("INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description, meta, created_at) VALUES (?, ?, ?, 'credit', ?, ?, 'successful', ?, ?, NOW())", 
                                        [$tenantId, $vendorUserId, $txRef, $vendorEarning, $paymentCurrency, $desc, $meta]);
                                }

                                // Stock updates
                                // Update shop product stock
                                $this->db->query("UPDATE shop_products SET stock_quantity = GREATEST(0, stock_quantity - ?) WHERE id = ?", 
                                    [$item['quantity'], $item['product_id']]);

                                // Find product SKU
                                $stmtProd = $this->db->prepare("SELECT sku, name FROM shop_products WHERE id = ?");
                                $stmtProd->execute([$item['product_id']]);
                                $productData = $stmtProd->fetch();

                                if ($productData && !empty($productData['sku'])) {
                                    // Check matching erp inventory item
                                    $stmtErpInv = $this->db->prepare("SELECT id, stock_quantity FROM erp_inventory_items WHERE sku = ? AND tenant_id = ?");
                                    $stmtErpInv->execute([$productData['sku'], $tenantId]);
                                    $erpInvItem = $stmtErpInv->fetch();

                                    if ($erpInvItem) {
                                        $newStock = max(0, $erpInvItem['stock_quantity'] - $item['quantity']);
                                        $this->db->query("UPDATE erp_inventory_items SET stock_quantity = ? WHERE id = ?", [$newStock, $erpInvItem['id']]);

                                        // Low stock notification (under 5)
                                        if ($newStock < 5 && isset($vendorUserId)) {
                                            \App\Core\Notification::send(
                                                $vendorUserId, 
                                                '⚠️ Low Stock Alert', 
                                                "Product {$productData['name']} (SKU: {$productData['sku']}) stock is low: {$newStock} remaining.", 
                                                '/erp/inventory'
                                            );
                                        }
                                    }
                                }
                            }

                            // Invoice Generation (Casjoe BOS)
                            $invoiceUuid = bin2hex(random_bytes(16));
                            $today = date('Y-m-d');
                            $due = date('Y-m-d', strtotime('+7 days'));
                            
                            $this->db->query(
                                "INSERT INTO erp_invoices (tenant_id, uuid, client_name, client_email, issue_date, due_date, total_amount, currency, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'paid')",
                                [$tenantId, $invoiceUuid, $customerName, $customerEmail, $today, $due, $order['total_amount'], $paymentCurrency, "Generated from Mart Order #" . $order['id']]
                            );
                            $invoiceId = $this->db->lastInsertId();

                            // Insert invoice items
                            foreach ($orderItems as $item) {
                                $stmtProd = $this->db->prepare("SELECT name FROM shop_products WHERE id = ?");
                                $stmtProd->execute([$item['product_id']]);
                                $pRow = $stmtProd->fetch();
                                $pName = $pRow ? $pRow['name'] : 'Shop Product';
                                
                                $this->db->query(
                                    "INSERT INTO erp_invoice_items (invoice_id, description, quantity, unit_price, amount) VALUES (?, ?, ?, ?, ?)",
                                    [$invoiceId, $pName, $item['quantity'], $item['price'], $item['quantity'] * $item['price']]
                                );
                            }

                            // Publish invoice created event
                            \App\Core\Services\EventBus::publish($tenantId, 'Finance', 'invoice_created', [
                                'invoice_id' => $invoiceId,
                                'client_name' => $customerName,
                                'total_amount' => $order['total_amount']
                            ]);

                            // Follow-up Email Sent log
                            $mailSubject = "Thank you for your order! - Casjoe Mart";
                            $mailBody = "
                                <h2>Thank you for your purchase, {$customerName}!</h2>
                                <p>We have successfully processed your order #{$order['id']}.</p>
                                <p>Total Amount: <strong>{$paymentCurrency} " . number_format($order['total_amount'], 2) . "</strong></p>
                                <br>
                                <p>Best regards,<br>The Casjoe Team</p>
                            ";
                            
                            try {
                                \App\Core\Mailer::send($customerEmail, $mailSubject, $mailBody);
                                if (!empty($order['user_id'])) {
                                    $this->db->query("INSERT IGNORE INTO system_email_logs (user_id, email_type) VALUES (?, ?)", 
                                        [$order['user_id'], 'shop_order_thankyou']);
                                }
                            } catch (\Exception $mailErr) {}

                            // AI Analytics Insight Log
                            $aiInsight = "Customer {$customerName} completed a new store purchase of {$paymentCurrency} " . number_format($order['total_amount'], 2) . ", boosting customer segment retention.";
                            try {
                                $aiService = new \App\Core\Services\AIService();
                                $aiPrompt = "A customer named {$customerName} placed an order on the store for a total of {$paymentCurrency} {$order['total_amount']}. Write a 1-sentence, high-level business insight analyzing the segment of this purchase (e.g. fashion trend, customer loyalty, segment impact). Keep it under 25 words.";
                                $aiInsightText = $aiService->generateText($aiPrompt);
                                if (!empty($aiInsightText)) {
                                    $aiInsight = trim($aiInsightText);
                                }
                            } catch (\Exception $aiErr) {}

                            $this->db->query(
                                "INSERT INTO erp_ai_insights (tenant_id, category, severity, title, message) VALUES (?, 'analytics', 'info', ?, ?)",
                                [$tenantId, 'Mart Sales Trend Analyzed', $aiInsight]
                            );

                        } catch (\Exception $settleEx) {
                            // Suppress errors to avoid blocking visual flow
                        }
                    }

                    // Clear Session (Success)
                    unset($_SESSION['cart']);
                    unset($_SESSION['coupon']);
                    unset($_SESSION['is_guest']);
                    
                    $_SESSION['last_order_id'] = $order['id'];
                    
                    // Show Success View
                    require_once __DIR__ . '/../Views/checkout/success.php';
                    return;
                }
            }
        }
        
        // If we got here, payment failed or verification failed
        $message = "Payment validation failed. Please contact support if you were charged.";
        if ($status === 'cancelled') $message = "Payment was cancelled.";
        
        // Redirect back to checkout or show error content
        echo "<div style='text-align:center; padding: 50px; font-family: sans-serif;'>
                <h1 style='color: red'>Payment Failed</h1>
                <p>$message</p>
                <a href='/shop/checkout'>Try Again</a>
              </div>";
    }

    public function applyCoupon()
    {
        $code = strtoupper(trim($_POST['code'] ?? ''));
        if (empty($code)) { header('Location: /shop/checkout'); exit; }

        $stmt = $this->db->prepare("SELECT * FROM shop_coupons WHERE code = ? AND tenant_id = ?");
        $stmt->execute([$code, $this->tenantId]);
        $coupon = $stmt->fetch();

        if (!$coupon) {
            $_SESSION['coupon_error'] = "Invalid coupon code.";
        } elseif ($coupon['expires_at'] && strtotime($coupon['expires_at']) < time()) {
            $_SESSION['coupon_error'] = "Coupon has expired.";
        } elseif ($coupon['usage_limit'] > 0 && $coupon['used_count'] >= $coupon['usage_limit']) {
            $_SESSION['coupon_error'] = "Coupon usage limit reached.";
        } else {
            $_SESSION['coupon'] = [
                'code' => $coupon['code'],
                'type' => $coupon['type'],
                'value' => $coupon['value'],
                'min_spend' => $coupon['min_spend']
            ];
            $_SESSION['coupon_success'] = "Coupon applied!";
        }

        header('Location: /shop/checkout');
        exit;
    }

    public function removeCoupon()
    {
        unset($_SESSION['coupon']);
        header('Location: /shop/checkout');
        exit;
    }
}

