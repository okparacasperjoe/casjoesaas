<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

class SystemController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
    }

    public function settings()
    {
        $stmt = $this->pdo->prepare("SELECT setting_key, setting_value FROM erp_settings WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $stmtTenant = $this->pdo->prepare("SELECT name, logo, country, currency FROM tenants WHERE id = ?");
        $stmtTenant->execute([$this->tenantId]);
        $tenant = $stmtTenant->fetch(PDO::FETCH_ASSOC) ?: [];

        if (empty($settings['company_name']) && !empty($tenant['name'])) {
            $settings['company_name'] = $tenant['name'];
        }

        require __DIR__ . '/../Views/system/settings.php';
    }

    public function updateSettings()
    {
        // Handle Logo Upload if present
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../../../public/uploads/logos/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])) {
                $filename = 'logo_' . $this->tenantId . '_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $filename)) {
                    $logoPath = '/uploads/logos/' . $filename;
                    $stmtL = $this->pdo->prepare("UPDATE tenants SET logo = ? WHERE id = ?");
                    $stmtL->execute([$logoPath, $this->tenantId]);
                }
            }
        }

        foreach ($_POST as $key => $value) {
            $sql = "INSERT INTO erp_settings (tenant_id, setting_key, setting_value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->tenantId, $key, $value, $value]);
        }

        if (!empty($_POST['company_name'])) {
            $stmtT = $this->pdo->prepare("UPDATE tenants SET name = ? WHERE id = ?");
            $stmtT->execute([trim($_POST['company_name']), $this->tenantId]);
            if (isset($_SESSION['user_id'])) {
                $stmtU = $this->pdo->prepare("UPDATE users SET business_name = ? WHERE id = ?");
                $stmtU->execute([trim($_POST['company_name']), $_SESSION['user_id']]);
            }
        }

        if (!empty($_POST['currency'])) {
            $stmtC = $this->pdo->prepare("UPDATE tenants SET currency = ? WHERE id = ?");
            $stmtC->execute([trim($_POST['currency']), $this->tenantId]);
            if (isset($_SESSION['user_id'])) {
                $stmtU = $this->pdo->prepare("UPDATE users SET currency = ? WHERE id = ?");
                $stmtU->execute([trim($_POST['currency']), $_SESSION['user_id']]);
            }
            $_SESSION['currency'] = trim($_POST['currency']);
        }

        if (!empty($_POST['country'])) {
            $stmtCo = $this->pdo->prepare("UPDATE tenants SET country = ? WHERE id = ?");
            $stmtCo->execute([trim($_POST['country']), $this->tenantId]);
            if (isset($_SESSION['user_id'])) {
                $stmtU = $this->pdo->prepare("UPDATE users SET country = ? WHERE id = ?");
                $stmtU->execute([trim($_POST['country']), $_SESSION['user_id']]);
            }
            $_SESSION['country'] = trim($_POST['country']);
        }
        
        header('Location: /erp/settings?saved=1');
        exit;
    }

    public function activity()
    {
        // Join with users table if it exists, otherwise just show raw ID
        $stmt = $this->pdo->prepare("SELECT * FROM erp_activity_log WHERE tenant_id = ? ORDER BY created_at DESC LIMIT 50");
        $stmt->execute([$this->tenantId]);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/system/activity.php';
    }

    public function announcements()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM erp_announcements WHERE tenant_id = ? ORDER BY created_at DESC");
        $stmt->execute([$this->tenantId]);
        $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/system/announcements.php';
    }

    public function createAnnouncement()
    {
        require __DIR__ . '/../Views/system/create_announcement.php';
    }

    public function storeAnnouncement()
    {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $createdBy = 1; // Default user for now

        $stmt = $this->pdo->prepare("INSERT INTO erp_announcements (tenant_id, title, content, created_by) VALUES (?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $title, $content, $createdBy]);

        header('Location: /erp/announcements');
        exit;
    }

    public function roles()
    {
        // Placeholder data or detailed implementation
        $roles = [
            ['name' => 'Admin', 'description' => 'Full System Access'],
            ['name' => 'Manager', 'description' => 'Department Access'],
            ['name' => 'Employee', 'description' => 'Self Service Only'],
        ];
        require __DIR__ . '/../Views/system/roles.php';
    }

    public function permissions()
    {
        $permissions = [
            'hrm_read' => 'View HR Data',
            'hrm_write' => 'Manage HR Data',
            'fin_read' => 'View Finance',
            'fin_write' => 'Manage Finance',
        ];
        require __DIR__ . '/../Views/system/permissions.php';
    }

    public function reports()
    {
        require __DIR__ . '/../Views/system/reports.php';
    }

    public function audit_log()
    {
        // Re-use activity log or separate
        $this->activity();
    }

    public function support()
    {
        header('Location: /erp/crm');
        exit;
    }

    public function company()
    {
        // Fetch company details from settings
        $stmt = $this->pdo->prepare("SELECT setting_key, setting_value FROM erp_settings WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        require __DIR__ . '/../Views/system/company.php';
    }

    public function users()
    {
        $stmt = $this->pdo->prepare("SELECT id, name, email, user_role, role, is_verified, created_at FROM users WHERE tenant_id = ? ORDER BY created_at DESC");
        $stmt->execute([$this->tenantId]);
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        require __DIR__ . '/../Views/system/users.php';
    }

    public function inviteUser()
    {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = $_POST['role'] ?? 'user';
        $userRole = $_POST['user_role'] ?? 'Staff';

        if (!empty($name) && !empty($email)) {
            $plainPassword = 'Welcome123!';
            $defaultPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
            $stmt = $this->pdo->prepare("INSERT INTO users (tenant_id, name, email, password, role, user_role, is_verified) VALUES (?, ?, ?, ?, ?, ?, 1)");
            try {
                $stmt->execute([$this->tenantId, $name, $email, $defaultPassword, $role, $userRole]);

                // Send email
                $loginUrl = "https://" . ($_SERVER['HTTP_HOST'] ?? 'app.casjoe.com') . "/login";
                $subject = "You have been invited to Casjoe ERP";
                $message = "<p>Hello " . htmlspecialchars($name) . ",</p>";
                $message .= "<p>You have been invited to join Casjoe ERP as a <strong>" . htmlspecialchars($userRole) . "</strong>.</p>";
                $message .= "<p>You can log in at <a href='" . htmlspecialchars($loginUrl) . "'>" . htmlspecialchars($loginUrl) . "</a> using the credentials below:</p>";
                $message .= "<ul>";
                $message .= "<li><strong>Email:</strong> " . htmlspecialchars($email) . "</li>";
                $message .= "<li><strong>Password:</strong> " . htmlspecialchars($plainPassword) . "</li>";
                $message .= "</ul>";
                $message .= "<p>Please ensure you change your password after logging in.</p>";
                
                \App\Core\Mailer::send($email, $subject, $message, false);
            } catch (\Exception $e) {
                // Handle or ignore dupes silently for now
                error_log("Failed to invite user or send email: " . $e->getMessage());
            }
        }
        header('Location: /erp/users');
        exit;
    }

    public function updateUserRole()
    {
        $userId = $_POST['user_id'] ?? 0;
        $role = $_POST['role'] ?? 'user';
        $userRole = $_POST['user_role'] ?? 'Staff';

        if ($userId) {
            $stmt = $this->pdo->prepare("UPDATE users SET role = ?, user_role = ? WHERE id = ? AND tenant_id = ?");
            $stmt->execute([$role, $userRole, $userId, $this->tenantId]);
        }
        header('Location: /erp/users');
        exit;
    }

    public function deleteUser()
    {
        $userId = $_POST['user_id'] ?? 0;
        if ($userId) {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ? AND tenant_id = ?");
            $stmt->execute([$userId, $this->tenantId]);
        }
        header('Location: /erp/users');
        exit;
    }

    public function generateApiKey()
    {
        $token = 'casjoe_live_' . bin2hex(random_bytes(32));
        
        $sql = "INSERT INTO erp_settings (tenant_id, setting_key, setting_value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$this->tenantId, 'api_secret_token', $token, $token]);
        
        header('Location: /erp/settings?api_generated=1');
        exit;
    }
}
