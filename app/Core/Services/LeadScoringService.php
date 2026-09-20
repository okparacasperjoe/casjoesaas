<?php
namespace App\Core\Services;

use App\Core\Database;
use PDO;

class LeadScoringService
{
    /**
     * Calculates a lead's score based on activity signals
     */
    public static function recalculateScore(int $tenantId, int $leadId): int
    {
        $db = Database::getInstance()->getConnection();
        
        // 1. Get sum of points from log
        $stmt = $db->prepare("SELECT SUM(points) as total_points FROM lead_score_log WHERE tenant_id = ? AND lead_id = ?");
        $stmt->execute([$tenantId, $leadId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $score = (int)($row['total_points'] ?? 0);
        
        // 2. Fetch lead to check profile completeness and decay
        $stmtLead = $db->prepare("SELECT email, phone, company, lead_score_updated_at, created_at FROM erp_crm_leads WHERE tenant_id = ? AND id = ?");
        $stmtLead->execute([$tenantId, $leadId]);
        $lead = $stmtLead->fetch(PDO::FETCH_ASSOC);
        
        if ($lead) {
            // Profile Completeness
            if (!empty($lead['email'])) {
                $score += 5;
            }
            if (!empty($lead['phone'])) {
                $score += 5;
            }
            if (!empty($lead['company'])) {
                $score += 3;
            }
            
            // Time Decay (Inactive Leads)
            $lastActivity = $lead['lead_score_updated_at'] ?? $lead['created_at'];
            if ($lastActivity) {
                $daysSince = (time() - strtotime($lastActivity)) / (60 * 60 * 24);
                if ($daysSince > 60) {
                    $score -= 30;
                } elseif ($daysSince > 30) {
                    $score -= 15;
                }
            }
        }
        
        // 3. Cap score between 0 and 100
        if ($score > 100) {
            $score = 100;
        }
        if ($score < 0) {
            $score = 0;
        }
        
        // 4. Update the lead's ai_score / lead_score
        try {
            $db->prepare("UPDATE erp_crm_leads SET lead_score = ?, ai_score = ? WHERE tenant_id = ? AND id = ?")
               ->execute([$score, $score, $tenantId, $leadId]);
        } catch (\Exception $e) {
            try {
                $db->prepare("UPDATE erp_crm_leads SET lead_score = ? WHERE tenant_id = ? AND id = ?")
                   ->execute([$score, $tenantId, $leadId]);
            } catch (\Exception $e2) {
                try {
                    $db->prepare("UPDATE erp_crm_leads SET ai_score = ? WHERE tenant_id = ? AND id = ?")
                       ->execute([$score, $tenantId, $leadId]);
                } catch (\Exception $e3) {}
            }
        }
           
        try {
            \App\Core\Services\WorkflowEngineService::dispatch($tenantId, 'score_threshold', [
                'lead_id' => $leadId,
                'score' => $score,
                'email' => $lead['email'] ?? null,
                'phone' => $lead['phone'] ?? null,
                'name' => $lead['name'] ?? 'Lead'
            ]);
        } catch (\Exception $e) {
            error_log('Workflow trigger error (score_threshold): ' . $e->getMessage());
        }
           
        return $score;
    }

    /**
     * Adds points to a lead's score and logs the activity
     */
    public static function addPoints(int $tenantId, int $leadId, string $reason, int $points): void
    {
        $db = Database::getInstance()->getConnection();
        
        // Log the event
        $stmt = $db->prepare("
            INSERT INTO lead_score_log (tenant_id, lead_id, reason, points, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$tenantId, $leadId, $reason, $points]);
        
        // Update the last activity timestamp on the lead to prevent decay if points > 0 (positive interaction)
        if ($points > 0) {
            $db->prepare("UPDATE erp_crm_leads SET lead_score_updated_at = NOW() WHERE tenant_id = ? AND id = ?")
               ->execute([$tenantId, $leadId]);
        }
        
        // Recalculate and update the AI score
        self::recalculateScore($tenantId, $leadId);
    }

    /**
     * Returns the scoring history/breakdown
     */
    public static function getScoreBreakdown(int $tenantId, int $leadId): array
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT * FROM lead_score_log 
            WHERE tenant_id = ? AND lead_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$tenantId, $leadId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns a human-readable label and color based on score
     */
    public static function getScoreLabel(int $score): array
    {
        if ($score <= 20) {
            return ['label' => 'Cold', 'color' => '#94a3b8'];
        } elseif ($score <= 40) {
            return ['label' => 'Warm', 'color' => '#f59e0b'];
        } elseif ($score <= 60) {
            return ['label' => 'Hot', 'color' => '#f97316'];
        } elseif ($score <= 80) {
            return ['label' => 'Very Hot', 'color' => '#ef4444'];
        } else {
            return ['label' => 'On Fire', 'color' => '#8b5cf6'];
        }
    }

    /**
     * Returns an HTML badge representation of the score
     */
    public static function getBadge(int $score): string
    {
        $info = self::getScoreLabel($score);
        return '<span class="lead-score-badge" style="background-color: ' . $info['color'] . '; color: white; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">' . $info['label'] . ' (' . $score . ')</span>';
    }
}
