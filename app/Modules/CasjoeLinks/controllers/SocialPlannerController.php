<?php

namespace App\Modules\CasjoeLinks\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Modules\CasjoeLinks\Services\SocialPlannerService;

class SocialPlannerController
{
    private $db;
    private $tenantId;
    private $userId;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
        $this->userId = $_SESSION['user_id'] ?? 0;
    }

    public function index()
    {
        $stats = [
            'total_scheduled' => 0,
            'total_published' => 0,
            'connected_accounts' => 0
        ];

        // Fetch stats
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM social_posts WHERE tenant_id = ? AND status = 'scheduled'");
        $stmt->execute([$this->tenantId]);
        $stats['total_scheduled'] = $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM social_posts WHERE tenant_id = ? AND status = 'published'");
        $stmt->execute([$this->tenantId]);
        $stats['total_published'] = $stmt->fetchColumn();

        $connectedAccountsList = SocialPlannerService::getConnectedAccounts($this->tenantId);
        $stats['connected_accounts'] = count($connectedAccountsList);

        $recentPosts = SocialPlannerService::getPosts($this->tenantId, ['limit' => 10, 'offset' => 0]);
        $connectedAccounts = $connectedAccountsList;

        require __DIR__ . '/../Views/social/index.php';
    }

    public function composer()
    {
        // Fetch funnels safely
        $funnels = [];
        try {
            $stmt = $this->db->prepare("SELECT id, name FROM sales_funnels WHERE tenant_id = ? AND status = 'active' ORDER BY created_at DESC");
            $stmt->execute([$this->tenantId]);
            $funnels = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $funnels = [];
        }

        // Fetch bio pages safely
        $bioPages = [];
        try {
            $stmt = $this->db->prepare("SELECT id, title, slug FROM links_bio_pages WHERE tenant_id = ? ORDER BY created_at DESC");
            $stmt->execute([$this->tenantId]);
            $bioPages = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $bioPages = [];
        }

        $connectedAccounts = SocialPlannerService::getConnectedAccounts($this->tenantId);

        require __DIR__ . '/../Views/social/composer.php';
    }

    public function store()
    {
        $content = $_POST['content'] ?? '';
        $platforms = $_POST['platforms'] ?? [];
        $scheduledAt = $_POST['scheduled_at'] ?? null;
        $status = $_POST['status'] ?? 'draft'; // 'draft', 'scheduled', 'publish_now'
        $mediaUrl = $_POST['media_url'] ?? null;
        $linkType = $_POST['link_type'] ?? null;
        $linkUrl = $_POST['link_url'] ?? null;

        if ($status === 'publish_now') {
            $status = 'published';
            $publishedAt = date('Y-m-d H:i:s');
        } else {
            $publishedAt = null;
        }

        $data = [
            'content' => $content,
            'platforms' => $platforms,
            'scheduled_at' => $scheduledAt,
            'status' => $status,
            'media_url' => $mediaUrl,
            'link_type' => $linkType,
            'link_url' => $linkUrl,
            'published_at' => $publishedAt
        ];

        SocialPlannerService::createPost($this->tenantId, $this->userId, $data);

        header('Location: /links/social?success=posted');
        exit;
    }

    public function calendar()
    {
        $month = $_GET['month'] ?? date('Y-m');
        $calendarPosts = SocialPlannerService::getCalendarPosts($this->tenantId, $month);

        require __DIR__ . '/../Views/social/calendar.php';
    }

    public function aiGenerate()
    {
        $topic = $_POST['topic'] ?? '';
        $platform = $_POST['platform'] ?? '';
        $linkUrl = $_POST['link_url'] ?? '';

        $variations = SocialPlannerService::generateAICaption($topic, $platform, $linkUrl);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'variations' => $variations]);
        exit;
    }

    public function connectAccount()
    {
        // POST endpoint to connect/toggle a social platform account
        $platform = $_POST['platform'] ?? '';
        $action = $_POST['action'] ?? 'connect';

        if ($action === 'connect') {
            // connection logic...
        } else {
            // disconnect logic...
        }

        header('Location: /links/social');
        exit;
    }

    public function delete()
    {
        $id = $_POST['id'] ?? 0;
        
        $stmt = $this->db->prepare("DELETE FROM social_posts WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);

        header('Location: /links/social?success=deleted');
        exit;
    }
}
