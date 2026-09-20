<?php

namespace App\Modules\CasjoeAcademy\Controllers;

use App\Core\Auth;
use App\Core\Database;

class KnowledgeBaseController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function index()
    {
        $user = Auth::user();
        
        $stmt = $this->pdo->query("SELECT * FROM academy_knowledge_base LIMIT 1");
        $kb = $stmt->fetch();
        $content = $kb ? $kb['content'] : '<h1>No content available</h1>';

        $canEdit = ($user && in_array($user['tenant_id'], [13, 14]));

        require __DIR__ . '/../Views/kb/index.php';
    }

    public function edit()
    {
        $user = Auth::user();
        
        if (!$user || !in_array($user['tenant_id'], [13, 14])) {
            header('Location: /academy/knowledge-base');
            exit;
        }

        $stmt = $this->pdo->query("SELECT * FROM academy_knowledge_base LIMIT 1");
        $kb = $stmt->fetch();
        $content = $kb ? $kb['content'] : '';

        require __DIR__ . '/../Views/kb/edit.php';
    }

    public function update()
    {
        $user = Auth::user();
        
        if (!$user || !in_array($user['tenant_id'], [13, 14])) {
            http_response_code(403);
            echo "Unauthorized";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = $_POST['content'] ?? '';

            // Update the single row
            $stmt = $this->pdo->query("SELECT id FROM academy_knowledge_base LIMIT 1");
            $kb = $stmt->fetch();

            if ($kb) {
                $update = $this->pdo->prepare("UPDATE academy_knowledge_base SET content = ? WHERE id = ?");
                $update->execute([$content, $kb['id']]);
            } else {
                $insert = $this->pdo->prepare("INSERT INTO academy_knowledge_base (content) VALUES (?)");
                $insert->execute([$content]);
            }

            header('Location: /academy/knowledge-base?success=1');
            exit;
        }
    }
}
