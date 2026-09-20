<?php

namespace App\Modules\CasjoeAcademy\Services;

use App\Core\Database;

class GamificationService
{
    /**
     * Award points to a user.
     */
    public static function awardPoints($userId, $tenantId, $amount)
    {
        $db = Database::getInstance();
        
        // Ensure row exists
        $stmt = $db->prepare("INSERT IGNORE INTO academy_gamification_stats (tenant_id, user_id, total_points) VALUES (?, ?, 0)");
        $stmt->execute([$tenantId, $userId]);
        
        // Add points
        $stmt = $db->prepare("UPDATE academy_gamification_stats SET total_points = total_points + ? WHERE tenant_id = ? AND user_id = ?");
        $stmt->execute([$amount, $tenantId, $userId]);
    }

    /**
     * Get a user's total points and rank.
     */
    public static function getUserStats($userId, $tenantId)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT total_points FROM academy_gamification_stats WHERE tenant_id = ? AND user_id = ?");
        $stmt->execute([$tenantId, $userId]);
        $points = $stmt->fetchColumn();
        
        if ($points === false) {
            $points = 0;
        }

        return [
            'points' => (int)$points,
            'rank'   => self::determineRank($points),
            'next_rank_progress' => self::calculateProgressToNextRank($points)
        ];
    }

    /**
     * Determine user rank based on points.
     */
    private static function determineRank($points)
    {
        if ($points >= 1000) return 'Grandmaster';
        if ($points >= 500)  return 'Master';
        if ($points >= 250)  return 'Scholar';
        if ($points >= 100)  return 'Apprentice';
        return 'Novice';
    }

    /**
     * Calculate percentage progress to the next rank.
     */
    private static function calculateProgressToNextRank($points)
    {
        $tiers = [
            0 => 100,
            100 => 250,
            250 => 500,
            500 => 1000,
            1000 => 999999
        ];

        $currentTierStart = 0;
        $nextTierTarget = 100;

        foreach ($tiers as $start => $target) {
            if ($points >= $start && $points < $target) {
                $currentTierStart = $start;
                $nextTierTarget = $target;
                break;
            }
        }

        if ($nextTierTarget == 999999) return 100; // Max rank

        $pointsInCurrentTier = $points - $currentTierStart;
        $tierSize = $nextTierTarget - $currentTierStart;
        
        return min(100, max(0, round(($pointsInCurrentTier / $tierSize) * 100)));
    }

    /**
     * Check and award predefined badges.
     */
    public static function checkAndAwardBadges($userId, $tenantId)
    {
        $db = Database::getInstance();
        $badgesAwarded = [];

        // Badge 1: First Lesson Completed
        $stmt = $db->prepare("SELECT COUNT(*) FROM academy_lesson_completions WHERE user_id = ?");
        $stmt->execute([$userId]);
        $lessonsCompleted = $stmt->fetchColumn();

        if ($lessonsCompleted >= 1) {
            if (self::awardBadge($userId, $tenantId, 'first_lesson')) {
                $badgesAwarded[] = 'first_lesson';
            }
        }

        // Badge 2: Quick Learner (5 lessons)
        if ($lessonsCompleted >= 5) {
            if (self::awardBadge($userId, $tenantId, 'quick_learner')) {
                $badgesAwarded[] = 'quick_learner';
            }
        }

        // Badge 3: Course Master (completed at least 1 course)
        $stmt = $db->prepare("SELECT COUNT(*) FROM academy_enrollments WHERE user_id = ? AND progress_percent = 100");
        $stmt->execute([$userId]);
        $coursesCompleted = $stmt->fetchColumn();

        if ($coursesCompleted >= 1) {
            if (self::awardBadge($userId, $tenantId, 'course_master')) {
                $badgesAwarded[] = 'course_master';
            }
        }

        return $badgesAwarded;
    }

    /**
     * Award a specific badge if not already earned.
     */
    private static function awardBadge($userId, $tenantId, $badgeId)
    {
        $db = Database::getInstance();
        try {
            $stmt = $db->prepare("INSERT INTO academy_user_badges (tenant_id, user_id, badge_id) VALUES (?, ?, ?)");
            $stmt->execute([$tenantId, $userId, $badgeId]);
            return true;
        } catch (\PDOException $e) {
            // Duplicate key means already earned
            return false;
        }
    }

    /**
     * Get user's earned badges.
     */
    public static function getUserBadges($userId, $tenantId)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT badge_id, earned_at FROM academy_user_badges WHERE tenant_id = ? AND user_id = ? ORDER BY earned_at DESC");
        $stmt->execute([$tenantId, $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
