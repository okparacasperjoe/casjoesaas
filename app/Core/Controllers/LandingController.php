<?php

namespace App\Core\Controllers;

use App\Core\Auth;
use App\Core\Database;
use PDO;

class LandingController
{
    public function index()
    {
        // Optional: If logged in, pass user data or redirect?
        // For now, just show landing page. Frontend can show "Dashboard" button if logged in.
        $isLoggedIn = Auth::check();
        
        // Fetch CMS Content
        $db = Database::getInstance();
        $settings = [];
        try {
            $stmt = $db->query("SELECT setting_key, setting_value FROM system_settings");
            while ($row = $stmt->fetch()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (\Exception $e) { /* Ignore */ }
        
        // Safe DB Migration check for is_published and SEO columns
        $pdo = $db->getConnection();
        try {
            $stmt = $pdo->query("DESCRIBE cms_posts");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!in_array('is_published', $columns)) {
                $pdo->exec("ALTER TABLE cms_posts ADD COLUMN is_published TINYINT(1) DEFAULT 1");
            }
            if (!in_array('meta_title', $columns)) {
                $pdo->exec("ALTER TABLE cms_posts ADD COLUMN meta_title VARCHAR(255) DEFAULT NULL");
            }
            if (!in_array('meta_description', $columns)) {
                $pdo->exec("ALTER TABLE cms_posts ADD COLUMN meta_description TEXT DEFAULT NULL");
            }
            if (!in_array('meta_keywords', $columns)) {
                $pdo->exec("ALTER TABLE cms_posts ADD COLUMN meta_keywords VARCHAR(500) DEFAULT NULL");
            }
        } catch (\Exception $e) { /* Ignore */ }

        // Fetch 3 Latest Posts
        $latestPosts = [];
        try {
            $latestPosts = $db->query("SELECT * FROM cms_posts WHERE is_published = 1 ORDER BY created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) { /* Ignore */ }

        require __DIR__ . '/../../Views/landing.php';
    }
}
