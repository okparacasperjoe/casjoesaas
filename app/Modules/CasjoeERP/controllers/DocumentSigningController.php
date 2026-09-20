<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Auth;
use PDO;

class DocumentSigningController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
        $this->ensureTableExists();
    }

    /**
     * Auto-migrates erp_documents table if not exists
     */
    private function ensureTableExists()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS erp_documents (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tenant_id INT NOT NULL,
                uuid VARCHAR(64) NOT NULL UNIQUE,
                title VARCHAR(255) NOT NULL,
                category VARCHAR(50) DEFAULT 'contract',
                document_type VARCHAR(20) DEFAULT 'text',
                content LONGTEXT NULL,
                file_path VARCHAR(255) NULL,
                recipient_name VARCHAR(255) NOT NULL,
                recipient_email VARCHAR(255) NOT NULL,
                recipient_type VARCHAR(50) DEFAULT 'client',
                status VARCHAR(30) DEFAULT 'draft',
                signing_deadline DATE NULL,
                signature LONGTEXT NULL,
                signature_type VARCHAR(30) DEFAULT 'drawn',
                signed_name VARCHAR(255) NULL,
                signed_at DATETIME NULL,
                signed_ip VARCHAR(50) NULL,
                signed_user_agent TEXT NULL,
                declined_reason TEXT NULL,
                created_by INT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (tenant_id),
                INDEX (uuid),
                INDEX (status)
            )";
            $this->pdo->exec($sql);
        } catch (\Throwable $e) {
            error_log("Error creating erp_documents table: " . $e->getMessage());
        }
    }

    private function checkAccess()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Main Documents Dashboard
     */
    public function index()
    {
        $this->checkAccess();

        $filter = $_GET['filter'] ?? 'all';
        $search = trim($_GET['search'] ?? '');

        $query = "SELECT * FROM erp_documents WHERE tenant_id = ?";
        $params = [$this->tenantId];

        if ($filter !== 'all') {
            $query .= " AND status = ?";
            $params[] = $filter;
        }

        if (!empty($search)) {
            $query .= " AND (title LIKE ? OR recipient_name LIKE ? OR recipient_email LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $query .= " ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate metrics
        $stmtMetrics = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status IN ('sent', 'viewed') THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'signed' THEN 1 ELSE 0 END) as signed,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft
            FROM erp_documents 
            WHERE tenant_id = ?
        ");
        $stmtMetrics->execute([$this->tenantId]);
        $metrics = $stmtMetrics->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'pending' => 0, 'signed' => 0, 'draft' => 0];

        $successMsg = $_SESSION['doc_success'] ?? null;
        $errorMsg = $_SESSION['doc_error'] ?? null;
        unset($_SESSION['doc_success'], $_SESSION['doc_error']);

        require __DIR__ . '/../Views/documents/index.php';
    }

    /**
     * Document Builder View
     */
    public function create()
    {
        $this->checkAccess();

        // Fetch clients, vendors, and employees for dropdown pickers
        $clients = [];
        $vendors = [];
        $employees = [];

        try {
            $stmtC = $this->pdo->prepare("SELECT id, name, email FROM erp_customers WHERE tenant_id = ? ORDER BY name ASC");
            $stmtC->execute([$this->tenantId]);
            $clients = $stmtC->fetchAll(PDO::FETCH_ASSOC);

            $stmtV = $this->pdo->prepare("SELECT id, name, email FROM erp_vendors WHERE tenant_id = ? ORDER BY name ASC");
            $stmtV->execute([$this->tenantId]);
            $vendors = $stmtV->fetchAll(PDO::FETCH_ASSOC);

            $stmtE = $this->pdo->prepare("SELECT id, CONCAT(first_name, ' ', last_name) as name, email FROM erp_employees WHERE tenant_id = ? ORDER BY first_name ASC");
            $stmtE->execute([$this->tenantId]);
            $employees = $stmtE->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            // Ignore if tables are empty
        }

        require __DIR__ . '/../Views/documents/create.php';
    }

    /**
     * Store Document
     */
    public function store()
    {
        $this->checkAccess();

        $title = trim($_POST['title'] ?? '');
        $category = $_POST['category'] ?? 'contract';
        $recipientName = trim($_POST['recipient_name'] ?? '');
        $recipientEmail = trim($_POST['recipient_email'] ?? '');
        $recipientType = $_POST['recipient_type'] ?? 'client';
        $deadline = !empty($_POST['signing_deadline']) ? $_POST['signing_deadline'] : null;
        $content = trim($_POST['content'] ?? '');
        $action = $_POST['submit_action'] ?? 'send'; // 'draft' or 'send'

        if (empty($title) || empty($recipientName) || empty($recipientEmail) || empty($content)) {
            $_SESSION['doc_error'] = "Please provide Document Title, Recipient Name, Recipient Email, and Document Content.";
            header('Location: /erp/documents/create');
            exit;
        }

        $uuid = bin2hex(random_bytes(16));
        $status = ($action === 'draft') ? 'draft' : 'sent';
        $user = Auth::user();
        $createdBy = $user['id'] ?? null;

        $stmt = $this->pdo->prepare("
            INSERT INTO erp_documents 
            (tenant_id, uuid, title, category, document_type, content, recipient_name, recipient_email, recipient_type, status, signing_deadline, created_by)
            VALUES (?, ?, ?, ?, 'text', ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $this->tenantId,
            $uuid,
            $title,
            $category,
            $content,
            $recipientName,
            $recipientEmail,
            $recipientType,
            $status,
            $deadline,
            $createdBy
        ]);

        if ($status === 'sent') {
            $_SESSION['doc_success'] = "Document '{$title}' created and ready for signing. Signing link generated!";
        } else {
            $_SESSION['doc_success'] = "Document '{$title}' saved as draft.";
        }

        header('Location: /erp/documents/show?uuid=' . $uuid);
        exit;
    }

    /**
     * View Document Details & Signature Certificate
     */
    public function show()
    {
        $this->checkAccess();

        $uuid = $_GET['uuid'] ?? '';
        $id = $_GET['id'] ?? null;

        if (!empty($uuid)) {
            $stmt = $this->pdo->prepare("SELECT * FROM erp_documents WHERE uuid = ? AND tenant_id = ?");
            $stmt->execute([$uuid, $this->tenantId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT * FROM erp_documents WHERE id = ? AND tenant_id = ?");
            $stmt->execute([$id, $this->tenantId]);
        }

        $doc = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$doc) {
            $_SESSION['doc_error'] = "Document not found.";
            header('Location: /erp/documents');
            exit;
        }

        // Host / App URL
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'casjoe.com';
        $signUrl = $protocol . $host . '/sign/' . $doc['uuid'];

        $successMsg = $_SESSION['doc_success'] ?? null;
        $errorMsg = $_SESSION['doc_error'] ?? null;
        unset($_SESSION['doc_success'], $_SESSION['doc_error']);

        require __DIR__ . '/../Views/documents/show.php';
    }

    /**
     * Edit Draft Document
     */
    public function edit()
    {
        $this->checkAccess();

        $id = $_GET['id'] ?? null;
        $stmt = $this->pdo->prepare("SELECT * FROM erp_documents WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doc) {
            header('Location: /erp/documents');
            exit;
        }

        require __DIR__ . '/../Views/documents/edit.php';
    }

    /**
     * Update Draft Document
     */
    public function update()
    {
        $this->checkAccess();

        $id = $_POST['id'];
        $title = trim($_POST['title'] ?? '');
        $category = $_POST['category'] ?? 'contract';
        $recipientName = trim($_POST['recipient_name'] ?? '');
        $recipientEmail = trim($_POST['recipient_email'] ?? '');
        $recipientType = $_POST['recipient_type'] ?? 'client';
        $deadline = !empty($_POST['signing_deadline']) ? $_POST['signing_deadline'] : null;
        $content = trim($_POST['content'] ?? '');
        $action = $_POST['submit_action'] ?? 'save';

        $stmt = $this->pdo->prepare("
            UPDATE erp_documents 
            SET title = ?, category = ?, recipient_name = ?, recipient_email = ?, recipient_type = ?, signing_deadline = ?, content = ?
            WHERE id = ? AND tenant_id = ?
        ");
        $stmt->execute([
            $title,
            $category,
            $recipientName,
            $recipientEmail,
            $recipientType,
            $deadline,
            $content,
            $id,
            $this->tenantId
        ]);

        if ($action === 'send') {
            $stmtSend = $this->pdo->prepare("UPDATE erp_documents SET status = 'sent' WHERE id = ? AND tenant_id = ?");
            $stmtSend->execute([$id, $this->tenantId]);
            $_SESSION['doc_success'] = "Document updated and activated for signing.";
        } else {
            $_SESSION['doc_success'] = "Document updated successfully.";
        }

        header('Location: /erp/documents/show?id=' . $id);
        exit;
    }

    /**
     * Delete Document
     */
    public function delete()
    {
        $this->checkAccess();

        $id = $_POST['id'] ?? null;
        $stmt = $this->pdo->prepare("DELETE FROM erp_documents WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);

        $_SESSION['doc_success'] = "Document deleted successfully.";
        header('Location: /erp/documents');
        exit;
    }

    /**
     * Clean Print View with Signature Certificate
     */
    public function printDocument()
    {
        $this->checkAccess();

        $uuid = $_GET['uuid'] ?? '';
        $stmt = $this->pdo->prepare("SELECT * FROM erp_documents WHERE uuid = ? AND tenant_id = ?");
        $stmt->execute([$uuid, $this->tenantId]);
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doc) {
            die("Document not found.");
        }

        require __DIR__ . '/../Views/documents/print.php';
    }

    // =========================================================================
    // PUBLIC SIGNING WORKFLOW (No login required for client / signer)
    // =========================================================================

    /**
     * Public Document Signing View: /sign/{uuid}
     */
    public function publicSignView($uuid = null)
    {
        if (is_array($uuid)) {
            $uuid = $uuid['uuid'] ?? '';
        }
        if (empty($uuid)) {
            $uuid = $_GET['uuid'] ?? '';
        }

        $stmt = $this->pdo->prepare("SELECT * FROM erp_documents WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doc) {
            http_response_code(404);
            require __DIR__ . '/../Views/documents/public_not_found.php';
            exit;
        }

        // Mark as 'viewed' if currently 'sent'
        if ($doc['status'] === 'sent') {
            $upStmt = $this->pdo->prepare("UPDATE erp_documents SET status = 'viewed' WHERE id = ?");
            $upStmt->execute([$doc['id']]);
            $doc['status'] = 'viewed';
        }

        // Fetch Tenant Brand/Settings
        $stmtSettings = $this->pdo->prepare("SELECT setting_key, setting_value FROM erp_settings WHERE tenant_id = ?");
        $stmtSettings->execute([$doc['tenant_id']]);
        $settings = $stmtSettings->fetchAll(PDO::FETCH_KEY_PAIR);
        $companyName = $settings['company_name'] ?? 'Casjoe Partner';

        require __DIR__ . '/../Views/documents/public_sign.php';
    }

    /**
     * Public Signature Submission: POST /sign/{uuid}/submit
     */
    public function publicSubmitSignature($uuid = null)
    {
        if (is_array($uuid)) {
            $uuid = $uuid['uuid'] ?? '';
        }
        if (empty($uuid)) {
            $uuid = $_POST['uuid'] ?? '';
        }

        $stmt = $this->pdo->prepare("SELECT * FROM erp_documents WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doc) {
            http_response_code(404);
            die("Invalid signing link.");
        }

        if ($doc['status'] === 'signed') {
            header("Location: /sign/{$uuid}?already_signed=1");
            exit;
        }

        $signature = $_POST['signature'] ?? '';
        $signedName = trim($_POST['signed_name'] ?? '');
        $agreed = !empty($_POST['agreed']);
        $signatureType = $_POST['signature_type'] ?? 'drawn';

        if (empty($signature) || empty($signedName) || !$agreed) {
            die("Error: Signature, legal printed name, and agreement checkbox are all required.");
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        }
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $now = date('Y-m-d H:i:s');

        $upStmt = $this->pdo->prepare("
            UPDATE erp_documents 
            SET status = 'signed',
                signature = ?,
                signature_type = ?,
                signed_name = ?,
                signed_at = ?,
                signed_ip = ?,
                signed_user_agent = ?
            WHERE id = ?
        ");
        $upStmt->execute([
            $signature,
            $signatureType,
            $signedName,
            $now,
            $ip,
            $userAgent,
            $doc['id']
        ]);

        header("Location: /sign/{$uuid}?completed=1");
        exit;
    }

    /**
     * Public Decline: POST /sign/{uuid}/decline
     */
    public function publicDeclineSignature($uuid = null)
    {
        if (is_array($uuid)) {
            $uuid = $uuid['uuid'] ?? '';
        }
        if (empty($uuid)) {
            $uuid = $_POST['uuid'] ?? '';
        }

        $reason = trim($_POST['declined_reason'] ?? 'Recipient chose to decline.');

        $stmt = $this->pdo->prepare("UPDATE erp_documents SET status = 'declined', declined_reason = ? WHERE uuid = ?");
        $stmt->execute([$reason, $uuid]);

        header("Location: /sign/{$uuid}?declined=1");
        exit;
    }
}
