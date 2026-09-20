<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class WorkflowController
{
    private \PDO $db;
    private int $tenantId;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
    }

    public function index()
    {
        // Fetch all workflows with step count and last 5 logs
        $stmt = $this->db->prepare("
            SELECT w.*, 
                   (SELECT COUNT(*) FROM crm_workflow_steps WHERE workflow_id = w.id) as step_count
            FROM crm_workflows w 
            WHERE w.tenant_id = :tenant_id
        ");
        $stmt->execute(['tenant_id' => $this->tenantId]);
        $workflows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Fetch last 5 logs for each workflow (this can be done via another query or loop)
        foreach ($workflows as &$workflow) {
            $logStmt = $this->db->prepare("
                SELECT * FROM crm_workflow_logs 
                WHERE workflow_id = :workflow_id 
                ORDER BY created_at DESC LIMIT 5
            ");
            $logStmt->execute(['workflow_id' => $workflow['id']]);
            $workflow['recent_logs'] = $logStmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        // Fetch pipeline stages
        $stmt = $this->db->prepare("SELECT id, name FROM erp_crm_stages WHERE tenant_id = :tenant_id ORDER BY sort_order ASC");
        $stmt->execute(['tenant_id' => $this->tenantId]);
        $stages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("SELECT id, title as name FROM smart_forms WHERE tenant_id = :tenant_id");
        $stmt->execute(['tenant_id' => $this->tenantId]);
        $forms = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/crm/workflows/index.php';
    }

    public function create()
    {
        $workflow = null;

        $stmt = $this->db->prepare("SELECT id, name FROM erp_crm_stages WHERE tenant_id = :tenant_id ORDER BY sort_order ASC");
        $stmt->execute(['tenant_id' => $this->tenantId]);
        $stages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("SELECT id, title as name FROM smart_forms WHERE tenant_id = :tenant_id");
        $stmt->execute(['tenant_id' => $this->tenantId]);
        $forms = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/crm/workflows/builder.php';
    }

    public function store()
    {
        $name = $_POST['name'] ?? '';
        $triggerType = $_POST['trigger_type'] ?? '';
        $triggerConfig = $_POST['trigger_config'] ?? '{}';
        $steps = isset($_POST['steps']) ? json_decode($_POST['steps'], true) : [];

        if (empty($name) || empty($triggerType)) {
            header('Location: /erp/crm/workflows/create?error=Validation failed');
            exit;
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("
                INSERT INTO crm_workflows (tenant_id, name, trigger_type, trigger_config, is_active, created_at) 
                VALUES (:tenant_id, :name, :trigger_type, :trigger_config, 1, NOW())
            ");
            $stmt->execute([
                'tenant_id' => $this->tenantId,
                'name' => $name,
                'trigger_type' => $triggerType,
                'trigger_config' => $triggerConfig
            ]);
            
            $workflowId = $this->db->lastInsertId();

            if (!empty($steps) && is_array($steps)) {
                $stepStmt = $this->db->prepare("
                    INSERT INTO crm_workflow_steps (workflow_id, tenant_id, step_order, action_type, action_config) 
                    VALUES (:workflow_id, :tenant_id, :step_order, :action_type, :action_config)
                ");
                foreach ($steps as $index => $step) {
                    $stepStmt->execute([
                        'workflow_id' => $workflowId,
                        'tenant_id' => $this->tenantId,
                        'step_order' => $index + 1,
                        'action_type' => $step['action_type'] ?? '',
                        'action_config' => json_encode($step['action_config'] ?? [])
                    ]);
                }
            }
            
            $this->db->commit();
            header('Location: /erp/crm/workflows');
            exit;
        } catch (\Exception $e) {
            $this->db->rollBack();
            header('Location: /erp/crm/workflows/create?error=Save failed');
            exit;
        }
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;

        $stmt = $this->db->prepare("SELECT * FROM crm_workflows WHERE id = :id AND tenant_id = :tenant_id");
        $stmt->execute(['id' => $id, 'tenant_id' => $this->tenantId]);
        $workflow = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$workflow) {
            header('Location: /erp/crm/workflows');
            exit;
        }

        $stmt = $this->db->prepare("SELECT * FROM crm_workflow_steps WHERE workflow_id = :workflow_id ORDER BY step_order ASC");
        $stmt->execute(['workflow_id' => $id]);
        $steps = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("SELECT id, name FROM erp_crm_stages WHERE tenant_id = :tenant_id ORDER BY sort_order ASC");
        $stmt->execute(['tenant_id' => $this->tenantId]);
        $stages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("SELECT id, title as name FROM smart_forms WHERE tenant_id = :tenant_id");
        $stmt->execute(['tenant_id' => $this->tenantId]);
        $forms = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/crm/workflows/builder.php';
    }

    public function update()
    {
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $triggerType = $_POST['trigger_type'] ?? '';
        $triggerConfig = $_POST['trigger_config'] ?? '{}';
        $steps = isset($_POST['steps']) ? json_decode($_POST['steps'], true) : [];

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("
                UPDATE crm_workflows 
                SET name = :name, trigger_type = :trigger_type, trigger_config = :trigger_config 
                WHERE id = :id AND tenant_id = :tenant_id
            ");
            $stmt->execute([
                'name' => $name,
                'trigger_type' => $triggerType,
                'trigger_config' => $triggerConfig,
                'id' => $id,
                'tenant_id' => $this->tenantId
            ]);

            $stmt = $this->db->prepare("DELETE FROM crm_workflow_steps WHERE workflow_id = :workflow_id");
            $stmt->execute(['workflow_id' => $id]);

            if (!empty($steps) && is_array($steps)) {
                $stepStmt = $this->db->prepare("
                    INSERT INTO crm_workflow_steps (workflow_id, tenant_id, step_order, action_type, action_config) 
                    VALUES (:workflow_id, :tenant_id, :step_order, :action_type, :action_config)
                ");
                foreach ($steps as $index => $step) {
                    $stepStmt->execute([
                        'workflow_id' => $id,
                        'tenant_id' => $this->tenantId,
                        'step_order' => $index + 1,
                        'action_type' => $step['action_type'] ?? '',
                        'action_config' => json_encode($step['action_config'] ?? [])
                    ]);
                }
            }

            $this->db->commit();
            header('Location: /erp/crm/workflows');
            exit;
        } catch (\Exception $e) {
            $this->db->rollBack();
            header('Location: /erp/crm/workflows/edit?id=' . $id . '&error=Update failed');
            exit;
        }
    }

    public function toggle()
    {
        $id = $_POST['id'] ?? 0;
        $isActive = $_POST['is_active'] ?? 0;

        $stmt = $this->db->prepare("
            UPDATE crm_workflows 
            SET is_active = :is_active 
            WHERE id = :id AND tenant_id = :tenant_id
        ");
        $stmt->execute([
            'is_active' => $isActive,
            'id' => $id,
            'tenant_id' => $this->tenantId
        ]);

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    public function delete()
    {
        $id = $_POST['id'] ?? 0;

        $stmt = $this->db->prepare("DELETE FROM crm_workflows WHERE id = :id AND tenant_id = :tenant_id");
        $stmt->execute(['id' => $id, 'tenant_id' => $this->tenantId]);

        header('Location: /erp/crm/workflows');
        exit;
    }

    public function logs()
    {
        $id = $_GET['id'] ?? null;

        $query = "
            SELECT l.*, w.name as workflow_name, ld.first_name, ld.last_name 
            FROM crm_workflow_logs l
            JOIN crm_workflows w ON l.workflow_id = w.id
            LEFT JOIN erp_crm_leads ld ON l.entity_id = ld.id AND l.entity_type = 'lead'
            WHERE w.tenant_id = :tenant_id
        ";
        
        $params = ['tenant_id' => $this->tenantId];

        if ($id) {
            $query .= " AND l.workflow_id = :workflow_id";
            $params['workflow_id'] = $id;
        }

        $query .= " ORDER BY l.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $logs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/crm/workflows/logs.php';
    }

    public function save()
    {
        if (!empty($_POST['id'])) {
            $this->update();
        } else {
            $this->store();
        }
    }

    public function editWithId($id = null)
    {
        if (is_array($id)) {
            $_GET['id'] = $id['id'] ?? 0;
        } elseif ($id) {
            $_GET['id'] = $id;
        }
        $this->edit();
    }
}
