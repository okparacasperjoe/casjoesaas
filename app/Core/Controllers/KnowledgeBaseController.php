<?php

namespace App\Core\Controllers;

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
        $stmt = $this->pdo->query("SELECT * FROM knowledge_base_articles ORDER BY created_at ASC");
        $articles = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $title = "Knowledge Base | Casjoe";
        require __DIR__ . '/../Views/support/kb_index.php';
    }

    public function view($slug)
    {
        // $slug might be an array if using parameterized router matching
        if (is_array($slug) && isset($slug['slug'])) {
            $slug = $slug['slug'];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM knowledge_base_articles WHERE slug = ?");
        $stmt->execute([$slug]);
        $article = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$article) {
            header('Location: /kb');
            exit;
        }

        // Fetch all articles for the sidebar
        $stmtAll = $this->pdo->query("SELECT title, slug FROM knowledge_base_articles ORDER BY created_at ASC");
        $allArticles = $stmtAll->fetchAll(\PDO::FETCH_ASSOC);

        $title = $article['title'] . " - Knowledge Base";
        require __DIR__ . '/../Views/support/kb_article.php';
    }
}
