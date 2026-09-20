<?php

namespace App\Core\Services;

use App\Core\Database;
use PDO;

/**
 * Permission Service
 * 
 * Handles dynamic permission checks for moderators
 * Admins always have full access
 */
class PermissionService
{
    private static $permissions = null;
    
    /**
     * Load permissions from database (cached for performance)
     */
    private static function loadPermissions()
    {
        if (self::$permissions !== null) {
            return;
        }
        
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();
            
            $stmt = $pdo->query("SELECT setting_key, setting_value 
                                FROM system_settings 
                                WHERE setting_key LIKE 'moderator_can_%'");
            
            self::$permissions = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $permission = str_replace('moderator_can_', '', $row['setting_key']);
                self::$permissions[$permission] = (bool)$row['setting_value'];
            }
        } catch (\Exception $e) {
            // If settings table doesn't exist or error, use safe defaults
            self::$permissions = [
                'manage_users' => true,
                'manage_kyc' => true,
                'view_soc' => true,
                'train_bot' => true,
                'manage_stores' => true,
            ];
        }
    }
    
    /**
     * Check if moderator has specific permission
     * 
     * @param string $permission Permission name (e.g., 'manage_users', 'view_deposits')
     * @return bool
     */
    public static function moderatorCan($permission)
    {
        self::loadPermissions();
        return self::$permissions[$permission] ?? false;
    }
    
    /**
     * Check if current user has permission
     * Admins always have all permissions
     * Moderators check against settings
     * 
     * @param string $permission Permission name
     * @return bool
     */
    public static function can($permission)
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        $user = \App\Core\Auth::user();
        
        if (!$user) {
            return false;
        }
        
        // Admins can do everything
        if (in_array($user['role'], ['admin', 'super_admin'])) {
            return true;
        }
        
        // Moderators check permission settings
        if ($user['role'] === 'moderator') {
            return self::moderatorCan($permission);
        }
        
        return false;
    }
    
    /**
     * Reset cached permissions (call after updating settings)
     */
    public static function clearCache()
    {
        self::$permissions = null;
    }
    
    /**
     * Get all moderator permissions with current values
     * 
     * @return array
     */
    public static function getAllPermissions()
    {
        self::loadPermissions();
        return self::$permissions;
    }
}
