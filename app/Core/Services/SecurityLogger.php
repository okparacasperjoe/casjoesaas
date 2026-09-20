<?php

namespace App\Core\Services;

use App\Core\Database;
use App\Core\Auth;
use App\Core\TenantContext;

class SecurityLogger
{
    public static function log($severity, $eventType, $message, $userId = null, $tenantId = null)
    {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Auto-detect context if not provided
            if ($userId === null && isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
            }
            if ($tenantId === null) {
                $tenantId = TenantContext::getTenantId();
            }

            $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

            try {
                $stmt = $db->prepare("INSERT INTO system_logs (tenant_id, user_id, severity, event_type, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$tenantId, $userId, $severity, $eventType, $message, $ip, substr($ua, 0, 250)]);
            } catch (\Exception $e1) {
                // Ignore if system_logs table has issues
            }

            try {
                // Also log to security_logs for the SOC dashboard
                $userEmail = null;
                if ($userId !== null) {
                    $stmtUser = $db->prepare("SELECT email FROM users WHERE id = ?");
                    $stmtUser->execute([$userId]);
                    $userEmail = $stmtUser->fetchColumn() ?: null;
                } elseif (isset($_SESSION['user_email'])) {
                    $userEmail = $_SESSION['user_email'];
                } elseif (preg_match('/(?:for|attempt|login|user):\s*([^\s,]+@[^\s,]+)/i', $message, $m)) {
                    $userEmail = $m[1];
                }
                $stmtSec = $db->prepare("INSERT INTO security_logs (tenant_id, user_id, user_email, event_type, severity, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmtSec->execute([$tenantId, $userId, $userEmail, $eventType, $severity, $message, $ip, substr($ua, 0, 250)]);
            } catch (\Exception $e2) {
                // Ignore if security_logs table is not ready
            }
            
        } catch (\Exception $e) {
            // Failsafe: Don't break app if logging fails
            error_log("SecurityLogger Error: " . $e->getMessage());
        }
    }

    public static function info($eventType, $message, $userId = null)
    {
        self::log('info', $eventType, $message, $userId);
    }

    public static function warning($eventType, $message, $userId = null)
    {
        self::log('warning', $eventType, $message, $userId);
    }

    public static function danger($eventType, $message, $userId = null)
    {
        self::log('danger', $eventType, $message, $userId);
    }
}
