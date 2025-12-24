<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

class CrmController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
    }

    public function customers()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM erp_crm_customers WHERE tenant_id = ? ORDER BY name ASC");
        $stmt->execute([$this->tenantId]);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/crm/customers/index.php';
    }

    public function storeCustomer()
    {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $company = $_POST['company'];
        $phone = $_POST['phone'];

        $stmt = $this->pdo->prepare("INSERT INTO erp_crm_customers (tenant_id, name, email, company, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $name, $email, $company, $phone]);

        // Create User Login if requested
        if (isset($_POST['create_login']) && $_POST['create_login'] == '1') {
            $password = $_POST['password'] ?? '';
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                // Check if user exists
                $check = $this->pdo->prepare("SELECT id FROM users WHERE email = ? AND tenant_id = ?");
                $check->execute([$email, $this->tenantId]);
                
                if (!$check->fetch()) {
                    $uStmt = $this->pdo->prepare("
                        INSERT INTO users (tenant_id, name, email, password, role, is_verified, created_at) 
                        VALUES (?, ?, ?, ?, 'client', 1, NOW())
                    ");
                    $uStmt->execute([$this->tenantId, $name, $email, $hash]);
                }
            }
        }

        header('Location: /erp/crm/customers');
        exit;
    }

    public function leads()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM erp_crm_leads WHERE tenant_id = ? ORDER BY created_at DESC");
        $stmt->execute([$this->tenantId]);
        $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/crm/leads/index.php';
    }

    public function storeLead()
    {
        $name = $_POST['name'];
        $source = $_POST['source'];
        $status = 'new'; 

        $stmt = $this->pdo->prepare("INSERT INTO erp_crm_leads (tenant_id, name, source, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $name, $source, $status]);

        header('Location: /erp/crm/leads');
        exit;
    }

    // --- Opportunities ---

    public function opportunities()
    {
        $stmt = $this->pdo->prepare("SELECT o.*, l.name as lead_name, c.name as customer_name FROM erp_crm_opportunities o LEFT JOIN erp_crm_leads l ON o.lead_id = l.id LEFT JOIN erp_crm_customers c ON o.customer_id = c.id WHERE o.tenant_id = ? ORDER BY o.created_at DESC");
        $stmt->execute([$this->tenantId]);
        $opportunities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch leads and customers for the modal
        $leads = $this->pdo->query("SELECT id, name FROM erp_crm_leads WHERE tenant_id = {$this->tenantId}")->fetchAll(PDO::FETCH_ASSOC);
        $customers = $this->pdo->query("SELECT id, name FROM erp_crm_customers WHERE tenant_id = {$this->tenantId}")->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/crm/opportunities/index.php';
    }

    public function storeOpportunity()
    {
        $title = $_POST['title'];
        $value = $_POST['value'];
        $stage = 'prospecting';
        $lead_id = !empty($_POST['lead_id']) ? $_POST['lead_id'] : null;
        $customer_id = !empty($_POST['customer_id']) ? $_POST['customer_id'] : null;

        $stmt = $this->pdo->prepare("INSERT INTO erp_crm_opportunities (tenant_id, title, value, stage, lead_id, customer_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $title, $value, $stage, $lead_id, $customer_id]);

        header('Location: /erp/crm/opportunities');
        exit;
    }

    // --- Sales ---

    public function sales()
    {
        // Simple listing for now
        $stmt = $this->pdo->prepare("SELECT s.*, c.name as customer_name FROM erp_crm_sales s JOIN erp_crm_customers c ON s.customer_id = c.id WHERE s.tenant_id = ? ORDER BY s.sale_date DESC");
        $stmt->execute([$this->tenantId]);
        $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch customers and inventory for new sale modal
        $customers = $this->pdo->query("SELECT id, name FROM erp_crm_customers WHERE tenant_id = {$this->tenantId}")->fetchAll(PDO::FETCH_ASSOC);
        $inventory = $this->pdo->query("SELECT id, name, unit_price FROM erp_inventory_items WHERE tenant_id = {$this->tenantId} AND status='active'")->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/crm/sales/index.php';
    }

    public function storeSale()
    {
        // For MVP, single item sale or simplified logic
        $customer_id = $_POST['customer_id'];
        $inventory_id = $_POST['inventory_item_id'];
        $quantity = $_POST['quantity'];
        
        // Fetch item price
        $stmt = $this->pdo->prepare("SELECT unit_price FROM erp_inventory_items WHERE id = ?");
        $stmt->execute([$inventory_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        $price = $item['unit_price'];
        $total = $price * $quantity;

        $this->pdo->beginTransaction();

        try {
            // 1. Create Sale Record
            $stmt = $this->pdo->prepare("INSERT INTO erp_crm_sales (tenant_id, customer_id, total_amount, status) VALUES (?, ?, ?, 'completed')");
            $stmt->execute([$this->tenantId, $customer_id, $total]);
            $sale_id = $this->pdo->lastInsertId();

            // 2. Add Sale Item
            $stmt = $this->pdo->prepare("INSERT INTO erp_crm_sale_items (sale_id, inventory_item_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$sale_id, $inventory_id, $quantity, $price, $total]);

            // 3. Update Inventory Stock (Simple decrement)
            $stmt = $this->pdo->prepare("UPDATE erp_inventory_items SET stock_quantity = stock_quantity - ? WHERE id = ?");
            $stmt->execute([$quantity, $inventory_id]);

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            // Handle error silently for now or log it
        }

        header('Location: /erp/crm/sales');
        exit;
    }
    public function pipeline()
    {
        // Get All Stages
        $stmtStages = $this->pdo->prepare("SELECT * FROM erp_crm_stages WHERE tenant_id = ? ORDER BY sort_order ASC");
        $stmtStages->execute([$this->tenantId]);
        $stages = $stmtStages->fetchAll(PDO::FETCH_ASSOC);
        
        // Get Leads grouped by Stage
        $pipelineData = [];
        foreach ($stages as $stage) {
            $stmt = $this->pdo->prepare("SELECT * FROM erp_crm_leads WHERE stage_id = ? AND tenant_id = ? ORDER BY created_at DESC");
            $stmt->execute([$stage['id'], $this->tenantId]);
            $pipelineData[$stage['id']] = [
                'stage' => $stage,
                'leads' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        }
        
        // Catch-all for leads with no stage (legacy)
        $stmtNoStage = $this->pdo->prepare("SELECT * FROM erp_crm_leads WHERE (stage_id IS NULL OR stage_id = 0) AND tenant_id = ?");
        $stmtNoStage->execute([$this->tenantId]);
        $unassigned = $stmtNoStage->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($unassigned)) {
             // If we have unassigned, we should probably auto-assign to first stage or show separate.
             // For now, let's treat them as first stage items or a "Backlog"
             if (isset($stages[0])) {
                  $pipelineData[$stages[0]['id']]['leads'] = array_merge($pipelineData[$stages[0]['id']]['leads'], $unassigned);
             }
        }

        require __DIR__ . '/../views/crm/pipeline.php';
    }

    public function updateStage()
    {
        // Expect JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        $leadId = $input['leadId'];
        $stageId = $input['stageId'];

        if (!$leadId || !$stageId) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing lead or stage ID']);
            return;
        }

        $stmt = $this->pdo->prepare("UPDATE erp_crm_leads SET stage_id = ? WHERE id = ? AND tenant_id = ?");
        $result = $stmt->execute([$stageId, $leadId, $this->tenantId]);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Update failed']);
        }
        exit;
    }
}
