<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Services\EvolutionApiService;

class WhatsAppController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
    }

    private function checkAdmin()
    {
        $role = \App\Core\Auth::user()['role'] ?? '';
        if ($role !== 'admin') {
            header('Location: /erp/dashboard');
            exit;
        }
    }

    public function migrate()
    {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        require __DIR__ . '/../../../../migrate_whatsapp.php';
        exit;
    }

    public function index()
    {
        $this->checkAdmin();

        // Get or Create WhatsApp Instance Record
        $stmt = $this->pdo->prepare("SELECT * FROM erp_whatsapp_instances WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $instance = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$instance) {
            $instanceName = 'tenant_' . $this->tenantId . '_' . time();
            $this->pdo->prepare("INSERT INTO erp_whatsapp_instances (tenant_id, instance_name) VALUES (?, ?)")->execute([$this->tenantId, $instanceName]);
            $instance = [
                'instance_name' => $instanceName,
                'status' => 'disconnected'
            ];
        }

        $qrCodeBase64 = null;
        $connectionStatus = 'disconnected';

        // Check live status
        $res = EvolutionApiService::getConnectionState($instance['instance_name']);
        
        if ($res['status'] === 200 && isset($res['data']['instance']['state'])) {
            $state = $res['data']['instance']['state'];
            
            if ($state === 'open') {
                $connectionStatus = 'connected';
            } else {
                // Fetch QR code
                $qrRes = EvolutionApiService::connect($instance['instance_name']);
                if ($qrRes['status'] === 200 && isset($qrRes['data']['base64'])) {
                    $qrCodeBase64 = $qrRes['data']['base64'];
                    $connectionStatus = 'connecting';
                }
            }
        } else {
            // Instance probably doesn't exist on server, create it
            $createRes = EvolutionApiService::createInstance($instance['instance_name']);
            if ($createRes['status'] === 201 && isset($createRes['data']['qrcode']['base64'])) {
                $qrCodeBase64 = $createRes['data']['qrcode']['base64'];
                $connectionStatus = 'connecting';
            }
        }

        // Update DB status
        $this->pdo->prepare("UPDATE erp_whatsapp_instances SET status = ? WHERE tenant_id = ?")
             ->execute([$connectionStatus, $this->tenantId]);
        $instance['status'] = $connectionStatus;

        require __DIR__ . '/../Views/settings/whatsapp.php';
    }

    public function logout()
    {
        $this->checkAdmin();
        $stmt = $this->pdo->prepare("SELECT * FROM erp_whatsapp_instances WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $instance = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($instance) {
            EvolutionApiService::logout($instance['instance_name']);
            $this->pdo->prepare("UPDATE erp_whatsapp_instances SET status = 'disconnected' WHERE tenant_id = ?")->execute([$this->tenantId]);
        }
        
        header("Location: /erp/whatsapp");
        exit;
    }
}
