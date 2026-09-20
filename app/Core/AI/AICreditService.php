<?php

namespace App\Core\AI;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

/**
 * AICreditService
 *
 * Central service for checking, consuming, and topping-up AI token credits.
 * All AI calls MUST go through `consume()` before hitting the AI provider.
 *
 * DEFENSIVE: Every DB call is wrapped in try/catch so the platform never crashes
 * even if the migration hasn't run yet. Falls back to safe defaults.
 *
 * Pricing model:
 *   - Admin API cost:  $1 per 1,000 tokens
 *   - User charge:     $4 per 1,000 tokens  (4x = 3x profit, 75% margin)
 *   - Credits roll over month-to-month (never expire)
 *   - Monthly allotment resets each billing cycle
 */
class AICreditService
{
    private $pdo;

    // Monthly token limits per plan tier
    const LIMITS = [
        'trial'             => 2000,
        'free'              => 2000,
        'basic'             => 2000,
        'premium'           => 5000,
        'all-access-bundle' => 15000,
        'active'            => 5000,
    ];

    // Credit pack definitions
    const PACKS = [
        'ai_5k'    => ['tokens' => 5000,  'price_ngn' => 5000,   'label' => '₦5,000 AI Credits Top Up'],
        'ai_10k'   => ['tokens' => 10000, 'price_ngn' => 10000,  'label' => '₦10,000 AI Credits Top Up'],
        'starter'  => ['tokens' => 1000,  'price_ngn' => 6400,   'label' => 'Starter Pack — 1,000 tokens'],
        'growth'   => ['tokens' => 5000,  'price_ngn' => 28000,  'label' => 'Growth Pack — 5,000 tokens'],
        'business' => ['tokens' => 15000, 'price_ngn' => 76800,  'label' => 'Business Pack — 15,000 tokens'],
        'power'    => ['tokens' => 50000, 'price_ngn' => 240000, 'label' => 'Power Pack — 50,000 tokens'],
    ];

    /** Safe default returned when DB columns don't exist yet */
    private static function safeDefault(): array
    {
        return [
            'limit'        => 2000,
            'used'         => 0,
            'credits'      => 0,
            'available'    => 2000,
            'percent_used' => 0,
            'plan'         => 'trial',
            'warn'         => false,
            'exhausted'    => false,
            'db_ready'     => false,  // signals migration hasn't run
        ];
    }

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // -------------------------------------------------------
    // PUBLIC: Check available balance
    // -------------------------------------------------------

    public function getBalance(int $tenantId): int
    {
        try {
            $sub = $this->getSubscriptionRow($tenantId);
            if (!$sub || !isset($sub['ai_tokens_limit'])) return 500; // safe default

            $this->maybeResetCycle($sub, $tenantId);

            $monthlyRemaining = max(0, (int)$sub['ai_tokens_limit'] - (int)($sub['ai_tokens_used'] ?? 0));
            $credits          = (int)($sub['ai_credits'] ?? 0);
            return $monthlyRemaining + $credits;
        } catch (\Exception $e) {
            error_log('AICreditService::getBalance error: ' . $e->getMessage());
            return 500;
        }
    }

    public function hasCredits(int $tenantId, int $tokensNeeded = 50): bool
    {
        return $this->getBalance($tenantId) >= $tokensNeeded;
    }

    public function getSummary(int $tenantId): array
    {
        try {
            $sub = $this->getSubscriptionRow($tenantId);

            // Migration hasn't run yet — return safe defaults, don't crash
            if (!$sub || !isset($sub['ai_tokens_limit'])) {
                return self::safeDefault();
            }

            $this->maybeResetCycle($sub, $tenantId);

            // Re-fetch after potential reset
            $sub = $this->getSubscriptionRow($tenantId);

            $limit     = (int)($sub['ai_tokens_limit'] ?? 500);
            $used      = (int)($sub['ai_tokens_used']  ?? 0);
            $credits   = (int)($sub['ai_credits']      ?? 0);
            $available = max(0, $limit - $used) + $credits;
            $pct       = $limit > 0 ? round(($used / $limit) * 100) : 0;

            return [
                'limit'        => $limit,
                'used'         => $used,
                'credits'      => $credits,
                'available'    => $available,
                'percent_used' => $pct,
                'plan'         => $sub['plan'] ?? 'trial',
                'warn'         => ($pct >= 80 && $available < max(500, $limit * 0.2)),
                'exhausted'    => $available <= 0,
                'db_ready'     => true,
            ];
        } catch (\Exception $e) {
            error_log('AICreditService::getSummary error: ' . $e->getMessage());
            return self::safeDefault();
        }
    }

