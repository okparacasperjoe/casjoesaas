<?php

namespace App\Core;

class ModuleManager
{
    public static function loadModules()
    {
        $modulesDir = __DIR__ . '/../modules';
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        // Get enabled modules for this tenant
        $stmt = $db->query("
            SELECT m.slug 
            FROM tenant_modules tm
            JOIN modules m ON m.id = tm.module_id
            WHERE tm.tenant_id = ? AND tm.status = 'enabled'
        ", [$tenantId]);

        $enabledModules = $stmt->fetchAll(\PDO::FETCH_COLUMN);

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
