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

        $subdomain = $_POST['subdomain'];

        if (empty($subdomain)) {
            header('Location: /links/static/edit/' . $id . '?error=missing_fields');
            exit;
        }

        // Validate subdomain
        if (!preg_match('/^[a-z0-9-]+$/', $subdomain)) {
            header('Location: /links/static/ edit/' . $id . '?error=invalid_name');
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

        // If subdomain changed, rename directory
        if ($subdomain !== $oldSubdomain) {
            $oldDir = __DIR__ . '/../../../../public/sites/' . $oldSubdomain;
            $newDir = __DIR__ . '/../../../../public/sites/' . $subdomain;

            if (is_dir($oldDir)) {
                rename($oldDir, $newDir);
            }

            // Update storage path
            $db->query("UPDATE links_static_sites SET subdomain = ?, storage_path = ? WHERE id = ?", 
                [$subdomain, 'public/sites/' . $subdomain, $id]);
        }

        // Handle new file upload if provided
        if (isset($_FILES['site_file']) && $_FILES['site_file']['error'] === UPLOAD_ERR_OK) {
            $targetDir = __DIR__ . '/../../../../public/sites/' . $subdomain;

            // Clear existing files
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

        header('Location: /links/static');
        exit;
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

