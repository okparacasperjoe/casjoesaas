<?php

namespace App\Modules\CasjoeLinks\Services;

use App\Core\Database;
use App\Core\Services\AIService;
use App\Core\Services\WorkflowEngineService;
use Exception;
use PDO;

class SocialPlannerService
{
    public static function createPost(int $tenantId, int $userId, array $data): int
    {
        $db = Database::getInstance()->getConnection();
        
        $content = $data['content'] ?? '';
        $platforms = isset($data['platforms']) ? json_encode($data['platforms']) : json_encode([]);
        $mediaUrl = $data['media_url'] ?? null;
        $linkType = $data['link_type'] ?? 'none';
        $linkUrl = $data['link_url'] ?? null;
        $status = $data['status'] ?? 'draft';
        $scheduledAt = $data['scheduled_at'] ?? null;
        $aiGenerated = $data['ai_generated'] ?? 0;
        
        if (!in_array($status, ['draft', 'scheduled'])) {
            $status = 'draft';
        }

        $stmt = $db->prepare("
            INSERT INTO social_posts (
                tenant_id, user_id, content, media_url, platforms, 
                link_type, link_url, status, scheduled_at, ai_generated
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $tenantId,
            $userId,
            $content,
            $mediaUrl,
            $platforms,
            $linkType,
            $linkUrl,
            $status,
            $scheduledAt,
            $aiGenerated
        ]);
        
        return (int) $db->lastInsertId();
    }

    public static function getPosts(int $tenantId, $filters = [], int $offset = 0): array
    {
        if (is_numeric($filters)) {
            $filters = ['limit' => (int)$filters, 'offset' => $offset];
        } elseif (!is_array($filters)) {
            $filters = [];
        }

        $db = Database::getInstance()->getConnection();
        
        $query = "SELECT * FROM social_posts WHERE tenant_id = ?";
        $params = [$tenantId];
        
        if (isset($filters['status'])) {
            $query .= " AND status = ?";
            $params[] = $filters['status'];
        }
        
        $query .= " ORDER BY created_at DESC";
        
        if (isset($filters['limit'])) {
            $query .= " LIMIT " . (int) $filters['limit'];
            if (isset($filters['offset'])) {
                $query .= " OFFSET " . (int) $filters['offset'];
            }
        }
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($posts as &$post) {
            $post['platforms'] = json_decode($post['platforms'], true);
            $post['metrics'] = $post['metrics'] ? json_decode($post['metrics'], true) : null;
        }
        
        return $posts;
    }

