<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

class IntegrationController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
    }

    public function index()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM erp_crm_integrations WHERE tenant_id = ? ORDER BY created_at DESC");
        $stmt->execute([$this->tenantId]);
        $integrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/crm/integrations/index.php';
    }

    public function create()
    {
        // Helper to get users for "Assign To" dropdown
        $stmt = $this->pdo->prepare("SELECT id, name FROM users WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Helper to get pipeline stages
        $stmt2 = $this->pdo->prepare("SELECT id, name FROM erp_crm_stages WHERE tenant_id = ? ORDER BY sort_order ASC");
        $stmt2->execute([$this->tenantId]);
        $stages = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/crm/integrations/create.php';
    }

    public function store()
    {
        $name = $_POST['name'];
        $provider = $_POST['provider'];
        $assigned_to = $_POST['assigned_to'] ?? null;
        $stage_id = $_POST['stage_id'] ?? null;

        // Generate a random secret for the webhook
        $webhook_secret = bin2hex(random_bytes(16));

        $config = json_encode([
            'assigned_to' => $assigned_to,
            'stage_id' => $stage_id
        ]);

        $stmt = $this->pdo->prepare("INSERT INTO erp_crm_integrations (tenant_id, name, provider, webhook_secret, config, is_active) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->execute([$this->tenantId, $name, $provider, $webhook_secret, $config]);

        header('Location: /erp/crm/integrations');
        exit;
    }

    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /erp/crm/integrations');
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM erp_crm_integrations WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $integration = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$integration) {
            header('Location: /erp/crm/integrations');
            exit;
        }

        $config = json_decode($integration['config'], true);

        // Helper to get users for "Assign To" dropdown
        $stmtUser = $this->pdo->prepare("SELECT id, name FROM users WHERE tenant_id = ?");
        $stmtUser->execute([$this->tenantId]);
        $users = $stmtUser->fetchAll(PDO::FETCH_ASSOC);

         // Helper to get pipeline stages
        $stmtStage = $this->pdo->prepare("SELECT id, name FROM erp_crm_stages WHERE tenant_id = ? ORDER BY sort_order ASC");
        $stmtStage->execute([$this->tenantId]);
        $stages = $stmtStage->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/crm/integrations/edit.php';
    }

    public function update()
    {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $assigned_to = $_POST['assigned_to'] ?? null;
        $stage_id = $_POST['stage_id'] ?? null;
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        // Fetch existing config to merge or overwrite
        $stmt = $this->pdo->prepare("SELECT config FROM erp_crm_integrations WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $config = $current['config'] ? json_decode($current['config'], true) : [];
        $config['assigned_to'] = $assigned_to;
        $config['stage_id'] = $stage_id;

        $stmt = $this->pdo->prepare("UPDATE erp_crm_integrations SET name = ?, config = ?, is_active = ? WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$name, json_encode($config), $is_active, $id, $this->tenantId]);

        header('Location: /erp/crm/integrations');
        exit;
    }

    public function delete()
    {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_crm_integrations WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);

        header('Location: /erp/crm/integrations');
        exit;
    }

    public function embed()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /erp/crm/integrations');
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM erp_crm_integrations WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $integration = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$integration) {
            header('Location: /erp/crm/integrations');
            exit;
        }

        require __DIR__ . '/../Views/crm/integrations/embed.php';
    }
}
