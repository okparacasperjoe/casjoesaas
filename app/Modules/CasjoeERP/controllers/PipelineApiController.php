<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class PipelineApiController
{
    /**
     * Get stages for a specific pipeline
     */
    public function getStages($params)
    {
        header('Content-Type: application/json');
        
        $pipelineId = is_array($params) ? $params['id'] : $params;
        $tenantId = TenantContext::getTenantId();
        
        $db = Database::getInstance();
        $stmt = $db->query(
            "SELECT id, name, sort_order as position FROM erp_crm_stages 
             WHERE pipeline_id = ? 
             ORDER BY sort_order ASC",
            [$pipelineId]
        );
        
        $stages = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode($stages);
        exit;
    }
}
