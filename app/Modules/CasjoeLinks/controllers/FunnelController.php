<?php

namespace App\Modules\CasjoeLinks\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class FunnelController
{
    private $db;
    private $tenantId;
    private $userId;

    public function __construct()
    {
        $this->tenantId = TenantContext::getTenantId();
        $this->userId = $_SESSION['user_id'] ?? 0;
        $this->db = Database::getInstance();
    }

    /**
     * List all funnels
     */
    public function index()
    {
        $stmt = $this->db->query(
            "SELECT sf.*, u.name as owner_name, 
                    (SELECT COUNT(*) FROM funnel_sessions WHERE funnel_id = sf.id) as total_sessions
             FROM sales_funnels sf
             LEFT JOIN users u ON sf.owner_id = u.id
             WHERE sf.tenant_id = ?
             ORDER BY sf.created_at DESC",
            [$this->tenantId]
        );
        $funnels = $stmt->fetchAll();

        require __DIR__ . '/../Views/funnels/index.php';
    }

    /**
     * Show funnel type selection OR ERP configuration
     */
    public function create()
    {
        // If type is selected, show ERP configuration
        if (isset($_GET['type'])) {
            $type = $_GET['type'];
            $allowedTypes = ['lead', 'service', 'product', 'event'];
            
            if (!in_array($type, $allowedTypes)) {
                header('Location: /links/funnels/create');
                exit;
            }

            // Fetch stages for selection
            $stmt = $this->db->query(
                "SELECT id, name FROM erp_crm_stages WHERE tenant_id = ? ORDER BY sort_order ASC",
                [$this->tenantId]
            );
            $stages = $stmt->fetchAll();

            // Fetch users for owner selection
            $stmt = $this->db->query(
                "SELECT id, name FROM users WHERE tenant_id = ? ORDER BY name ASC",
                [$this->tenantId]
            );
            $users = $stmt->fetchAll();

            $currentUserId = $this->userId;

            require __DIR__ . '/../Views/funnels/configure.php';
            return;
        }

        // Show type selection
        require __DIR__ . '/../Views/funnels/create.php';
    }

    /**
     * Store new funnel with ERP binding
     */
    public function store()
    {
        $type = $_POST['type'] ?? 'lead';
        $allowedTypes = ['lead', 'service', 'product', 'event'];
        if (!in_array($type, $allowedTypes)) {
            $type = 'lead';
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $name = ucfirst($type) . ' Funnel';
        }

        // Smart fallbacks for stage, owner, and source if not specified
        $stageId = !empty($_POST['default_stage_id']) ? (int)$_POST['default_stage_id'] : 0;
        if (empty($stageId)) {
            $stmt = $this->db->query("SELECT id FROM erp_crm_stages WHERE tenant_id = ? ORDER BY sort_order ASC LIMIT 1", [$this->tenantId]);
            $stageId = (int)$stmt->fetchColumn() ?: 1;
        }

        $leadSource = trim($_POST['lead_source'] ?? '');
        if (empty($leadSource)) {
            $leadSource = ucfirst($type) . ' Funnel';
        }

        $ownerId = !empty($_POST['owner_id']) ? (int)$_POST['owner_id'] : 0;
        if (empty($ownerId)) {
            $ownerId = $this->userId ?: 1;
        }

        // Create funnel with ERP binding
        $stmt = $this->db->prepare(
            "INSERT INTO sales_funnels 
            (tenant_id, user_id, name, type, status, default_stage_id, lead_source, owner_id) 
            VALUES (?, ?, ?, ?, 'draft', ?, ?, ?)"
        );
        $stmt->execute([
            $this->tenantId,
            $this->userId,
            $name,
            $type,
            $stageId,
            $leadSource,
            $ownerId
        ]);
        
        $funnelId = $this->db->getConnection()->lastInsertId();

        // Auto-create default steps (Templates)
        require_once __DIR__ . '/../Services/FunnelBuilder.php';
        $builder = new \App\Modules\CasjoeLinks\Services\FunnelBuilder();

        if ($type === 'lead') {
            $builder->addStep($funnelId, 'landing', $builder->getDefaultConfig('landing'));
            $builder->addStep($funnelId, 'form', $builder->getDefaultConfig('form'));
            $builder->addStep($funnelId, 'thankyou', $builder->getDefaultConfig('thankyou'));
        } elseif ($type === 'product') {
            // High-converting GHL-style revenue funnel: Sales -> Payment with Order Bump -> 1-Click Upsell -> Downsell -> Thank You
            $builder->addStep($funnelId, 'sales', $builder->getDefaultConfig('sales')); 
            $paymentConfig = array_merge($builder->getDefaultConfig('payment'), [
                'has_order_bump' => true,
                'bump_title' => 'VIP Fast-Track Access',
                'bump_amount' => 5000,
                'bump_description' => 'Get instant access to priority onboarding, templates, and video guides.'
            ]);
            $builder->addStep($funnelId, 'payment', $paymentConfig);
            $builder->addStep($funnelId, 'upsell', $builder->getDefaultConfig('upsell'));
            $builder->addStep($funnelId, 'downsell', $builder->getDefaultConfig('downsell'));
            $builder->addStep($funnelId, 'thankyou', $builder->getDefaultConfig('thankyou'));
        } elseif ($type === 'service') {
            $builder->addStep($funnelId, 'landing', $builder->getDefaultConfig('sales')); 
            $builder->addStep($funnelId, 'form', $builder->getDefaultConfig('form'));
            $builder->addStep($funnelId, 'thankyou', $builder->getDefaultConfig('thankyou'));
        } elseif ($type === 'event') {
            $builder->addStep($funnelId, 'landing', $builder->getDefaultConfig('landing'));
            $builder->addStep($funnelId, 'form', $builder->getDefaultConfig('form'));
            $builder->addStep($funnelId, 'thankyou', $builder->getDefaultConfig('thankyou'));
        } else {
            // Fallback
            $builder->addStep($funnelId, 'landing', $builder->getDefaultConfig('landing'));
        }

        // Redirect to builder
        header('Location: /links/funnels/edit/' . $funnelId . '?success=created');
        exit;
    }

