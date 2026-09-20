<?php

namespace App\Core\Services;

use App\Core\Database;
use Exception;
use PDO;

class InboxService
{
    public static function createConversation(array $data): int
    {
        $db = Database::getInstance()->getConnection();
        
        $sql = "INSERT INTO inbox_conversations (
            tenant_id, contact_id, contact_name, contact_email, contact_phone, 
            channel, subject, source_type, source_id, assigned_to, metadata, created_at
        ) VALUES (
            :tenant_id, :contact_id, :contact_name, :contact_email, :contact_phone, 
            :channel, :subject, :source_type, :source_id, :assigned_to, :metadata, NOW()
        )";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':tenant_id' => $data['tenant_id'],
            ':contact_id' => $data['contact_id'] ?? null,
            ':contact_name' => $data['contact_name'],
            ':contact_email' => $data['contact_email'] ?? null,
            ':contact_phone' => $data['contact_phone'] ?? null,
            ':channel' => $data['channel'],
            ':subject' => $data['subject'] ?? null,
            ':source_type' => $data['source_type'] ?? null,
            ':source_id' => $data['source_id'] ?? null,
            ':assigned_to' => $data['assigned_to'] ?? null,
            ':metadata' => isset($data['metadata']) ? json_encode($data['metadata']) : null
        ]);
        
        return (int)$db->lastInsertId();
    }

    public static function addMessage(int $conversationId, array $data): int
    {
        $db = Database::getInstance()->getConnection();
        
        try {
            $db->beginTransaction();
            
            $sql = "INSERT INTO inbox_messages (
                conversation_id, tenant_id, direction, sender_type, sender_id, 
                sender_name, content, content_type, channel, metadata, created_at
            ) VALUES (
                :conversation_id, :tenant_id, :direction, :sender_type, :sender_id, 
                :sender_name, :content, :content_type, :channel, :metadata, NOW()
            )";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':conversation_id' => $conversationId,
                ':tenant_id' => $data['tenant_id'],
                ':direction' => $data['direction'],
                ':sender_type' => $data['sender_type'],
                ':sender_id' => $data['sender_id'] ?? null,
                ':sender_name' => $data['sender_name'] ?? null,
                ':content' => $data['content'],
                ':content_type' => $data['content_type'] ?? 'text',
                ':channel' => $data['channel'],
                ':metadata' => isset($data['metadata']) ? json_encode($data['metadata']) : null
            ]);
            
            $messageId = (int)$db->lastInsertId();
            
            $preview = mb_substr(strip_tags($data['content']), 0, 200);
            
            $updateSql = "UPDATE inbox_conversations SET 
                last_message_at = NOW(), 
                last_message_preview = :preview";
                
            $params = [
                ':preview' => $preview,
                ':conversation_id' => $conversationId,
                ':tenant_id' => $data['tenant_id']
            ];
            
            if ($data['direction'] === 'inbound') {
                $updateSql .= ", unread_count = unread_count + 1";
            }
            
            $updateSql .= " WHERE id = :conversation_id AND tenant_id = :tenant_id";
            
            $updateStmt = $db->prepare($updateSql);
            $updateStmt->execute($params);
            
            if ($data['direction'] === 'inbound') {
                try {
                    \App\Core\Services\WorkflowEngineService::dispatch($data['tenant_id'], 'inbox_message_received', [
                        'conversation_id' => $conversationId,
                        'channel' => $data['channel'],
                        'content' => $data['content'],
                        'sender_name' => $data['sender_name'] ?? null
                    ]);
                } catch (\Exception $e) {
                    error_log('Workflow trigger error (inbox_message_received): ' . $e->getMessage());
                }
            }
            
            $db->commit();
            return $messageId;
            
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function getConversations(int $tenantId, array $filters = []): array
    {
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT c.*, l.ai_score 
                FROM inbox_conversations c 
                LEFT JOIN erp_crm_leads l ON c.contact_id = l.id 
                WHERE c.tenant_id = :tenant_id";
        
        $params = [':tenant_id' => $tenantId];
        
        if (isset($filters['status'])) {
            $sql .= " AND c.status = :status";
            $params[':status'] = $filters['status'];
        } else {
            $sql .= " AND c.status = 'open'";
        }
        
        if (isset($filters['channel'])) {
            $sql .= " AND c.channel = :channel";
            $params[':channel'] = $filters['channel'];
        }
        
        if (isset($filters['search']) && trim($filters['search']) !== '') {
            $sql .= " AND (c.contact_name LIKE :search OR c.contact_email LIKE :search OR c.subject LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY c.last_message_at DESC";
        
        $limit = isset($filters['limit']) ? (int)$filters['limit'] : 50;
        $offset = isset($filters['offset']) ? (int)$filters['offset'] : 0;
        
        $sql .= " LIMIT $limit OFFSET $offset";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getMessages(int $conversationId, int $tenantId): array
    {
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT * FROM inbox_messages 
                WHERE conversation_id = :conversation_id AND tenant_id = :tenant_id 
                ORDER BY created_at ASC";
                
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':conversation_id' => $conversationId,
            ':tenant_id' => $tenantId
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function markAsRead(int $conversationId, int $tenantId): void
    {
        $db = Database::getInstance()->getConnection();
        
        try {
            $db->beginTransaction();
            
            $sql = "UPDATE inbox_messages SET is_read = 1 
                    WHERE conversation_id = :conversation_id AND tenant_id = :tenant_id AND is_read = 0";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':conversation_id' => $conversationId,
                ':tenant_id' => $tenantId
            ]);
            
            $sql2 = "UPDATE inbox_conversations SET unread_count = 0 
                     WHERE id = :conversation_id AND tenant_id = :tenant_id";
            $stmt2 = $db->prepare($sql2);
            $stmt2->execute([
                ':conversation_id' => $conversationId,
                ':tenant_id' => $tenantId
            ]);
            
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function findConversationBySource(int $tenantId, string $sourceType, int $sourceId): ?array
    {
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT * FROM inbox_conversations 
                WHERE tenant_id = :tenant_id AND source_type = :source_type AND source_id = :source_id 
                LIMIT 1";
                
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':tenant_id' => $tenantId,
            ':source_type' => $sourceType,
            ':source_id' => $sourceId
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public static function findOrCreateByContact(int $tenantId, string $channel, ?string $email, ?string $phone, string $contactName): int
    {
        $db = Database::getInstance()->getConnection();
        
        $conditions = [];
        $params = [
            ':tenant_id' => $tenantId,
            ':channel' => $channel
        ];
        
        if (!empty($email)) {
            $conditions[] = "contact_email = :email";
            $params[':email'] = $email;
        }
        
        if (!empty($phone)) {
            $conditions[] = "contact_phone = :phone";
            $params[':phone'] = $phone;
        }
        
        if (!empty($conditions)) {
            $sql = "SELECT id FROM inbox_conversations 
                    WHERE tenant_id = :tenant_id AND channel = :channel AND status = 'open' 
                    AND (" . implode(" OR ", $conditions) . ") 
                    LIMIT 1";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return (int)$result['id'];
            }
        }
        
        return self::createConversation([
            'tenant_id' => $tenantId,
            'channel' => $channel,
            'contact_email' => $email,
            'contact_phone' => $phone,
            'contact_name' => $contactName
        ]);
    }

    public static function getUnreadCount(int $tenantId): int
    {
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT SUM(unread_count) as total FROM inbox_conversations 
                WHERE tenant_id = :tenant_id AND status = 'open'";
                
        $stmt = $db->prepare($sql);
        $stmt->execute([':tenant_id' => $tenantId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && $result['total'] ? (int)$result['total'] : 0;
    }

    public static function closeConversation(int $conversationId, int $tenantId): void
    {
        $db = Database::getInstance()->getConnection();
        
        $sql = "UPDATE inbox_conversations SET status = 'closed' 
                WHERE id = :conversation_id AND tenant_id = :tenant_id";
                
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':conversation_id' => $conversationId,
            ':tenant_id' => $tenantId
        ]);
    }
}
