<?php

namespace App\Modules\CasjoeCloud\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\SubscriptionManager;
use PDO;

class CloudController
{
    private $pdo;
    private $tenantId;
    private $userId;
    private $storageRoot;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        try {
            $this->pdo->exec("ALTER TABLE cloud_folders ADD COLUMN share_token VARCHAR(64) NULL UNIQUE");
        } catch (\Exception $e) {}
        try {
            $this->pdo->exec("ALTER TABLE cloud_files ADD COLUMN share_token VARCHAR(64) NULL UNIQUE");
        } catch (\Exception $e) {}
        try {
            $this->pdo->exec("ALTER TABLE cloud_folders ADD COLUMN share_permission VARCHAR(20) DEFAULT 'editor'");
        } catch (\Exception $e) {}
        try {
            $this->pdo->exec("ALTER TABLE cloud_folders ADD COLUMN access_mode VARCHAR(32) DEFAULT 'preview_login'");
        } catch (\Exception $e) {}
        try {
            $this->pdo->exec("ALTER TABLE cloud_files ADD COLUMN access_mode VARCHAR(32) DEFAULT 'preview_login'");
        } catch (\Exception $e) {}
        try {
            $this->pdo->exec("ALTER TABLE cloud_folders ADD COLUMN share_password VARCHAR(255) NULL");
        } catch (\Exception $e) {}
        try {
            $this->pdo->exec("ALTER TABLE cloud_files ADD COLUMN share_password VARCHAR(255) NULL");
        } catch (\Exception $e) {}
        try {
            $this->pdo->exec("ALTER TABLE cloud_folders ADD COLUMN user_id INT NULL");
        } catch (\Exception $e) {}
        try {
            $this->pdo->exec("ALTER TABLE cloud_files ADD COLUMN user_id INT NULL");
        } catch (\Exception $e) {}
        try {
            $this->pdo->exec("UPDATE cloud_folders cf SET user_id = (SELECT id FROM users u WHERE u.tenant_id = cf.tenant_id ORDER BY u.id ASC LIMIT 1) WHERE (cf.user_id IS NULL OR cf.user_id = 0)");
        } catch (\Exception $e) {}
        try {
            $this->pdo->exec("UPDATE cloud_files cf SET user_id = (SELECT id FROM users u WHERE u.tenant_id = cf.tenant_id ORDER BY u.id ASC LIMIT 1) WHERE (cf.user_id IS NULL OR cf.user_id = 0)");
        } catch (\Exception $e) {}
        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS cloud_access_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tenant_id INT NOT NULL,
                user_id INT NULL,
                resource_type VARCHAR(20) NOT NULL,
                resource_id INT NOT NULL,
                action VARCHAR(32) NOT NULL,
                ip_address VARCHAR(64) NULL,
                device_info VARCHAR(255) NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_tenant (tenant_id),
                INDEX idx_resource (resource_type, resource_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } catch (\Exception $e) {}
        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS cloud_sharing_policies (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tenant_id INT NOT NULL UNIQUE,
                always_require_login TINYINT(1) DEFAULT 0,
                disable_public_links TINYINT(1) DEFAULT 0,
                force_expiry_days INT DEFAULT 0,
                max_download_count INT DEFAULT 0,
                default_sharing_mode VARCHAR(32) DEFAULT 'preview_login',
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } catch (\Exception $e) {}

        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $isPublicShare = (strpos($uri, '/cloud/share') === 0 || strpos($uri, '/cloud/file/share') === 0 || strpos($uri, '/cloud/file/download') === 0 || (strpos($uri, '/cloud/preview') === 0 && !empty($_GET['share_token'])));
        if (!$isPublicShare) {
            if (!\App\Core\Auth::check()) {
                header('Location: /login');
                exit;
            }
            $user = \App\Core\Auth::user();
            $this->tenantId = (int)($user['tenant_id'] ?? TenantContext::getTenantId());
            $this->userId = (int)($user['id'] ?? 0);
            SubscriptionManager::requireActive($this->tenantId);
            
            // Define storage root for this tenant
            $this->storageRoot = __DIR__ . '/../../../../storage/tenants/' . $this->tenantId . '/cloud/';
            if (!is_dir($this->storageRoot)) {
                mkdir($this->storageRoot, 0777, true);
            }
        }
    }

    private function logAccess($tenantId, $resourceType, $resourceId, $action)
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown Device', 0, 250);
            $stmt = $this->pdo->prepare("INSERT INTO cloud_access_logs (tenant_id, user_id, resource_type, resource_id, action, ip_address, device_info) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tenantId, $userId, $resourceType, $resourceId, $action, $ip, $userAgent]);
        } catch (\Exception $e) {}
    }

    public function index()
    {
        $folderId = $_GET['folder'] ?? null;
        
        // Breadcrumb logic
        $breadcrumbs = [['id' => null, 'name' => 'Home']];
        if ($folderId) {
            $curr = $folderId;
            $path = [];
            while ($curr) {
                $stmt = $this->pdo->prepare("SELECT id, name, parent_id FROM cloud_folders WHERE id = ? AND tenant_id = ? AND (user_id = ? OR (share_token IS NOT NULL AND share_token != ''))");
                $stmt->execute([$curr, $this->tenantId, $this->userId]);
                $f = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$f) break;
                array_unshift($path, $f);
                $curr = $f['parent_id'];
            }
            $breadcrumbs = array_merge($breadcrumbs, $path);
        }

        // Fetch My Folders
        $stmt = $this->pdo->prepare("SELECT * FROM cloud_folders WHERE parent_id " . ($folderId ? "= ?" : "IS NULL") . " AND tenant_id = ? AND user_id = ? ORDER BY name ASC");
        if ($folderId) $stmt->execute([$folderId, $this->tenantId, $this->userId]);
        else $stmt->execute([$this->tenantId, $this->userId]);
        $folders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch My Files
        $stmt = $this->pdo->prepare("SELECT * FROM cloud_files WHERE folder_id " . ($folderId ? "= ?" : "IS NULL") . " AND tenant_id = ? AND user_id = ? ORDER BY uploaded_at DESC");
        if ($folderId) $stmt->execute([$folderId, $this->tenantId, $this->userId]);
        else $stmt->execute([$this->tenantId, $this->userId]);
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch Shared With Staff / Team (shared items in this tenant from colleagues)
        $sharedFolders = [];
        $sharedFiles = [];
        if (!$folderId) {
            $stmt = $this->pdo->prepare("SELECT * FROM cloud_folders WHERE tenant_id = ? AND user_id != ? AND share_token IS NOT NULL AND share_token != '' ORDER BY name ASC");
            $stmt->execute([$this->tenantId, $this->userId]);
            $sharedFolders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $this->pdo->prepare("SELECT * FROM cloud_files WHERE tenant_id = ? AND user_id != ? AND share_token IS NOT NULL AND share_token != '' ORDER BY uploaded_at DESC");
            $stmt->execute([$this->tenantId, $this->userId]);
            $sharedFiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Calc total usage
        $usedBytes = 0;
        try {
            $stmt = $this->pdo->prepare("SELECT SUM(size_bytes) FROM cloud_files WHERE tenant_id = ? AND user_id = ?");
            $stmt->execute([$this->tenantId, $this->userId]);
            $usedBytes = (int)($stmt->fetchColumn() ?: 0);
        } catch (\Exception $e) {
            $usedBytes = 0;
        }
        $totalBytes = 20 * 1024 * 1024 * 1024; // 20GB limit

        // Fetch Telemetry & Audit Log Stats
        $logStats = ['view' => 0, 'download' => 0, 'share' => 0, 'upload' => 0];
        try {
            $stmt = $this->pdo->prepare("SELECT action, COUNT(*) as cnt FROM cloud_access_logs WHERE tenant_id = ? GROUP BY action");
            $stmt->execute([$this->tenantId]);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $logStats[$row['action']] = (int)$row['cnt'];
            }
        } catch (\Exception $e) {}

        // Fetch Recent Access Logs
        $recentLogs = [];
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM cloud_access_logs WHERE tenant_id = ? ORDER BY created_at DESC LIMIT 15");
            $stmt->execute([$this->tenantId]);
            $recentLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // Fetch Sharing Policy
        $sharingPolicy = ['always_require_login' => 0, 'disable_public_links' => 0, 'default_sharing_mode' => 'preview_login'];
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM cloud_sharing_policies WHERE tenant_id = ?");
            $stmt->execute([$this->tenantId]);
            $policy = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($policy) {
                $sharingPolicy = $policy;
            }
        } catch (\Exception $e) {}

        require __DIR__ . '/../Views/dashboard.php';
    }

    public function apiListFiles()
    {
        $type = $_GET['type'] ?? 'image';
        
        $mimeCondition = "";
        if ($type === 'image') {
            $mimeCondition = "AND type LIKE 'image/%'";
        } elseif ($type === 'video') {
            $mimeCondition = "AND type LIKE 'video/%'";
        }

        $stmt = $this->pdo->prepare("SELECT id, name, type, share_token FROM cloud_files WHERE tenant_id = ? AND (user_id = ? OR user_id IS NULL OR user_id = 0) $mimeCondition ORDER BY uploaded_at DESC");
        $stmt->execute([$this->tenantId, $this->userId]);
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ensure all returned files have a share token so they can be publicly embedded in emails
        foreach ($files as &$file) {
            if (empty($file['share_token'])) {
                $token = bin2hex(random_bytes(16));
                $update = $this->pdo->prepare("UPDATE cloud_files SET share_token = ? WHERE id = ?");
                $update->execute([$token, $file['id']]);
                $file['share_token'] = $token;
            }
            $file['url'] = 'https://app.casjoe.com/cloud/preview?id=' . $file['id'] . '&share_token=' . $file['share_token'];
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'files' => $files]);
        exit;
    }

    public function createFolder()
    {
        $name = $_POST['name'] ?? 'New Folder';
        $parentId = $_POST['parent_id'] ?: null;

        $stmt = $this->pdo->prepare("INSERT INTO cloud_folders (tenant_id, user_id, parent_id, name) VALUES (?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $this->userId, $parentId, $name]);

        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    public function upload()
    {
        $folderId = $_POST['folder_id'] ?: null;
        $filesToProcess = [];

        if (!empty($_FILES['file']['name'])) {
            $filesToProcess[] = [
                'name' => $_FILES['file']['name'],
                'tmp_name' => $_FILES['file']['tmp_name'],
                'size' => $_FILES['file']['size'],
                'type' => $_FILES['file']['type']
            ];
        } elseif (!empty($_FILES['files']['name']) && is_array($_FILES['files']['name'])) {
            $count = count($_FILES['files']['name']);
            for ($i = 0; $i < $count; $i++) {
                if (!empty($_FILES['files']['name'][$i])) {
                    $filesToProcess[] = [
                        'name' => basename($_FILES['files']['name'][$i]),
                        'tmp_name' => $_FILES['files']['tmp_name'][$i],
                        'size' => $_FILES['files']['size'][$i],
                        'type' => $_FILES['files']['type'][$i]
                    ];
                }
            }
        }

        foreach ($filesToProcess as $file) {
            $name = $file['name'];
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $hashName = uniqid() . '.' . $ext;
            $target = $this->storageRoot . $hashName;

            if (move_uploaded_file($file['tmp_name'], $target)) {
                $stmt = $this->pdo->prepare("INSERT INTO cloud_files (tenant_id, user_id, folder_id, name, path, size_bytes, type) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$this->tenantId, $this->userId, $folderId, $name, $hashName, $file['size'], $file['type']]);
                $this->logAccess($this->tenantId, 'folder', $folderId ?: 0, 'upload');
            }
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    public function deleteFile()
    {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("SELECT * FROM cloud_files WHERE id = ? AND tenant_id = ? AND (user_id = ? OR user_id IS NULL OR user_id = 0)");
        $stmt->execute([$id, $this->tenantId, $this->userId]);
        $file = $stmt->fetch();

        if ($file) {
            $path = $this->storageRoot . $file['path'];
            if (file_exists($path)) unlink($path);
            
            $stmt = $this->pdo->prepare("DELETE FROM cloud_files WHERE id = ?");
            $stmt->execute([$id]);
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    public function preview()
    {
        $id = $_GET['id'] ?? null;
        $shareToken = $_GET['share_token'] ?? null;
        if (!$id) {
            http_response_code(404);
            exit;
        }

        if (!empty($shareToken)) {
            $stmt = $this->pdo->prepare("SELECT cf.* FROM cloud_files cf LEFT JOIN cloud_folders fo ON cf.folder_id = fo.id WHERE cf.id = ? AND (cf.share_token = ? OR fo.share_token = ?)");
            $stmt->execute([$id, $shareToken, $shareToken]);
            $file = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            if (!\App\Core\Auth::check()) {
                http_response_code(403);
                exit("Unauthorized access to file preview");
            }
            $stmt = $this->pdo->prepare("SELECT * FROM cloud_files WHERE id = ? AND tenant_id = ? AND (user_id = ? OR user_id IS NULL OR user_id = 0)");
            $stmt->execute([$id, $this->tenantId, $this->userId]);
            $file = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if ($file && !empty($file['path'])) {
            $storageDir = __DIR__ . '/../../../../storage/tenants/' . $file['tenant_id'] . '/cloud/';
            $path = $storageDir . $file['path'];
            if (file_exists($path)) {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $mimes = [
                    'png' => 'image/png',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp',
                    'svg' => 'image/svg+xml',
                    'pdf' => 'application/pdf'
                ];
                $mime = $mimes[$ext] ?? 'application/octet-stream';

                header('Content-Type: ' . $mime);
                header('Content-Length: ' . filesize($path));
                header('Content-Disposition: inline; filename="' . basename($file['name']) . '"');
                readfile($path);
                exit;
            }
        }
        http_response_code(404);
        exit;
    }

    public function shareFolder()
    {
        $id = $_POST['id'] ?? null;
        $permission = $_POST['permission'] ?? null;
        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Folder ID required']);
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM cloud_folders WHERE id = ? AND tenant_id = ? AND (user_id = ? OR user_id IS NULL OR user_id = 0)");
        $stmt->execute([$id, $this->tenantId, $this->userId]);
        $folder = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$folder) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Folder not found']);
            exit;
        }

        if (in_array($permission, ['viewer', 'editor'])) {
            $updatePerm = $this->pdo->prepare("UPDATE cloud_folders SET share_permission = ? WHERE id = ? AND tenant_id = ?");
            $updatePerm->execute([$permission, $id, $this->tenantId]);
            $folder['share_permission'] = $permission;
        }

        if (isset($_POST['password'])) {
            $passVal = trim($_POST['password']) ?: null;
            $updatePass = $this->pdo->prepare("UPDATE cloud_folders SET share_password = ? WHERE id = ? AND tenant_id = ?");
            $updatePass->execute([$passVal, $id, $this->tenantId]);
        }

        $token = $folder['share_token'];
        if (empty($token)) {
            $token = bin2hex(random_bytes(16));
            $update = $this->pdo->prepare("UPDATE cloud_folders SET share_token = ? WHERE id = ? AND tenant_id = ?");
            $update->execute([$token, $id, $this->tenantId]);
        }

        $this->logAccess($this->tenantId, 'folder', $id, 'share');

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'token' => $token,
            'permission' => $folder['share_permission'] ?: 'editor',
            'url' => 'https://app.casjoe.com/cloud/share?token=' . $token
        ]);
        exit;
    }

    public function sharedFolder()
    {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            die("Invalid share token.");
        }

        $stmt = $this->pdo->prepare("SELECT * FROM cloud_folders WHERE share_token = ?");
        $stmt->execute([$token]);
        $folder = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$folder) {
            die("Shared folder not found or invite link has expired.");
        }

        if (!empty($folder['share_password'])) {
            if (isset($_POST['unlock_password'])) {
                if ($_POST['unlock_password'] === $folder['share_password'] || password_verify($_POST['unlock_password'], $folder['share_password'])) {
                    $_SESSION['cloud_unlock_' . $token] = true;
                    header('Location: /cloud/share?token=' . urlencode($token));
                    exit;
                }
                $passError = "Incorrect folder access password.";
            }
            if (empty($_SESSION['cloud_unlock_' . $token])) {
                $this->renderPasswordPrompt($token, 'folder', $folder['name'], $passError ?? null);
                return;
            }
        }

        $this->logAccess($folder['tenant_id'], 'folder', $folder['id'], 'view');

        $stmt = $this->pdo->prepare("SELECT * FROM cloud_files WHERE folder_id = ? ORDER BY uploaded_at DESC");
        $stmt->execute([$folder['id']]);
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/shared_folder.php';
    }

    private function renderPasswordPrompt($token, $type, $name, $error = null)
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
            <meta charset="UTF-8">
            <title>Protected <?= htmlspecialchars(ucfirst($type)) ?> — Casjoe Cloud</title>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
            <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
            <style>
                body {
                    margin: 0; padding: 0;
                    background: #050b18; color: #fff;
                    font-family: 'Outfit', sans-serif;
                    display: flex; align-items: center; justify-content: center;
                    min-height: 100vh;
                }
                .lock-card {
                    background: rgba(11, 19, 41, 0.9);
                    border: 1px solid rgba(255, 255, 255, 0.12);
                    border-radius: 20px;
                    padding: 36px;
                    max-width: 440px; width: 90%;
                    text-align: center;
                    box-shadow: 0 25px 50px rgba(0,0,0,0.6);
                }
            </style>
        </head>
        <body>
        <div class="lock-card">
            <ion-icon name="lock-closed" style="font-size: 3.5rem; color: #FFA600;"></ion-icon>
            <h2 style="margin: 16px 0 8px 0; font-size: 1.4rem;">Password Protected Resource</h2>
            <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 24px;">
                You are attempting to access <strong><?= htmlspecialchars($name) ?></strong>. Please enter the password to unlock preview and downloads.
            </p>
            <?php if ($error): ?>
                <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #f87171; border-radius: 10px; padding: 10px; margin-bottom: 16px; font-size: 0.85rem;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <input type="password" name="unlock_password" placeholder="Enter Access Password..." required style="width: 100%; box-sizing: border-box; background: #050b18; border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; padding: 14px; color: #fff; font-size: 0.95rem; margin-bottom: 16px;">
                <button type="submit" style="width: 100%; background: #FFA600; color: #050b18; font-weight: 700; border: none; border-radius: 12px; padding: 14px; font-size: 1rem; cursor: pointer;">
                    Unlock Access
                </button>
            </form>
        </div>
        </body>
        </html>
        <?php
        exit;
    }

    public function uploadShared()
    {
        $token = $_POST['share_token'] ?? '';
        if (empty($token) || !\App\Core\Auth::check()) {
            header('Location: /login');
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM cloud_folders WHERE share_token = ?");
        $stmt->execute([$token]);
        $folder = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($folder && ($folder['share_permission'] ?? 'editor') === 'editor' && !empty($_FILES['file']['name'])) {
            $file = $_FILES['file'];
            $name = $file['name'];
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $hashName = uniqid() . '.' . $ext;

            $targetDir = __DIR__ . '/../../../../storage/tenants/' . $folder['tenant_id'] . '/cloud/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            $target = $targetDir . $hashName;

            if (move_uploaded_file($file['tmp_name'], $target)) {
                $stmt = $this->pdo->prepare("INSERT INTO cloud_files (tenant_id, folder_id, name, path, size_bytes, type) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$folder['tenant_id'], $folder['id'], $name, $hashName, $file['size'], $file['type']]);
                $this->logAccess($folder['tenant_id'], 'folder', $folder['id'], 'upload');
            }
        }

        header('Location: /cloud/share?token=' . urlencode($token));
        exit;
    }

    public function deleteSharedFile()
    {
        $token = $_POST['share_token'] ?? '';
        $fileId = $_POST['id'] ?? null;
        if (empty($token) || !\App\Core\Auth::check()) {
            header('Location: /login');
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM cloud_folders WHERE share_token = ?");
        $stmt->execute([$token]);
        $folder = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($folder && ($folder['share_permission'] ?? 'editor') === 'editor') {
            $fStmt = $this->pdo->prepare("SELECT * FROM cloud_files WHERE id = ? AND folder_id = ?");
            $fStmt->execute([$fileId, $folder['id']]);
            $file = $fStmt->fetch(PDO::FETCH_ASSOC);

            if ($file) {
                $path = __DIR__ . '/../../../../storage/tenants/' . $folder['tenant_id'] . '/cloud/' . $file['path'];
                if (file_exists($path)) @unlink($path);
                $del = $this->pdo->prepare("DELETE FROM cloud_files WHERE id = ?");
                $del->execute([$fileId]);
            }
        }

        header('Location: /cloud/share?token=' . urlencode($token));
        exit;
    }

    public function shareFile()
    {
        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'File ID required']);
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM cloud_files WHERE id = ? AND tenant_id = ? AND (user_id = ? OR user_id IS NULL OR user_id = 0)");
        $stmt->execute([$id, $this->tenantId, $this->userId]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$file) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'File not found']);
            exit;
        }

        $token = $file['share_token'];
        if (empty($token)) {
            $token = bin2hex(random_bytes(16));
            $update = $this->pdo->prepare("UPDATE cloud_files SET share_token = ? WHERE id = ? AND tenant_id = ?");
            $update->execute([$token, $id, $this->tenantId]);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'token' => $token,
            'url' => 'https://app.casjoe.com/cloud/file/share?token=' . $token
        ]);
        exit;
    }

    public function sharedFileView()
    {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            die("Invalid share token.");
        }

        $stmt = $this->pdo->prepare("SELECT * FROM cloud_files WHERE share_token = ?");
        $stmt->execute([$token]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$file) {
            die("Shared file not found or link has expired.");
        }

        $this->logAccess($file['tenant_id'], 'file', $file['id'], 'view');

        require __DIR__ . '/../Views/shared_file.php';
    }

    public function downloadSharedFile()
    {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            die("Invalid download token.");
        }

        if (!\App\Core\Auth::check()) {
            $_SESSION['redirect_after_login'] = '/cloud/file/download?token=' . urlencode($token);
            header('Location: /register?msg=' . urlencode('Please log in or create a free Casjoe account to download this file.'));
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM cloud_files WHERE share_token = ?");
        $stmt->execute([$token]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($file && !empty($file['path'])) {
            $path = __DIR__ . '/../../../../storage/tenants/' . $file['tenant_id'] . '/cloud/' . $file['path'];
            if (file_exists($path)) {
                $this->logAccess($file['tenant_id'], 'file', $file['id'], 'download');
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $mimes = [
                    'png' => 'image/png',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp',
                    'svg' => 'image/svg+xml',
                    'pdf' => 'application/pdf'
                ];
                $mime = $mimes[$ext] ?? 'application/octet-stream';

                header('Content-Type: ' . $mime);
                header('Content-Length: ' . filesize($path));
                header('Content-Disposition: attachment; filename="' . basename($file['name']) . '"');
                readfile($path);
                exit;
            }
        }
        http_response_code(404);
        die("File not found on server.");
    }

    public function downloadSharedFolderFile()
    {
        $token = $_GET['token'] ?? '';
        $id = $_GET['id'] ?? null;
        if (empty($token) || empty($id)) {
            die("Invalid download parameters.");
        }

        if (!\App\Core\Auth::check()) {
            $_SESSION['redirect_after_login'] = '/cloud/share/download?token=' . urlencode($token) . '&id=' . urlencode($id);
            header('Location: /register?msg=' . urlencode('Please log in or create a Casjoe account to download files from this shared folder.'));
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT cf.* FROM cloud_files cf JOIN cloud_folders fo ON cf.folder_id = fo.id WHERE cf.id = ? AND fo.share_token = ?");
        $stmt->execute([$id, $token]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($file && !empty($file['path'])) {
            $path = __DIR__ . '/../../../../storage/tenants/' . $file['tenant_id'] . '/cloud/' . $file['path'];
            if (file_exists($path)) {
                $this->logAccess($file['tenant_id'], 'file', $file['id'], 'download');
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $mimes = [
                    'png' => 'image/png',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp',
                    'svg' => 'image/svg+xml',
                    'pdf' => 'application/pdf'
                ];
                $mime = $mimes[$ext] ?? 'application/octet-stream';

                header('Content-Type: ' . $mime);
                header('Content-Length: ' . filesize($path));
                header('Content-Disposition: attachment; filename="' . basename($file['name']) . '"');
                readfile($path);
                exit;
            }
        }
        http_response_code(404);
        die("File not found.");
    }

    public function saveSettings()
    {
        $defaultMode = $_POST['default_sharing_mode'] ?? 'preview_login';
        $alwaysLogin = !empty($_POST['always_require_login']) ? 1 : 0;
        $disablePublic = !empty($_POST['disable_public_links']) ? 1 : 0;
        $forceExpiry = (int)($_POST['force_expiry_days'] ?? 0);
        $maxDl = (int)($_POST['max_download_count'] ?? 0);

        $stmt = $this->pdo->prepare("INSERT INTO cloud_sharing_policies (tenant_id, default_sharing_mode, always_require_login, disable_public_links, force_expiry_days, max_download_count) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE default_sharing_mode = VALUES(default_sharing_mode), always_require_login = VALUES(always_require_login), disable_public_links = VALUES(disable_public_links), force_expiry_days = VALUES(force_expiry_days), max_download_count = VALUES(max_download_count)");
        $stmt->execute([$this->tenantId, $defaultMode, $alwaysLogin, $disablePublic, $forceExpiry, $maxDl]);

        $this->logAccess($this->tenantId, 'folder', 0, 'permission_change');

        $_SESSION['cloud_flash'] = 'Enterprise sharing policies & defaults updated successfully!';
        header('Location: /cloud');
        exit;
    }

    public function folderTree()
    {
        header('Content-Type: application/json');
        if (!$this->tenantId) {
            echo json_encode(['tree' => []]);
            exit;
        }

        try {
            $stmt = $this->pdo->prepare("SELECT id, name, parent_id FROM cloud_folders WHERE tenant_id = ? ORDER BY name ASC");
            $stmt->execute([$this->tenantId]);
            $folders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $tree = [];
            $lookup = [];
            foreach ($folders as $f) {
                $f['children'] = [];
                $lookup[$f['id']] = $f;
            }
            foreach ($lookup as $id => &$node) {
                if (!empty($node['parent_id']) && isset($lookup[$node['parent_id']])) {
                    $lookup[$node['parent_id']]['children'][] = &$node;
                } else {
                    $tree[] = &$node;
                }
            }
            echo json_encode(['tree' => $tree]);
        } catch (\Throwable $t) {
            echo json_encode(['tree' => []]);
        }
        exit;
    }

    public function apiImages()
    {
        header('Content-Type: application/json');
        if (!$this->tenantId) {
            echo json_encode(['success' => false, 'files' => []]);
            exit;
        }

        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, disk_name, extension, size 
                FROM cloud_files 
                WHERE tenant_id = ? AND LOWER(extension) IN ('jpg', 'jpeg', 'png', 'gif', 'webp', 'svg')
                ORDER BY created_at DESC LIMIT 50
            ");
            $stmt->execute([$this->tenantId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $files = [];
            foreach ($rows as $r) {
                $url = '/cloud/asset/' . $this->tenantId . '/' . urlencode($r['disk_name']);
                $files[] = [
                    'id' => $r['id'],
                    'name' => $r['name'],
                    'thumbnail' => $url,
                    'url' => $url
                ];
            }
            echo json_encode(['success' => true, 'files' => $files]);
        } catch (\Throwable $t) {
            echo json_encode(['success' => false, 'files' => [], 'error' => $t->getMessage()]);
        }
        exit;
    }
}
