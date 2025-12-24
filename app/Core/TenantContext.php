<?php

namespace App\Core;

class TenantContext
{
    private static $tenantId = null;
    private static $tenantData = null;

    public static function resolve()
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // Remove port if present
        if (strpos($host, ':') !== false) {
            $host = explode(':', $host)[0];
        }

        $db = Database::getInstance();

        // 1. Try custom domain
        $stmt = $db->query("SELECT * FROM tenants WHERE domain = ?", [$host]);
        $tenant = $stmt->fetch();

        // 2. Try subdomain
        if (!$tenant) {
            $parts = explode('.', $host);
            if (count($parts) > 1) {
                // Assuming subdomain.domain.com
                $subdomain = $parts[0];
                $stmt = $db->query("SELECT * FROM tenants WHERE subdomain = ?", [$subdomain]);
                $tenant = $stmt->fetch();
            }
        }

        // 3. Fallback (Optional: Load default or show error)
        if (!$tenant) {
            // For safety, you might want to stop here or load a landing page tenant
            // die("Tenant not found");
            return false;
        }

        self::$tenantId = $tenant['id'];
        self::$tenantData = $tenant;
        return true;
    }

    public static function getTenantId()
    {
        if (self::$tenantId === null) {
            throw new \Exception("Tenant not resolved");
        }
        return self::$tenantId;
    }

    public static function getTenant()
    {
        return self::$tenantData;
    }
}
