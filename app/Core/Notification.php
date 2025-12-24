<?php

namespace App\Core;

class Notification
{
    public static function send($userId, $title, $message, $link = '#')
    {
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        $stmt = $db->prepare("INSERT INTO notifications (tenant_id, user_id, title, message, link) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$tenantId, $userId, $title, $message, $link]);
    }

    public static function getUnread($userId)
    {
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        $stmt = $db->prepare("SELECT * FROM notifications WHERE tenant_id = ? AND user_id = ? AND is_read = 0 ORDER BY created_at DESC");
        $stmt->execute([$tenantId, $userId]);
        return $stmt->fetchAll();
    }

    public static function markAsRead($notificationId)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        $stmt->execute([$notificationId]);
    }
}
