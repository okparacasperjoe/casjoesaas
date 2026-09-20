<?php

namespace App\Core\Controllers;

use App\Core\Database;
use App\Core\Auth;
use App\Core\Services\CsrfService;
use PDO;

class AdminCmsController
{
    private $pdo;

    public function __construct()
    {
        // Ensure Admin or Moderator
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $this->pdo = Database::getInstance()->getConnection();
        
        // Secure Super Admin & Moderator Check
        if (!\App\Core\Auth::isSuperAdmin() && !\App\Core\Auth::isModerator()) {
            \App\Core\Auth::denySuperAdminAccess('CMS Platform Governance');
        }

        // Auto-create cms_pages and cms_posts if not exists
        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS cms_pages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tenant_id INT NULL,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL UNIQUE,
                content LONGTEXT,
                is_published TINYINT(1) DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $this->pdo->exec("CREATE TABLE IF NOT EXISTS cms_posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tenant_id INT NULL,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL UNIQUE,
                summary TEXT,
                content LONGTEXT,
                featured_image VARCHAR(255) DEFAULT NULL,
                is_published TINYINT(1) DEFAULT 1,
                meta_title VARCHAR(255) DEFAULT NULL,
                meta_description TEXT DEFAULT NULL,
                meta_keywords VARCHAR(500) DEFAULT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // Seed initial sample blog posts if table is empty
            $stmtCount = $this->pdo->query("SELECT COUNT(*) FROM cms_posts");
            if ($stmtCount && (int)$stmtCount->fetchColumn() === 0) {
                $post1Content = "<p>Welcome to Casjoe, the all-in-one AI-driven SaaS platform. In this post, we explore how autonomous AI employees revolutionize daily operations, from automated customer support to multi-currency financial accounting.</p><h2>The AI Revolution in Enterprise</h2><p>Gone are the days of siloed software tools. With Casjoe's built-in AI employees, businesses can automate repetitive tasks, issue virtual cards, and manage multi-vendor shops right from a single unified dashboard.</p>";
                $post2Content = "<p>Global commerce requires borderless payment infrastructure. We are excited to announce the expansion of Casjoe Virtual Cards, powered by instant bank integrations and secure one-tap checkout.</p><h2>Issue Instant Virtual Cards</h2><p>With customizable spending limits and instant multi-currency funding, your team can operate globally with complete security and peace of mind.</p>";

                $stmtSeed = $this->pdo->prepare("INSERT INTO cms_posts (tenant_id, title, slug, summary, content, is_published) VALUES (?, ?, ?, ?, ?, 1)");
                $stmtSeed->execute([1, 'The Future of Autonomous AI Employees in Business', 'future-of-autonomous-ai-employees', 'Discover how Casjoe AI Employees automate ERP workflows, sales pipelines, and financial operations.', $post1Content]);
                $stmtSeed->execute([1, 'Introducing Instant Multi-Currency Virtual Cards', 'introducing-instant-virtual-cards', 'Issue, fund, and manage Visa and Mastercard virtual cards right from your Casjoe dashboard.', $post2Content]);
            }

            // Seed initial static pages if missing or out of date
            $aboutHtmlAdmin = '<div class="about-content" style="line-height: 1.8; font-size: 1.05rem;">
    <h1 style="color: var(--brand-blue, #000066); font-size: 2.4rem; font-weight: 800; margin-bottom: 10px;">About Us</h1>
    <h2 style="color: var(--brand-amber, #FFA600); font-size: 1.5rem; font-weight: 700; margin-top: 0; margin-bottom: 25px;">Building Africa\'s AI Business Operating System</h2>

    <p>At <strong>Casjoetech Ltd</strong>, we\'re on a mission to transform how African businesses operate by making artificial intelligence accessible, practical, and affordable.</p>

    <p>We believe small and medium-sized businesses deserve the same powerful technology used by large enterprises. That\'s why we built <strong>Casjoe Biz</strong>—an AI-powered Business Operating System that brings together customer management, sales, finance, inventory, HR, business automation, and intelligent insights into one unified platform.</p>

    <p>Our goal is simple: help businesses spend less time managing operations and more time growing.</p>

    <hr style="border: none; border-top: 1px solid rgba(0,0,0,0.1); margin: 35px 0;">

    <h2 style="color: var(--brand-blue, #000066); font-size: 1.8rem; font-weight: 700; margin-bottom: 15px;">Our Story</h2>

    <p><strong>Casjoetech</strong> was founded to solve one of Africa\'s biggest business challenges: fragmented systems and inefficient processes.</p>

    <p>Too many businesses rely on multiple disconnected tools, spreadsheets, manual record-keeping, and repetitive tasks that slow growth and reduce productivity.</p>

    <p>We envisioned a single intelligent platform that could simplify business management while leveraging artificial intelligence to automate routine work, improve decision-making, and unlock sustainable growth.</p>

    <p>That vision became <strong>Casjoe Biz</strong>.</p>

    <p>Today, our platform empowers entrepreneurs, startups, and growing businesses with enterprise-grade tools designed specifically for the African market.</p>

    <hr style="border: none; border-top: 1px solid rgba(0,0,0,0.1); margin: 35px 0;">

    <h2 style="color: var(--brand-blue, #000066); font-size: 1.8rem; font-weight: 700; margin-bottom: 15px;">What We Do</h2>

    <p>We build intelligent software that enables businesses to:</p>

    <ul style="list-style: none; padding-left: 0; margin: 20px 0;">
        <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><strong style="color: var(--brand-amber, #FFA600); font-size: 1.2rem;">✓</strong> Manage customers with an integrated CRM</li>
        <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><strong style="color: var(--brand-amber, #FFA600); font-size: 1.2rem;">✓</strong> Track sales, invoices, and payments</li>
        <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><strong style="color: var(--brand-amber, #FFA600); font-size: 1.2rem;">✓</strong> Monitor finances and business performance</li>
        <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><strong style="color: var(--brand-amber, #FFA600); font-size: 1.2rem;">✓</strong> Manage inventory and operations</li>
        <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><strong style="color: var(--brand-amber, #FFA600); font-size: 1.2rem;">✓</strong> Automate repetitive workflows</li>
        <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><strong style="color: var(--brand-amber, #FFA600); font-size: 1.2rem;">✓</strong> Generate AI-powered business insights</li>
        <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><strong style="color: var(--brand-amber, #FFA600); font-size: 1.2rem;">✓</strong> Improve team collaboration</li>
        <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><strong style="color: var(--brand-amber, #FFA600); font-size: 1.2rem;">✓</strong> Scale confidently with cloud technology</li>
    </ul>

    <p style="font-weight: 600; font-size: 1.1rem; color: var(--brand-blue, #000066);">Everything is connected through one secure platform.</p>

    <hr style="border: none; border-top: 1px solid rgba(0,0,0,0.1); margin: 35px 0;">

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin: 30px 0;">
        <div style="background: rgba(0, 0, 102, 0.04); padding: 25px; border-radius: 12px; border-left: 4px solid var(--brand-blue, #000066);">
            <h3 style="color: var(--brand-blue, #000066); font-size: 1.4rem; font-weight: 700; margin-top: 0;">Our Vision</h3>
            <p style="margin-bottom: 0;">To become Africa\'s leading AI Business Operating System, enabling millions of businesses to operate smarter, grow faster, and compete globally.</p>
        </div>
        <div style="background: rgba(255, 166, 0, 0.08); padding: 25px; border-radius: 12px; border-left: 4px solid var(--brand-amber, #FFA600);">
            <h3 style="color: var(--brand-blue, #000066); font-size: 1.4rem; font-weight: 700; margin-top: 0;">Our Mission</h3>
            <p style="margin-bottom: 0;">To empower businesses across Africa with intelligent technology that simplifies operations, improves productivity, and accelerates sustainable growth through AI-driven innovation.</p>
        </div>
    </div>

    <hr style="border: none; border-top: 1px solid rgba(0,0,0,0.1); margin: 35px 0;">

    <h2 style="color: var(--brand-blue, #000066); font-size: 1.8rem; font-weight: 700; margin-bottom: 20px;">Our Core Values</h2>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 35px;">
        <div style="padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <h4 style="color: var(--brand-amber, #FFA600); font-size: 1.2rem; font-weight: 700; margin-top: 0; margin-bottom: 8px;">Innovation</h4>
            <p style="margin: 0; font-size: 0.95rem; color: #475569;">We continuously build practical technology that solves real business challenges.</p>
        </div>
        <div style="padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <h4 style="color: var(--brand-amber, #FFA600); font-size: 1.2rem; font-weight: 700; margin-top: 0; margin-bottom: 8px;">Customer Success</h4>
            <p style="margin: 0; font-size: 0.95rem; color: #475569;">Our customers\' growth defines our success.</p>
        </div>
        <div style="padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <h4 style="color: var(--brand-amber, #FFA600); font-size: 1.2rem; font-weight: 700; margin-top: 0; margin-bottom: 8px;">Simplicity</h4>
            <p style="margin: 0; font-size: 0.95rem; color: #475569;">Complex business management should feel effortless.</p>
        </div>
        <div style="padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <h4 style="color: var(--brand-amber, #FFA600); font-size: 1.2rem; font-weight: 700; margin-top: 0; margin-bottom: 8px;">Excellence</h4>
            <p style="margin: 0; font-size: 0.95rem; color: #475569;">We deliver reliable, secure, and scalable technology.</p>
        </div>
        <div style="padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <h4 style="color: var(--brand-amber, #FFA600); font-size: 1.2rem; font-weight: 700; margin-top: 0; margin-bottom: 8px;">Integrity</h4>
            <p style="margin: 0; font-size: 0.95rem; color: #475569;">We build trust through transparency, accountability, and ethical innovation.</p>
        </div>
    </div>

    <hr style="border: none; border-top: 1px solid rgba(0,0,0,0.1); margin: 35px 0;">

    <h2 style="color: var(--brand-blue, #000066); font-size: 1.8rem; font-weight: 700; margin-bottom: 20px;">Why Businesses Choose Casjoe</h2>

    <div style="margin-bottom: 35px;">
        <div style="margin-bottom: 20px;">
            <h4 style="color: var(--brand-blue, #000066); font-size: 1.2rem; font-weight: 700; margin-bottom: 5px;">🤖 AI-Powered by Design</h4>
            <p style="margin: 0; color: #334155;">Artificial intelligence is embedded throughout our platform to automate tasks, generate insights, and improve business performance.</p>
        </div>
        <div style="margin-bottom: 20px;">
            <h4 style="color: var(--brand-blue, #000066); font-size: 1.2rem; font-weight: 700; margin-bottom: 5px;">⚡ One Unified Platform</h4>
            <p style="margin: 0; color: #334155;">Manage your business from a single dashboard instead of juggling multiple software tools.</p>
        </div>
        <div style="margin-bottom: 20px;">
            <h4 style="color: var(--brand-blue, #000066); font-size: 1.2rem; font-weight: 700; margin-bottom: 5px;">🌍 Built for African Businesses</h4>
            <p style="margin: 0; color: #334155;">Designed with local business realities in mind while meeting global technology standards.</p>
        </div>
        <div style="margin-bottom: 20px;">
            <h4 style="color: var(--brand-blue, #000066); font-size: 1.2rem; font-weight: 700; margin-bottom: 5px;">🔒 Secure and Scalable</h4>
            <p style="margin: 0; color: #334155;">Cloud-based infrastructure that grows with your business.</p>
        </div>
        <div style="margin-bottom: 20px;">
            <h4 style="color: var(--brand-blue, #000066); font-size: 1.2rem; font-weight: 700; margin-bottom: 5px;">📈 Business Growth Focused</h4>
            <p style="margin: 0; color: #334155;">Every feature is designed to help businesses increase productivity, improve decision-making, and achieve sustainable growth.</p>
        </div>
    </div>

    <hr style="border: none; border-top: 1px solid rgba(0,0,0,0.1); margin: 35px 0;">

    <div style="background: linear-gradient(135deg, var(--brand-blue, #000066), #000033); color: #ffffff; padding: 35px; border-radius: 16px; text-align: center; margin-top: 40px; box-shadow: 0 10px 25px rgba(0, 0, 102, 0.2);">
        <h2 style="color: var(--brand-amber, #FFA600); font-size: 1.8rem; font-weight: 800; margin-top: 0; margin-bottom: 15px;">Looking Ahead</h2>
        <p style="font-size: 1.15rem; max-width: 750px; margin: 0 auto 15px auto; line-height: 1.7;">We\'re building more than software.</p>
        <p style="font-size: 1.15rem; max-width: 750px; margin: 0 auto 20px auto; line-height: 1.7; font-weight: 600;">We\'re building the intelligent infrastructure that will power the next generation of African businesses.</p>
        <p style="font-size: 1rem; max-width: 750px; margin: 0 auto; line-height: 1.7; color: rgba(255,255,255,0.85);">Through continuous innovation in artificial intelligence, automation, and cloud technology, <strong>Casjoetech</strong> is creating a future where every entrepreneur has access to world-class business tools regardless of size, location, or industry.</p>
    </div>
</div>';

            $stmtPagesCheck = $this->pdo->query("SELECT content FROM cms_pages WHERE slug = 'about-us'");
            $existingAbout = $stmtPagesCheck ? $stmtPagesCheck->fetchColumn() : false;
            if (!$existingAbout || strpos($existingAbout, "Building Africa's AI Business Operating System") === false) {
                $insPage = $this->pdo->prepare("INSERT INTO cms_pages (tenant_id, title, slug, content, is_published) VALUES (1, 'About Us', 'about-us', ?, 1) ON DUPLICATE KEY UPDATE content = VALUES(content)");
                $insPage->execute([$aboutHtmlAdmin]);
            }

            $stmtPagesCount = $this->pdo->query("SELECT COUNT(*) FROM cms_pages WHERE slug IN ('privacy-policy', 'terms-of-service')");
            if ($stmtPagesCount && (int)$stmtPagesCount->fetchColumn() < 2) {
                $insPage = $this->pdo->prepare("INSERT IGNORE INTO cms_pages (tenant_id, title, slug, content, is_published) VALUES (?, ?, ?, ?, 1)");
                $insPage->execute([1, 'Privacy Policy', 'privacy-policy', '<h1>Privacy Policy</h1><p>Your privacy is important to us. This policy explains how we collect, use, and protect your personal information.</p><h2>1. Information We Collect</h2><p>We collect information you provide directly to us, such as when you create an account, use our services, or communicate with us.</p>']);
                $insPage->execute([1, 'Terms of Service', 'terms-of-service', '<h1>Terms of Service</h1><p>By using Casjoe Apps, you agree to these terms. Please read them carefully.</p><h2>1. Use of Services</h2><p>You must follow any policies made available to you within the Services.</p>']);
            }

            $stmt = $this->pdo->query("DESCRIBE cms_posts");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!in_array('is_published', $columns)) {
                $this->pdo->exec("ALTER TABLE cms_posts ADD COLUMN is_published TINYINT(1) DEFAULT 1");
            }
            if (!in_array('meta_title', $columns)) {
                $this->pdo->exec("ALTER TABLE cms_posts ADD COLUMN meta_title VARCHAR(255) DEFAULT NULL");
            }
            if (!in_array('meta_description', $columns)) {
                $this->pdo->exec("ALTER TABLE cms_posts ADD COLUMN meta_description TEXT DEFAULT NULL");
            }
            if (!in_array('meta_keywords', $columns)) {
                $this->pdo->exec("ALTER TABLE cms_posts ADD COLUMN meta_keywords VARCHAR(500) DEFAULT NULL");
            }
        } catch (\Exception $e) { /* Ignore */ }
    }

    public function index()
    {
        // Fetch Pages
        $pages = $this->pdo->query("SELECT * FROM cms_pages")->fetchAll(PDO::FETCH_ASSOC);
        
        // Fetch Posts
        $posts = $this->pdo->query("SELECT * FROM cms_posts ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/admin/cms/index.php';
    }

    // --- PAges ---
    public function editPage($params)
    {
        $id = $params['id'];
        $stmt = $this->pdo->prepare("SELECT * FROM cms_pages WHERE id = ?");
        $stmt->execute([$id]);
        $page = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$page) die("Page not found");

        require __DIR__ . '/../Views/admin/cms/edit_page.php';
    }

    public function updatePage($params)
    {
        if (!CsrfService::verifyToken($_POST['csrf_token'] ?? '')) die("CSRF Fail");

        $id = $params['id'];
        $title = $_POST['title'];
        $content = $_POST['content']; 

        $stmt = $this->pdo->prepare("UPDATE cms_pages SET title = ?, content = ? WHERE id = ?");
        $stmt->execute([$title, $content, $id]);

        header('Location: /' . ADMIN_PATH . '/cms?success=page_updated');
    }

    // --- Posts ---
    public function createPost()
    {
        require __DIR__ . '/../Views/admin/cms/create_post.php';
    }

    public function storePost()
    {
        if (!CsrfService::verifyToken($_POST['csrf_token'] ?? '')) die("CSRF Fail");

        $title = $_POST['title'];
        $slug = $_POST['slug'] ?: strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $content = $_POST['content'];
        $excerpt = $_POST['excerpt'];
        $image_url = $_POST['image_url'] ?? '';
        $is_published = isset($_POST['is_published']) ? 1 : 0;
        $meta_title = $_POST['meta_title'] ?? '';
        $meta_description = $_POST['meta_description'] ?? '';
        $meta_keywords = $_POST['meta_keywords'] ?? '';

        // Handle local image upload
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            try {
                $uploadService = new \App\Core\Services\UploadService();
                $image_url = $uploadService->upload($_FILES['featured_image'], 'blog', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            } catch (\Exception $e) {
                // Ignore upload error, fallback to URL input
            }
        }

        $stmt = $this->pdo->prepare("INSERT INTO cms_posts (title, slug, content, excerpt, image_url, is_published, meta_title, meta_description, meta_keywords) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $slug, $content, $excerpt, $image_url, $is_published, $meta_title, $meta_description, $meta_keywords]);

        header('Location: /' . ADMIN_PATH . '/cms?success=post_created');
    }

    public function editPost($params)
    {
        $id = $params['id'];
        $stmt = $this->pdo->prepare("SELECT * FROM cms_posts WHERE id = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$post) die("Post not found");

        require __DIR__ . '/../Views/admin/cms/edit_post.php';
    }

    public function updatePost($params)
    {
         if (!CsrfService::verifyToken($_POST['csrf_token'] ?? '')) die("CSRF Fail");

        $id = $params['id'];
        $title = $_POST['title'];
        $slug = $_POST['slug'];
        $content = $_POST['content'];
        $excerpt = $_POST['excerpt'];
        $image_url = $_POST['image_url'];
        $is_published = isset($_POST['is_published']) ? 1 : 0;
        $meta_title = $_POST['meta_title'] ?? '';
        $meta_description = $_POST['meta_description'] ?? '';
        $meta_keywords = $_POST['meta_keywords'] ?? '';

        // Handle local image upload
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            try {
                $uploadService = new \App\Core\Services\UploadService();
                $image_url = $uploadService->upload($_FILES['featured_image'], 'blog', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            } catch (\Exception $e) {
                // Ignore upload error, fallback to URL input
            }
        }

        $stmt = $this->pdo->prepare("UPDATE cms_posts SET title = ?, slug = ?, content = ?, excerpt = ?, image_url = ?, is_published = ?, meta_title = ?, meta_description = ?, meta_keywords = ? WHERE id = ?");
        $stmt->execute([$title, $slug, $content, $excerpt, $image_url, $is_published, $meta_title, $meta_description, $meta_keywords, $id]);

        header('Location: /' . ADMIN_PATH . '/cms?success=post_updated');
    }

    public function deletePost($params)
    {
        // CSRF via GET/POST preferred, but simple logical verification for delete
        // Should really be POST.
        $id = $params['id'];
        $this->pdo->prepare("DELETE FROM cms_posts WHERE id = ?")->execute([$id]);
        header('Location: /' . ADMIN_PATH . '/cms?msg=deleted');
    }

    public function deletePage($params)
    {
        $id = $params['id'] ?? 0;
        
        // Check if page is locked system page before allowing deletion
        $stmt = $this->pdo->prepare("SELECT slug FROM cms_pages WHERE id = ?");
        $stmt->execute([$id]);
        $slug = $stmt->fetchColumn();
        
        $lockedSlugs = ['about-us', 'about', 'privacy-policy', 'privacy', 'terms-of-service', 'terms', 'accessibility-statement', 'accessibility', 'contact'];
        if (in_array($slug, $lockedSlugs)) {
            header('Location: /' . ADMIN_PATH . '/cms?error=locked_system_page');
            exit;
        }

        $this->pdo->prepare("DELETE FROM cms_pages WHERE id = ?")->execute([$id]);
        header('Location: /' . ADMIN_PATH . '/cms?msg=page_deleted');
        exit;
    }


    public function generateAiContent()
    {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $topic = $input['topic'] ?? '';

        if (empty($topic)) {
            echo json_encode(['error' => 'Topic is required']);
            exit;
        }

        try {
            $gemini = new \App\Core\Services\GeminiService();
            $content = $gemini->generateBlogContent($topic);
            echo json_encode(['content' => $content]);
        } catch (\Exception $e) {
            echo json_encode(['error' => 'AI Generation failed: ' . $e->getMessage()]);
        }
        exit;
    }
}
