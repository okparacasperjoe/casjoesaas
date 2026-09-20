<?php

namespace App\Core\Controllers;

use App\Core\Database;
use App\Core\Auth;
use App\Core\Services\CsrfService;

class AdminBotController
{
    private $db;

    public function __construct()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        if (!\App\Core\Auth::isSuperAdmin() && !\App\Core\Auth::isModerator()) {
            \App\Core\Auth::denySuperAdminAccess('AI Bot Training Management');
        }

        $this->db = Database::getInstance();
    }

    public function index()
    {
        $rules = [];
        try {
            $stmt = $this->db->query("SELECT * FROM system_bot_rules ORDER BY id DESC");
            if ($stmt) {
                $rules = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        } catch (\Exception $e) {
            // Table might not exist yet
        }
        
        require __DIR__ . '/../Views/admin/bot_training.php';
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfService::validateToken($_POST['csrf_token'] ?? '')) {
            header("Location: /casper-joe/bot-training?error=Invalid Request");
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $keywords = trim($_POST['keywords'] ?? '');
        $response = trim($_POST['response'] ?? '');

        if (empty($keywords) || empty($response)) {
            header("Location: /casper-joe/bot-training?error=All fields are required");
            exit;
        }

        try {
            if ($id > 0) {
                $stmt = $this->db->prepare("UPDATE system_bot_rules SET keywords = ?, response = ? WHERE id = ?");
                $stmt->execute([$keywords, $response, $id]);
            } else {
                $stmt = $this->db->prepare("INSERT INTO system_bot_rules (tenant_id, keywords, response) VALUES (1, ?, ?)");
                $stmt->execute([$keywords, $response]);
            }
            header("Location: /casper-joe/bot-training?msg=Rule saved successfully");
        } catch (\Exception $e) {
            header("Location: /casper-joe/bot-training?error=" . urlencode("Database error: " . $e->getMessage()));
        }
        exit;
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfService::validateToken($_POST['csrf_token'] ?? '')) {
            header("Location: /casper-joe/bot-training?error=Invalid Request");
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if ($id > 0) {
            try {
                $stmt = $this->db->prepare("DELETE FROM system_bot_rules WHERE id = ?");
                $stmt->execute([$id]);
                header("Location: /casper-joe/bot-training?msg=Rule deleted successfully");
                exit;
            } catch (\Exception $e) {
                // Ignore error
            }
        }
        
        header("Location: /casper-joe/bot-training?error=Failed to delete rule");
        exit;
    }
}
