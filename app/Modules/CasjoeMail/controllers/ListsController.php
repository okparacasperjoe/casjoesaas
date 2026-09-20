<?php

namespace App\Modules\CasjoeMail\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\SubscriptionManager;
use PDO;

class ListsController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->tenantId = TenantContext::getTenantId();
        SubscriptionManager::requireActive($this->tenantId);
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function index()
    {
        // Get all lists with subscriber count
        $stmt = $this->pdo->query("
            SELECT l.*, COUNT(s.id) as subscriber_count 
            FROM cm_lists l 
            LEFT JOIN cm_subscribers s ON l.id = s.list_id 
            WHERE l.tenant_id = {$this->tenantId} 
            GROUP BY l.id
            ORDER BY l.created_at DESC
        ");
        $lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/lists/index.php';
    }

    public function create()
    {
        require __DIR__ . '/../Views/lists/create.php';
    }

    public function store()
    {
        $name = $_POST['name'] ?? '';

        if (empty($name)) {
            header('Location: /mail/lists/create?error=Name is required');
            exit;
        }

        $stmt = $this->pdo->prepare("INSERT INTO cm_lists (tenant_id, name) VALUES (?, ?)");
        $stmt->execute([$this->tenantId, $name]);

        header('Location: /mail/lists');
        exit;
    }

    public function updateName()
    {
        $listId = $_POST['list_id'] ?? 0;
        $name = trim($_POST['name'] ?? '');
        
        if (empty($name)) {
            header("Location: /mail/lists/view?id=$listId&error=Name is required");
            exit;
        }

        $stmt = $this->pdo->prepare("UPDATE cm_lists SET name = ? WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$name, $listId, $this->tenantId]);

        header("Location: /mail/lists/view?id=$listId");
        exit;
    }

    public function show()
    {
        $id = $_GET['id'] ?? 0;
        
        // Fetch List
        $stmt = $this->pdo->prepare("SELECT * FROM cm_lists WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $list = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$list) {
            header('Location: /mail/lists');
            exit;
        }

        // Fetch Subscribers
        $stmt = $this->pdo->prepare("SELECT * FROM cm_subscribers WHERE list_id = ? AND tenant_id = ? ORDER BY created_at DESC");
        $stmt->execute([$id, $this->tenantId]);
        $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/lists/show.php';
    }

    public function addSubscriber()
    {
        $listId = $_POST['list_id'] ?? 0;
        $email = $_POST['email'] ?? '';
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';

        if (empty($email) || empty($listId)) {
            header("Location: /mail/lists/view?id=$listId&error=Email required");
            exit;
        }

        try {
            $stmt = $this->pdo->prepare("INSERT INTO cm_subscribers (tenant_id, list_id, email, first_name, last_name) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$this->tenantId, $listId, $email, $firstName, $lastName]);
        } catch (\PDOException $e) {
            // Likely duplicate email
            header("Location: /mail/lists/view?id=$listId&error=Subscriber already exists");
            exit;
        }

        header("Location: /mail/lists/view?id=$listId");
        exit;
    }

    public function import()
    {
        $listId = $_GET['list_id'] ?? 0;
        if (!$listId) {
            header('Location: /mail/lists');
            exit;
        }
        require __DIR__ . '/../Views/lists/import.php';
    }

    public function importProcess()
    {
        $listId = $_POST['list_id'] ?? 0;
        
        if (empty($_FILES['csv_file']['tmp_name'])) {
            header("Location: /mail/lists/import?list_id=$listId&error=No file uploaded");
            exit;
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");
        
        if ($handle === FALSE) {
            header("Location: /mail/lists/import?list_id=$listId&error=Could not read file");
            exit;
        }

        $header = fgetcsv($handle, 1000, ",", "\"", "\\");
        if (!$header) {
            fclose($handle);
            header("Location: /mail/lists/import?list_id=$listId&error=Invalid CSV file format");
            exit;
        }

        // Clean headers
        $header = array_map('trim', $header);
        $header = array_map('strtolower', $header);
        if (!empty($header[0])) {
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        }

        $emailIndex = false;
        foreach (['email', 'email address', 'e-mail'] as $col) {
            $emailIndex = array_search($col, $header);
            if ($emailIndex !== false) break;
        }
        
        if ($emailIndex === false) {
            fclose($handle);
            header("Location: /mail/lists/import?list_id=$listId&error=CSV must contain an 'email' column");
            exit;
        }

        $firstNameIndex = false;
        foreach (['first_name', 'first name'] as $col) {
            $firstNameIndex = array_search($col, $header);
            if ($firstNameIndex !== false) break;
        }

        $lastNameIndex = false;
        foreach (['last_name', 'last name'] as $col) {
            $lastNameIndex = array_search($col, $header);
            if ($lastNameIndex !== false) break;
        }
        
        $count = 0;
        while (($data = fgetcsv($handle, 1000, ",", "\"", "\\")) !== FALSE) {
            $email = trim($data[$emailIndex] ?? '');
            $firstName = $firstNameIndex !== false ? trim($data[$firstNameIndex] ?? '') : '';
            $lastName = $lastNameIndex !== false ? trim($data[$lastNameIndex] ?? '') : '';
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            try {
                $stmt = $this->pdo->prepare("INSERT INTO cm_subscribers (tenant_id, list_id, email, first_name, last_name) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$this->tenantId, $listId, $email, $firstName, $lastName]);
                $count++;
            } catch (\PDOException $e) {
                // Ignore duplicate emails
            }
        }
        
        fclose($handle);
        
        header("Location: /mail/lists/import?list_id=$listId&status=success&count=$count");
        exit;
    }

    public function importLinkProcess()
    {
        $listId = $_POST['list_id'] ?? 0;
        $url = trim($_POST['csv_url'] ?? '');
        
        // Transform Google Sheets URL to CSV export URL
        if (preg_match('/docs\.google\.com\/spreadsheets\/d\/([a-zA-Z0-9-_]+)/', $url, $matches)) {
            $url = "https://docs.google.com/spreadsheets/d/" . $matches[1] . "/export?format=csv";
        }
        
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            header("Location: /mail/lists/import?list_id=$listId&error=" . urlencode("Invalid URL provided"));
            exit;
        }

        // Fetch CSV data
        $csvData = @file_get_contents($url);
        
        if ($csvData === false) {
            header("Location: /mail/lists/import?list_id=$listId&error=" . urlencode("Could not fetch data from URL"));
            exit;
        }

        // Create temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'csv_import');
        file_put_contents($tempFile, $csvData);
        
        $handle = fopen($tempFile, "r");
        if ($handle === FALSE) {
            header("Location: /mail/lists/import?list_id=$listId&error=" . urlencode("Could not read fetched data"));
            exit;
        }

        $header = fgetcsv($handle, 1000, ",", "\"", "\\");
        if (!$header) {
            fclose($handle);
            unlink($tempFile);
            header("Location: /mail/lists/import?list_id=$listId&error=" . urlencode("Invalid CSV format from URL"));
            exit;
        }

        // Clean headers
        $header = array_map('trim', $header);
        $header = array_map('strtolower', $header);
        if (!empty($header[0])) {
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        }

        $emailIndex = false;
        foreach (['email', 'email address', 'e-mail'] as $col) {
            $emailIndex = array_search($col, $header);
            if ($emailIndex !== false) break;
        }
        
        if ($emailIndex === false) {
            fclose($handle);
            unlink($tempFile);
            $debugHeader = htmlspecialchars(implode(', ', array_slice($header, 0, 5)));
            header("Location: /mail/lists/import?list_id=$listId&error=" . urlencode("CSV from URL must contain an 'email' column. Found headers: " . $debugHeader));
            exit;
        }

        $firstNameIndex = false;
        foreach (['first_name', 'first name'] as $col) {
            $firstNameIndex = array_search($col, $header);
            if ($firstNameIndex !== false) break;
        }

        $lastNameIndex = false;
        foreach (['last_name', 'last name'] as $col) {
            $lastNameIndex = array_search($col, $header);
            if ($lastNameIndex !== false) break;
        }
        
        $count = 0;
        while (($data = fgetcsv($handle, 1000, ",", "\"", "\\")) !== FALSE) {
            $email = trim($data[$emailIndex] ?? '');
            $firstName = $firstNameIndex !== false ? trim($data[$firstNameIndex] ?? '') : '';
            $lastName = $lastNameIndex !== false ? trim($data[$lastNameIndex] ?? '') : '';
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            try {
                $stmt = $this->pdo->prepare("INSERT INTO cm_subscribers (tenant_id, list_id, email, first_name, last_name) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$this->tenantId, $listId, $email, $firstName, $lastName]);
                $count++;
            } catch (\PDOException $e) {
                // Ignore duplicate emails
            }
        }
        
        fclose($handle);
        unlink($tempFile);
        
        header("Location: /mail/lists/import?list_id=$listId&status=success&count=$count");
        exit;
    }

    public function syncErp()
    {
        $listId = $_POST['list_id'] ?? 0;
        
        if (!$listId) {
            header('Location: /mail/lists');
            exit;
        }

        try {
            $stmt = $this->pdo->prepare("SELECT email, first_name, last_name FROM customers WHERE tenant_id = ? AND email IS NOT NULL AND email != ''");
            $stmt->execute([$this->tenantId]);
            $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $count = 0;
            foreach ($customers as $customer) {
                if (!filter_var($customer['email'], FILTER_VALIDATE_EMAIL)) continue;
                
                try {
                    $insertStmt = $this->pdo->prepare("INSERT INTO cm_subscribers (tenant_id, list_id, email, first_name, last_name) VALUES (?, ?, ?, ?, ?)");
                    $insertStmt->execute([$this->tenantId, $listId, $customer['email'], $customer['first_name'], $customer['last_name']]);
                    $count++;
                } catch (\PDOException $e) {
                    // Ignore duplicates
                }
            }
            
            header("Location: /mail/lists/import?list_id=$listId&status=success&count=$count");
            exit;
            
        } catch (\PDOException $e) {
            header("Location: /mail/lists/import?list_id=$listId&error=" . urlencode("Could not sync from ERP. Check if customers table exists."));
            exit;
        }
    }
}
