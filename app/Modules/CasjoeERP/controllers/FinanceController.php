<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

class FinanceController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
    }

    private function checkAdmin()
    {
        $role = \App\Core\Auth::user()['role'] ?? '';
        if ($role !== 'admin') {
            header('Location: /erp/dashboard');
            exit;
        }
    }

    private function getTenantCurrencyInfo(): array
    {
        $stmt = $this->pdo->prepare("SELECT currency FROM tenants WHERE id = ?");
        $stmt->execute([$this->tenantId]);
        $tenantData = $stmt->fetch(\PDO::FETCH_ASSOC) ?: ['currency' => 'NGN'];

        $rawCode = strtoupper(trim($tenantData['currency'] ?? 'NGN'));
        if (empty($rawCode) || $rawCode === 'NULL') $rawCode = 'NGN';

        $cCode = $rawCode;
        $currencySymbol = $rawCode;

        if ($rawCode === 'NGN' || $rawCode === '₦' || $rawCode === 'NAIRA') {
            $currencySymbol = '₦';
            $cCode = 'NGN';
        } elseif ($rawCode === 'USD' || $rawCode === '$' || $rawCode === 'DOLLAR' || $rawCode === 'DOLLARS') {
            $currencySymbol = '$';
            $cCode = 'USD';
        } elseif ($rawCode === 'GBP' || $rawCode === '£' || $rawCode === 'POUND' || $rawCode === 'POUNDS') {
            $currencySymbol = '£';
            $cCode = 'GBP';
        } elseif ($rawCode === 'EUR' || $rawCode === '€' || $rawCode === 'EURO' || $rawCode === 'EUROS') {
            $currencySymbol = '€';
            $cCode = 'EUR';
        } elseif ($rawCode === 'CAD') {
            $currencySymbol = 'CA$';
        } elseif ($rawCode === 'AUD') {
            $currencySymbol = 'A$';
        } elseif ($rawCode === 'GHS') {
            $currencySymbol = 'GH₵';
        } elseif ($rawCode === 'KES') {
            $currencySymbol = 'KSh';
        } elseif ($rawCode === 'ZAR') {
            $currencySymbol = 'R';
        }

        return ['code' => $cCode, 'symbol' => $currencySymbol];
    }

    public function inventory()
    {
        $this->checkAdmin();
        $stmt = $this->pdo->prepare("SELECT * FROM erp_inventory WHERE tenant_id = ? ORDER BY item_name ASC");
        $stmt->execute([$this->tenantId]);
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/inventory/index.php';
    }

    public function createInventory()
    {
        $this->checkAdmin();
        require __DIR__ . '/../Views/inventory/create.php';
    }

    public function storeInventory()
    {
        $this->checkAdmin();
        $name = $_POST['item_name'];
        $sku = $_POST['sku'];
        $qty = $_POST['quantity'];
        $price = $_POST['unit_price'];

        $stmt = $this->pdo->prepare("INSERT INTO erp_inventory (tenant_id, item_name, sku, quantity, unit_price) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $name, $sku, $qty, $price]);

        header('Location: /erp/inventory');
        exit;
    }

    public function syncExpensesToTransactions()
    {
        try {
            // Find all active/approved/pending expenses in erp_expenses for this tenant
            $stmt = $this->pdo->prepare("
                SELECT id, description, amount, date, category, status 
                FROM erp_expenses 
                WHERE tenant_id = ? AND (status IS NULL OR LOWER(TRIM(status)) != 'rejected')
            ");
            $stmt->execute([$this->tenantId]);
            $expenses = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($expenses)) return;

            // Fetch existing expense transactions for this tenant
            $stmtTx = $this->pdo->prepare("
                SELECT id, description, amount, date 
                FROM erp_transactions 
                WHERE tenant_id = ? AND LOWER(TRIM(type)) IN ('expense', 'debit', 'dr', 'outflow', 'payout')
            ");
            $stmtTx->execute([$this->tenantId]);
            $existingTx = $stmtTx->fetchAll(\PDO::FETCH_ASSOC);

            // Index existing transactions by date + rounded amount + normalized description
            $txMap = [];
            foreach ($existingTx as $tx) {
                $normDesc = strtolower(trim(preg_replace('/\s*\((?:expense|manual)\)$/i', '', $tx['description'])));
                $key = $tx['date'] . '_' . number_format((float)$tx['amount'], 2, '.', '') . '_' . $normDesc;
                $txMap[$key] = true;
                $txMap['amt_' . $tx['date'] . '_' . number_format((float)$tx['amount'], 2, '.', '')] = true;
            }

            $insertStmt = $this->pdo->prepare("
                INSERT INTO erp_transactions (tenant_id, description, amount, type, date, category) 
                VALUES (?, ?, ?, 'expense', ?, ?)
            ");

            foreach ($expenses as $exp) {
                $normDesc = strtolower(trim($exp['description']));
                $key = $exp['date'] . '_' . number_format((float)$exp['amount'], 2, '.', '') . '_' . $normDesc;
                $amtKey = 'amt_' . $exp['date'] . '_' . number_format((float)$exp['amount'], 2, '.', '');
                
                if (!isset($txMap[$key]) && !isset($txMap[$amtKey])) {
                    $insertStmt->execute([
                        $this->tenantId,
                        $exp['description'],
                        (float)$exp['amount'],
                        $exp['date'],
                        $exp['category'] ?? 'General'
                    ]);
                    $txMap[$key] = true;
                    $txMap[$amtKey] = true;
                }
            }
        } catch (\Throwable $e) {
            error_log("Expense sync notice: " . $e->getMessage());
        }
    }

    public function transactions()
    {
        $this->checkAdmin();
        $this->syncExpensesToTransactions();

        $stmt = $this->pdo->prepare("SELECT * FROM erp_transactions WHERE tenant_id = ? ORDER BY date DESC, id DESC");
        $stmt->execute([$this->tenantId]);
        $transactions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $currInfo = $this->getTenantCurrencyInfo();
        $cCode = $currInfo['code'];
        $currencySymbol = $currInfo['symbol'];

        $successMsg = $_SESSION['import_success'] ?? null;
        $errorMsg = $_SESSION['import_error'] ?? null;
        unset($_SESSION['import_success'], $_SESSION['import_error']);

        require __DIR__ . '/../Views/finance/transactions.php';
    }

    public function createTransaction()
    {
        $this->checkAdmin();
        $currInfo = $this->getTenantCurrencyInfo();
        $cCode = $currInfo['code'];
        $currencySymbol = $currInfo['symbol'];

        require __DIR__ . '/../Views/finance/create_transaction.php';
    }

    public function storeTransaction()
    {
        $this->checkAdmin();
        $desc = $_POST['description'];
        $amount = (float)$_POST['amount'];
        $type = $_POST['type'] ?? 'income';
        $date = $_POST['date'] ?? date('Y-m-d');
        $category = $_POST['category'] ?? 'General';

        $stmt = $this->pdo->prepare("INSERT INTO erp_transactions (tenant_id, description, amount, type, date, category) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $desc, $amount, $type, $date, $category]);

        $_SESSION['import_success'] = "Transaction recorded successfully.";
        header('Location: /erp/transactions');
        exit;
    }
    
    public function index()
    {
        $this->checkAdmin();
        // Chart of Accounts
        $stmt = $this->pdo->prepare("SELECT * FROM erp_accounts WHERE tenant_id = ? ORDER BY code ASC");
        $stmt->execute([$this->tenantId]);
        $accounts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $currInfo = $this->getTenantCurrencyInfo();
        $cCode = $currInfo['code'];
        $currencySymbol = $currInfo['symbol'];

        require __DIR__ . '/../Views/finance/index.php';
    }

    public function dashboard()
    {
         $this->checkAdmin();

         // Auto-sync any recorded expenses to transaction ledger
         $this->syncExpensesToTransactions();

         // 1. Income from transactions (case-insensitive & trimmed)
         $stmtInc = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) 
            FROM erp_transactions 
            WHERE tenant_id = ? AND LOWER(TRIM(type)) IN ('income', 'credit', 'cr')
         ");
         $stmtInc->execute([$this->tenantId]);
         $income = (float)$stmtInc->fetchColumn();

         // Also check paid invoices
         $stmtInv = $this->pdo->prepare("
            SELECT COALESCE(SUM(total_amount), 0) 
            FROM erp_invoices 
            WHERE tenant_id = ? AND LOWER(TRIM(status)) = 'paid'
         ");
         $stmtInv->execute([$this->tenantId]);
         $paidInvoicesTotal = (float)$stmtInv->fetchColumn();
         if ($paidInvoicesTotal > $income) {
             $income = $paidInvoicesTotal;
         }

         // 2. Expenses from transactions
         $stmtTxExp = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) 
            FROM erp_transactions 
            WHERE tenant_id = ? AND LOWER(TRIM(type)) IN ('expense', 'debit', 'dr', 'outflow', 'payout')
         ");
         $stmtTxExp->execute([$this->tenantId]);
         $txExpense = (float)$stmtTxExp->fetchColumn();

         // Direct query on erp_expenses
         $stmtDirExp = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) 
            FROM erp_expenses 
            WHERE tenant_id = ? AND (status IS NULL OR LOWER(TRIM(status)) != 'rejected')
         ");
         $stmtDirExp->execute([$this->tenantId]);
         $directExpense = (float)$stmtDirExp->fetchColumn();

         // Total expense is accurately calculated from both sources
         $expense = max($txExpense, $directExpense);

         // 3. Net Profit / Loss
         $profit = $income - $expense;

         // 4. Cash Balance from Chart of Accounts or Net Cashflow
         $stmtAcc = $this->pdo->prepare("
            SELECT COALESCE(SUM(balance), 0) 
            FROM erp_accounts 
            WHERE tenant_id = ? AND LOWER(TRIM(type)) = 'asset'
         ");
         $stmtAcc->execute([$this->tenantId]);
         $accountCashBalance = (float)$stmtAcc->fetchColumn();

         $cashBalance = $accountCashBalance > 0 ? $accountCashBalance : $profit;

         $currInfo = $this->getTenantCurrencyInfo();
         $cCode = $currInfo['code'];
         $currencySymbol = $currInfo['symbol'];

         // 5. Recent transactions
         $stmtRecent = $this->pdo->prepare("SELECT * FROM erp_transactions WHERE tenant_id = ? ORDER BY date DESC, id DESC LIMIT 10");
         $stmtRecent->execute([$this->tenantId]);
         $recentTransactions = $stmtRecent->fetchAll(\PDO::FETCH_ASSOC);

         require __DIR__ . '/../Views/finance/dashboard.php';
    }

    public function invoices()
    {
        $this->checkAdmin();
        $stmt = $this->pdo->prepare("SELECT * FROM erp_invoices WHERE tenant_id = ? ORDER BY created_at DESC");
        $stmt->execute([$this->tenantId]);
        $invoices = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $currInfo = $this->getTenantCurrencyInfo();
        $cCode = $currInfo['code'];
        require __DIR__ . '/../Views/finance/invoices.php';
    }

    public function createInvoice()
    {
        $this->checkAdmin();
        require __DIR__ . '/../Views/finance/create_invoice.php';
    }

    public function storeInvoice()
    {
        $this->checkAdmin();
        $clientName = trim($_POST['client_name'] ?? '');
        $clientEmail = !empty($_POST['client_email']) ? trim($_POST['client_email']) : null;
        $issueDate = $_POST['issue_date'] ?? date('Y-m-d');
        $dueDate = $_POST['due_date'] ?? date('Y-m-d', strtotime('+3 days'));
        $currency = $_POST['currency'] ?? 'NGN';
        $notes = $_POST['notes'] ?? '';
        $items = json_decode($_POST['items_json'] ?? '[]', true) ?: [];

        // Calculate Total
        $total = 0;
        foreach ($items as $item) {
            $total += ($item['quantity'] ?? 1) * ($item['price'] ?? 0);
        }

        $uuid = bin2hex(random_bytes(16)); // Secure UUID

        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("INSERT INTO erp_invoices (tenant_id, uuid, client_name, client_email, issue_date, due_date, total_amount, currency, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft')");
            $stmt->execute([$this->tenantId, $uuid, $clientName, $clientEmail, $issueDate, $dueDate, $total, $currency, $notes]);
            $invoiceId = $this->pdo->lastInsertId();

            $stmtItem = $this->pdo->prepare("INSERT INTO erp_invoice_items (invoice_id, description, quantity, unit_price, amount) VALUES (?, ?, ?, ?, ?)");
            foreach ($items as $item) {
                $amount = $item['quantity'] * $item['price'];
                $stmtItem->execute([$invoiceId, $item['description'], $item['quantity'], $item['price'], $amount]);
            }

            $this->pdo->commit();
            
            \App\Core\Services\EventBus::publish($this->tenantId, 'Finance', 'invoice_created', [
                'invoice_id' => $invoiceId,
                'client_name' => $clientName,
                'total_amount' => $total
            ]);

            // Email Client
            if (!empty($clientEmail)) {
                $invoiceUrl = "https://" . ($_SERVER['HTTP_HOST'] ?? 'app.casjoe.com') . "/invoice/" . $uuid;
                $subject = "New Invoice from Casjoe";
                $message = "<p>Hello " . htmlspecialchars($clientName) . ",</p>";
                $message .= "<p>A new invoice has been generated for you.</p>";
                $message .= "<p><strong>Amount:</strong> " . htmlspecialchars($currency) . " " . number_format($total, 2) . "</p>";
                $message .= "<p><strong>Due Date:</strong> " . htmlspecialchars($dueDate) . "</p>";
                $message .= "<p>You can view and pay your invoice online here: <a href='" . htmlspecialchars($invoiceUrl) . "'>View Invoice</a></p>";
                
                try {
                    \App\Core\Mailer::send($clientEmail, $subject, $message, false);
                } catch (\Exception $e) {
                    error_log("Failed to send invoice email: " . $e->getMessage());
                }
            }

            // Award lead scoring points (if linked to a lead)
            try {
                if (!empty($clientEmail)) {
                    $stmtLead = $this->pdo->prepare("SELECT id FROM erp_crm_leads WHERE tenant_id = ? AND email = ? LIMIT 1");
                    $stmtLead->execute([$this->tenantId, $clientEmail]);
                    $leadId = $stmtLead->fetchColumn();
                    if ($leadId) {
                        \App\Core\Services\LeadScoringService::addPoints($this->tenantId, (int)$leadId, 'Invoice issued', 15);
                    }
                }
            } catch (\Exception $e) {
                error_log('Lead scoring error: ' . $e->getMessage());
            }

            header('Location: /erp/finance/invoices');
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            die("Error creating invoice: " . $e->getMessage());
        }
    }

    public function showInvoice() {
         $this->checkAdmin();
         $uuid = $_GET['uuid'] ?? '';
         // For internal view, we could use ID but UUID is consistent
         // Security: Valid session required (checked in constructor/router usually? actually checked here just in case)
          if (!isset($_SESSION['user_id'])) {
             die("Access Denied");
         }
         
         $stmt = $this->pdo->prepare("SELECT * FROM erp_invoices WHERE uuid = ? AND tenant_id = ?");
         $stmt->execute([$uuid, $this->tenantId]);
         $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
         
         if (!$invoice) die("Invoice Not Found");

         $stmtItems = $this->pdo->prepare("SELECT * FROM erp_invoice_items WHERE invoice_id = ?");
         $stmtItems->execute([$invoice['id']]);
         $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

         // Reuse public view but add admin controls? Or separate view. 
         // For now, let's just make a simple Admin View.
         // Actually, let's reuse public view style but wrapped in admin layout? 
         // Let's create a dedicated internal view later if needed, but for now we often just want to see what the client sees.
         // Let's redirect to public link for viewing "as client"
         header("Location: /invoice/$uuid"); 
    }

    public function publicInvoice($params)
    {
        $uuid = $params['uuid'];
        
        $stmt = $this->pdo->prepare("SELECT * FROM erp_invoices WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            http_response_code(404);
            die("Invoice Not Found");
        }

        $stmtItems = $this->pdo->prepare("SELECT * FROM erp_invoice_items WHERE invoice_id = ?");
        $stmtItems->execute([$invoice['id']]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        
        // Tenant Info (Company Name & Logo)
        $stmtTenant = $this->pdo->prepare("SELECT name, logo FROM tenants WHERE id = ?");
        $stmtTenant->execute([$invoice['tenant_id']]);
        $tenantRow = $stmtTenant->fetch(PDO::FETCH_ASSOC) ?: [];
        $companyName = $tenantRow['name'] ?? 'Company';
        $companyLogo = $tenantRow['logo'] ?? '';

        // Tenant Contact (Email and Phone from admin user)
        $stmtContact = $this->pdo->prepare("SELECT email, phone FROM users WHERE tenant_id = ? AND role = 'admin' LIMIT 1");
        $stmtContact->execute([$invoice['tenant_id']]);
        $tenantContact = $stmtContact->fetch(PDO::FETCH_ASSOC);

        $tenantEmail = $tenantContact['email'] ?? '';
        $tenantPhone = $tenantContact['phone'] ?? '';

        require __DIR__ . '/../Views/finance/invoice_public.php';
    }
    
    public function processPayment($params)
    {
        $uuid = $params['uuid'];
        
        $stmt = $this->pdo->prepare("SELECT * FROM erp_invoices WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $invoice = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$invoice || $invoice['status'] === 'paid') {
             die("Invoice already paid or not found.");
        }
        
        $amount = (float)$invoice['total_amount'];
        $currency = $invoice['currency'] ?? 'NGN';
        $email = $invoice['client_email'] ?? 'client@casjoe.com';
        $clientName = $invoice['client_name'] ?? 'Client';
        $txRef = 'INV-' . $uuid . '-' . time();

        // Award lead scoring points (if linked to a lead)
        try {
            if (!empty($email) && $email !== 'client@casjoe.com') {
                $stmtLead = $this->pdo->prepare("SELECT id FROM erp_crm_leads WHERE tenant_id = ? AND email = ? LIMIT 1");
                $stmtLead->execute([$invoice['tenant_id'], $email]);
                $leadId = $stmtLead->fetchColumn();
                if ($leadId) {
                    \App\Core\Services\LeadScoringService::addPoints($invoice['tenant_id'], (int)$leadId, 'Initiated invoice payment', 5);
                }
            }
        } catch (\Exception $e) {
            error_log('Lead scoring error: ' . $e->getMessage());
        }

        if ($currency === 'NGN') {
            // Get Paystack Key
            $stmtSettings = $this->pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'paystack_secret_key'");
            $stmtSettings->execute();
            $paystackKey = $stmtSettings->fetchColumn();

            $payload = [
                'email' => $email,
                'amount' => $amount * 100, // kobo
                'reference' => $txRef,
                'callback_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/billing/callback'
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer " . $paystackKey,
                    "Content-Type: application/json"
                ],
            ]);
            
            $response = curl_exec($curl);
            $res = json_decode($response, true);
            
            if (isset($res['status']) && $res['status'] === true) {
                header('Location: ' . $res['data']['authorization_url']);
                exit;
            } else {
                die("Error communicating with Paystack: " . ($res['message'] ?? 'Unknown error'));
            }
        } else {
            // Route to Flutterwave for KES, USD, GHS, ZAR
            $stmtSettings = $this->pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'flutterwave_secret_key'");
            $stmtSettings->execute();
            $flwKey = $stmtSettings->fetchColumn();

            $payload = [
                'tx_ref' => $txRef,
                'amount' => $amount,
                'currency' => $currency,
                'redirect_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/billing/callback',
                'payment_options' => 'card, mobilemoney',
                'customer' => [
                    'email' => $email,
                    'name' => $clientName
                ],
                'customizations' => [
                    'title' => "Invoice Payment #" . $invoice['id'],
                    'description' => "Payment for Casjoe ERP Invoice"
                ]
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $flwKey,
                    'Content-Type: application/json'
                ],
            ]);

            $response = curl_exec($curl);
            $res = json_decode($response, true);

            if (isset($res['status']) && $res['status'] === 'success') {
                header('Location: ' . $res['data']['link']);
                exit;
            } else {
                die("Error communicating with Flutterwave: " . ($res['message'] ?? 'Unknown error'));
            }
        }
    }

    // --- ESTIMATES ---
    public function estimates()
    {
        $this->checkAdmin();
        $stmt = $this->pdo->prepare("SELECT * FROM erp_estimates WHERE tenant_id = ? ORDER BY created_at DESC");
        $stmt->execute([$this->tenantId]);
        $estimates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmtTenant = $this->pdo->prepare("SELECT currency FROM tenants WHERE id = ?");
        $stmtTenant->execute([$this->tenantId]);
        $tenantData = $stmtTenant->fetch(PDO::FETCH_ASSOC) ?: ['currency' => 'NGN'];

        $cCode = $tenantData['currency'] ?? 'NGN';
        $currencySymbol = $cCode;
        if ($cCode === 'NGN') $currencySymbol = '₦';
        if ($cCode === 'USD') $currencySymbol = '$';
        if ($cCode === 'GBP') $currencySymbol = '£';
        if ($cCode === 'EUR') $currencySymbol = '€';

        require __DIR__ . '/../Views/finance/estimates.php';
    }

    public function createEstimate()
    {
        $this->checkAdmin();
        require __DIR__ . '/../Views/finance/create_estimate.php';
    }

    public function storeEstimate()
    {
        $this->checkAdmin();
        $clientName = $_POST['client_name'];
        $clientEmail = $_POST['client_email'];
        $issueDate = $_POST['issue_date'];
        $expiryDate = $_POST['expiry_date'];
        $notes = $_POST['notes'];
        $items = json_decode($_POST['items_json'], true);

        // Calculate Total
        $total = 0;
        foreach ($items as $item) {
            $total += $item['quantity'] * $item['price'];
        }

        $uuid = bin2hex(random_bytes(16));

        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("INSERT INTO erp_estimates (tenant_id, uuid, client_name, client_email, issue_date, expiry_date, total_amount, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'draft')");
            $stmt->execute([$this->tenantId, $uuid, $clientName, $clientEmail, $issueDate, $expiryDate, $total, $notes]);
            $estimateId = $this->pdo->lastInsertId();

            $stmtItem = $this->pdo->prepare("INSERT INTO erp_estimate_items (estimate_id, description, quantity, unit_price, amount) VALUES (?, ?, ?, ?, ?)");
            foreach ($items as $item) {
                $amount = $item['quantity'] * $item['price'];
                $stmtItem->execute([$estimateId, $item['description'], $item['quantity'], $item['price'], $amount]);
            }

            $this->pdo->commit();

            // Email Client
            if (!empty($clientEmail)) {
                $estimateUrl = "https://" . ($_SERVER['HTTP_HOST'] ?? 'app.casjoe.com') . "/estimate/" . $uuid;
                $subject = "New Estimate from Casjoe";
                $message = "<p>Hello " . htmlspecialchars($clientName) . ",</p>";
                $message .= "<p>A new estimate has been generated for you.</p>";
                $message .= "<p><strong>Amount:</strong> " . number_format($total, 2) . "</p>";
                $message .= "<p><strong>Valid Until:</strong> " . htmlspecialchars($expiryDate) . "</p>";
                $message .= "<p>You can view your estimate and accept or reject it online here: <a href='" . htmlspecialchars($estimateUrl) . "'>View Estimate</a></p>";
                
                try {
                    \App\Core\Mailer::send($clientEmail, $subject, $message, false);
                } catch (\Exception $e) {
                    error_log("Failed to send estimate email: " . $e->getMessage());
                }
            }

            header('Location: /erp/finance/estimates');
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            die("Error creating estimate: " . $e->getMessage());
        }
    }

    public function publicEstimate($params)
    {
        $uuid = $params['uuid'];
        
        $stmt = $this->pdo->prepare("SELECT * FROM erp_estimates WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $estimate = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$estimate) {
            http_response_code(404);
            die("Estimate Not Found");
        }

        $stmtItems = $this->pdo->prepare("SELECT * FROM erp_estimate_items WHERE estimate_id = ?");
        $stmtItems->execute([$estimate['id']]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        
        // Tenant Info
        $stmtTenant = $this->pdo->prepare("SELECT name FROM tenants WHERE id = ?");
        $stmtTenant->execute([$estimate['tenant_id']]);
        $companyName = $stmtTenant->fetchColumn();

        require __DIR__ . '/../Views/finance/estimate_public.php';
    }

    public function acceptEstimate($params)
    {
        $uuid = $params['uuid'];
        $stmt = $this->pdo->prepare("UPDATE erp_estimates SET status = 'accepted' WHERE uuid = ?");
        $stmt->execute([$uuid]);
        header("Location: /estimate/$uuid?status=accepted");
    }

    public function rejectEstimate($params)
    {
        $uuid = $params['uuid'];
        $stmt = $this->pdo->prepare("UPDATE erp_estimates SET status = 'rejected' WHERE uuid = ?");
        $stmt->execute([$uuid]);
        header("Location: /estimate/$uuid?status=rejected");
    }

    public function convertEstimate($params)
    {
        $this->checkAdmin();
        $uuid = $params['uuid'];
        // Fetch Estimate
        $stmt = $this->pdo->prepare("SELECT * FROM erp_estimates WHERE uuid = ? AND tenant_id = ?");
        $stmt->execute([$uuid, $this->tenantId]);
        $est = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$est) die("Estimate not found");

        $items = $this->pdo->prepare("SELECT * FROM erp_estimate_items WHERE estimate_id = ?");
        $items->execute([$est['id']]);
        $lineItems = $items->fetchAll(PDO::FETCH_ASSOC);

        // Create Invoice from Estimate
        $invUuid = bin2hex(random_bytes(16));
        $dueDate = date('Y-m-d', strtotime('+30 days')); // Default 30 days

        $this->pdo->beginTransaction();
        try {
            $stmtInv = $this->pdo->prepare("INSERT INTO erp_invoices (tenant_id, uuid, client_name, client_email, issue_date, due_date, total_amount, notes, status) VALUES (?, ?, ?, ?, CURDATE(), ?, ?, ?, 'draft')");
            $stmtInv->execute([$this->tenantId, $invUuid, $est['client_name'], $est['client_email'], $dueDate, $est['total_amount'], "Converted from Estimate #" . $est['id']]);
            $invId = $this->pdo->lastInsertId();

            $stmtItem = $this->pdo->prepare("INSERT INTO erp_invoice_items (invoice_id, description, quantity, unit_price, amount) VALUES (?, ?, ?, ?, ?)");
            foreach ($lineItems as $item) {
                $stmtItem->execute([$invId, $item['description'], $item['quantity'], $item['unit_price'], $item['amount']]);
            }

            // Update Estimate Status
            $this->pdo->prepare("UPDATE erp_estimates SET status = 'converted' WHERE id = ?")->execute([$est['id']]);

            $this->pdo->commit();
            header("Location: /erp/finance/invoices");

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            die("Error converting estimate: " . $e->getMessage());
        }
    }

    // --- EXPENSES ---
    public function expenses()
    {
        $this->checkAdmin();
        $stmt = $this->pdo->prepare("
            SELECT e.*, v.name as vendor_name 
            FROM erp_expenses e 
            LEFT JOIN erp_vendors v ON e.vendor_id = v.id 
            WHERE e.tenant_id = ? 
            ORDER BY e.date DESC
        ");
        $stmt->execute([$this->tenantId]);
        $expenses = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $currInfo = $this->getTenantCurrencyInfo();
        $cCode = $currInfo['code'];
        $currencySymbol = $currInfo['symbol'];

        $successMsg = $_SESSION['import_success'] ?? null;
        $errorMsg = $_SESSION['import_error'] ?? null;
        unset($_SESSION['import_success'], $_SESSION['import_error']);

        require __DIR__ . '/../Views/finance/expenses.php';
    }

    public function createExpense() {
        $this->checkAdmin();
        // Get Vendors
        $stmt = $this->pdo->prepare("SELECT * FROM erp_vendors WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $vendors = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $currInfo = $this->getTenantCurrencyInfo();
        $cCode = $currInfo['code'];
        $currencySymbol = $currInfo['symbol'];
        
        require __DIR__ . '/../Views/finance/create_expense.php';
    }

    public function storeExpense()
    {
        $this->checkAdmin();
        $desc = $_POST['description'];
        $amount = (float)$_POST['amount'];
        $date = $_POST['date'];
        $vendorId = !empty($_POST['vendor_id']) ? $_POST['vendor_id'] : null;
        $category = $_POST['category'] ?? 'General';
        $status = $_POST['status'] ?? 'approved';

        $stmt = $this->pdo->prepare("INSERT INTO erp_expenses (tenant_id, description, amount, date, vendor_id, category, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $desc, $amount, $date, $vendorId, $category, $status]);

        // Log to P&L (Transactions)
        $stmtTrans = $this->pdo->prepare("INSERT INTO erp_transactions (tenant_id, description, amount, type, date, category) VALUES (?, ?, ?, 'expense', ?, ?)");
        $stmtTrans->execute([$this->tenantId, "$desc (Expense)", $amount, $date, $category]);

        $_SESSION['import_success'] = "Expense recorded successfully.";
        header('Location: /erp/finance/expenses');
        exit;
    }

    // --- VENDORS ---
    public function vendors()
    {
        $this->checkAdmin();
        $stmt = $this->pdo->prepare("SELECT * FROM erp_vendors WHERE tenant_id = ? ORDER BY name ASC");
        $stmt->execute([$this->tenantId]);
        $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $successMsg = $_SESSION['import_success'] ?? null;
        $errorMsg = $_SESSION['import_error'] ?? null;
        unset($_SESSION['import_success'], $_SESSION['import_error']);

        require __DIR__ . '/../Views/finance/vendors.php';
    }

    public function storeVendor()
    {
        $this->checkAdmin();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (empty($name)) {
            $_SESSION['import_error'] = "Vendor name is required.";
            header('Location: /erp/finance/vendors');
            exit;
        }

        $stmt = $this->pdo->prepare("INSERT INTO erp_vendors (tenant_id, name, email, phone, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $name, $email, $phone, $address]);

        $_SESSION['import_success'] = "Vendor '{$name}' created successfully.";
        header('Location: /erp/finance/vendors');
        exit;
    }

    public function storeAccount()
    {
        $this->checkAdmin();
        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $type = $_POST['type'] ?? 'asset';
        $balance = (float)($_POST['balance'] ?? 0.00);

        if (empty($code) || empty($name)) {
            die("Code and Name are required");
        }

        $stmt = $this->pdo->prepare("INSERT INTO erp_accounts (tenant_id, code, name, type, balance) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $code, $name, $type, $balance]);

        header('Location: /erp/finance?saved=1');
        exit;
    }

    public function storeJournalEntry()
    {
        $this->checkAdmin();
        $debitId = (int)($_POST['debit_account_id'] ?? 0);
        $creditId = (int)($_POST['credit_account_id'] ?? 0);
        $amount = (float)($_POST['amount'] ?? 0.00);
        $desc = trim($_POST['description'] ?? 'Journal Entry');
        $date = $_POST['date'] ?? date('Y-m-d');

        if ($debitId === $creditId) {
            die("Debit and Credit accounts must be different.");
        }
        if ($amount <= 0) {
            die("Amount must be greater than zero.");
        }

        $this->pdo->beginTransaction();
        try {
            // Get Debit Account
            $stmtDeb = $this->pdo->prepare("SELECT * FROM erp_accounts WHERE id = ? AND tenant_id = ? FOR UPDATE");
            $stmtDeb->execute([$debitId, $this->tenantId]);
            $debitAcc = $stmtDeb->fetch(PDO::FETCH_ASSOC);

            // Get Credit Account
            $stmtCred = $this->pdo->prepare("SELECT * FROM erp_accounts WHERE id = ? AND tenant_id = ? FOR UPDATE");
            $stmtCred->execute([$creditId, $this->tenantId]);
            $creditAcc = $stmtCred->fetch(PDO::FETCH_ASSOC);

            if (!$debitAcc || !$creditAcc) {
                throw new \Exception("Accounts not found.");
            }

            // Adjust Debit Account balance
            $newDebBal = $debitAcc['balance'];
            if (in_array($debitAcc['type'], ['asset', 'expense'])) {
                $newDebBal += $amount;
            } else {
                $newDebBal -= $amount;
            }

            // Adjust Credit Account balance
            $newCredBal = $creditAcc['balance'];
            if (in_array($creditAcc['type'], ['liability', 'equity', 'income'])) {
                $newCredBal += $amount;
            } else {
                $newCredBal -= $amount;
            }

            // Save Debit Update
            $updDeb = $this->pdo->prepare("UPDATE erp_accounts SET balance = ? WHERE id = ?");
            $updDeb->execute([$newDebBal, $debitId]);

            // Save Credit Update
            $updCred = $this->pdo->prepare("UPDATE erp_accounts SET balance = ? WHERE id = ?");
            $updCred->execute([$newCredBal, $creditId]);

            // Determine transaction type log
            $transType = ($debitAcc['type'] === 'expense') ? 'expense' : 'income';

            // Insert into transaction logs
            $stmtTrans = $this->pdo->prepare("INSERT INTO erp_transactions (tenant_id, description, amount, type, date, category) VALUES (?, ?, ?, ?, ?, 'Journal')");
            $stmtTrans->execute([$this->tenantId, $desc, $amount, $transType, $date]);

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            die("Transaction failed: " . $e->getMessage());
        }

        header('Location: /erp/finance?saved=1');
        exit;
    }


    // ==========================================
    // TRANSACTIONS (Edit/Update/Delete)
    // ==========================================
    public function editTransaction() {
        $this->checkAdmin();
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: /erp/transactions'); exit; }
        $stmt = $this->pdo->prepare("SELECT * FROM erp_transactions WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$transaction) { header('Location: /erp/transactions'); exit; }
        require __DIR__ . '/../Views/finance/edit_transaction.php';
    }

    public function updateTransaction() {
        $this->checkAdmin();
        $id = $_POST['id'];
        $desc = $_POST['description'];
        $amount = (float)$_POST['amount'];
        $type = $_POST['type'];
        $date = $_POST['date'];
        $category = $_POST['category'] ?? 'General';
        $stmt = $this->pdo->prepare("UPDATE erp_transactions SET description = ?, amount = ?, type = ?, date = ?, category = ? WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$desc, $amount, $type, $date, $category, $id, $this->tenantId]);
        $_SESSION['import_success'] = "Transaction updated successfully.";
        header('Location: /erp/transactions');
        exit;
    }

    public function deleteTransaction() {
        $this->checkAdmin();
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_transactions WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $_SESSION['import_success'] = "Transaction deleted successfully.";
        header('Location: /erp/transactions');
        exit;
    }

    public function deleteAllTransactions() {
        $this->checkAdmin();
        
        $stmtCount = $this->pdo->prepare("SELECT COUNT(*) FROM erp_transactions WHERE tenant_id = ?");
        $stmtCount->execute([$this->tenantId]);
        $count = (int)$stmtCount->fetchColumn();

        $stmtDelTx = $this->pdo->prepare("DELETE FROM erp_transactions WHERE tenant_id = ?");
        $stmtDelTx->execute([$this->tenantId]);

        $stmtDelExp = $this->pdo->prepare("DELETE FROM erp_expenses WHERE tenant_id = ?");
        $stmtDelExp->execute([$this->tenantId]);

        $_SESSION['import_success'] = "All transactions and recorded expenses ({$count} records) have been deleted successfully.";
        header('Location: /erp/transactions');
        exit;
    }

    public function bulkDeleteTransactions() {
        $this->checkAdmin();
        
        $ids = $_POST['ids'] ?? [];
        if (!is_array($ids) || empty($ids)) {
            $_SESSION['import_error'] = "No transactions were selected for deletion.";
            header('Location: /erp/transactions');
            exit;
        }

        $ids = array_filter(array_map('intval', $ids));
        if (empty($ids)) {
            $_SESSION['import_error'] = "Invalid transaction selection.";
            header('Location: /erp/transactions');
            exit;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        // Fetch to clean up any matching expenses
        $fetchStmt = $this->pdo->prepare("SELECT date, amount, type FROM erp_transactions WHERE tenant_id = ? AND id IN ($placeholders)");
        $fetchStmt->execute(array_merge([$this->tenantId], $ids));
        $deletedTx = $fetchStmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare("DELETE FROM erp_transactions WHERE tenant_id = ? AND id IN ($placeholders)");
        $stmt->execute(array_merge([$this->tenantId], $ids));
        $deletedCount = $stmt->rowCount();

        foreach ($deletedTx as $tx) {
            if (strtolower(trim($tx['type'])) === 'expense') {
                $delExpStmt = $this->pdo->prepare("
                    DELETE FROM erp_expenses 
                    WHERE tenant_id = ? AND date = ? AND amount = ? 
                    LIMIT 1
                ");
                $delExpStmt->execute([$this->tenantId, $tx['date'], $tx['amount']]);
            }
        }

        $_SESSION['import_success'] = "Successfully deleted {$deletedCount} selected transaction(s).";
        header('Location: /erp/transactions');
        exit;
    }

    // ==========================================
    // EXPENSES (Edit/Update/Delete)
    // ==========================================
    public function editExpense() {
        $this->checkAdmin();
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: /erp/finance/expenses'); exit; }
        $stmt = $this->pdo->prepare("SELECT * FROM erp_expenses WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $expense = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$expense) { header('Location: /erp/finance/expenses'); exit; }

        $stmtVendors = $this->pdo->prepare("SELECT id, name FROM erp_vendors WHERE tenant_id = ? ORDER BY name ASC");
        $stmtVendors->execute([$this->tenantId]);
        $vendors = $stmtVendors->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/finance/edit_expense.php';
    }

    public function updateExpense() {
        $this->checkAdmin();
        $id = $_POST['id'];
        $desc = $_POST['description'];
        $amount = (float)$_POST['amount'];
        $date = $_POST['date'];
        $category = $_POST['category'] ?? 'General';
        $status = $_POST['status'] ?? 'approved';
        $vendorId = !empty($_POST['vendor_id']) ? $_POST['vendor_id'] : null;

        // Fetch previous expense record for matching transaction update
        $stmtOld = $this->pdo->prepare("SELECT * FROM erp_expenses WHERE id = ? AND tenant_id = ?");
        $stmtOld->execute([$id, $this->tenantId]);
        $oldExp = $stmtOld->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare("UPDATE erp_expenses SET description = ?, amount = ?, date = ?, category = ?, status = ?, vendor_id = ? WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$desc, $amount, $date, $category, $status, $vendorId, $id, $this->tenantId]);

        if ($oldExp) {
            $stmtUpTx = $this->pdo->prepare("
                UPDATE erp_transactions 
                SET description = ?, amount = ?, date = ?, category = ? 
                WHERE tenant_id = ? AND LOWER(TRIM(type)) = 'expense' AND date = ? AND amount = ? 
                LIMIT 1
            ");
            $stmtUpTx->execute([$desc, $amount, $date, $category, $this->tenantId, $oldExp['date'], $oldExp['amount']]);
        }

        $_SESSION['import_success'] = "Expense updated successfully.";
        header('Location: /erp/finance/expenses');
        exit;
    }

    public function deleteExpense() {
        $this->checkAdmin();
        $id = $_POST['id'];

        $stmtOld = $this->pdo->prepare("SELECT * FROM erp_expenses WHERE id = ? AND tenant_id = ?");
        $stmtOld->execute([$id, $this->tenantId]);
        $oldExp = $stmtOld->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare("DELETE FROM erp_expenses WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);

        if ($oldExp) {
            $stmtDelTx = $this->pdo->prepare("
                DELETE FROM erp_transactions 
                WHERE tenant_id = ? AND LOWER(TRIM(type)) = 'expense' AND date = ? AND amount = ? 
                LIMIT 1
            ");
            $stmtDelTx->execute([$this->tenantId, $oldExp['date'], $oldExp['amount']]);
        }

        $_SESSION['import_success'] = "Expense record deleted successfully.";
        header('Location: /erp/finance/expenses');
        exit;
    }

    // ==========================================
    // VENDORS (Edit/Update/Delete)
    // ==========================================
    public function editVendor() {
        $this->checkAdmin();
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: /erp/finance/vendors'); exit; }
        $stmt = $this->pdo->prepare("SELECT * FROM erp_vendors WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $vendor = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$vendor) { header('Location: /erp/finance/vendors'); exit; }
        require __DIR__ . '/../Views/finance/edit_vendor.php';
    }

    public function updateVendor() {
        $this->checkAdmin();
        $id = $_POST['id'];
        $name = $_POST['name'];
        $contact = $_POST['contact'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $stmt = $this->pdo->prepare("UPDATE erp_vendors SET name = ?, contact_person = ?, email = ?, phone = ?, address = ? WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$name, $contact, $email, $phone, $address, $id, $this->tenantId]);
        $_SESSION['import_success'] = "Vendor updated successfully.";
        header('Location: /erp/finance/vendors');
        exit;
    }

    public function deleteVendor() {
        $this->checkAdmin();
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_vendors WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $_SESSION['import_success'] = "Vendor deleted successfully.";
        header('Location: /erp/finance/vendors');
        exit;
    }

    // ==========================================
    // OFFICE ASSETS (CRUD & Management)
    // ==========================================
    public function assets() {
        $this->checkAdmin();
        $stmt = $this->pdo->prepare("
            SELECT a.*, CONCAT(e.first_name, ' ', e.last_name) as assigned_employee 
            FROM erp_assets a 
            LEFT JOIN erp_employees e ON a.assigned_to = e.id 
            WHERE a.tenant_id = ? 
            ORDER BY a.created_at DESC
        ");
        $stmt->execute([$this->tenantId]);
        $assets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $currInfo = $this->getTenantCurrencyInfo();
        $currencySymbol = $currInfo['symbol'];

        $totalAssets = count($assets);
        $totalValuation = array_sum(array_column($assets, 'value'));
        $activeCount = count(array_filter($assets, fn($a) => $a['status'] === 'active'));
        $repairCount = count(array_filter($assets, fn($a) => $a['status'] === 'repair'));

        $successMsg = $_SESSION['import_success'] ?? null;
        $errorMsg = $_SESSION['import_error'] ?? null;
        unset($_SESSION['import_success'], $_SESSION['import_error']);

        require __DIR__ . '/../Views/system/assets.php';
    }

    public function createAsset() {
        $this->checkAdmin();
        $stmtEmp = $this->pdo->prepare("SELECT id, first_name, last_name FROM erp_employees WHERE tenant_id = ? ORDER BY first_name ASC");
        $stmtEmp->execute([$this->tenantId]);
        $employees = $stmtEmp->fetchAll(PDO::FETCH_ASSOC);

        $currInfo = $this->getTenantCurrencyInfo();
        $currencySymbol = $currInfo['symbol'];

        require __DIR__ . '/../Views/system/register_asset.php';
    }

    public function storeAsset() {
        $this->checkAdmin();
        $assetName = trim($_POST['asset_name'] ?? '');
        $serialNumber = trim($_POST['serial_number'] ?? '');
        $value = (float)($_POST['value'] ?? 0.00);
        $purchaseDate = !empty($_POST['purchase_date']) ? $_POST['purchase_date'] : date('Y-m-d');
        $status = $_POST['status'] ?? 'active';
        $assignedTo = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;

        if (empty($assetName)) {
            $_SESSION['import_error'] = "Asset name is required.";
            header('Location: /erp/assets/create');
            exit;
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO erp_assets (tenant_id, asset_name, serial_number, value, purchase_date, status, assigned_to) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$this->tenantId, $assetName, $serialNumber, $value, $purchaseDate, $status, $assignedTo]);

        $_SESSION['import_success'] = "Asset '{$assetName}' registered successfully.";
        header('Location: /erp/assets');
        exit;
    }

    public function editAsset() {
        $this->checkAdmin();
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: /erp/assets'); exit; }

        $stmt = $this->pdo->prepare("SELECT * FROM erp_assets WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $asset = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$asset) { header('Location: /erp/assets'); exit; }

        $stmtEmp = $this->pdo->prepare("SELECT id, first_name, last_name FROM erp_employees WHERE tenant_id = ? ORDER BY first_name ASC");
        $stmtEmp->execute([$this->tenantId]);
        $employees = $stmtEmp->fetchAll(PDO::FETCH_ASSOC);

        $currInfo = $this->getTenantCurrencyInfo();
        $currencySymbol = $currInfo['symbol'];

        require __DIR__ . '/../Views/system/edit_asset.php';
    }

    public function updateAsset() {
        $this->checkAdmin();
        $id = $_POST['id'];
        $assetName = trim($_POST['asset_name'] ?? '');
        $serialNumber = trim($_POST['serial_number'] ?? '');
        $value = (float)($_POST['value'] ?? 0.00);
        $purchaseDate = !empty($_POST['purchase_date']) ? $_POST['purchase_date'] : date('Y-m-d');
        $status = $_POST['status'] ?? 'active';
        $assignedTo = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;

        $stmt = $this->pdo->prepare("
            UPDATE erp_assets 
            SET asset_name = ?, serial_number = ?, value = ?, purchase_date = ?, status = ?, assigned_to = ? 
            WHERE id = ? AND tenant_id = ?
        ");
        $stmt->execute([$assetName, $serialNumber, $value, $purchaseDate, $status, $assignedTo, $id, $this->tenantId]);

        $_SESSION['import_success'] = "Asset '{$assetName}' updated successfully.";
        header('Location: /erp/assets');
        exit;
    }

    public function deleteAsset() {
        $this->checkAdmin();
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_assets WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);

        $_SESSION['import_success'] = "Asset record deleted successfully.";
        header('Location: /erp/assets');
        exit;
    }

    // ==========================================
    // ESTIMATES (Edit/Update/Delete)
    // ==========================================
    public function editEstimate() {
        $this->checkAdmin();
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: /erp/finance/estimates'); exit; }
        $stmt = $this->pdo->prepare("SELECT * FROM erp_estimates WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $estimate = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$estimate) { header('Location: /erp/finance/estimates'); exit; }
        
        $stmtItems = $this->pdo->prepare("SELECT * FROM erp_estimate_items WHERE estimate_id = ?");
        $stmtItems->execute([$estimate['id']]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/finance/edit_estimate.php';
    }

    public function updateEstimate() {
        $this->checkAdmin();
        $id = $_POST['estimate_id'];
        $client_name = $_POST['client_name'];
        $client_email = $_POST['client_email'];
        $issue_date = $_POST['issue_date'];
        $valid_until = $_POST['valid_until'];
        $status = $_POST['status'];
        $notes = $_POST['notes'];
        
        // Items and Total
        $itemsJson = $_POST['items_json'] ?? '[]';
        $items = json_decode($itemsJson, true);
        $totalAmount = 0;
        foreach ($items as $itm) {
            $totalAmount += ($itm['quantity'] * $itm['price']);
        }

        $stmt = $this->pdo->prepare("UPDATE erp_estimates SET client_name=?, client_email=?, issue_date=?, valid_until=?, status=?, total_amount=?, notes=? WHERE id=? AND tenant_id=?");
        $stmt->execute([$client_name, $client_email, $issue_date, $valid_until, $status, $totalAmount, $notes, $id, $this->tenantId]);

        // Replace items
        $del = $this->pdo->prepare("DELETE FROM erp_estimate_items WHERE estimate_id = ?");
        $del->execute([$id]);

        $ins = $this->pdo->prepare("INSERT INTO erp_estimate_items (estimate_id, description, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)");
        foreach ($items as $itm) {
            $ins->execute([$id, $itm['description'], $itm['quantity'], $itm['price'], $itm['quantity'] * $itm['price']]);
        }

        header('Location: /erp/finance/estimates');
        exit;
    }

    public function deleteEstimate() {
        $this->checkAdmin();
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_estimates WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        header('Location: /erp/finance/estimates');
        exit;
    }

    // ==========================================
    // INVOICES (Edit/Update/Delete)
    // ==========================================
    public function editInvoice() {
        $this->checkAdmin();
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: /erp/finance/invoices'); exit; }
        $stmt = $this->pdo->prepare("SELECT * FROM erp_invoices WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$invoice) { header('Location: /erp/finance/invoices'); exit; }
        
        $stmtItems = $this->pdo->prepare("SELECT * FROM erp_invoice_items WHERE invoice_id = ?");
        $stmtItems->execute([$invoice['id']]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/finance/edit_invoice.php';
    }

    public function updateInvoice() {
        $this->checkAdmin();
        $id = (int)$_POST['invoice_id'];
        $client_name = trim($_POST['client_name'] ?? '');
        $client_email = !empty($_POST['client_email']) ? trim($_POST['client_email']) : null;
        $issue_date = $_POST['issue_date'] ?? date('Y-m-d');
        $due_date = $_POST['due_date'] ?? date('Y-m-d');
        $status = $_POST['status'] ?? 'sent';
        $notes = $_POST['notes'] ?? '';
        
        // Check previous status
        $stmtOld = $this->pdo->prepare("SELECT status, total_amount FROM erp_invoices WHERE id = ? AND tenant_id = ?");
        $stmtOld->execute([$id, $this->tenantId]);
        $oldInvoice = $stmtOld->fetch(\PDO::FETCH_ASSOC);
        $wasPaid = ($oldInvoice && $oldInvoice['status'] === 'paid');

        // Items and Total
        $itemsJson = $_POST['items_json'] ?? '[]';
        $items = json_decode($itemsJson, true) ?: [];
        $totalAmount = 0;
        foreach ($items as $itm) {
            $totalAmount += (($itm['quantity'] ?? 1) * ($itm['price'] ?? 0));
        }

        $stmt = $this->pdo->prepare("UPDATE erp_invoices SET client_name=?, client_email=?, issue_date=?, due_date=?, status=?, total_amount=?, notes=? WHERE id=? AND tenant_id=?");
        $stmt->execute([$client_name, $client_email, $issue_date, $due_date, $status, $totalAmount, $notes, $id, $this->tenantId]);

        // Auto-log to Transactions when paid (via bank transfer, cash, or POS)
        if ($status === 'paid' && !$wasPaid) {
            $stmtTrans = $this->pdo->prepare("INSERT INTO erp_transactions (tenant_id, description, amount, type, date) VALUES (?, ?, ?, 'income', CURDATE())");
            $stmtTrans->execute([$this->tenantId, "Invoice Payment #" . $id . " (" . $client_name . ")", $totalAmount]);
        }

        // Replace items
        $del = $this->pdo->prepare("DELETE FROM erp_invoice_items WHERE invoice_id = ?");
        $del->execute([$id]);

        $ins = $this->pdo->prepare("INSERT INTO erp_invoice_items (invoice_id, description, quantity, unit_price, amount) VALUES (?, ?, ?, ?, ?)");
        foreach ($items as $itm) {
            $amt = ($itm['quantity'] ?? 1) * ($itm['price'] ?? 0);
            $ins->execute([$id, $itm['description'], $itm['quantity'], $itm['price'], $amt]);
        }

        header('Location: /erp/finance/invoices');
        exit;
    }

    public function deleteInvoice() {
        $this->checkAdmin();
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_invoices WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        header('Location: /erp/finance/invoices');
        exit;
    }

    // ==========================================
    // TRANSACTIONS (CSV / Excel Bulk Import)
    // ==========================================
    public function importTransactionsView()
    {
        $this->checkAdmin();
        $stmtTenant = $this->pdo->prepare("SELECT currency FROM tenants WHERE id = ?");
        $stmtTenant->execute([$this->tenantId]);
        $tenantData = $stmtTenant->fetch(\PDO::FETCH_ASSOC) ?: ['currency' => 'NGN'];

        $cCode = $tenantData['currency'] ?? 'NGN';
        $currencySymbol = $cCode === 'NGN' ? '₦' : ($cCode === 'USD' ? '$' : ($cCode === 'GBP' ? '£' : ($cCode === 'EUR' ? '€' : $cCode)));

        $successMsg = $_SESSION['import_success'] ?? null;
        $errorMsg = $_SESSION['import_error'] ?? null;
        unset($_SESSION['import_success'], $_SESSION['import_error']);

        require __DIR__ . '/../Views/finance/import_transactions.php';
    }

    public function downloadSampleTransactionsCsv()
    {
        $this->checkAdmin();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="sample_transactions_import.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Date', 'Description', 'Type', 'Amount', 'Category'], ',', '"', '\\');
        fputcsv($output, [date('Y-m-01'), 'Client Project Payment', 'income', '250000.00', 'Revenue'], ',', '"', '\\');
        fputcsv($output, [date('Y-m-03'), 'Office High-Speed Internet', 'expense', '35000.00', 'Utilities'], ',', '"', '\\');
        fputcsv($output, [date('Y-m-05'), 'Cloud Server Hosting & Domains', 'expense', '75000.00', 'Infrastructure'], ',', '"', '\\');
        fputcsv($output, [date('Y-m-10'), 'Consulting Retainer Milestone', 'income', '500000.00', 'Consulting'], ',', '"', '\\');
        fputcsv($output, [date('Y-m-15'), 'Staff Workstation Equipment', 'expense', '120000.00', 'Office Operations'], ',', '"', '\\');
        fclose($output);
        exit;
    }

    public function importTransactions()
    {
        $this->checkAdmin();

        if (empty($_FILES['csv_file']['tmp_name']) || !is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
            $_SESSION['import_error'] = 'Please select a valid CSV or Excel file to upload.';
            header('Location: /erp/transactions/import');
            exit;
        }

        $fileTmp = $_FILES['csv_file']['tmp_name'];
        $fileName = $_FILES['csv_file']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, ['csv', 'txt', 'xlsx', 'xls'])) {
            $_SESSION['import_error'] = 'Invalid file type. Please upload an Excel (.xlsx, .xls) or CSV (.csv) file.';
            header('Location: /erp/transactions/import');
            exit;
        }

        require_once __DIR__ . '/../Helpers/SpreadsheetReader.php';
        $rows = \App\Modules\CasjoeERP\Helpers\SpreadsheetReader::read($fileTmp, $fileName);

        if (empty($rows)) {
            $_SESSION['import_error'] = 'The uploaded spreadsheet is empty or could not be read.';
            header('Location: /erp/transactions/import');
            exit;
        }

        $syncExpenses = !empty($_POST['sync_expenses']);
        $skipDuplicates = !isset($_POST['skip_duplicates']) || !empty($_POST['skip_duplicates']);

        // Read header row
        $headers = array_shift($rows);
        if (empty($headers)) {
            $_SESSION['import_error'] = 'No column headers found in the uploaded file.';
            header('Location: /erp/transactions/import');
            exit;
        }

        // Identify column indices
        $dateIdx = -1;
        $descIdx = -1;
        $typeIdx = -1;
        $amountIdx = -1;
        $debitIdx = -1;
        $creditIdx = -1;
        $catIdx = -1;

        foreach ($headers as $idx => $col) {
            $cleaned = strtolower(trim((string)$col));
            $cleanedAlpha = preg_replace('/[^a-z0-9]/', '', $cleaned);

            if ($dateIdx === -1 && (strpos($cleanedAlpha, 'date') !== false || strpos($cleanedAlpha, 'time') !== false || strpos($cleanedAlpha, 'created') !== false || strpos($cleanedAlpha, 'txndate') !== false || strpos($cleanedAlpha, 'valuedate') !== false || strpos($cleanedAlpha, 'postdate') !== false)) {
                $dateIdx = $idx;
            } elseif ($descIdx === -1 && (strpos($cleanedAlpha, 'desc') !== false || strpos($cleanedAlpha, 'detail') !== false || strpos($cleanedAlpha, 'narration') !== false || strpos($cleanedAlpha, 'particular') !== false || strpos($cleanedAlpha, 'memo') !== false || strpos($cleanedAlpha, 'title') !== false || strpos($cleanedAlpha, 'name') !== false || strpos($cleanedAlpha, 'item') !== false || strpos($cleanedAlpha, 'remark') !== false || strpos($cleanedAlpha, 'payee') !== false || strpos($cleanedAlpha, 'beneficiary') !== false || strpos($cleanedAlpha, 'note') !== false || strpos($cleanedAlpha, 'ref') !== false)) {
                $descIdx = $idx;
            } elseif ($debitIdx === -1 && (strpos($cleanedAlpha, 'debit') !== false || $cleanedAlpha === 'dr' || strpos($cleanedAlpha, 'withdrawal') !== false || strpos($cleanedAlpha, 'outflow') !== false || strpos($cleanedAlpha, 'paidout') !== false)) {
                $debitIdx = $idx;
            } elseif ($creditIdx === -1 && (strpos($cleanedAlpha, 'credit') !== false || $cleanedAlpha === 'cr' || strpos($cleanedAlpha, 'deposit') !== false || strpos($cleanedAlpha, 'inflow') !== false || strpos($cleanedAlpha, 'paidin') !== false || strpos($cleanedAlpha, 'lodgement') !== false)) {
                $creditIdx = $idx;
            } elseif ($typeIdx === -1 && (strpos($cleanedAlpha, 'type') !== false || strpos($cleanedAlpha, 'flow') !== false || strpos($cleanedAlpha, 'direction') !== false || strpos($cleanedAlpha, 'drcr') !== false || strpos($cleanedAlpha, 'crdr') !== false)) {
                $typeIdx = $idx;
            } elseif ($amountIdx === -1 && (strpos($cleanedAlpha, 'amount') !== false || strpos($cleanedAlpha, 'total') !== false || strpos($cleanedAlpha, 'price') !== false || strpos($cleanedAlpha, 'value') !== false || strpos($cleanedAlpha, 'sum') !== false || strpos($cleanedAlpha, 'net') !== false)) {
                $amountIdx = $idx;
            } elseif ($catIdx === -1 && (strpos($cleanedAlpha, 'cat') !== false || strpos($cleanedAlpha, 'group') !== false || strpos($cleanedAlpha, 'tag') !== false || strpos($cleanedAlpha, 'class') !== false || strpos($cleanedAlpha, 'account') !== false || strpos($cleanedAlpha, 'ledger') !== false || strpos($cleanedAlpha, 'department') !== false)) {
                $catIdx = $idx;
            }
        }

        // Fallbacks if columns not named by headers
        if ($dateIdx === -1) $dateIdx = 0;
        if ($descIdx === -1) $descIdx = 1;
        if ($amountIdx === -1 && $debitIdx === -1 && $creditIdx === -1) {
            $amountIdx = (count($headers) >= 4) ? 3 : 2;
        }
        if ($typeIdx === -1 && count($headers) >= 3 && $amountIdx !== 2 && $debitIdx === -1) {
            $typeIdx = 2;
        }
        if ($catIdx === -1 && count($headers) >= 5) $catIdx = 4;

        $imported = 0;
        $incomeCount = 0;
        $expenseCount = 0;
        $totalIncome = 0;
        $totalExpense = 0;
        $skipped = 0;
        $duplicateCount = 0;
        $seenKeys = [];

        $stmtCheckTxn = $this->pdo->prepare("
            SELECT id FROM erp_transactions 
            WHERE tenant_id = ? AND date = ? AND amount = ? AND description = ? AND type = ? 
            LIMIT 1
        ");

        $stmtCheckExp = $this->pdo->prepare("
            SELECT id FROM erp_expenses 
            WHERE tenant_id = ? AND date = ? AND amount = ? AND description = ? 
            LIMIT 1
        ");

        $stmtInsertTxn = $this->pdo->prepare("
            INSERT INTO erp_transactions (tenant_id, description, amount, type, date, category)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmtInsertExp = $this->pdo->prepare("
            INSERT INTO erp_expenses (tenant_id, description, amount, date, category, status)
            VALUES (?, ?, ?, ?, ?, 'approved')
        ");

        $this->pdo->beginTransaction();

        try {
            foreach ($rows as $row) {
                // Skip empty lines
                if (empty(array_filter($row, fn($v) => $v !== null && trim($v) !== ''))) continue;

                $rawDate = trim((string)($row[$dateIdx] ?? ''));
                $rawDesc = trim((string)($row[$descIdx] ?? ''));
                $rawType = strtolower(trim((string)($row[$typeIdx] ?? '')));
                $rawAmount = trim((string)($row[$amountIdx] ?? '0'));
                $rawCat = trim((string)($row[$catIdx] ?? 'General'));

                // Handle separate Debit / Credit columns if present
                $hasDebitCredit = ($debitIdx !== -1 || $creditIdx !== -1);
                $rawDebit = ($debitIdx !== -1) ? trim((string)($row[$debitIdx] ?? '')) : '';
                $rawCredit = ($creditIdx !== -1) ? trim((string)($row[$creditIdx] ?? '')) : '';

                $cleanDebitStr = preg_replace('/[^0-9.-]/', '', str_replace(',', '', $rawDebit));
                $cleanCreditStr = preg_replace('/[^0-9.-]/', '', str_replace(',', '', $rawCredit));
                $cleanDebitVal = (float)$cleanDebitStr;
                $cleanCreditVal = (float)$cleanCreditStr;

                $typeVal = 'income';
                $amountVal = 0.0;

                if ($hasDebitCredit && ($cleanDebitVal > 0 || $cleanCreditVal > 0)) {
                    if ($cleanDebitVal > 0) {
                        $typeVal = 'expense';
                        $amountVal = $cleanDebitVal;
                    } else {
                        $typeVal = 'income';
                        $amountVal = $cleanCreditVal;
                    }
                } else {
                    // Normalize Amount: strip currency symbols, commas, spaces
                    $cleanAmountStr = preg_replace('/[^0-9.-]/', '', str_replace(',', '', $rawAmount));
                    $amountVal = (float)$cleanAmountStr;

                    // Auto-determine Type if amount is negative
                    if ($amountVal < 0) {
                        $typeVal = 'expense';
                        $amountVal = abs($amountVal);
                    } elseif (
                        in_array($rawType, ['expense', 'outflow', 'debit', 'dr', 'cost', 'payment', 'withdrawal', 'payout']) ||
                        strpos($rawType, 'exp') !== false ||
                        strpos($rawType, 'deb') !== false
                    ) {
                        $typeVal = 'expense';
                    } elseif (
                        in_array($rawType, ['income', 'inflow', 'credit', 'cr', 'revenue', 'deposit', 'sales']) ||
                        strpos($rawType, 'inc') !== false ||
                        strpos($rawType, 'cred') !== false
                    ) {
                        $typeVal = 'income';
                    }
                }

                if ($amountVal == 0 && empty($rawDesc)) {
                    $skipped++;
                    continue;
                }

                // Default description if blank
                if (empty($rawDesc)) {
                    $rawDesc = ($typeVal === 'income') ? 'Imported Revenue' : 'Imported Expense';
                }

                // Normalize Date (support Excel numeric serial dates e.g. 45535 or formatted strings)
                if (is_numeric($rawDate) && (float)$rawDate > 1000 && (float)$rawDate < 100000) {
                    $unixTimestamp = ((float)$rawDate - 25569) * 86400;
                    $dateVal = gmdate('Y-m-d', (int)$unixTimestamp);
                } else {
                    $parsedTime = strtotime($rawDate);
                    if ($parsedTime === false) {
                        // Try replacing / with - for European format d/m/Y
                        $d = str_replace('/', '-', $rawDate);
                        $parsedTime = strtotime($d);
                    }
                    $dateVal = $parsedTime ? date('Y-m-d', $parsedTime) : date('Y-m-d');
                }

                // In-batch duplicate check
                $rowKey = $dateVal . '|' . number_format($amountVal, 2, '.', '') . '|' . $typeVal . '|' . mb_strtolower(trim($rawDesc));
                if (isset($seenKeys[$rowKey])) {
                    $duplicateCount++;
                    continue;
                }
                $seenKeys[$rowKey] = true;

                // Database duplicate check
                if ($skipDuplicates) {
                    $stmtCheckTxn->execute([$this->tenantId, $dateVal, $amountVal, $rawDesc, $typeVal]);
                    if ($stmtCheckTxn->fetch()) {
                        $duplicateCount++;
                        continue;
                    }
                }

                // Insert into erp_transactions
                $stmtInsertTxn->execute([
                    $this->tenantId,
                    $rawDesc,
                    $amountVal,
                    $typeVal,
                    $dateVal,
                    !empty($rawCat) ? $rawCat : 'Imported'
                ]);

                // Optionally sync to erp_expenses
                if ($syncExpenses && $typeVal === 'expense') {
                    $stmtCheckExp->execute([$this->tenantId, $dateVal, $amountVal, $rawDesc]);
                    if (!$stmtCheckExp->fetch()) {
                        $stmtInsertExp->execute([
                            $this->tenantId,
                            $rawDesc,
                            $amountVal,
                            $dateVal,
                            !empty($rawCat) ? $rawCat : 'General'
                        ]);
                    }
                }

                $imported++;
                if ($typeVal === 'income') {
                    $incomeCount++;
                    $totalIncome += $amountVal;
                } else {
                    $expenseCount++;
                    $totalExpense += $amountVal;
                }
            }

            $this->pdo->commit();

            $msgParts = [];
            $msgParts[] = "Successfully imported <strong>{$imported}</strong> new transactions (" . number_format($incomeCount) . " Income, " . number_format($expenseCount) . " Expenses).";
            $msgParts[] = "Total Income: <strong>" . number_format($totalIncome, 2) . "</strong> | Total Expenses: <strong>" . number_format($totalExpense, 2) . "</strong>.";

            if ($duplicateCount > 0) {
                $msgParts[] = "<em>({$duplicateCount} duplicate records were automatically skipped).</em>";
            }
            if ($skipped > 0) {
                $msgParts[] = "({$skipped} empty rows skipped).";
            }

            $_SESSION['import_success'] = implode(' ', $msgParts);
            header('Location: /erp/transactions');
            exit;

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['import_error'] = 'Import failed: ' . $e->getMessage();
            header('Location: /erp/transactions/import');
            exit;
        }
    }

    public function cleanDuplicateTransactions()
    {
        $this->checkAdmin();

        // Remove exact duplicates keeping the oldest record
        $sql = "
            DELETE t1 FROM erp_transactions t1
            INNER JOIN erp_transactions t2 
            WHERE t1.id > t2.id 
              AND t1.tenant_id = ? 
              AND t2.tenant_id = ? 
              AND t1.date = t2.date 
              AND t1.amount = t2.amount 
              AND t1.description = t2.description 
              AND t1.type = t2.type
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->tenantId, $this->tenantId]);
            $removed = $stmt->rowCount();

            $_SESSION['import_success'] = "Duplicate cleanup complete. Successfully removed <strong>{$removed}</strong> duplicate transaction(s).";
        } catch (\Exception $e) {
            $_SESSION['import_error'] = "Could not clean duplicates: " . $e->getMessage();
        }

        header('Location: /erp/transactions');
        exit;
    }

    public function reports()
    {
        $this->checkAdmin();
        require __DIR__ . '/../Views/system/reports.php';
    }

    public function settings()
    {
        $this->checkAdmin();
        header('Location: /erp/settings');
        exit;
    }

    public function reminders()
    {
        $this->checkAdmin();
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM erp_invoice_reminders WHERE tenant_id = ? ORDER BY days_offset ASC");
            $stmt->execute([$this->tenantId]);
            $reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $reminders = [];
        }
        require __DIR__ . '/../Views/finance/reminders.php';
    }

    public function createReminder()
    {
        $this->checkAdmin();
        require __DIR__ . '/../Views/finance/create_reminder.php';
    }

    public function storeReminder()
    {
        $this->checkAdmin();
        $name = trim($_POST['name'] ?? '');
        $daysOffset = (int)($_POST['days_offset'] ?? 0);
        $freqDays = (int)($_POST['frequency_days'] ?? 0);
        $subject = trim($_POST['subject'] ?? '');
        $body = trim($_POST['body'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        try {
            $stmt = $this->pdo->prepare("INSERT INTO erp_invoice_reminders (tenant_id, name, days_offset, frequency_days, subject, body, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$this->tenantId, $name, $daysOffset, $freqDays, $subject, $body, $isActive]);
            $_SESSION['import_success'] = "Reminder sequence saved successfully.";
        } catch (\Exception $e) {
            $_SESSION['import_error'] = "Could not save reminder: " . $e->getMessage();
        }

        header('Location: /erp/finance/reminders');
        exit;
    }

    public function editReminder()
    {
        $this->checkAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $this->pdo->prepare("SELECT * FROM erp_invoice_reminders WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $reminder = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reminder) {
            header('Location: /erp/finance/reminders');
            exit;
        }

        require __DIR__ . '/../Views/finance/edit_reminder.php';
    }

    public function updateReminder()
    {
        $this->checkAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $daysOffset = (int)($_POST['days_offset'] ?? 0);
        $freqDays = (int)($_POST['frequency_days'] ?? 0);
        $subject = trim($_POST['subject'] ?? '');
        $body = trim($_POST['body'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        try {
            $stmt = $this->pdo->prepare("UPDATE erp_invoice_reminders SET name = ?, days_offset = ?, frequency_days = ?, subject = ?, body = ?, is_active = ? WHERE id = ? AND tenant_id = ?");
            $stmt->execute([$name, $daysOffset, $freqDays, $subject, $body, $isActive, $id, $this->tenantId]);
            $_SESSION['import_success'] = "Reminder updated successfully.";
        } catch (\Exception $e) {
            $_SESSION['import_error'] = "Could not update reminder: " . $e->getMessage();
        }

        header('Location: /erp/finance/reminders');
        exit;
    }

    public function deleteReminder()
    {
        $this->checkAdmin();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                $stmt = $this->pdo->prepare("DELETE FROM erp_invoice_reminders WHERE id = ? AND tenant_id = ?");
                $stmt->execute([$id, $this->tenantId]);
                $_SESSION['import_success'] = "Reminder deleted successfully.";
            } catch (\Exception $e) {
                $_SESSION['import_error'] = "Could not delete reminder: " . $e->getMessage();
            }
        }
        header('Location: /erp/finance/reminders');
        exit;
    }
}