    /**
     * Show funnel builder/editor
     */
    public function edit($params)
    {
        $id = is_array($params) ? $params['id'] : $params;
        
        $stmt = $this->db->query(
            "SELECT * FROM sales_funnels WHERE id = ? AND tenant_id = ?",
            [$id, $this->tenantId]
        );
        $funnel = $stmt->fetch();

        if (!$funnel) {
            header('Location: /links/funnels?error=' . urlencode('Funnel not found'));
            exit;
        }

        // Get funnel steps
        $stmt = $this->db->query(
            "SELECT * FROM funnel_steps WHERE funnel_id = ? ORDER BY step_order ASC",
            [$id]
        );
        $steps = $stmt->fetchAll();

        require __DIR__ . '/../Views/funnels/builder.php';
    }

    /**
     * Update funnel
     */
    public function update($params)
    {
        $id = is_array($params) ? $params['id'] : $params;
        
        // Will be implemented in later phases
        header('Location: /links/funnels/edit/' . $id);
        exit;
    }

    /**
     * Delete funnel
     */
    public function delete($params)
    {
        $id = is_array($params) ? $params['id'] : $params;
        
        $this->db->query(
            "DELETE FROM sales_funnels WHERE id = ? AND tenant_id = ?",
            [$id, $this->tenantId]
        );

        header('Location: /links/funnels');
        exit;
    }

    /**
     * Toggle funnel status
     */
    public function toggleStatus($params)
    {
        header('Content-Type: application/json');
        $id = is_array($params) ? $params['id'] : $params;
        
        $input = json_decode(file_get_contents('php://input'), true);
        $newStatus = $input['status'] ?? 'draft';
        
        $allowedStatuses = ['draft', 'active', 'paused', 'archived'];
        if (!in_array($newStatus, $allowedStatuses)) {
            echo json_encode(['success' => false, 'error' => 'Invalid status']);
            exit;
        }
        
        $this->db->query(
            "UPDATE sales_funnels SET status = ? WHERE id = ? AND tenant_id = ?",
            [$newStatus, $id, $this->tenantId]
        );
        
        echo json_encode(['success' => true, 'status' => $newStatus]);
        exit;
    }

    /**
     * Add step to funnel
     */
    public function addStep()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        
        $funnelId = $input['funnel_id'] ?? 0;
        $stepType = $input['step_type'] ?? '';
        
        require_once __DIR__ . '/../Services/FunnelBuilder.php';
        $builder = new \App\Modules\CasjoeLinks\Services\FunnelBuilder();
        
        $config = $builder->getDefaultConfig($stepType);
        $stepId = $builder->addStep($funnelId, $stepType, $config);
        
