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

        require __DIR__ . '/../Views/crm/customers/index.php';
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

        require __DIR__ . '/../Views/crm/leads/index.php';
    }

    public function storeLead()
    {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? null;
        $phone = $_POST['phone'] ?? null;
        $company = $_POST['company'] ?? null;
        $notes = $_POST['notes'] ?? null;
        $source = $_POST['source'] ?? 'Other';
        $status = 'new'; 

        $stmt = $this->pdo->prepare("
            INSERT INTO erp_crm_leads 
            (tenant_id, name, email, phone, company, source, status, notes, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$this->tenantId, $name, $email, $phone, $company, $source, $status, $notes]);

        \App\Core\Services\EventBus::publish($this->tenantId, 'CRM', 'lead_created', [
            'name' => $name,
            'email' => $email,
            'source' => $source
        ]);

        // Award lead scoring points for profile completeness
        try {
            $newLeadId = $this->pdo->lastInsertId();
            
            try {
                \App\Core\Services\WorkflowEngineService::dispatch($this->tenantId, 'lead_created', [
                    'lead_id' => $newLeadId,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'company' => $company,
                    'source' => $source
                ]);
            } catch (\Exception $e) {
                error_log('Workflow trigger error (lead_created): ' . $e->getMessage());
            }

            $profilePoints = 0;
            if (!empty($email)) $profilePoints += 5;
            if (!empty($phone)) $profilePoints += 5;
            if (!empty($company)) $profilePoints += 3;
            if ($profilePoints > 0) {
                \App\Core\Services\LeadScoringService::addPoints($this->tenantId, (int)$newLeadId, 'Profile completeness bonus', $profilePoints);
            }
        } catch (\Exception $e) {
            error_log('Lead scoring error: ' . $e->getMessage());
        }

        header('Location: /erp/crm/leads');
        exit;
    }

    public function updateLead()
    {
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? null;
        $phone = $_POST['phone'] ?? null;
        $company = $_POST['company'] ?? null;
        $notes = $_POST['notes'] ?? null;
        $source = $_POST['source'] ?? 'Other';
        $status = $_POST['status'] ?? 'new';

        if ($id) {
            $stmt = $this->pdo->prepare("
                UPDATE erp_crm_leads 
                SET name = ?, email = ?, phone = ?, company = ?, source = ?, status = ?, notes = ?, updated_at = NOW()
                WHERE id = ? AND tenant_id = ?
            ");
            $stmt->execute([$name, $email, $phone, $company, $source, $status, $notes, $id, $this->tenantId]);
        }

        header('Location: /erp/crm/leads');
        exit;
    }

    public function deleteLead()
    {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $stmt = $this->pdo->prepare("DELETE FROM erp_crm_leads WHERE id = ? AND tenant_id = ?");
            $stmt->execute([$id, $this->tenantId]);
        }
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

        require __DIR__ . '/../Views/crm/opportunities/index.php';
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

        $stmtTenant = $this->pdo->prepare("SELECT currency FROM tenants WHERE id = ?");
        $stmtTenant->execute([$this->tenantId]);
        $tenantData = $stmtTenant->fetch(PDO::FETCH_ASSOC) ?: ['currency' => '$'];
        
        $cCode = $tenantData['currency'] ?? '$';
        $currencySymbol = $cCode;
        if ($cCode === 'NGN') $currencySymbol = '₦';
        if ($cCode === 'USD') $currencySymbol = '$';
        if ($cCode === 'GBP') $currencySymbol = '£';
        if ($cCode === 'EUR') $currencySymbol = '€';

        require __DIR__ . '/../Views/crm/sales/index.php';
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

        require __DIR__ . '/../Views/crm/pipeline.php';
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
            try {
                \App\Core\Services\WorkflowEngineService::dispatch($this->tenantId, 'stage_changed', [
                    'lead_id' => (int)$leadId,
                    'stage_id' => (int)$stageId
                ]);
            } catch (\Exception $e) {
                error_log('Workflow trigger error (stage_changed): ' . $e->getMessage());
            }

            // Award lead scoring points for pipeline advancement
            try {
                \App\Core\Services\LeadScoringService::addPoints($this->tenantId, (int)$leadId, 'Pipeline stage advanced', 10);
            } catch (\Exception $e) {
                error_log('Lead scoring error: ' . $e->getMessage());
            }
            
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Update failed']);
        }
        exit;
    }

    public function storePipelineLead()
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $source = 'Pipeline Board';
        $status = 'new';
        $stageId = !empty($_POST['stage_id']) ? (int)$_POST['stage_id'] : null;

        if (empty($name)) {
            die("Lead name is required");
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO erp_crm_leads 
            (tenant_id, name, email, phone, company, source, status, notes, stage_id, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $this->tenantId,
            $name,
            empty($email) ? null : $email,
            empty($phone) ? null : $phone,
            empty($company) ? null : $company,
            $source,
            $status,
            empty($notes) ? null : $notes,
            $stageId
        ]);

        header('Location: /erp/crm/leads');
        exit;
    }


    // ==========================================
    // CUSTOMERS (Edit/Update/Delete)
    // ==========================================
    public function updateCustomer() {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $company = $_POST['company'];
        $phone = $_POST['phone'];
        $stmt = $this->pdo->prepare("UPDATE erp_crm_customers SET name = ?, email = ?, company = ?, phone = ? WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$name, $email, $company, $phone, $id, $this->tenantId]);
        header('Location: /erp/crm/customers');
        exit;
    }

    public function deleteCustomer() {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_crm_customers WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        header('Location: /erp/crm/customers');
        exit;
    }

    // ==========================================
    // OPPORTUNITIES (Edit/Update/Delete)
    // ==========================================
    public function updateOpportunity() {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $lead_id = $_POST['lead_id'] ?: null;
        $customer_id = $_POST['customer_id'] ?: null;
        $amount = $_POST['amount'];
        $stage = $_POST['stage'];
        $close_date = $_POST['close_date'];
        $stmt = $this->pdo->prepare("UPDATE erp_crm_opportunities SET title=?, lead_id=?, customer_id=?, amount=?, stage=?, close_date=? WHERE id=? AND tenant_id=?");
        $stmt->execute([$title, $lead_id, $customer_id, $amount, $stage, $close_date, $id, $this->tenantId]);
        header('Location: /erp/crm/opportunities');
        exit;
    }

    public function deleteOpportunity() {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_crm_opportunities WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        header('Location: /erp/crm/opportunities');
        exit;
    }

    // ==========================================
    // SALES (Edit/Update/Delete)
    // ==========================================
    public function updateSale() {
        $id = $_POST['id'];
        $customer_id = $_POST['customer_id'] ?: null;
        $amount = $_POST['amount'];
        $sale_date = $_POST['sale_date'];
        $status = $_POST['status'];
        $stmt = $this->pdo->prepare("UPDATE erp_crm_sales SET customer_id=?, amount=?, sale_date=?, status=? WHERE id=? AND tenant_id=?");
        $stmt->execute([$customer_id, $amount, $sale_date, $status, $id, $this->tenantId]);
        header('Location: /erp/crm/sales');
        exit;
    }

    public function deleteSale() {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_crm_sales WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        header('Location: /erp/crm/sales');
        exit;
    }

    public function clients()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM erp_clients WHERE tenant_id = ? ORDER BY company_name ASC");
        $stmt->execute([$this->tenantId]);
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../Views/crm/clients/index.php';
    }

    public function createClient()
    {
        require __DIR__ . '/../Views/crm/clients/create.php';
    }

    public function storeClient()
    {
        $companyName = trim($_POST['company_name'] ?? '');
        $contactPerson = trim($_POST['contact_person'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        $stmt = $this->pdo->prepare("INSERT INTO erp_clients (tenant_id, company_name, contact_person, email, phone, status) VALUES (?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$this->tenantId, $companyName, $contactPerson, $email, $phone]);

        header('Location: /erp/clients');
        exit;
    }
}
