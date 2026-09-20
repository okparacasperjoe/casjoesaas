<?php
require_once 'app/core/bootstrap.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();
$tenantId = 1; // Default for seeding

echo "Patching CRM Schema...\n";

try {
    // 1. Create Stages Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `erp_crm_stages` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `tenant_id` int(11) NOT NULL,
      `name` varchar(100) NOT NULL,
      `sort_order` int(11) NOT NULL DEFAULT 0,
      `color` varchar(20) DEFAULT '#ccc',
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `tenant_id` (`tenant_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "[OK] erp_crm_stages table checked.\n";

    // 2. Seed Stages (if empty)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM erp_crm_stages WHERE tenant_id = ?");
    $stmt->execute([$tenantId]);
    if ($stmt->fetchColumn() == 0) {
        $stages = [
            ['New Lead', 1, '#3b82f6'],
            ['Contacted', 2, '#8b5cf6'],
            ['Qualified', 3, '#10b981'],
            ['Proposal Sent', 4, '#f59e0b'],
            ['Won', 5, '#15803d'],
            ['Lost', 6, '#ef4444']
        ];
        $ins = $pdo->prepare("INSERT INTO erp_crm_stages (tenant_id, name, sort_order, color) VALUES (?, ?, ?, ?)");
        foreach ($stages as $s) {
            $ins->execute([$tenantId, $s[0], $s[1], $s[2]]);
        }
        echo "[OK] Seeded default stages.\n";
    }

    // 3. Add stage_id to Leads
    try {
        $pdo->exec("ALTER TABLE erp_crm_leads ADD COLUMN stage_id INT(11) DEFAULT NULL");
        echo "[FIXED] Added 'stage_id' to erp_crm_leads.\n";
        
        // Index
        try { $pdo->exec("CREATE INDEX idx_stage_id ON erp_crm_leads(stage_id)"); } catch(Exception $e){}

    } catch (PDOException $e) {
        echo "[NOTE] 'stage_id' column: " . $e->getMessage() . "\n";
    }

    // 4. Migrate Data (Map status -> stage_id)
    // Fetch First Stage ID
    $stmt = $pdo->prepare("SELECT id FROM erp_crm_stages WHERE tenant_id = ? ORDER BY sort_order ASC LIMIT 1");
    $stmt->execute([$tenantId]);
    $firstStage = $stmt->fetchColumn();

    if ($firstStage) {
        $pdo->exec("UPDATE erp_crm_leads SET stage_id = $firstStage WHERE stage_id IS NULL");
        echo "[OK] Migrated leads to default stage ID: $firstStage.\n";
    }

} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage();
}
