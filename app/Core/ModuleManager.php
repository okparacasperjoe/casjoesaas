<?php

namespace App\Core;

class ModuleManager
{
    public static function loadModules()
    {
        $modulesDir = __DIR__ . '/../Modules';
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        // Get subscription status
        $stmtSub = $db->query("SELECT plan, status, trial_ends_at FROM subscriptions WHERE tenant_id = ? LIMIT 1", [$tenantId]);
        $sub = $stmtSub->fetch(\PDO::FETCH_ASSOC);

        $isTrialOrAllAccess = false;
        if ($sub) {
            if ($sub['status'] === 'trial' && (!empty($sub['trial_ends_at']) && strtotime($sub['trial_ends_at']) > time())) {
                $isTrialOrAllAccess = true;
            } elseif ($sub['status'] === 'active' && $sub['plan'] === 'all-access-bundle') {
                $isTrialOrAllAccess = true;
            }
        } else {
            // If no subscription record exists yet, it's a new signup, treat as free trial!
            $isTrialOrAllAccess = true;
        }

        if ($isTrialOrAllAccess) {
            // Load all modules for free trial/all-access bundle
            $stmt = $db->query("SELECT slug FROM modules");
            $enabledModules = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } else {
            // Get enabled modules for this tenant
            $stmt = $db->query("
                SELECT m.slug 
                FROM tenant_modules tm
                JOIN modules m ON m.id = tm.module_id
                WHERE tm.tenant_id = ? AND tm.status = 'enabled'
            ", [$tenantId]);
            $enabledModules = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        }

        // Scan directory
        $dirs = glob($modulesDir . '/*', GLOB_ONLYDIR);

        foreach ($dirs as $dir) {
            $moduleJsonPath = $dir . '/module.json';
            if (file_exists($moduleJsonPath)) {
                $config = json_decode(file_get_contents($moduleJsonPath), true);
                $slug = $config['slug'];

                // Check if enabled
                if (in_array($slug, $enabledModules)) {
                    // Load routes
                    $routesPath = $dir . '/routes.php';
                    if (file_exists($routesPath)) {
                        require_once $routesPath;
                    }
                }
            }
        }
    }

    // Helper to register a module globally (admin function usually)
    public static function registerModule($name, $slug)
    {
        $db = Database::getInstance();
        try {
            $db->query("INSERT INTO modules (name, slug) VALUES (?, ?)", [$name, $slug]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
