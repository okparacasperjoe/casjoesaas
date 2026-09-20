<?php
namespace App\Core\AI;
use App\Core\Database;
class ContextAggregator {
    protected Database $db;
    protected int $tenantId;
    public string $companyName = "Casjoe Business";

    public function __construct(int $tenantId) { 
        $this->db = Database::getInstance(); 
        $this->tenantId = $tenantId;
        $this->loadCompanyProfile();
    }

    protected function loadCompanyProfile() {
        try {
            $stmt = $this->db->getConnection()->prepare("SELECT name FROM tenants WHERE id = ?");
            $stmt->execute([$this->tenantId]);
            $tenant = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($tenant && !empty($tenant['name'])) {
                $this->companyName = (string)$tenant['name'];
            }
        } catch (\Exception $e) {}
    }

    public function getDailyContext(): array { 
        $pdo = $this->db->getConnection();
        $today = date("Y-m-d");

        // 1. Today's Revenue
        $revenue = [];
        try {
            $stmt = $pdo->prepare("SELECT SUM(amount) as total, currency FROM cp_transactions WHERE tenant_id = ? AND type = 'credit' AND status = 'successful' AND DATE(created_at) = ? GROUP BY currency");
            $stmt->execute([$this->tenantId, $today]);
            $revenue = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Exception $e) {}

        // 2. CRM - New Leads + Client Count
        $crm = ['new_leads' => 0, 'clients_count' => 0];
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) as new_leads FROM erp_crm_leads WHERE tenant_id = ? AND DATE(created_at) = ?");
            $stmt->execute([$this->tenantId, $today]);
            $leadRow = $stmt->fetch(\PDO::FETCH_ASSOC);
            $crm['new_leads'] = (int)($leadRow['new_leads'] ?? 0);

            $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM erp_crm_customers WHERE tenant_id = ?");
            $stmt->execute([$this->tenantId]);
            $clientRow = $stmt->fetch(\PDO::FETCH_ASSOC);
            $crm['clients_count'] = (int)($clientRow['cnt'] ?? 0);
        } catch (\Exception $e) {}

        // 3. HR - Staff count + Pending job applications
        $hr = ['staff_count' => 0, 'pending_applications' => 0];
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM erp_employees WHERE tenant_id = ? AND status = 'active'");
            $stmt->execute([$this->tenantId]);
            $staffRow = $stmt->fetch(\PDO::FETCH_ASSOC);
            $hr['staff_count'] = (int)($staffRow['cnt'] ?? 0);

            $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM erp_job_applications WHERE tenant_id = ? AND status = 'pending'");
            $stmt->execute([$this->tenantId]);
            $appRow = $stmt->fetch(\PDO::FETCH_ASSOC);
            $hr['pending_applications'] = (int)($appRow['cnt'] ?? 0);
        } catch (\Exception $e) {}

        // 4. Marketing - Email lists
        $marketing = ['email_lists' => 0];
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM mail_lists WHERE tenant_id = ?");
            $stmt->execute([$this->tenantId]);
            $mlRow = $stmt->fetch(\PDO::FETCH_ASSOC);
            $marketing['email_lists'] = (int)($mlRow['cnt'] ?? 0);
        } catch (\Exception $e) {}

        // 5. Finance - Overdue invoices count + Pending tasks
        $finance = ['overdue_invoices' => 0, 'pending_tasks' => 0];
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM erp_invoices WHERE tenant_id = ? AND status IN ('sent','overdue') AND due_date < CURDATE()");
            $stmt->execute([$this->tenantId]);
            $invRow = $stmt->fetch(\PDO::FETCH_ASSOC);
            $finance['overdue_invoices'] = (int)($invRow['cnt'] ?? 0);

            $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM erp_tasks WHERE tenant_id = ? AND status != 'done' AND due_date < CURDATE()");
            $stmt->execute([$this->tenantId]);
            $taskRow = $stmt->fetch(\PDO::FETCH_ASSOC);
            $finance['pending_tasks'] = (int)($taskRow['cnt'] ?? 0);
        } catch (\Exception $e) {}

        return [ 
            "date"      => $today, 
            "financials" => $revenue,
            "crm"       => $crm,
            "hr"        => $hr,
            "marketing" => $marketing,
            "finance"   => $finance,
        ]; 
    }
}
