<?php

namespace App\Modules\CasjoeLinks\Controllers;

use App\Core\Auth;
use App\Core\Database;

class QrCodeController
{
    public function index()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();

        $stmt = $db->query("SELECT * FROM links_qr_codes WHERE tenant_id = ? AND user_id = ? ORDER BY created_at DESC", 
            [$user['tenant_id'], $user['id']]);
        $qrcodes = $stmt->fetchAll();

        require __DIR__ . '/../Views/qr/index.php';
    }

    public function create()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }
        require __DIR__ . '/../Views/qr/create.php';
    }

    public function store()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();

        $name = $_POST['name'];
        $type = $_POST['type'];
        $content = $_POST['content'];
        $designConfig = $_POST['design_config']; // JSON string from frontend

        if (empty($name) || empty($content)) {
             header('Location: /links/qr/create?error=missing_content');
             exit;
        }

        $db->query("INSERT INTO links_qr_codes (tenant_id, user_id, name, type, content, design_config) VALUES (?, ?, ?, ?, ?, ?)", 
            [$user['tenant_id'], $user['id'], $name, $type, $content, $designConfig]
        );
        header('Location: /links/qr');
    }

    public function delete($params)
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();
        $id = $params['id'] ?? 0;

        $stmt = $db->prepare("DELETE FROM links_qr_codes WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $user['tenant_id']]);

        header('Location: /links/qr');
        exit;
    }
}
