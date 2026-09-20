<?php
/**
 * AI Lead Scoring Engine (Cron Job)
 * Recommended Schedule: Run once every hour (0 * * * *)
 */

require_once __DIR__ . '/app/Core/bootstrap.php';

use App\Core\Database;
use App\Core\Services\LeadScoringService;

if (php_sapi_name() !== 'cli') {
    $token = $_GET['token'] ?? '';
    // Adjust token if needed for external calls
    if ($token !== 'casjoe_cron_key_9911') {
        die("Unauthorized access.");
    }
}

echo "--- AI Lead Scoring Engine ---\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";

try {
    $db = Database::getInstance()->getConnection();

    // Fetch all active tenants
    $stmtTenants = $db->query("SELECT id FROM tenants");
    $tenants = $stmtTenants->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($tenants) . " active tenant(s).\n";

    foreach ($tenants as $tenant) {
        $tenantId = $tenant['id'];
        echo "Processing Tenant ID {$tenantId}...\n";

        // Fetch all leads for this tenant
        $stmtLeads = $db->prepare("SELECT id FROM erp_crm_leads WHERE tenant_id = ?");
        $stmtLeads->execute([$tenantId]);
        $leads = $stmtLeads->fetchAll(PDO::FETCH_ASSOC);

        echo " -> Found " . count($leads) . " lead(s).\n";

        $recalculated = 0;
        foreach ($leads as $lead) {
            // recalculateScore calculates current sum, applies completeness & decay, then caps/floors and updates ai_score
            LeadScoringService::recalculateScore($tenantId, $lead['id']);
            $recalculated++;
        }
        
        echo " -> Recalculated scores for {$recalculated} lead(s).\n";
    }

} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
}

echo "Finished processing lead scores.\n";
