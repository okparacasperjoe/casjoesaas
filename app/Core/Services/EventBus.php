<?php

namespace App\Core\Services;

use App\Core\Database;
use Exception;
use PDO;

class EventBus
{
    /**
     * Publishes an event to the unified log.
     */
    public static function publish(int $tenantId, string $module, string $eventType, array $payload): bool
    {
        $pdo = Database::getInstance()->getConnection();
        
        $stmt = $pdo->prepare("
            INSERT INTO system_events (tenant_id, module, event_type, payload)
            VALUES (?, ?, ?, ?)
        ");

        try {
            return $stmt->execute([
                $tenantId,
                $module,
                $eventType,
                json_encode($payload)
            ]);
        } catch (Exception $e) {
            error_log("Failed to publish event $eventType: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves recent events for a tenant (used for AI RAG context).
     */
    public static function getRecentEvents(int $tenantId, int $limit = 50): array
    {
        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("
                SELECT module, event_type, payload, created_at 
                FROM system_events 
                WHERE tenant_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?
            ");
            
            $stmt->bindValue(1, $tenantId, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }
}
