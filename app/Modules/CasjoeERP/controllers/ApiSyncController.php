<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

class ApiSyncController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->authenticate();
    }

    private function authenticate()
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        // Fallback for Nginx/Apache not passing Authorization header properly
        if (empty($authHeader) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }
        
        if (empty($authHeader) && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $this->respondUnauthorized('Missing or invalid Authorization header. Expected Bearer token.');
        }

        $token = $matches[1];

        $stmt = $this->pdo->prepare("SELECT tenant_id FROM erp_settings WHERE setting_key = 'api_secret_token' AND setting_value = ?");
        $stmt->execute([$token]);
        $tenantId = $stmt->fetchColumn();

        if (!$tenantId) {
            $this->respondUnauthorized('Invalid API Key');
        }

        $this->tenantId = $tenantId;
    }

    private function respondUnauthorized($message)
    {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unauthorized', 'message' => $message]);
        exit;
    }

    private function respondJson($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function syncSummary()
    {
        $summary = [
            'status' => 'success',
            'message' => 'API Connection Successful',
            'tenant_id' => $this->tenantId,
            'counts' => [
                'leads' => $this->getCount('erp_crm_leads'),
                'customers' => $this->getCount('erp_crm_customers'),
                'invoices' => $this->getCount('erp_invoices'),
                'projects' => $this->getCount('erp_projects'),
                'tasks' => $this->getCount('erp_tasks')
            ]
        ];
        $this->respondJson($summary);
    }

    public function getLeads()
    {
        $stmt = $this->pdo->prepare("SELECT id, name, email, phone, status, created_at FROM erp_crm_leads WHERE tenant_id = ? ORDER BY id DESC LIMIT 500");
        $stmt->execute([$this->tenantId]);
        $this->respondJson(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }

    public function getCustomers()
    {
        $stmt = $this->pdo->prepare("SELECT id, name, email, phone, company, created_at FROM erp_crm_customers WHERE tenant_id = ? ORDER BY id DESC LIMIT 500");
        $stmt->execute([$this->tenantId]);
        $this->respondJson(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }

    private function getCount($table)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$table} WHERE tenant_id = ?");
            $stmt->execute([$this->tenantId]);
            return (int) $stmt->fetchColumn();
        } catch (\Exception $e) {
            return 0;
        }
    }
}
