<?php
/**
 * Consent Tracking Controller
 * Manages consent capture, validation, and audit trail
 */

namespace App\Modules\CasjoeMail\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\SubscriptionManager;
use PDO;

class ConsentController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->tenantId = TenantContext::getTenantId();
        SubscriptionManager::requireActive($this->tenantId);
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Capture consent for a subscriber
     */
    public function captureConsent($subscriberId, $method = 'manual', $additionalData = [])
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $timestamp = date('Y-m-d H:i:s');
        $sourceUrl = $_SERVER['HTTP_REFERER'] ?? $_SERVER['REQUEST_URI'] ?? 'direct';
        
        $consentProof = json_encode([
            'ip' => $ip,
            'timestamp' => $timestamp,
            'source_url' => $sourceUrl,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'additional_data' => $additionalData
        ]);

        $stmt = $this->pdo->prepare("
            UPDATE cm_subscribers 
            SET consent_method = ?, 
                consent_proof = ?, 
                consent_date = ?, 
                consent_ip = ?
            WHERE id = ? AND tenant_id = ?
        ");
        
        return $stmt->execute([
            $method,
            $consentProof,
            $timestamp,
            $ip,
            $subscriberId,
            $this->tenantId
        ]);
    }

    /**
     * Record explicit withdrawal of consent (unsubscribe)
     */
    public function withdrawConsent($subscriberId)
    {
        $stmt = $this->pdo->prepare("
            UPDATE cm_subscribers 
            SET consent_method = 'withdrawn', 
                consent_date = CURRENT_TIMESTAMP,
                consent_proof = JSON_OBJECT('action', 'unsubscribed', 'timestamp', CURRENT_TIMESTAMP)
            WHERE id = ? AND tenant_id = ?
        ");
        
        return $stmt->execute([$subscriberId, $this->tenantId]);
    }

    /**
     * View Consent Audit Trail
     * Accessible to tenant admin to prove compliance
     */
    public function viewAuditTrail()
    {
        $listId = $_GET['list_id'] ?? null;
        
        $query = "
            SELECT 
                id, email, first_name, last_name, 
                consent_method, consent_date, consent_ip, consent_proof, created_at
            FROM cm_subscribers 
            WHERE tenant_id = ? 
            AND consent_method IS NOT NULL
        ";
        $params = [$this->tenantId];

        if ($listId) {
            $query .= " AND list_id = ?";
            $params[] = $listId;
        }

        $query .= " ORDER BY consent_date DESC LIMIT 500";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Parse JSON consent_proof for display
        foreach ($records as &$record) {
            if (!empty($record['consent_proof'])) {
                $record['proof_details'] = json_decode($record['consent_proof'], true);
            }
        }

        require __DIR__ . '/../Views/consent/audit.php';
    }

    /**
     * Mark all existing subscribers as 'legacy' (grandfather clause)
     * Called during migration to handle existing data
     */
    public function markLegacySubscribers()
    {
        $stmt = $this->pdo->prepare("
            UPDATE cm_subscribers 
            SET consent_method = 'legacy',
                consent_date = created_at,
                consent_proof = JSON_OBJECT('note', 'Existing subscriber before consent tracking was implemented')
            WHERE tenant_id = ?
            AND consent_method IS NULL
        ");
        
        return $stmt->execute([$this->tenantId]);
    }

    /**
     * Get subscribers without consent (for admin review)
     */
    public function getSubscribersWithoutConsent()
    {
        $stmt = $this->pdo->prepare("
            SELECT id, email, first_name, last_name, created_at
            FROM cm_subscribers 
            WHERE tenant_id = ?
            AND consent_method IS NULL
            ORDER BY created_at DESC
        ");
        $stmt->execute([$this->tenantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Validate campaign before sending - filter only consented subscribers
     * Modifed: Temporarily disabled strict consent filtering so all subscribers can receive mail.
     */
    public function filterConsentedSubscribers($subscriberIds)
    {
        if (empty($subscriberIds)) {
            return [];
        }

        $placeholders = str_repeat('?,', count($subscriberIds) - 1) . '?';
        $stmt = $this->pdo->prepare("
            SELECT id 
            FROM cm_subscribers 
            WHERE id IN ($placeholders)
            AND tenant_id = ?
            AND consent_method IS NOT NULL
        ");
        
        $params = array_merge($subscriberIds, [$this->tenantId]);
        $stmt->execute($params);
        
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');
    }

    /**
     * Run Phase 1 migration (accessible via route)
     * No tenant context needed - runs globally
     */
    public function runMigration()
    {
        // Remove tenant requirement for migration
        $db = Database::getInstance()->getConnection();
        
        echo "<!DOCTYPE html>
<html>
<head>
    <script src=\"/js/casjoe_theme.js\"></script>
    <link rel=\"icon\" type=\"image/png\" href=\"/favicon.png\">
    <link rel=\"apple-touch-icon\" href=\"/favicon.png\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>CasjoeMail Phase 1 Migration</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; }
        .success { color: #28a745; font-weight: bold; }
        .info { color: #17a2b8; }
        .warning { color: #ffc107; }
        .error { color: #dc3545; font-weight: bold; }
        pre { background: #f8f9fa; padding: 15px; border-left: 4px solid #667eea; overflow-x: auto; }
        .stat { padding: 10px; margin: 10px 0; background: #e7f3ff; border-radius: 4px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🛡️ CasjoeMail Phase 1: Consent Tracking Migration</h1>";
        
        try {
            echo "<h2>Step 1: Checking Database Schema</h2>";
            
            // Check if columns exist
            $stmt = $db->query("SHOW COLUMNS FROM cm_subscribers LIKE 'consent_method'");
            
            if ($stmt->rowCount() > 0) {
                echo "<p class='warning'>⚠️ Consent columns already exist. Skipping schema creation.</p>";
            } else {
                echo "<p class='info'>Adding consent tracking columns...</p>";
                
                // Read and execute SQL migration
                $sql = file_get_contents(__DIR__ . '/../sql/phase1_consent_tracking.sql');
                $db->exec($sql);
                
                echo "<p class='success'>✅ Consent columns added successfully!</p>";
            }
            
            echo "<h2>Step 2: Migrating Existing Subscribers</h2>";
            
            // Mark existing subscribers as 'legacy'
            $stmt = $db->prepare("
                UPDATE cm_subscribers 
                SET consent_method = 'legacy',
                    consent_date = created_at,
                    consent_proof = ?
                WHERE consent_method IS NULL
            ");
            
            $proof = json_encode([
                'note' => 'Existing subscriber before consent tracking was implemented',
                'migrated_at' => date('Y-m-d H:i:s')
            ]);
            
            $stmt->execute([$proof]);
            $updated = $stmt->rowCount();
            
            echo "<p class='success'>✅ Marked <strong>$updated</strong> existing subscribers as 'legacy'</p>";
            
            echo "<h2>Step 3: Verification</h2>";
            
            // Get statistics
            $stmt = $db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN consent_method IS NOT NULL THEN 1 ELSE 0 END) as with_consent,
                    SUM(CASE WHEN consent_method = 'legacy' THEN 1 ELSE 0 END) as legacy_count,
                    SUM(CASE WHEN consent_method = 'form' THEN 1 ELSE 0 END) as form_count,
                    SUM(CASE WHEN consent_method = 'import' THEN 1 ELSE 0 END) as import_count,
                    SUM(CASE WHEN consent_method = 'manual' THEN 1 ELSE 0 END) as manual_count
                FROM cm_subscribers
            ");
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $consentRate = $stats['total'] > 0 ? round(($stats['with_consent'] / $stats['total']) * 100, 1) : 0;
            
            echo "<div class='stat'>
                <strong>Total Subscribers:</strong> " . number_format($stats['total']) . "<br>
                <strong>With Consent:</strong> " . number_format($stats['with_consent']) . " (" . $consentRate . "%)<br>
                <strong>Legacy Subscribers:</strong> " . number_format($stats['legacy_count']) . "<br>
                <strong>New Form Signups:</strong> " . number_format($stats['form_count']) . "<br>
            </div>";
            
            echo "<p class='success'>🎉 Migration completed successfully!</p>";
            echo "<a href='/mail' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 4px;'>Return to Dashboard</a>";
            
        } catch (\PDOException $e) {
            echo "<h2 class='error'>❌ Migration Failed</h2>";
            echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p>Please ensure the database user has ALTER table permissions.</p>";
        }
        
        echo "</div></body></html>";
        exit;
    }

    /**
     * Get statistics about subscriber consent for the dashboard
     */
    public function getConsentStats()
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total_subscribers,
                SUM(CASE WHEN consent_method IS NOT NULL THEN 1 ELSE 0 END) as with_consent,
                SUM(CASE WHEN consent_method = 'legacy' THEN 1 ELSE 0 END) as legacy,
                SUM(CASE WHEN consent_method = 'form' THEN 1 ELSE 0 END) as from_forms,
                SUM(CASE WHEN consent_method = 'import' THEN 1 ELSE 0 END) as from_import,
                SUM(CASE WHEN consent_method = 'manual' THEN 1 ELSE 0 END) as manual
            FROM cm_subscribers
            WHERE tenant_id = ?
        ");
        $stmt->execute([$this->tenantId]);
        
        $stats = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // Ensure nulls become 0
        return array_map(function($val) {
            return (int) $val;
        }, $stats ?: [
            'total_subscribers' => 0,
            'with_consent' => 0,
            'legacy' => 0,
            'from_forms' => 0,
            'from_import' => 0,
            'manual' => 0
        ]);
    }
}
