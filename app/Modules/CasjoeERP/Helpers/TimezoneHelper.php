<?php

namespace App\Modules\CasjoeERP\Helpers;

use PDO;

class TimezoneHelper
{
    public static function applyTenantTimezone($pdo, $tenantId)
    {
        if (!$pdo || !$tenantId) {
            return;
        }

        try {
            $stmt = $pdo->prepare("SELECT setting_value FROM erp_settings WHERE tenant_id = ? AND setting_key = 'timezone'");
            $stmt->execute([$tenantId]);
            $timezone = $stmt->fetchColumn();

            if ($timezone) {
                date_default_timezone_set($timezone);
            }
        } catch (\Exception $e) {
            // Fallback or log if needed, but fail silently to avoid crashing app for timezone
            error_log("Failed to set timezone for tenant $tenantId: " . $e->getMessage());
        }
    }
}
