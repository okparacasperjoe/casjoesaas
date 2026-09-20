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

        $normalizedHost = strtolower(trim($host));

        $db = Database::getInstance();

        // System domains that belong exclusively to the main platform
        $systemHosts = ['app.casjoe.com', 'casjoe.com', 'www.casjoe.com', 'localhost', '127.0.0.1'];
        if (in_array($normalizedHost, $systemHosts)) {
            // 1. If logged in, resolve to user's active tenant
            if (isset($_SESSION['tenant_id']) && !empty($_SESSION['tenant_id'])) {
                try {
                    $stmt = $db->query("SELECT * FROM tenants WHERE id = ?", [$_SESSION['tenant_id']]);
                    if ($t = $stmt->fetch()) {
                        self::$tenantId = $t['id'];
                        self::$tenantData = $t;
                        return true;
                    }
                } catch (\Exception $e) {}
            }

            // 2. Default platform tenant (Casjoe LLC id=3, or first available)
            try {
                $stmt = $db->query("SELECT * FROM tenants WHERE id = 3");
                if ($t = $stmt->fetch()) {
                    self::$tenantId = $t['id'];
                    self::$tenantData = $t;
                    return true;
                }
                $stmt = $db->query("SELECT * FROM tenants ORDER BY id ASC LIMIT 1");
                if ($t = $stmt->fetch()) {
                    self::$tenantId = $t['id'];
                    self::$tenantData = $t;
                    return true;
                }
            } catch (\Exception $e) {}
            
            return false;
        }

        $reservedSubdomains = ['app', 'www', 'mail', 'api', 'admin', 'casjoe', 'test', 'demo', 'portal'];

        // 1. Try custom domain
        $stmt = $db->query("SELECT * FROM tenants WHERE domain = ?", [$host]);
        $tenant = $stmt->fetch();

        // 2. Try subdomain
        if (!$tenant) {
            $parts = explode('.', $normalizedHost);
            if (count($parts) > 1) {
                // Assuming subdomain.domain.com
                $subdomain = $parts[0];
                if (!in_array($subdomain, $reservedSubdomains)) {
                    $stmt = $db->query("SELECT * FROM tenants WHERE subdomain = ?", [$subdomain]);
                    $tenant = $stmt->fetch();
                }
            }
        }

        // 3. Fallback: If still not found, check if authenticated session tenant exists
        if (!$tenant && isset($_SESSION['tenant_id']) && !empty($_SESSION['tenant_id'])) {
            try {
                $stmt = $db->query("SELECT * FROM tenants WHERE id = ?", [$_SESSION['tenant_id']]);
                $tenant = $stmt->fetch();
            } catch (\Exception $e) {}
        }

        // 4. Default to platform tenant (id=3)
        if (!$tenant) {
            try {
                $stmt = $db->query("SELECT * FROM tenants WHERE id = 3");
                $tenant = $stmt->fetch();
            } catch (\Exception $e) {}
        }

        if (!$tenant) {
            self::$tenantId = null;
            self::$tenantData = null;
            return false;
        }

        self::$tenantId = $tenant['id'];
        self::$tenantData = $tenant;
        return true;
    }

    public static function getTenantId()
    {
        // 1. Prioritize authenticated session tenant_id
        if (isset($_SESSION['tenant_id']) && !empty($_SESSION['tenant_id'])) {
            return $_SESSION['tenant_id'];
        }

        if (self::$tenantId === null) {
            throw new \Exception("Tenant not resolved");
        }
        return self::$tenantId;
    }

    public static function getTenant()
    {
        if (isset($_SESSION['tenant_id']) && !empty($_SESSION['tenant_id'])) {
            if (self::$tenantData && (int)self::$tenantData['id'] === (int)$_SESSION['tenant_id']) {
                return self::$tenantData;
            }
            try {
                $db = Database::getInstance();
                $stmt = $db->query("SELECT * FROM tenants WHERE id = ?", [$_SESSION['tenant_id']]);
                $t = $stmt->fetch();
                if ($t) {
                    return $t;
                }
            } catch (\Exception $e) {}
        }
        return self::$tenantData;
    }

    public static function isLocal()
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return in_array(str_replace('www.', '', $host), ['localhost', '127.0.0.1']) || strpos($host, '.test') !== false || strpos($host, '.local') !== false;
    }

    public static function isSystemHost()
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        if (strpos($host, ':') !== false) {
            $host = explode(':', $host)[0];
        }
        $normalizedHost = strtolower(trim($host));
        $systemHosts = ['app.casjoe.com', 'casjoe.com', 'www.casjoe.com', 'localhost', '127.0.0.1'];
        return in_array($normalizedHost, $systemHosts);
    }

    public static function getSetting($key, $default = '')
    {
        try {
            $tenantId = self::getTenantId();
            $db = Database::getInstance();
            $stmt = $db->query("SELECT setting_value FROM erp_settings WHERE tenant_id = ? AND setting_key = ?", [$tenantId, $key]);
            $row = $stmt->fetch();
            return $row ? $row['setting_value'] : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
}
