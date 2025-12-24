<?php

namespace App\Modules\CasjoeMail\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\SubscriptionManager;
use App\Core\Services\GeminiService;
use PDO;

class MailController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        // Basic Init
    }

    private function init() {
        $this->tenantId = TenantContext::getTenantId();
        SubscriptionManager::requireActive($this->tenantId);
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function index()
    {
        $this->init();
        
        // Dashboard Stats
        $stats = [
            'campaigns' => $this->pdo->query("SELECT COUNT(*) FROM cm_campaigns WHERE tenant_id = {$this->tenantId}")->fetchColumn(),
            'subscribers' => $this->pdo->query("SELECT COUNT(*) FROM cm_subscribers WHERE tenant_id = {$this->tenantId}")->fetchColumn(),
            'emails_sent' => $this->pdo->query("SELECT COALESCE(SUM(sent_count), 0) FROM cm_campaigns WHERE tenant_id = {$this->tenantId}")->fetchColumn(),
        ];

        require __DIR__ . '/../views/dashboard.php';
    }

    public function campaigns()
    {
        $this->init();
        $stmt = $this->pdo->query("SELECT * FROM cm_campaigns WHERE tenant_id = {$this->tenantId} ORDER BY created_at DESC");
        $campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../views/campaigns/index.php';
    }

    public function createCampaign()
    {
        $this->init();
        // Fetch User and System Templates
        $stmt = $this->pdo->prepare("SELECT * FROM cm_templates WHERE tenant_id = ? OR tenant_id IS NULL");
        $stmt->execute([$this->tenantId]);
        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch Lists
        $stmt = $this->pdo->prepare("SELECT * FROM cm_lists WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/campaigns/create.php';
    }

    public function generateAiContent()
    {
        header('Content-Type: application/json');
        
        // Basic Security
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $prompt = $_POST['prompt'] ?? '';
        $tone = $_POST['tone'] ?? 'professional';

        if (empty($prompt)) {
            echo json_encode(['error' => 'Prompt is required']);
            exit;
        }

        $gemini = new GeminiService();
        $content = $gemini->generateEmailContent($prompt, $tone);

        echo json_encode(['content' => $content]);
        exit;
    }

    public function templateContent($id)
    {
        $this->init();
        // Return raw content for AJAX
        $id = $_GET['id'] ?? 0;
        $stmt = $this->pdo->prepare("SELECT content FROM cm_templates WHERE id = ? AND (tenant_id = ? OR tenant_id IS NULL)");
        $stmt->execute([$id, $this->tenantId]);
        echo $stmt->fetchColumn();
        exit;
    }
}
