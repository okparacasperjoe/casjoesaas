<?php

namespace App\Core;

class Notification
{
    public static function send($userId, $title, $message, $link = '#')
    {
        try {
            $db = Database::getInstance();
            $tenantId = TenantContext::getTenantId();

            $stmt = $db->prepare("INSERT INTO notifications (tenant_id, user_id, title, message, link) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$tenantId, $userId, $title, $message, $link]);
        } catch (\Exception $e) {
            error_log('Notification::send failed: ' . $e->getMessage());
        }
    }

    public static function getUnread($userId)
    {
        try {
            $db = Database::getInstance();
            $tenantId = TenantContext::getTenantId();

            $stmt = $db->prepare("SELECT * FROM notifications WHERE tenant_id = ? AND user_id = ? AND is_read = 0 ORDER BY created_at DESC");
            $stmt->execute([$tenantId, $userId]);
            return $stmt->fetchAll() ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public static function getAll($userId)
    {
        try {
            $db = Database::getInstance();
            $tenantId = TenantContext::getTenantId();

            $stmt = $db->prepare("SELECT * FROM notifications WHERE tenant_id = ? AND user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$tenantId, $userId]);
            return $stmt->fetchAll() ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public static function markAsRead($notificationId)
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
            $stmt->execute([$notificationId]);
        } catch (\Exception $e) {
            error_log('Notification::markAsRead failed: ' . $e->getMessage());
        }
    }
}