    // -------------------------------------------------------
    // PUBLIC: Consume tokens
    // -------------------------------------------------------

    public function consume(int $tenantId, int $userId, string $action, int $tokens, int $promptLen = 0): bool
    {
        try {
            if (!$this->hasCredits($tenantId, $tokens)) {
                return false;
            }

            $sub = $this->getSubscriptionRow($tenantId);
            if (!$sub || !isset($sub['ai_tokens_limit'])) {
                // Migration not run — allow AI but don't track (fail open)
                return true;
            }

            $monthlyRemaining = max(0, (int)$sub['ai_tokens_limit'] - (int)($sub['ai_tokens_used'] ?? 0));

            if ($monthlyRemaining >= $tokens) {
                $this->pdo->prepare("UPDATE subscriptions SET ai_tokens_used = ai_tokens_used + ? WHERE tenant_id = ?")
                    ->execute([$tokens, $tenantId]);
            } else {
                $fromCredits = $tokens - $monthlyRemaining;
                $this->pdo->prepare("UPDATE subscriptions
                    SET ai_tokens_used = ai_tokens_limit,
                        ai_credits = GREATEST(0, ai_credits - ?)
                    WHERE tenant_id = ?")
                    ->execute([$fromCredits, $tenantId]);
            }

            // Log (non-fatal if table missing)
            try {
                $this->pdo->prepare("INSERT INTO ai_usage_log
                    (tenant_id, user_id, action, tokens_used, prompt_length, created_at)
                    VALUES (?, ?, ?, ?, ?, NOW())")
                    ->execute([$tenantId, $userId, $action, $tokens, $promptLen]);
            } catch (\Exception $e) {
                // ai_usage_log table might not exist yet — ignore
                error_log('AI usage log insert failed: ' . $e->getMessage());
            }

            return true;
        } catch (\Exception $e) {
            error_log('AICreditService::consume error: ' . $e->getMessage());
            return true; // fail open — don't break AI because of credit tracking
        }
    }

    // -------------------------------------------------------
    // PUBLIC: Top-up purchased credits
    // -------------------------------------------------------

    public function topUp(int $tenantId, int $userId, string $packKey, string $reference = '', float $amountPaid = 0, string $currency = 'NGN'): bool
    {
        if (!isset(self::PACKS[$packKey])) return false;

        try {
            $pack   = self::PACKS[$packKey];
            $tokens = $pack['tokens'];

            $this->pdo->prepare("UPDATE subscriptions SET ai_credits = ai_credits + ? WHERE tenant_id = ?")
                ->execute([$tokens, $tenantId]);

            $this->pdo->prepare("INSERT INTO ai_credit_purchases
                (tenant_id, user_id, pack_name, tokens_granted, amount_paid, currency, reference, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())")
                ->execute([$tenantId, $userId, $pack['label'], $tokens, $amountPaid, $currency, $reference]);

            return true;
        } catch (\Exception $e) {
            error_log('AICreditService::topUp error: ' . $e->getMessage());
            return false;
        }
    }

    public function grantPlanAllotment(int $tenantId, string $plan): void
    {
        try {
            $limit = self::LIMITS[$plan] ?? self::LIMITS['active'];
            $this->pdo->prepare("UPDATE subscriptions
                SET ai_tokens_limit = ?, ai_tokens_used = 0, ai_cycle_reset_at = NOW()
                WHERE tenant_id = ?")
                ->execute([$limit, $tenantId]);
        } catch (\Exception $e) {
            error_log('AICreditService::grantPlanAllotment error: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------
    // PRIVATE Helpers
    // -------------------------------------------------------

    private function getSubscriptionRow(int $tenantId): ?array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM subscriptions WHERE tenant_id = ? LIMIT 1");
            $stmt->execute([$tenantId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function maybeResetCycle(array $sub, int $tenantId): void
    {
        try {
            // Use null coalescing — column may not exist if migration pending
            $resetAt     = $sub['ai_cycle_reset_at'] ?? null;
            $shouldReset = false;

            if (empty($resetAt)) {
                $shouldReset = true;
            } else {
                $daysSinceReset = (time() - strtotime($resetAt)) / 86400;
                if ($daysSinceReset >= 30) {
                    $shouldReset = true;
                }
            }

            if ($shouldReset) {
                $this->pdo->prepare("UPDATE subscriptions
                    SET ai_tokens_used = 0, ai_cycle_reset_at = NOW()
                    WHERE tenant_id = ?")
                    ->execute([$tenantId]);
            }
        } catch (\Exception $e) {
            // Column doesn't exist yet — migration pending, ignore silently
            error_log('AICreditService::maybeResetCycle error (migration pending?): ' . $e->getMessage());
        }
    }
}
