<?php

namespace App\Modules\CasjoeLinks\Controllers;

use App\Core\Auth;
use App\Core\Database;

class StaticSiteController
{
    public function index()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();

        $stmt = $db->query("SELECT * FROM links_static_sites WHERE tenant_id = ? AND user_id = ? ORDER BY created_at DESC", 
            [$user['tenant_id'], $user['id']]);
        $sites = $stmt->fetchAll();

        require __DIR__ . '/../Views/static/index.php';
    }

    public function create()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }
        require __DIR__ . '/../Views/static/create.php';
    }

    public function aiCreate()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }
        require __DIR__ . '/../Views/static/ai_create.php';
    }

    public function aiGenerate()
    {
        set_time_limit(300);
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();

        $subdomain = trim($_POST['subdomain'] ?? '');
        $prompt = trim($_POST['prompt'] ?? '');
        $style = trim($_POST['style'] ?? 'modern dark glassmorphic');

        if (empty($subdomain) || empty($prompt)) {
            header('Location: /links/static/ai-create?error=missing_fields');
            exit;
        }

        // Validate subdomain
        if (!preg_match('/^[a-z0-9-]+$/', $subdomain)) {
            header('Location: /links/static/ai-create?error=invalid_name');
            exit;
        }

        // Check if exists
        $stmt = $db->query("SELECT id FROM links_static_sites WHERE subdomain = ?", [$subdomain]);
        if ($stmt->fetch()) {
            header('Location: /links/static/ai-create?error=name_taken');
            exit;
        }

        try {
            $aiService = new \App\Core\Services\AIService();

            $systemPrompt = "You are a world-class UI/UX designer and elite frontend developer. Generate a stunning, high-converting, award-winning single-file HTML landing page.
            CRITICAL DESIGN & TECHNICAL REQUIREMENTS:
            1. Full HTML document with <!DOCTYPE html>, <head>, and <body>.
            2. CDN Includes in <head>:
               - Tailwind CSS CDN: <script src=\"https://cdn.tailwindcss.com\"></script>
               - FontAwesome icons: <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css\">
               - Google Fonts: <link href=\"https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap\" rel=\"stylesheet\">
            3. AESTHETIC DESIGN RULES (MUST LOOK LIKE A $10,000 PREMIUM SITE):
               - Theme style requested: {$style}.
               - DO NOT use generic placeholder images like via.placeholder.com. Use high quality Unsplash photo URLs (e.g., https://images.unsplash.com/photo-...) or FontAwesome vector icons in colorful gradient containers.
               - Rich color palette: Use deep backgrounds (slate-950, zinc-900, indigo-950), vibrant accent gradients (amber-400, yellow-500, blue-600), glassmorphic panels (bg-white/5 or bg-slate-900/60 backdrop-blur-xl border border-white/10), and glowing buttons.
               - Sections required:
                 * Sticky Navigation bar with logo/brand name and Action Button.
                 * Hero section with strong headline, subheadline, trust badges/logos, and primary + secondary CTA buttons.
                 * Key Features / Services grid with FontAwesome icons, hover card animations, and crisp descriptions.
                 * Social Proof / Impact section (Stats counter, testimonials, or brand logos).
                 * Interactive / Pricing / Content section tailored to the prompt.
                 * Full Contact / CTA banner and professional Footer.
            4. Fully responsive on mobile, tablet, and desktop (use Tailwind responsive prefixes sm:, md:, lg:, xl:).
            5. Output ONLY raw HTML code. Do NOT wrap in markdown backticks or commentary.";

            $userPrompt = "Design a complete high-converting website for: " . $prompt;

            $generatedCode = $aiService->generateText($userPrompt, $systemPrompt);

            // Clean code block markers if present
            $generatedCode = preg_replace('/^```html\s*/i', '', $generatedCode);
            $generatedCode = preg_replace('/^```\s*/i', '', $generatedCode);
            $generatedCode = preg_replace('/```$/i', '', $generatedCode);
            $generatedCode = trim($generatedCode);

            if (empty($generatedCode)) {
                throw new \Exception("AI generated empty content. Please try again.");
            }

            // Save to filesystem
            $targetDir = __DIR__ . '/../../../../public/sites/' . $subdomain;
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            file_put_contents($targetDir . '/index.html', $generatedCode);

            // Save to DB
            $db->query("INSERT INTO links_static_sites (tenant_id, user_id, subdomain, storage_path) VALUES (?, ?, ?, ?)", 
                [$user['tenant_id'], $user['id'], $subdomain, 'public/sites/' . $subdomain]
            );

            header('Location: /links/static');
            exit;
        } catch (\Exception $e) {
            header('Location: /links/static/ai-create?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function store()
    {
        set_time_limit(300); // Allow time for npm install and build
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();

        $subdomain = $_POST['subdomain']; // acting as folder name
        
        if (empty($subdomain) || empty($_FILES['site_file']['name'])) {
             header('Location: /links/static/create?error=missing_fields');
             exit;
        }

        // Validate subdomain (alphanumeric)
        if (!preg_match('/^[a-z0-9-]+$/', $subdomain)) {
             header('Location: /links/static/create?error=invalid_name');
             exit;
        }

        // Check if exists
        $stmt = $db->query("SELECT id FROM links_static_sites WHERE subdomain = ?", [$subdomain]);
        if ($stmt->fetch()) {
             header('Location: /links/static/create?error=name_taken');
             exit;
        }

        // Handle File Upload
        $targetDir = __DIR__ . '/../../../../public/sites/' . $subdomain;
        
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $zipFile = $_FILES['site_file']['tmp_name'];
        $zip = new \ZipArchive;
        if ($zip->open($zipFile) === TRUE) {
            $zip->extractTo($targetDir);
            $zip->close();

            // Auto-hoist if the zip contains a single directory (like 'dist' or 'build')
            $this->hoistIfSingleDirectory($targetDir);

            // Save to DB
            $db->query("INSERT INTO links_static_sites (tenant_id, user_id, subdomain, storage_path) VALUES (?, ?, ?, ?)", 
                [$user['tenant_id'], $user['id'], $subdomain, 'public/sites/' . $subdomain]
            );

            header('Location: /links/static');
        } else {
             header('Location: /links/static/create?error=zip_extraction_failed');
             exit;
        }
    }

    public function delete($params)
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $id = is_array($params) ? $params['id'] : $params;
        $user = Auth::user();
        $db = Database::getInstance();

        // Get site info before deleting
        $stmt = $db->query("SELECT * FROM links_static_sites WHERE id = ? AND tenant_id = ? AND user_id = ?", 
            [$id, $user['tenant_id'], $user['id']]);
        $site = $stmt->fetch();

        if ($site) {
            // Delete directory
            $targetDir = __DIR__ . '/../../../../public/sites/' . $site['subdomain'];
            if (is_dir($targetDir)) {
                $this->deleteDirectory($targetDir);
            }

            // Delete from database
            $db->query("DELETE FROM links_static_sites WHERE id = ?", [$id]);
        }

        header('Location: /links/static');
        exit;
    }

    public function edit($params)
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $id = is_array($params) ? $params['id'] : $params;
        $user = Auth::user();
        $db = Database::getInstance();

        $stmt = $db->query("SELECT * FROM links_static_sites WHERE id = ? AND tenant_id = ? AND user_id = ?", 
            [$id, $user['tenant_id'], $user['id']]);
        $site = $stmt->fetch();

        if (!$site) {
            header('Location: /links/static');
            exit;
        }

        // Read index.html content if it exists
        $indexPath = __DIR__ . '/../../../../public/sites/' . $site['subdomain'] . '/index.html';
        $htmlCode = file_exists($indexPath) ? file_get_contents($indexPath) : '';

        require __DIR__ . '/../Views/static/edit.php';
    }

    public function update($params)
    {
        set_time_limit(300); // Allow time for npm install and build
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $id = is_array($params) ? $params['id'] : $params;
        $user = Auth::user();
        $db = Database::getInstance();

        $subdomain = trim($_POST['subdomain'] ?? '');

        if (empty($subdomain)) {
            header('Location: /links/static/edit/' . $id . '?error=missing_fields');
            exit;
        }

        // Validate subdomain
        if (!preg_match('/^[a-z0-9-]+$/', $subdomain)) {
            header('Location: /links/static/edit/' . $id . '?error=invalid_name');
            exit;
        }

        // Check if new subdomain already exists (excluding current site)
        $stmt = $db->query("SELECT id FROM links_static_sites WHERE subdomain = ? AND id != ?", [$subdomain, $id]);
        if ($stmt->fetch()) {
            header('Location: /links/static/edit/' . $id . '?error=name_taken');
            exit;
        }

        // Get current site
        $stmt = $db->query("SELECT * FROM links_static_sites WHERE id = ? AND tenant_id = ? AND user_id = ?", 
            [$id, $user['tenant_id'], $user['id']]);
        $site = $stmt->fetch();

        if (!$site) {
            header('Location: /links/static');
            exit;
        }

        $oldSubdomain = $site['subdomain'];
        $targetDir = __DIR__ . '/../../../../public/sites/' . $subdomain;

        // If subdomain changed, rename directory
        if ($subdomain !== $oldSubdomain) {
            $oldDir = __DIR__ . '/../../../../public/sites/' . $oldSubdomain;

            if (is_dir($oldDir)) {
                rename($oldDir, $targetDir);
            }

            // Update storage path
            $db->query("UPDATE links_static_sites SET subdomain = ?, storage_path = ? WHERE id = ?", 
                [$subdomain, 'public/sites/' . $subdomain, $id]);
        }

        // Handle Code Editor update
        if (isset($_POST['html_code']) && !empty($_POST['html_code'])) {
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            file_put_contents($targetDir . '/index.html', $_POST['html_code']);
        }

        // Handle ZIP file upload if provided
        if (isset($_FILES['site_file']) && $_FILES['site_file']['error'] === UPLOAD_ERR_OK) {
            if (is_dir($targetDir)) {
                $this->deleteDirectory($targetDir);
                mkdir($targetDir, 0777, true);
            }

            $zipFile = $_FILES['site_file']['tmp_name'];
            $zip = new \ZipArchive;
            if ($zip->open($zipFile) === TRUE) {
                $zip->extractTo($targetDir);
                $zip->close();

                // Auto-hoist if the zip contains a single directory
                $this->hoistIfSingleDirectory($targetDir);
            }
        }

        header('Location: /links/static/edit/' . $id . '?saved=1');
        exit;
    }

    public function aiRefine($params)
    {
        set_time_limit(300);
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $id = is_array($params) ? $params['id'] : $params;
        $user = Auth::user();
        $db = Database::getInstance();

        $stmt = $db->query("SELECT * FROM links_static_sites WHERE id = ? AND tenant_id = ? AND user_id = ?", 
            [$id, $user['tenant_id'], $user['id']]);
        $site = $stmt->fetch();

        if (!$site) {
            header('Location: /links/static');
            exit;
        }

        $refinePrompt = trim($_POST['refine_prompt'] ?? '');
        if (empty($refinePrompt)) {
            header('Location: /links/static/edit/' . $id . '?error=empty_refine_prompt');
            exit;
        }

        $indexPath = __DIR__ . '/../../../../public/sites/' . $site['subdomain'] . '/index.html';
        $currentCode = file_exists($indexPath) ? file_get_contents($indexPath) : '';

        try {
            $aiService = new \App\Core\Services\AIService();

            $systemPrompt = "You are an expert web developer updating an existing single-file HTML website based on user instructions.
            - Modify the HTML code to implement the requested changes while maintaining the existing structure, Tailwind CSS styling, FontAwesome icons, and general responsiveness.
            - Ensure output is a complete, valid single-file HTML document.
            - Do NOT add markdown code block backticks or extra text. Output ONLY the raw HTML.";

            $userPrompt = "Current HTML Website Code:\n" . $currentCode . "\n\nUser Request to Modify Website:\n" . $refinePrompt;

            $updatedCode = $aiService->generateText($userPrompt, $systemPrompt);

            // Clean code block markers
            $updatedCode = preg_replace('/^```html\s*/i', '', $updatedCode);
            $updatedCode = preg_replace('/^```\s*/i', '', $updatedCode);
            $updatedCode = preg_replace('/```$/i', '', $updatedCode);
            $updatedCode = trim($updatedCode);

            if (!empty($updatedCode)) {
                file_put_contents($indexPath, $updatedCode);
            }

            header('Location: /links/static/edit/' . $id . '?ai_updated=1');
            exit;
        } catch (\Exception $e) {
            header('Location: /links/static/edit/' . $id . '?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    private function hoistIfSingleDirectory($dir)
    {
        $items = array_diff(scandir($dir), ['.', '..', '__MACOSX']); // ignore macosx hidden folder
        
        // If there's only one item and it's a directory
        if (count($items) === 1) {
            $singleItem = reset($items);
            $singleDirPath = $dir . '/' . $singleItem;
            
            if (is_dir($singleDirPath)) {
                // Move everything from inside this directory to the root of $dir
                $this->moveDirectoryContents($singleDirPath, $dir);
                // Delete the now-empty subdirectory
                $this->deleteDirectory($singleDirPath);
            }
        }
    }

    private function moveDirectoryContents($src, $dst)
    {
        if (!is_dir($src)) return;
        if (!is_dir($dst)) mkdir($dst, 0777, true);
        
        $files = array_diff(scandir($src), ['.', '..']);
        foreach ($files as $file) {
            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . $file;
            
            if (is_dir($srcPath)) {
                $this->moveDirectoryContents($srcPath, $dstPath);
            } else {
                rename($srcPath, $dstPath);
            }
        }
    }
}

