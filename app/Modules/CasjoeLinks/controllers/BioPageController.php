<?php

namespace App\Modules\CasjoeLinks\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;

class BioPageController
{
    public function index()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();

        $stmt = $db->query("SELECT * FROM links_bio_pages WHERE tenant_id = ? AND user_id = ? ORDER BY created_at DESC", 
            [$user['tenant_id'], $user['id']]);
        $pages = $stmt->fetchAll();

        require __DIR__ . '/../Views/bio/index.php';
    }

    public function create()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }
        require __DIR__ . '/../Views/bio/create.php';
    }

    public function store()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();

        $title = $_POST['title'];
        $slug = $_POST['slug'];

        // Basic validation
        if (empty($title) || empty($slug)) {
            // In real app, flash error
            header('Location: /links/bio/create?error=missing_fields');
            exit;
        }

        try {
            $db->query("INSERT INTO links_bio_pages (tenant_id, user_id, title, slug, theme_config, blocks, settings) VALUES (?, ?, ?, ?, ?, ?, ?)", 
                [
                    $user['tenant_id'], 
                    $user['id'], 
                    $title, 
                    $slug, 
                    json_encode(['bg_color' => '#ffffff', 'text_color' => '#000000']), // Default theme
                    json_encode([]), // Empty blocks
                    json_encode(['seo_title' => $title])
                ]
            );
            header('Location: /links/bio');
        } catch (\PDOException $e) {
             header('Location: /links/bio/create?error=slug_taken');
        }
    }

    private function getTemplates()
    {
        return [
            'light' => ['name' => 'Minimal Light', 'bg_color' => '#ffffff', 'text_color' => '#333333', 'btn_bg' => '#f1f5f9', 'btn_text' => '#1e293b'],
            'dark' => ['name' => 'Midnight Dark', 'bg_color' => '#0f172a', 'text_color' => '#f8fafc', 'btn_bg' => '#1e293b', 'btn_text' => '#ffffff'],
            'glass' => ['name' => 'Frosted Glass', 'bg_color' => '#cbd5e1', 'text_color' => '#0f172a', 'btn_bg' => 'rgba(255,255,255,0.4)', 'btn_text' => '#0f172a', 'glass' => true],
            'neon' => ['name' => 'Cyber Neon', 'bg_color' => '#000000', 'text_color' => '#00ffcc', 'btn_bg' => '#111111', 'btn_text' => '#ff00ff'],
            'casjoe' => ['name' => 'Casjoe Corporate', 'bg_color' => '#000066', 'text_color' => '#ffffff', 'btn_bg' => '#FFA600', 'btn_text' => '#000066'],
            
            // New Premium
            'sunset' => ['name' => 'Sunset Glow', 'bg_color' => 'linear-gradient(135deg, #f6d365 0%, #fda085 100%)', 'text_color' => '#ffffff', 'btn_bg' => 'rgba(255,255,255,0.2)', 'btn_text' => '#ffffff', 'glass' => true],
            'ocean' => ['name' => 'Deep Ocean', 'bg_color' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)', 'text_color' => '#0f172a', 'btn_bg' => '#ffffff', 'btn_text' => '#0f172a'],
            'nature' => ['name' => 'Forest Retreat', 'bg_color' => '#2d3a2e', 'text_color' => '#e8eedc', 'btn_bg' => '#e8eedc', 'btn_text' => '#2d3a2e'],
            'retro' => ['name' => '90s Retro', 'bg_color' => '#ff00ff', 'text_color' => '#ffff00', 'btn_bg' => '#00ffff', 'btn_text' => '#000000', 'border' => '4px solid #000000'],
            'luxury' => ['name' => 'Luxury Gold', 'bg_color' => '#050505', 'text_color' => '#dfba6b', 'btn_bg' => 'linear-gradient(135deg, #dfba6b 0%, #c29b46 100%)', 'btn_text' => '#050505'],
        ];
    }

    public function edit($params)
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();
        $id = $params['id'] ?? 0;

        $stmt = $db->prepare("SELECT * FROM links_bio_pages WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $user['tenant_id']]);
        $page = $stmt->fetch();

        if (!$page) {
            header('Location: /links/bio');
            exit;
        }
        
        $templates = $this->getTemplates();

        require __DIR__ . '/../Views/bio/edit.php';
    }

    public function update($params)
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();
        $id = $_POST['id'] ?? ($params['id'] ?? 0);

        $stmt = $db->prepare("SELECT * FROM links_bio_pages WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $user['tenant_id']]);
        $page = $stmt->fetch();

        if (!$page) {
            header('Location: /links/bio');
            exit;
        }

        $title = $_POST['title'] ?? $page['title'];
        $slug = $_POST['slug'] ?? $page['slug'];
        $blocks = $_POST['blocks'] ?? $page['blocks'];
        
        // Handle Settings
        $settings = json_decode($page['settings'] ?? '{}', true);
        if (isset($_POST['profile_name'])) $settings['profile_name'] = $_POST['profile_name'];
        if (isset($_POST['description'])) $settings['description'] = $_POST['description'];
        if (isset($_POST['custom_bg_color'])) $settings['custom_bg_color'] = $_POST['custom_bg_color'];
        
        // Handle file uploads/cloud URLs
        if (!empty($_POST['profile_image_url'])) {
            $settings['profile_image'] = $_POST['profile_image_url'];
        } elseif (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            // Very simple file handling (should use proper upload service in production)
            $filename = time() . '_' . $_FILES['profile_image']['name'];
            move_uploaded_file($_FILES['profile_image']['tmp_name'], __DIR__ . '/../../../public/uploads/bio/profiles/' . $filename);
            $settings['profile_image'] = '/uploads/bio/profiles/' . $filename;
        }

        if (!empty($_POST['banner_image_url'])) {
            $settings['banner_image'] = $_POST['banner_image_url'];
        } elseif (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
            $filename = time() . '_' . $_FILES['banner_image']['name'];
            move_uploaded_file($_FILES['banner_image']['tmp_name'], __DIR__ . '/../../../public/uploads/bio/banners/' . $filename);
            $settings['banner_image'] = '/uploads/bio/banners/' . $filename;
        }

        if (!empty($_POST['custom_bg_image_url'])) {
            $settings['custom_bg_image'] = $_POST['custom_bg_image_url'];
        } elseif (isset($_FILES['custom_bg_image']) && $_FILES['custom_bg_image']['error'] === UPLOAD_ERR_OK) {
            $filename = time() . '_' . $_FILES['custom_bg_image']['name'];
            move_uploaded_file($_FILES['custom_bg_image']['tmp_name'], __DIR__ . '/../../../public/uploads/bio/bg/' . $filename);
            $settings['custom_bg_image'] = '/uploads/bio/bg/' . $filename;
        }

        $settingsJson = json_encode($settings);

        // Handle Theme selection
        $theme_config = $page['theme_config'];
        if (!empty($_POST['template_key'])) {
            $templates = $this->getTemplates();
            if (isset($templates[$_POST['template_key']])) {
                $theme_config = json_encode($templates[$_POST['template_key']]);
            }
        }

        $stmt = $db->prepare("UPDATE links_bio_pages SET title = ?, slug = ?, theme_config = ?, settings = ?, blocks = ? WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$title, $slug, $theme_config, $settingsJson, $blocks, $id, $user['tenant_id']]);

        header('Location: /links/bio/edit/' . $id);
        exit;
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

        $stmt = $db->prepare("DELETE FROM links_bio_pages WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $user['tenant_id']]);

        header('Location: /links/bio');
        exit;
    }

    public function subscribe()
    {
        $bioPageId = (int)($_POST['bio_page_id'] ?? 0);
        $slug = trim($_POST['slug'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (!empty($email)) {
            $db = Database::getInstance();
            try {
                $db->query("CREATE TABLE IF NOT EXISTS links_bio_subscribers (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    tenant_id INT NULL,
                    bio_page_id INT NOT NULL,
                    name VARCHAR(255) NULL,
                    email VARCHAR(255) NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_bio_page (bio_page_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

                $stmt = $db->prepare("SELECT tenant_id FROM links_bio_pages WHERE id = ?");
                $stmt->execute([$bioPageId]);
                $tenantId = $stmt->fetchColumn() ?: 1;

                $insert = $db->prepare("INSERT INTO links_bio_subscribers (tenant_id, bio_page_id, name, email) VALUES (?, ?, ?, ?)");
                $insert->execute([$tenantId, $bioPageId, $name, $email]);
            } catch (\Throwable $t) {}
        }

        $redirect = !empty($slug) ? '/bio/' . urlencode($slug) . '?subscribed=1' : '/';
        header("Location: {$redirect}");
        exit;
    }
}