        echo json_encode(['success' => true, 'step_id' => $stepId]);
        exit;
    }

    /**
     * Update step configuration
     */
    public function updateStep($params)
    {
        header('Content-Type: application/json');
        $stepId = is_array($params) ? $params['id'] : $params;
        $input = json_decode(file_get_contents('php://input'), true);
        
        require_once __DIR__ . '/../Services/FunnelBuilder.php';
        $builder = new \App\Modules\CasjoeLinks\Services\FunnelBuilder();
        $builder->updateStep($stepId, $input['config'] ?? []);
        
        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * Delete step
     */
    public function deleteStep($params)
    {
        header('Content-Type: application/json');
        $stepId = is_array($params) ? $params['id'] : $params;
        $input = json_decode(file_get_contents('php://input'), true);
        $funnelId = $input['funnel_id'] ?? 0;
        
        require_once __DIR__ . '/../Services/FunnelBuilder.php';
        $builder = new \App\Modules\CasjoeLinks\Services\FunnelBuilder();
        $builder->deleteStep($stepId, $funnelId);
        
        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * Get step configuration
     */
    public function getStep($params)
    {
        header('Content-Type: application/json');
        $stepId = is_array($params) ? $params['id'] : $params;
        
        $stmt = $this->db->query("SELECT * FROM funnel_steps WHERE id = ?", [$stepId]);
        $step = $stmt->fetch();
        
        if (!$step) {
             echo json_encode(['success' => false, 'error' => 'Step not found']);
             exit;
        }
        
        echo json_encode(['success' => true, 'config' => json_decode($step['config'] ?? '{}', true)]);
        exit;
    }

    /**
     * Get available templates
     */
    public function getTemplates()
    {
        header('Content-Type: application/json');
        require_once __DIR__ . '/../Services/FunnelBuilder.php';
        $builder = new \App\Modules\CasjoeLinks\Services\FunnelBuilder();
        
        echo json_encode(['success' => true, 'templates' => $builder->getTemplates()]);
        exit;
    }

    /**
     * Apply template to step
     */
    public function applyTemplate()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        
        $stepId = $input['step_id'] ?? 0;
        $stepType = $input['step_type'] ?? '';
        $templateKey = $input['template_key'] ?? '';
        
        if (!$stepId || !$templateKey) {
            echo json_encode(['success' => false, 'error' => 'Missing parameters']);
            exit;
        }

        require_once __DIR__ . '/../Services/FunnelBuilder.php';
        $builder = new \App\Modules\CasjoeLinks\Services\FunnelBuilder();
        $templates = $builder->getTemplates();

        // Find the template config
        $config = [];
        if (isset($templates[$stepType][$templateKey]['config'])) {
            $config = $templates[$stepType][$templateKey]['config'];
        } else {
             echo json_encode(['success' => false, 'error' => 'Template not found']);
             exit;
        }

        // Update the step
        $builder->updateStep($stepId, $config);
        
        echo json_encode(['success' => true, 'config' => $config]);
        exit;
    }
    /**
     * Get available products (from shop_products)
     */
    public function getProducts()
    {
        header('Content-Type: application/json');

        // shop_products has no tenant_id column — query by active status only
        $stmt = $this->db->query(
            "SELECT id, name, price, type
             FROM shop_products
             WHERE status = 'active'
             ORDER BY name ASC"
        );
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Format price for display
        $products = array_map(function($p) {
            return [
                'id'    => $p['id'],
                'name'  => $p['name'],
                'price' => '₦' . number_format((float)$p['price'], 2),
                'type'  => $p['type'],
            ];
        }, $rows);

        echo json_encode(['success' => true, 'products' => $products]);
        exit;
    }

    /**
     * Get available smart forms
     */
    public function getForms()
    {
        header('Content-Type: application/json');
        
        $stmt = $this->db->query(
            "SELECT id, title FROM smart_forms WHERE tenant_id = ? AND status = 'active' ORDER BY title ASC",
            [$this->tenantId]
        );
        $forms = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'forms' => $forms]);
        exit;
    }

    /**
     * Reorder funnel steps (called after drag-and-drop)
     */
    public function reorderSteps()
    {
        header('Content-Type: application/json');
        $input   = json_decode(file_get_contents('php://input'), true);
        $stepIds = $input['step_ids'] ?? [];
        $funnelId = (int)($input['funnel_id'] ?? 0);

        if (empty($stepIds) || !$funnelId) {
            echo json_encode(['success' => false, 'error' => 'Missing parameters']);
            exit;
        }

        require_once __DIR__ . '/../Services/FunnelBuilder.php';
        $builder = new \App\Modules\CasjoeLinks\Services\FunnelBuilder();
        $builder->reorderSteps($funnelId, $stepIds);

        echo json_encode(['success' => true]);
        exit;
    }
}
