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

    public function inventory()
    {
        $this->checkAdmin();
        $stmt = $this->pdo->prepare("SELECT * FROM erp_inventory WHERE tenant_id = ? ORDER BY item_name ASC");
        $stmt->execute([$this->tenantId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/inventory/index.php';
    }

    public function createInventory()
    {
        $this->checkAdmin();
        require __DIR__ . '/../views/inventory/create.php';
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

    public function transactions()
    {
        $this->checkAdmin();
        $stmt = $this->pdo->prepare("SELECT * FROM erp_transactions WHERE tenant_id = ? ORDER BY date DESC");
        $stmt->execute([$this->tenantId]);
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/finance/transactions.php';
    }

    public function createTransaction()
    {
        $this->checkAdmin();
        require __DIR__ . '/../views/finance/create_transaction.php';
    }

    public function storeTransaction()
    {
        $this->checkAdmin();
        $desc = $_POST['description'];
        $amount = $_POST['amount'];
        $type = $_POST['type'];
        $date = $_POST['date'];

        $stmt = $this->pdo->prepare("INSERT INTO erp_transactions (tenant_id, description, amount, type, date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $desc, $amount, $type, $date]);

        header('Location: /erp/transactions');
        exit;
    }
    
    public function index()
    {
        $this->checkAdmin();
        // Chart of Accounts
        $stmt = $this->pdo->prepare("SELECT * FROM erp_accounts WHERE tenant_id = ? ORDER BY code ASC");
        $stmt->execute([$this->tenantId]);
        $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/finance/index.php';
    }

    public function dashboard()
    {
         $this->checkAdmin();
         // Simple P&L summary
         $stmt = $this->pdo->prepare("
            SELECT type, SUM(amount) as total 
            FROM erp_transactions 
            WHERE tenant_id = ? 
            GROUP BY type
         ");
         $stmt->execute([$this->tenantId]);
         $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // ['income' => 5000, 'expense' => 2000]
         
         $income = $results['income'] ?? 0;
         $expense = $results['expense'] ?? 0;
         $profit = $income - $expense;

         require __DIR__ . '/../views/finance/dashboard.php';
    }
    public function invoices()
    {
        $this->checkAdmin();
        $stmt = $this->pdo->prepare("SELECT * FROM erp_invoices WHERE tenant_id = ? ORDER BY created_at DESC");
        $stmt->execute([$this->tenantId]);
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/finance/invoices.php';
    }

    public function createInvoice()
    {
        $this->checkAdmin();
        require __DIR__ . '/../views/finance/create_invoice.php';
    }

    public function storeInvoice()
    {
        $this->checkAdmin();
        $clientName = $_POST['client_name'];
        $clientEmail = $_POST['client_email'];
        $issueDate = $_POST['issue_date'];
        $dueDate = $_POST['due_date'];
        $notes = $_POST['notes'];
        $items = json_decode($_POST['items_json'], true);

        // Calculate Total
        $total = 0;
        foreach ($items as $item) {
            $total += $item['quantity'] * $item['price'];
        }

        $uuid = bin2hex(random_bytes(16)); // Secure UUID

        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("INSERT INTO erp_invoices (tenant_id, uuid, client_name, client_email, issue_date, due_date, total_amount, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'draft')");
            $stmt->execute([$this->tenantId, $uuid, $clientName, $clientEmail, $issueDate, $dueDate, $total, $notes]);
            $invoiceId = $this->pdo->lastInsertId();

            $stmtItem = $this->pdo->prepare("INSERT INTO erp_invoice_items (invoice_id, description, quantity, unit_price, amount) VALUES (?, ?, ?, ?, ?)");
            foreach ($items as $item) {
                $amount = $item['quantity'] * $item['price'];
                $stmtItem->execute([$invoiceId, $item['description'], $item['quantity'], $item['price'], $amount]);
            }

            $this->pdo->commit();
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
        
        // Tenant Info (Company Name)
        $stmtTenant = $this->pdo->prepare("SELECT name FROM tenants WHERE id = ?");
        $stmtTenant->execute([$invoice['tenant_id']]);
        $companyName = $stmtTenant->fetchColumn();

        require __DIR__ . '/../views/finance/invoice_public.php';
    }
    
    public function processPayment($params)
    {
        $uuid = $params['uuid'];
        // In a real app, this would verify payment via gateway callback.
        // For simulation/demo:
        
        $stmt = $this->pdo->prepare("UPDATE erp_invoices SET status = 'paid' WHERE uuid = ?");
        $stmt->execute([$uuid]);
        
        // Also log transaction
        $stmtInv = $this->pdo->prepare("SELECT * FROM erp_invoices WHERE uuid = ?");
        $stmtInv->execute([$uuid]);
        $invoice = $stmtInv->fetch(PDO::FETCH_ASSOC);
        
        if ($invoice) {
             $stmtTrans = $this->pdo->prepare("INSERT INTO erp_transactions (tenant_id, description, amount, type, date) VALUES (?, ?, ?, 'income', CURDATE())");
             $stmtTrans->execute([$invoice['tenant_id'], "Invoice Payment #" . $invoice['id'], $invoice['total_amount']]);
        }

        header("Location: /invoice/$uuid?success=1");
    }

    // --- ESTIMATES ---
    public function estimates()
    {
        $this->checkAdmin();
        $stmt = $this->pdo->prepare("SELECT * FROM erp_estimates WHERE tenant_id = ? ORDER BY created_at DESC");
        $stmt->execute([$this->tenantId]);
        $estimates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/finance/estimates.php';
    }

    public function createEstimate()
    {
        $this->checkAdmin();
        require __DIR__ . '/../views/finance/create_estimate.php';
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

        require __DIR__ . '/../views/finance/estimate_public.php';
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
        $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/finance/expenses.php';
    }

    public function createExpense() {
        $this->checkAdmin();
        // Get Vendors
        $stmt = $this->pdo->prepare("SELECT * FROM erp_vendors WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require __DIR__ . '/../views/finance/create_expense.php';
    }

    public function storeExpense()
    {
        $this->checkAdmin();
        $desc = $_POST['description'];
        $amount = $_POST['amount'];
        $date = $_POST['date'];
        $vendorId = !empty($_POST['vendor_id']) ? $_POST['vendor_id'] : null;
        $category = $_POST['category'];

        $stmt = $this->pdo->prepare("INSERT INTO erp_expenses (tenant_id, description, amount, date, vendor_id, category, status) VALUES (?, ?, ?, ?, ?, ?, 'approved')");
        $stmt->execute([$this->tenantId, $desc, $amount, $date, $vendorId, $category]);

        // Log to P&L (Transactions)
        $stmtTrans = $this->pdo->prepare("INSERT INTO erp_transactions (tenant_id, description, amount, type, date) VALUES (?, ?, ?, 'expense', ?)");
        $stmtTrans->execute([$this->tenantId, "$desc (Expense)", $amount, $date]);

        header('Location: /erp/finance/expenses');
    }

    // --- VENDORS ---
    public function vendors()
    {
        $this->checkAdmin();
        $stmt = $this->pdo->prepare("SELECT * FROM erp_vendors WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../views/finance/vendors.php';
    }

    public function storeVendor()
    {
        $this->checkAdmin();
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];

        $stmt = $this->pdo->prepare("INSERT INTO erp_vendors (tenant_id, name, email, phone, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $name, $email, $phone, $address]);

        header('Location: /erp/finance/vendors');
    }
}