    public static function getCalendarPosts(int $tenantId, string $yearMonth): array
    {
        $db = Database::getInstance()->getConnection();
        
        $likePattern = $yearMonth . '%';
        $stmt = $db->prepare("
            SELECT * FROM social_posts 
            WHERE tenant_id = ? 
            AND (scheduled_at LIKE ? OR created_at LIKE ?)
        ");
        
        $stmt->execute([$tenantId, $likePattern, $likePattern]);
        
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($posts as &$post) {
            $post['platforms'] = json_decode($post['platforms'], true);
            $post['metrics'] = $post['metrics'] ? json_decode($post['metrics'], true) : null;
        }
        
        return $posts;
    }

    public static function generateAICaption(string $topic, string $platform = 'all', ?string $linkUrl = null): array
    {
        try {
            $ai = new AIService();
            $cleanTopic = trim($topic ?: 'our latest product update');
            $prompt = "You are an expert social media copywriter. Write 3 high-converting post variations for {$platform} about '{$cleanTopic}' with relevant hashtags and a call-to-action to visit '{$linkUrl}'. Return ONLY a valid JSON array matching this exact schema without any markdown wrapping: [{\"variant\": 1, \"text\": \"Post copy here...\", \"hashtags\": [\"#tag1\", \"#tag2\"]}, {\"variant\": 2, \"text\": \"...\", \"hashtags\": [\"#...\"]}, {\"variant\": 3, \"text\": \"...\", \"hashtags\": [\"#...\"]}]";
            
            $response = $ai->generateText($prompt);
            
            // Try to extract JSON if there's surrounding markdown or text
            if (preg_match('/\[\s*\{.*\}\s*\]/s', $response, $matches)) {
                $jsonString = $matches[0];
            } else {
                $jsonString = $response;
            }
            
            $decoded = json_decode($jsonString, true);
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
            
            if (!empty($response)) {
                return [
                    ['variant' => 1, 'text' => $response, 'hashtags' => ['#growth', '#trending']]
                ];
            }
            throw new Exception("Empty response from AI");
        } catch (\Throwable $e) {
            $targetTopic = $topic ? htmlspecialchars($topic) : 'our brand new growth solution';
            $targetLink = $linkUrl ? " Check it out here: " . htmlspecialchars($linkUrl) : "";
            return [
                [
                    'variant' => 1,
                    'text' => "🚀 Ready to accelerate your business? We're thrilled to introduce {$targetTopic}! See how it can help you scale today.{$targetLink}",
                    'hashtags' => ['#growth', '#innovation', '#business']
                ],
                [
                    'variant' => 2,
                    'text' => "⚡ Looking for faster results? Don't miss our latest update on {$targetTopic}. Everything you need to get ahead.{$targetLink}",
                    'hashtags' => ['#success', '#entrepreneur', '#productivity']
                ],
                [
                    'variant' => 3,
                    'text' => "🔥 Stop doing things the hard way! Take advantage of {$targetTopic} and start converting more audience today:{$targetLink}",
                    'hashtags' => ['#marketing', '#salesfunnel', '#digital']
                ]
            ];
        }
    }

    public static function publishScheduledPosts(): int
    {
        $db = Database::getInstance()->getConnection();
        
        $now = date('Y-m-d H:i:s');
        $stmt = $db->prepare("
            SELECT id, tenant_id FROM social_posts 
            WHERE status = 'scheduled' AND (scheduled_at <= NOW() OR scheduled_at <= ?)
        ");
        $stmt->execute([$now]);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $count = 0;
        foreach ($posts as $post) {
            $updateStmt = $db->prepare("
                UPDATE social_posts 
                SET status = 'published', published_at = NOW() 
                WHERE id = ?
            ");
            if ($updateStmt->execute([$post['id']])) {
                $count++;
                try {
                    // Trigger workflow or log activity
                    if (class_exists(WorkflowEngineService::class)) {
                        WorkflowEngineService::dispatch($post['tenant_id'], 'social_post_published', ['post_id' => $post['id']]);
                    }
                } catch (Exception $e) {
                    // Log error but continue
                    error_log("Failed to dispatch workflow for social post: " . $e->getMessage());
                }
            }
        }
        
        return $count;
    }

    public static function getConnectedAccounts(int $tenantId): array
    {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT * FROM social_accounts WHERE tenant_id = ? AND status = 'active'");
        $stmt->execute([$tenantId]);
        $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($accounts)) {
            // Provide mock accounts
            return [
                [
                    'id' => 9991,
                    'tenant_id' => $tenantId,
                    'platform' => 'facebook',
                    'account_name' => 'Demo Facebook Page',
                    'account_handle' => '@demo_page',
                    'avatar_url' => 'https://ui-avatars.com/api/?name=FB&background=1877F2&color=fff',
                    'status' => 'active'
                ],
                [
                    'id' => 9992,
                    'tenant_id' => $tenantId,
                    'platform' => 'instagram',
                    'account_name' => 'Demo Instagram',
                    'account_handle' => '@demo_insta',
                    'avatar_url' => 'https://ui-avatars.com/api/?name=IG&background=E1306C&color=fff',
                    'status' => 'active'
                ]
            ];
        }
        
        return $accounts;
    }
}
