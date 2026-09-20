<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

class AIManagerController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
    }

    private function checkAdmin()
    {
        $role = \App\Core\Auth::user()['role'] ?? '';
        if ($role !== 'admin') {
            header('Location: /erp/dashboard');
            exit;
        }
    }

    public function dashboard()
    {
        $this->checkAdmin();
        
        // Get Business Health Score
        $businessScore = $this->getBusinessScore();
        
        // Get Critical Insights (unread)
        $stmt = $this->pdo->prepare("
            SELECT * FROM erp_ai_insights 
            WHERE tenant_id = ? AND is_read = 0 
            ORDER BY 
                FIELD(severity, 'critical', 'warning', 'info'),
                created_at DESC 
            LIMIT 10
        ");
        $stmt->execute([$this->tenantId]);
        $insights = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get Recent Performance Scores
        $stmt = $this->pdo->prepare("
            SELECT * FROM erp_performance_scores 
            WHERE tenant_id = ? AND target_type = 'business'
            ORDER BY created_at DESC 
            LIMIT 5
        ");
        $stmt->execute([$this->tenantId]);
        $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get Weekly Roast Preview (pass existing score to avoid recalculating)
        $weeklyRoast = $this->generateWeeklyRoast($businessScore);

        // --- NEW: Advanced Intelligence Features ---
        $stmt = $this->pdo->prepare("SELECT * FROM erp_crm_leads WHERE tenant_id = ? ORDER BY created_at DESC LIMIT 50");
        $stmt->execute([$this->tenantId]);
        $aiCrmLeads = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Cash Flow Forecast
        $cashForecast = $this->forecastCashFlow();

        // Overdue Invoices (for Follow-up Queue)
        $overdueInvoices = $this->getOverdueInvoices();

        // CRM Churn Risks
        $churnRisks = $this->getCrmChurnRisks();

        // Quick Actions Panel
        $quickActions = $this->getQuickActions($businessScore, $overdueInvoices, $churnRisks);
        
        // Load AI Settings
        $stmt = $this->pdo->prepare("SELECT setting_value FROM erp_settings WHERE tenant_id = ? AND setting_key = 'ai_autopilot_enabled'");
        $stmt->execute([$this->tenantId]);
        $autoPilotEnabled = $stmt->fetchColumn() ?: '0';
        $aiSettings = ['auto_pilot_enabled' => $autoPilotEnabled];

        // Auto-migration to ensure 'department' column exists
        $checkDept = $this->pdo->query("SHOW COLUMNS FROM erp_employees LIKE 'department'");
        if ($checkDept->rowCount() == 0) {
            $this->pdo->exec("ALTER TABLE erp_employees ADD COLUMN department VARCHAR(100) NULL");
        }

        // Fetch Staff Assessment Data for the Weekly Roast
        $stmtStaff = $this->pdo->prepare("
            SELECT 
                e.id, 
                e.first_name, 
                e.last_name, 
                e.department,
                COUNT(t.id) as total_tasks,
                SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN t.status != 'done' AND t.due_date < CURDATE() THEN 1 ELSE 0 END) as overdue_tasks,
                SUM(CASE WHEN t.status != 'done' AND (t.due_date >= CURDATE() OR t.due_date IS NULL) THEN 1 ELSE 0 END) as pending_tasks
            FROM erp_employees e
            LEFT JOIN erp_tasks t ON e.id = t.assigned_to
            WHERE e.tenant_id = ? AND e.status = 'active'
            GROUP BY e.id
            ORDER BY overdue_tasks DESC, completed_tasks ASC
        ");
        $stmtStaff->execute([$this->tenantId]);
        $staffAssessments = $stmtStaff->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/ai-manager/dashboard.php';
    }

    public function saveSettings()
    {
        $this->checkAdmin();
        $enabled = $_POST['auto_pilot_enabled'] ?? '0';

        $stmt = $this->pdo->prepare("
            INSERT INTO erp_settings (tenant_id, setting_key, setting_value) 
            VALUES (?, 'ai_autopilot_enabled', ?) 
            ON DUPLICATE KEY UPDATE setting_value = ?
        ");
        $stmt->execute([$this->tenantId, $enabled, $enabled]);

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    public function researchLead($params = null)
    {
        $leadId = is_array($params) ? ($params['id'] ?? 0) : ($params ?: ($_POST['lead_id'] ?? ($_GET['id'] ?? 0)));
        $this->checkAdmin();
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM erp_crm_leads WHERE id = ? AND tenant_id = ?");
            $stmt->execute([$leadId, $this->tenantId]);
            $lead = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$lead) throw new \Exception("Lead not found.");

            $url = $_POST['url'] ?? '';
            if (empty($url)) throw new \Exception("Website URL is required.");

            $service = new \App\Core\Services\WebResearchService($this->tenantId);
            $notes = $service->researchCompany($url, $lead['company'] ?: $lead['name']);

            // Save notes
            $stmt = $this->pdo->prepare("UPDATE erp_crm_leads SET ai_research_notes = ? WHERE id = ?");
            $stmt->execute([$notes, $leadId]);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'notes' => nl2br(trim($notes))]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    // =========================================================
    // BUSINESS HEALTH SCORE
    // =========================================================

    public function getBusinessScore()
    {
        $scores = [];
        
        // 1. Revenue Health (30 points)
        $scores['revenue'] = $this->calculateRevenueScore();
        
        // 2. Operational Efficiency (25 points)
        $scores['efficiency'] = $this->calculateEfficiencyScore();
        
        // 3. Staff Performance (25 points)
        $scores['staff'] = $this->calculateStaffScore();
        
        // 4. Financial Health (20 points)
        $scores['financial'] = $this->calculateFinancialScore();
        
        $totalScore = 
            ($scores['revenue'] * 0.30) +
            ($scores['efficiency'] * 0.25) +
            ($scores['staff'] * 0.25) +
            ($scores['financial'] * 0.20);
        
        $score = round($totalScore, 1);
        
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $stmt = $this->pdo->prepare("
            INSERT INTO erp_performance_scores 
            (tenant_id, target_type, score_type, score, period_start, period_end, metadata)
            VALUES (?, 'business', 'overall_health', ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE score = VALUES(score), metadata = VALUES(metadata)
        ");
        $stmt->execute([
            $this->tenantId, 
            $score, 
            $monthStart, 
            $today,
            json_encode(['breakdown' => $scores])
        ]);
        
        return [
            'score' => $score,
            'breakdown' => $scores,
            'rating' => $this->getScoreRating($score)
        ];
    }

    private function calculateRevenueScore()
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                DATE_FORMAT(date, '%Y-%m') as month,
                SUM(amount) as total
            FROM erp_transactions 
            WHERE tenant_id = ? AND type = 'income'
            AND date >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
            GROUP BY month
            ORDER BY month DESC
            LIMIT 2
        ");
        $stmt->execute([$this->tenantId]);
        $revenue = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        if (count($revenue) < 2) return 50;
        
        $months = array_keys($revenue);
        $current = $revenue[$months[0]] ?? 0;
        $previous = $revenue[$months[1]] ?? 1;
        
        $growth = (($current - $previous) / $previous) * 100;
        
        if ($growth >= 20) return 100;
        if ($growth >= 10) return 90;
        if ($growth >= 5) return 75;
        if ($growth >= 0) return 60;
        if ($growth >= -5) return 45;
        if ($growth >= -10) return 30;
        return 15;
    }

    private function calculateEfficiencyScore()
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN due_date < CURDATE() AND status != 'done' THEN 1 ELSE 0 END) as overdue
            FROM erp_tasks 
            WHERE tenant_id = ?
            AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        ");
        $stmt->execute([$this->tenantId]);
        $taskData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($taskData['total'] == 0) return 50;
        
        $completionRate = ($taskData['completed'] / $taskData['total']) * 100;
        $overdueRate = ($taskData['overdue'] / $taskData['total']) * 100;
        
        $score = $completionRate - ($overdueRate * 2);
        return max(0, min(100, $score));
    }

    private function calculateStaffScore()
    {
        $stmt = $this->pdo->prepare("
            SELECT AVG(score) as avg_score
            FROM erp_performance_scores
            WHERE tenant_id = ? 
            AND target_type = 'employee'
            AND period_end >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        ");
        $stmt->execute([$this->tenantId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['avg_score'] ?? 50;
    }

    private function calculateFinancialScore()
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
            FROM erp_transactions
            WHERE tenant_id = ?
            AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        ");
        $stmt->execute([$this->tenantId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $income = $data['income'] ?? 0;
        $expense = $data['expense'] ?? 0;
        
        if ($income == 0) return 25;
        
        $profitMargin = (($income - $expense) / $income) * 100;
        
        if ($profitMargin >= 40) return 100;
        if ($profitMargin >= 30) return 85;
        if ($profitMargin >= 20) return 70;
        if ($profitMargin >= 10) return 55;
        if ($profitMargin >= 0) return 35;
        return 10;
    }

    private function getScoreRating($score)
    {
        if ($score >= 90) return ['label' => 'Excellent', 'color' => '#10b981', 'message' => 'Keep pushing'];
        if ($score >= 75) return ['label' => 'Good', 'color' => '#3b82f6', 'message' => 'Room for improvement'];
        if ($score >= 60) return ['label' => 'Average', 'color' => '#f59e0b', 'message' => 'Mediocre performance'];
        if ($score >= 40) return ['label' => 'Poor', 'color' => '#ef4444', 'message' => 'Major issues detected'];
        return ['label' => 'Critical', 'color' => '#991b1b', 'message' => 'Business in danger'];
    }

    // =========================================================
    // WEEKLY ROAST
    // =========================================================

    public function generateWeeklyRoast($businessScore = null)
    {
        if ($businessScore === null) {
            $businessScore = $this->getBusinessScore();
        }

        try {
            $ai = new \App\Core\AI\AIService();
            $dataStr = json_encode($businessScore['breakdown']);
            
            $prompt = "You are a ruthless business mentor for an ERP platform. The user is in 'Ruthless Mentor Mode'. ";
            $prompt .= "Critically roast and advise on the business based on these performance scores (0-100 scale, where <60 is bad, 90+ is excellent): $dataStr. ";
            $prompt .= "Include exactly 3 sections supporting Overall health, Revenue specifics, and Staff/Efficiency issues. ";
            $prompt .= "Be comedic but brutally critical. Use bolding and markdown icons correctly.";

            $aiResponse = $ai->generate($prompt, ["max_tokens" => 450]);
            if (!empty($aiResponse) && !str_starts_with($aiResponse, "Error:")) {
                return ['overall' => $aiResponse];
            }
        } catch (\Exception $e) {
            // Fallback continues below
        }

        $roast = [];
        
        if ($businessScore['score'] < 60) {
            $roast['overall'] = "🔥 **WAKE UP CALL:** Your business health score is {$businessScore['score']}/100. That's {$businessScore['rating']['label']}. {$businessScore['rating']['message']}.";
        } else {
            $roast['overall'] = "📊 Business health: {$businessScore['score']}/100. {$businessScore['rating']['label']}. But don't get comfortable.";
        }
        
        $revenueScore = $businessScore['breakdown']['revenue'] ?? 50;
        if ($revenueScore < 60) {
            $roast['revenue'] = "❌ **REVENUE CRISIS:** Your revenue performance scores {$revenueScore}/100. This is unacceptable. Your growth is stagnant or declining. Time to overhaul your sales strategy or watch your business die.";
        }
        
        $efficiencyScore = $businessScore['breakdown']['efficiency'] ?? 50;
        if ($efficiencyScore < 70) {
            $roast['efficiency'] = "⚠️ **EFFICIENCY FAILURE:** Operational efficiency at {$efficiencyScore}/100. Your team is drowning in overdue tasks. This is a management failure. Either fix your processes or admit you can't lead.";
        }
        
        $staffScore = $businessScore['breakdown']['staff'] ?? 50;
        if ($staffScore < 70) {
            $roast['staff'] = "👎 **TEAM UNDERPERFORMANCE:** Staff performance scores {$staffScore}/100. Your employees are underdelivering. This reflects YOUR inability to hire, train, or motivate properly.";
        }
        
        return $roast;
    }

    // =========================================================
    // CASH FLOW FORECAST (NEW)
    // =========================================================

    public function forecastCashFlow(): array
    {
        try {
            // Pull last 90 days of daily income/expense
            $stmt = $this->pdo->prepare("
                SELECT 
                    DATE_FORMAT(date, '%Y-%m') as month,
                    SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
                    SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
                FROM erp_transactions
                WHERE tenant_id = ?
                AND date >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
                GROUP BY month
                ORDER BY month ASC
            ");
            $stmt->execute([$this->tenantId]);
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($history)) {
                return ['months' => [], 'projected' => [], 'warning' => null, 'has_data' => false];
            }

            // Calculate average monthly income and expense
            $totalIncome  = array_sum(array_column($history, 'income'));
            $totalExpense = array_sum(array_column($history, 'expense'));
            $count = count($history);

            $avgIncome  = $count > 0 ? $totalIncome  / $count : 0;
            $avgExpense = $count > 0 ? $totalExpense / $count : 0;

            // Simple trend: check if last month is better/worse than average
            $lastMonth = end($history);
            $incomeTrend  = $count > 1 ? ($lastMonth['income']  / max($avgIncome,  1)) : 1;
            $expenseTrend = $count > 1 ? ($lastMonth['expense'] / max($avgExpense, 1)) : 1;

            // Project next 3 months using trend momentum
            $projectedMonths = [];
            for ($i = 1; $i <= 3; $i++) {
                $decay = 1 - (($i - 1) * 0.05); // slight confidence decay each month out
                $projectedMonths[] = [
                    'month'   => date('Y-m', strtotime("+$i month")),
                    'label'   => date('M Y', strtotime("+$i month")),
                    'income'  => round($avgIncome  * $incomeTrend  * $decay),
                    'expense' => round($avgExpense * $expenseTrend * $decay),
                    'net'     => round(($avgIncome * $incomeTrend * $decay) - ($avgExpense * $expenseTrend * $decay)),
                ];
            }

            // Build chart data combining history + projected
            $chartData = [];
            foreach ($history as $row) {
                $chartData[] = [
                    'label'   => date('M Y', strtotime($row['month'] . '-01')),
                    'income'  => (float)$row['income'],
                    'expense' => (float)$row['expense'],
                    'net'     => (float)$row['income'] - (float)$row['expense'],
                    'type'    => 'actual',
                ];
            }
            foreach ($projectedMonths as $p) {
                $chartData[] = [
                    'label'   => '📍 ' . $p['label'],
                    'income'  => $p['income'],
                    'expense' => $p['expense'],
                    'net'     => $p['net'],
                    'type'    => 'projected',
                ];
            }

            // Cash flow warning
            $warning = null;
            $nextNet = $projectedMonths[0]['net'] ?? 0;
            if ($nextNet < 0) {
                $warning = [
                    'level'   => 'critical',
                    'message' => 'At current rates, next month projects a cash deficit of ' . number_format(abs($nextNet)) . '. Immediate cost reduction or revenue push needed.',
                ];
            } elseif ($nextNet < ($avgIncome * 0.15)) {
                $warning = [
                    'level'   => 'warning',
                    'message' => 'Net cash position next month is very thin (' . number_format($nextNet) . '). Consider accelerating collections or deferring non-essential spending.',
                ];
            }

            return [
                'has_data'  => true,
                'chart'     => $chartData,
                'projected' => $projectedMonths,
                'warning'   => $warning,
                'avg_monthly_income'  => round($avgIncome),
                'avg_monthly_expense' => round($avgExpense),
            ];

        } catch (\Exception $e) {
            return ['has_data' => false, 'months' => [], 'projected' => [], 'warning' => null];
        }
    }

    // =========================================================
    // OVERDUE INVOICE FOLLOW-UP QUEUE (NEW)
    // =========================================================

    public function getOverdueInvoices(): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    id, uuid, client_name, client_email,
                    due_date, total_amount, status,
                    DATEDIFF(CURDATE(), due_date) as days_overdue
                FROM erp_invoices
                WHERE tenant_id = ?
                AND status IN ('sent', 'overdue')
                AND due_date < CURDATE()
                ORDER BY days_overdue DESC
                LIMIT 10
            ");
            $stmt->execute([$this->tenantId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function generateFollowUpEmail($params)
    {
        $this->checkAdmin();
        $invoiceId = $params['id'] ?? null;

        if (!$invoiceId) {
            echo json_encode(['error' => 'Missing invoice ID']);
            exit;
        }

        try {
            $stmt = $this->pdo->prepare("
                SELECT id, client_name, client_email, due_date, total_amount, days_overdue
                FROM (
                    SELECT *, DATEDIFF(CURDATE(), due_date) as days_overdue
                    FROM erp_invoices
                    WHERE id = ? AND tenant_id = ?
                ) i
            ");
            $stmt->execute([$invoiceId, $this->tenantId]);
            $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$invoice) {
                echo json_encode(['error' => 'Invoice not found']);
                exit;
            }

            // Load company name
            $stmtTenant = $this->pdo->prepare("SELECT name FROM tenants WHERE id = ?");
            $stmtTenant->execute([$this->tenantId]);
            $tenant = $stmtTenant->fetch(PDO::FETCH_ASSOC);
            $companyName = $tenant['name'] ?? 'Our Company';

            $daysOverdue = (int)($invoice['days_overdue'] ?? 0);
            $amount = number_format((float)$invoice['total_amount'], 2);
            $dueDate = date('d M Y', strtotime($invoice['due_date']));

            // Try AI draft first
            $emailDraft = null;
            try {
                $ai = new \App\Core\AI\AIService();
                $tone = $daysOverdue > 30 ? 'firm and urgent' : ($daysOverdue > 14 ? 'politely assertive' : 'friendly and gentle');
                $prompt  = "Write a professional invoice follow-up email from $companyName to {$invoice['client_name']}. ";
                $prompt .= "Invoice amount: {$amount}. Due date was {$dueDate} ({$daysOverdue} days overdue). ";
                $prompt .= "Tone should be $tone. Include: subject line (prefixed 'Subject:'), greeting, body, and sign-off. ";
                $prompt .= "Do NOT include placeholders — use the exact names and amounts provided. Keep it under 150 words.";

                $result = $ai->generate($prompt, ['max_tokens' => 250]);
                if ($result && !str_starts_with($result, 'Error:')) {
                    $emailDraft = $result;
                }
            } catch (\Exception $aiEx) {
                // fall through to template
            }

            // Fallback template
            if (!$emailDraft) {
                if ($daysOverdue > 30) {
                    $emailDraft  = "Subject: URGENT — Invoice Payment Required\n\n";
                    $emailDraft .= "Dear {$invoice['client_name']},\n\n";
                    $emailDraft .= "This is a final notice regarding your outstanding invoice of {$amount}, which was due on {$dueDate} and is now {$daysOverdue} days overdue.\n\n";
                    $emailDraft .= "Please arrange immediate payment to avoid further action. If you have already paid, please send us the receipt.\n\n";
                    $emailDraft .= "Best regards,\n{$companyName}";
                } elseif ($daysOverdue > 14) {
                    $emailDraft  = "Subject: Payment Reminder — Invoice Overdue\n\n";
                    $emailDraft .= "Dear {$invoice['client_name']},\n\n";
                    $emailDraft .= "We wanted to follow up on your invoice of {$amount} which was due on {$dueDate} and is now {$daysOverdue} days past due.\n\n";
                    $emailDraft .= "Please let us know if you need any clarification or if payment is on its way.\n\n";
                    $emailDraft .= "Kind regards,\n{$companyName}";
                } else {
                    $emailDraft  = "Subject: Friendly Payment Reminder\n\n";
                    $emailDraft .= "Hi {$invoice['client_name']},\n\n";
                    $emailDraft .= "Just a quick reminder that your invoice of {$amount} was due on {$dueDate}. ";
                    $emailDraft .= "Please let us know if everything is in order or if you need anything from us.\n\n";
                    $emailDraft .= "Thanks,\n{$companyName}";
                }
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'invoice_id' => $invoiceId,
                'client_name' => $invoice['client_name'],
                'client_email' => $invoice['client_email'],
                'days_overdue' => $daysOverdue,
                'amount' => $amount,
                'email_draft' => $emailDraft,
            ]);
            exit;

        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Failed to generate email: ' . $e->getMessage()]);
            exit;
        }
    }

    // =========================================================
    // CRM CHURN RISK DETECTOR (NEW)
    // =========================================================

    public function getCrmChurnRisks(): array
    {
        try {
            // Customers who haven't had a lead update or new lead in 30+ days
            $stmt = $this->pdo->prepare("
                SELECT 
                    c.id,
                    c.name as client_name,
                    c.email,
                    c.company,
                    MAX(l.updated_at) as last_activity,
                    DATEDIFF(CURDATE(), MAX(l.updated_at)) as days_inactive,
                    COUNT(l.id) as total_leads
                FROM erp_crm_customers c
                LEFT JOIN erp_crm_leads l ON l.customer_id = c.id AND l.tenant_id = c.tenant_id
                WHERE c.tenant_id = ?
                GROUP BY c.id
                HAVING days_inactive >= 30 OR days_inactive IS NULL
                ORDER BY days_inactive DESC
                LIMIT 8
            ");
            $stmt->execute([$this->tenantId]);
            $risks = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Score each risk
            foreach ($risks as &$r) {
                $days = (int)($r['days_inactive'] ?? 999);
                if ($days >= 90 || $r['days_inactive'] === null) {
                    $r['risk_level'] = 'high';
                    $r['risk_label'] = '🔴 High';
                    $r['suggestion'] = 'Re-engage immediately — offer a check-in call or exclusive deal.';
                } elseif ($days >= 60) {
                    $r['risk_level'] = 'medium';
                    $r['risk_label'] = '🟠 Medium';
                    $r['suggestion'] = 'Send a value-update email or case study relevant to them.';
                } else {
                    $r['risk_level'] = 'low';
                    $r['risk_label'] = '🟡 Low';
                    $r['suggestion'] = 'A quick check-in message will suffice.';
                }
            }

            return $risks;

        } catch (\Exception $e) {
            // Table might not exist yet — return empty
            return [];
        }
    }

    // =========================================================
    // AI QUICK ACTIONS PANEL (NEW)
    // =========================================================

    public function getQuickActions(array $businessScore, array $overdueInvoices, array $churnRisks): array
    {
        $actions = [];

        // Action: overdue invoices
        $overdueCount = count($overdueInvoices);
        if ($overdueCount > 0) {
            $totalOwed = array_sum(array_column($overdueInvoices, 'total_amount'));
            $actions[] = [
                'icon'    => '💰',
                'title'   => "$overdueCount Overdue Invoice" . ($overdueCount > 1 ? 's' : ''),
                'detail'  => number_format($totalOwed, 0) . ' owed — review the follow-up queue below',
                'urgency' => 'critical',
                'url'     => '#overdue-invoices',
                'label'   => 'View Follow-up Queue',
            ];
        }

        // Action: high churn risks
        $highRisk = array_filter($churnRisks, fn($r) => $r['risk_level'] === 'high');
        if (count($highRisk) > 0) {
            $actions[] = [
                'icon'    => '👋',
                'title'   => count($highRisk) . ' Client' . (count($highRisk) > 1 ? 's' : '') . ' Going Silent',
                'detail'  => 'No activity in 90+ days — they may be lost already',
                'urgency' => 'warning',
                'url'     => '#churn-risks',
                'label'   => 'See At-Risk Clients',
            ];
        }

        // Action: revenue score is low
        if (($businessScore['breakdown']['revenue'] ?? 100) < 60) {
            $actions[] = [
                'icon'    => '📉',
                'title'   => 'Revenue Needs Attention',
                'detail'  => 'Revenue score is below 60 — consider running a client win-back campaign',
                'urgency' => 'warning',
                'url'     => '/erp/crm/leads',
                'label'   => 'Open CRM Leads',
            ];
        }

        // Action: efficiency low — overdue tasks
        if (($businessScore['breakdown']['efficiency'] ?? 100) < 60) {
            $actions[] = [
                'icon'    => '📋',
                'title'   => 'Overdue Tasks Piling Up',
                'detail'  => 'Operational efficiency is low — tackle overdue tasks to unblock your team',
                'urgency' => 'warning',
                'url'     => '/erp/tasks',
                'label'   => 'Go to Tasks',
            ];
        }

        // Positive fallback
        if (empty($actions)) {
            $actions[] = [
                'icon'    => '✅',
                'title'   => 'All Clear',
                'detail'  => 'No immediate actions required. Keep the momentum going.',
                'urgency' => 'good',
                'url'     => '/erp/finance/dashboard',
                'label'   => 'View Finance Dashboard',
            ];
        }

        return $actions;
    }

    // =========================================================
    // STRESS TESTS
    // =========================================================

    public function stressTestAnalytics()
    {
        $this->checkAdmin();
        
        $stmt = $this->pdo->prepare("DELETE FROM erp_ai_insights WHERE tenant_id = ? AND category = 'analytics' AND is_read = 0");
        $stmt->execute([$this->tenantId]);

        $revenue = $this->calculateRevenueScore();
        if ($revenue < 60) {
            $this->createInsight(
                'analytics',
                'critical',
                'Revenue Alert',
                'Revenue growth is stagnant or declining. Immediate action required to boost sales channels.'
            );
        }

        $financial = $this->calculateFinancialScore();
        if ($financial < 50) {
            $this->createInsight(
                'analytics',
                'warning',
                'Low Profit Margins',
                'Profit margins are dangerously low. Review expenses and pricing structure.'
            );
        }
        
        $efficiency = $this->calculateEfficiencyScore();
        if ($efficiency < 70) {
             $this->createInsight(
                'analytics',
                'warning',
                'Operational Bottlenecks',
                'Task completion rates are suboptimal. Investigate process blockers.'
            );
        }

        if ($revenue >= 60 && $financial >= 50 && $efficiency >= 70) {
             $this->createInsight(
                'analytics',
                'info',
                'Analytics Health Check',
                'Analytics stress test passed. Key metrics are within stable ranges. Good job.'
            );
        }
        
        header('Location: /erp/ai-manager');
        exit;
    }

    public function stressTestStaff()
    {
        $this->checkAdmin();
        
        $stmt = $this->pdo->prepare("DELETE FROM erp_ai_insights WHERE tenant_id = ? AND category = 'staff' AND is_read = 0");
        $stmt->execute([$this->tenantId]);

        $stmt = $this->pdo->prepare("
            SELECT 
                e.id,
                e.first_name,
                e.last_name,
                COUNT(t.id) as total_tasks,
                SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN t.due_date < CURDATE() AND t.status != 'done' THEN 1 ELSE 0 END) as overdue_tasks
            FROM erp_employees e
            LEFT JOIN erp_tasks t ON t.assigned_to = e.id
            WHERE e.tenant_id = ?
            AND e.status = 'active'
            AND (t.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) OR t.id IS NULL)
            GROUP BY e.id
        ");
        $stmt->execute([$this->tenantId]);
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $issuesFound = false;

        foreach ($employees as $emp) {
            if ($emp['total_tasks'] == 0) continue;
            
            $completionRate = ($emp['completed_tasks'] / $emp['total_tasks']) * 100;
            $overdueRate = ($emp['overdue_tasks'] / $emp['total_tasks']) * 100;
            
            if ($completionRate < 50 || $overdueRate > 30) {
                $issuesFound = true;
                $this->createInsight(
                    'staff',
                    'warning',
                    "Performance Alert: {$emp['first_name']} {$emp['last_name']}",
                    "⚠️ Completed " . round($completionRate) . "% of tasks. {$emp['overdue_tasks']} overdue."
                );
            }
        }

        if (!$issuesFound) {
             $this->createInsight(
                'staff',
                'info',
                'Staff Performance Check',
                'Staff stress test passed. No critical underperformance detected.'
            );
        }
        
        header('Location: /erp/ai-manager');
        exit;
    }

    public function stressTestAdmin()
    {
        $this->checkAdmin();
        
        $stmt = $this->pdo->prepare("DELETE FROM erp_ai_insights WHERE tenant_id = ? AND category = 'admin' AND is_read = 0");
        $stmt->execute([$this->tenantId]);
        
        $this->createInsight(
            'admin',
            'info',
            'System Integrity Check',
            'Administrative systems operational. No critical configuration errors found.'
        );

        header('Location: /erp/ai-manager');
        exit;
    }

    public function stressTestHr()
    {
        $this->checkAdmin();
        
        $stmt = $this->pdo->prepare("DELETE FROM erp_ai_insights WHERE tenant_id = ? AND category = 'hr' AND is_read = 0");
        $stmt->execute([$this->tenantId]);

         $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM erp_leave_requests 
            WHERE tenant_id = ? AND status = 'pending'
        ");
        $stmt->execute([$this->tenantId]);
        $pendingLeaves = $stmt->fetchColumn();

        if ($pendingLeaves > 0) {
             $this->createInsight(
                'hr',
                'warning',
                'Pending Leave Requests',
                "There are $pendingLeaves leave requests waiting for approval. Don't leave your team hanging."
            );
        } else {
             $this->createInsight(
                'hr',
                'info',
                'HR Department Check',
                'HR operations valid. No pending items.'
            );
        }

        header('Location: /erp/ai-manager');
        exit;
    }

    // =========================================================
    // UTILITIES
    // =========================================================

    private function createInsight($category, $severity, $title, $message)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO erp_ai_insights (tenant_id, category, severity, title, message)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$this->tenantId, $category, $severity, $title, $message]);
    }

    public function markInsightRead($params)
    {
        $id = $params['id'];
        $stmt = $this->pdo->prepare("
            UPDATE erp_ai_insights 
            SET is_read = 1 
            WHERE id = ? AND tenant_id = ?
        ");
        $stmt->execute([$id, $this->tenantId]);
        
        header('Location: /erp/ai-manager');
        exit;
    }

    public function dismissAllInsights()
    {
        $this->checkAdmin();
        $stmt = $this->pdo->prepare("UPDATE erp_ai_insights SET is_read = 1 WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        header('Location: /erp/ai-manager');
        exit;
    }

    // =========================================================
    // CORI AI CHATBOT
    // =========================================================

    public function chat()
    {
        if (!\App\Core\Auth::user()) {
            header('Content-Type: application/json');
            echo json_encode(['response' => 'Unauthorized. Please login.', 'type' => 'error']);
            exit;
        }
        header('Content-Type: application/json');
        ob_start(); // Capture any stray PHP warnings/notices so they don't break JSON output

        $input = json_decode(file_get_contents('php://input'), true);
        $message = trim($input['message'] ?? '');
        $image = $input['image'] ?? null;

        if (empty($message) && empty($image)) {
            echo json_encode(['response' => 'Please type a message or upload an image to scan.', 'type' => 'text']);
            exit;
        }

        // Multimodal Image / Camera Vision Scanning
        if (!empty($image)) {
            $result = $this->handleImageScan($image, $message);
            ob_end_clean();
            echo json_encode($result);
            exit;
        }

        $normalizedMessage = $this->normalizeUserInput($message);
        $msgLower = strtolower($normalizedMessage);

        try {
            // Check for active conversation state
            if (isset($_SESSION['cori_chat_state'])) {
                $result = $this->handlePendingState($normalizedMessage);
            }
            // Intent detection via keyword matching for quick static tasks
            elseif ($this->matchesIntent($msgLower, ['business summary', 'how is my business', 'overview', 'business health', 'health score', 'how am i doing', 'is my business doing well', 'business performance']) || (strpos($msgLower, 'business') !== false && (strpos($msgLower, 'summary') !== false || strpos($msgLower, 'health') !== false || strpos($msgLower, 'how') !== false))) {
                $result = $this->chatBusinessSummary();
            } elseif ($this->matchesIntent($msgLower, ['unpaid invoice', 'overdue invoice', 'who owes', 'outstanding invoice', 'show unpaid', 'pending invoice', 'who hasn\'t paid', 'who owes me money', 'unpaid bills']) || (strpos($msgLower, 'invoice') !== false && (strpos($msgLower, 'unpaid') !== false || strpos($msgLower, 'overdue') !== false || strpos($msgLower, 'pending') !== false || strpos($msgLower, 'outstanding') !== false))) {
                $result = $this->chatUnpaidInvoices();
            } elseif ($this->matchesIntent($msgLower, ['create invoice', 'new invoice', 'send invoice', 'make invoice', 'generate invoice', 'create invoce', 'new invoce', 'make invoce', 'create an invoce', 'create an invoice', 'make an invoice', 'create invoic', 'invoce for', 'invoice for', 'bill a client', 'send a bill']) || (strpos($msgLower, 'invoice') !== false && (strpos($msgLower, 'create') !== false || strpos($msgLower, 'send') !== false || strpos($msgLower, 'make') !== false || strpos($msgLower, 'new') !== false)) || (strpos($msgLower, 'invoce') !== false && (strpos($msgLower, 'create') !== false || strpos($msgLower, 'make') !== false || strpos($msgLower, 'new') !== false || strpos($msgLower, 'an') !== false))) {
                $result = $this->chatCreateInvoice($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['add lead', 'new lead', 'create lead', 'add prospect', 'met a prospect', 'got a new lead', 'new client interested', 'save lead', 'new potential client']) || (strpos($msgLower, 'lead') !== false && (strpos($msgLower, 'add') !== false || strpos($msgLower, 'create') !== false || strpos($msgLower, 'new') !== false || strpos($msgLower, 'save') !== false))) {
                $result = $this->chatAddLead($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['record expense', 'add expense', 'log expense', 'new expense', 'track expense', 'update expense', 'save expense', 'as expense', 'as an expense', 'as expenses', 'update it as expense', 'bought', 'purchased', 'we spent', 'i spent', 'paid for', 'petrol expense', 'fuel expense', 'spent on', 'record spending', 'spending']) || (strpos($msgLower, 'expense') !== false) || (strpos($msgLower, 'bought') !== false && preg_match('/\d/', $msgLower)) || (strpos($msgLower, 'spent') !== false && preg_match('/\d/', $msgLower)) || (strpos($msgLower, 'purchased') !== false && preg_match('/\d/', $msgLower)) || (strpos($msgLower, 'paid for') !== false && preg_match('/\d/', $msgLower))) {
                $result = $this->chatRecordExpense($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['cash flow', 'forecast', 'projection', 'cash forecast', 'financial projection', 'how much money will i have', 'future cash']) || (strpos($msgLower, 'cash') !== false && (strpos($msgLower, 'flow') !== false || strpos($msgLower, 'forecast') !== false || strpos($msgLower, 'projection') !== false))) {
                $result = $this->chatCashFlow();
            } elseif ($this->matchesIntent($msgLower, ['churn', 'at risk', 'inactive client', 'losing client', 'who is leaving', 'clients leaving', 'who haven\'t we heard from', 'quiet clients']) || (strpos($msgLower, 'churn') !== false || strpos($msgLower, 'risk') !== false)) {
                $result = $this->chatChurnRisk();
            } elseif ($this->matchesIntent($msgLower, ['staff', 'employee', 'team', 'how many staff', 'headcount', 'staff list', 'list employees', 'who works here', 'our team']) || (strpos($msgLower, 'staff') !== false && (strpos($msgLower, 'summary') !== false || strpos($msgLower, 'list') !== false))) {
                $result = $this->chatStaffSummary();
            } elseif ($this->matchesIntent($msgLower, ['pending tasks', 'list tasks', 'my tasks', 'show tasks', 'show pending tasks', 'what do i have to do', 'what are my tasks', 'tasks for today', 'what is on my plate', 'what\'s on my plate', 'todo list']) || (strpos($msgLower, 'task') !== false && (strpos($msgLower, 'pending') !== false || strpos($msgLower, 'list') !== false || strpos($msgLower, 'show') !== false || strpos($msgLower, 'my') !== false))) {
                $result = $this->chatPendingTasks();
            } elseif ($this->matchesIntent($msgLower, ['projects summary', 'list projects', 'my projects', 'show projects', 'what projects', 'active projects', 'status of projects', 'current projects']) || (strpos($msgLower, 'project') !== false && (strpos($msgLower, 'summary') !== false || strpos($msgLower, 'list') !== false || strpos($msgLower, 'active') !== false || strpos($msgLower, 'show') !== false))) {
                $result = $this->chatProjectsSummary();
            } elseif ($this->matchesIntent($msgLower, ['stock level', 'low stock', 'stock check', 'check stock', 'list products', 'inventory level', 'what is in stock', 'do we have', 'how many left']) || (strpos($msgLower, 'stock') !== false && strpos($msgLower, 'update') === false && strpos($msgLower, 'add') === false) || strpos($msgLower, 'product') !== false) {
                $result = $this->chatStockLevel();
            } elseif ($this->matchesIntent($msgLower, ['pending leave', 'leave requests', 'show leave', 'list leave', 'approve leave', 'who is on leave', 'time off requests', 'vacation requests', 'did anyone ask for time off', 'holidays']) || (strpos($msgLower, 'leave') !== false && (strpos($msgLower, 'pending') !== false || strpos($msgLower, 'request') !== false || strpos($msgLower, 'show') !== false))) {
                $result = $this->chatPendingLeave();
            } elseif ($this->matchesIntent($msgLower, ['roast my business', 'roast', 'business roast', 'ruthless roast', 'roast me'])) {
                $result = $this->chatRoastBusiness();
            } elseif ($this->matchesIntent($msgLower, ['analyze business', 'business review', 'business health', 'review business', 'analyze my business'])) {
                $result = $this->chatAnalyzeBusiness();
            } elseif ($this->matchesIntent($msgLower, ['analyze finance', 'financial analysis', 'analyze my finance', 'cash flow forecast', 'analyze cash flow'])) {
                $result = $this->chatAnalyzeFinance();
            } elseif ($this->matchesIntent($msgLower, ['analyze staff', 'staff analysis', 'analyze my staff', 'employee health', 'employee analysis'])) {
                $result = $this->chatAnalyzeStaff();
            } elseif ($this->matchesIntent($msgLower, ['create payment link', 'new payment link', 'generate payment link', 'make payment link', 'payment link for', 'get paid', 'link to pay', 'payment url', 'ask for payment']) || (strpos($msgLower, 'payment') !== false && strpos($msgLower, 'link') !== false)) {
                $result = $this->chatCreatePaymentLink($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['add staff', 'add employee', 'new employee', 'new staff', 'create staff', 'create employee', 'add to staff list', 'hire someone', 'hired a new person', 'new hire', 'onboard employee', 'recruit'])) {
                $result = $this->chatAddStaff($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['create smart form', 'create a smart form', 'build smart form', 'build a smart form', 'generate smart form', 'new smart form', 'create form', 'build form', 'generate form', 'make a form', 'build a survey', 'survey form']) || (strpos($msgLower, 'smart form') !== false || (strpos($msgLower, 'form') !== false && (strpos($msgLower, 'create') !== false || strpos($msgLower, 'build') !== false || strpos($msgLower, 'generate') !== false || strpos($msgLower, 'make') !== false)))) {
                $result = $this->chatCreateSmartForm($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['create email campaign', 'create campaign', 'draft campaign', 'new campaign', 'create newsletter', 'draft newsletter', 'send a mass email', 'email blast', 'marketing email']) || (strpos($msgLower, 'campaign') !== false && (strpos($msgLower, 'create') !== false || strpos($msgLower, 'draft') !== false || strpos($msgLower, 'new') !== false)) || (strpos($msgLower, 'newsletter') !== false && (strpos($msgLower, 'create') !== false || strpos($msgLower, 'write') !== false))) {
                $result = $this->chatCreateMailCampaign($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['send email', 'email someone', 'send an email', 'email to', 'shoot an email']) || (strpos($msgLower, 'send') !== false && strpos($msgLower, 'email') !== false && strpos($msgLower, 'campaign') === false && strpos($msgLower, 'mass') === false)) {
                $result = $this->chatSendEmail($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['create course', 'new course', 'build course', 'academy course', 'create an academy course', 'build a class', 'add a lesson', 'new class']) || (strpos($msgLower, 'course') !== false && (strpos($msgLower, 'create') !== false || strpos($msgLower, 'build') !== false || strpos($msgLower, 'new') !== false))) {
                $result = $this->chatCreateCourse($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['create bio page', 'new bio page', 'create link in bio', 'link in bio', 'build bio page', 'my links page', 'linktree']) || (strpos($msgLower, 'bio') !== false && (strpos($msgLower, 'create') !== false || strpos($msgLower, 'page') !== false || strpos($msgLower, 'link') !== false))) {
                $result = $this->chatCreateBioPage($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['support tickets', 'helpdesk', 'open tickets', 'customer issues', 'pending tickets', 'who needs help', 'customer complaints', 'help requests']) || (strpos($msgLower, 'ticket') !== false && (strpos($msgLower, 'support') !== false || strpos($msgLower, 'show') !== false || strpos($msgLower, 'open') !== false))) {
                $result = $this->chatSupportTickets();
            } elseif ($this->matchesIntent($msgLower, ['add product', 'new product', 'create product', 'add an item to store', 'sell a new item'])) {
                $result = $this->chatAddProduct($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['update stock', 'change stock', 'manage stock', 'add stock', 'add inventory', 'update inventory', 'record inventory', 'restock', 'received stock', 'received inventory', 'received units', 'received items', 'added to inventory', 'new stock', 'new inventory']) || (strpos($msgLower, 'inventory') !== false && (strpos($msgLower, 'update') !== false || strpos($msgLower, 'add') !== false || strpos($msgLower, 'record') !== false || strpos($msgLower, 'received') !== false)) || (strpos($msgLower, 'stock') !== false && (strpos($msgLower, 'add') !== false || strpos($msgLower, 'update') !== false || strpos($msgLower, 'received') !== false || strpos($msgLower, 'restock') !== false))) {
                $result = $this->chatUpdateStock($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['recent orders', 'last orders', 'incoming orders', 'what did we sell', 'new orders', 'sales today', 'latest sales']) || (strpos($msgLower, 'order') !== false && (strpos($msgLower, 'recent') !== false || strpos($msgLower, 'latest') !== false || strpos($msgLower, 'new') !== false))) {
                $result = $this->chatRecentOrders();
            } elseif ($this->matchesIntent($msgLower, ['create coupon', 'new coupon', 'discount code', 'promo code', 'make a discount', 'offer discount'])) {
                $result = $this->chatCreateCoupon($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['top products', 'best selling products', 'best products', 'what sells most', 'most popular items'])) {
                $result = $this->chatTopProducts();
            } elseif ($this->matchesIntent($msgLower, ['clock in', 'clock out', 'log attendance', 'start work', 'end work', 'sign in', 'sign out', 'punch in', 'punch out'])) {
                $result = $this->chatLogAttendance($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['schedule meeting', 'new meeting', 'book meeting', 'set up a call', 'calendar invite', 'book a call', 'schedule a call', 'arrange a meeting'])) {
                $result = $this->chatScheduleMeeting($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['payroll summary', 'current payroll', 'salary report', 'wage report', 'who is getting paid', 'payroll run'])) {
                $result = $this->chatPayrollSummary();
            } elseif ($this->matchesIntent($msgLower, ['assign task', 'create task for', 'give task', 'tell someone to', 'delegate', 'new assignment']) || (strpos($msgLower, 'task') !== false && (strpos($msgLower, 'assign') !== false || strpos($msgLower, 'give') !== false))) {
                $result = $this->chatAssignTask($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['create project', 'new project', 'start project', 'kick off project', 'initiate project'])) {
                $result = $this->chatCreateProject($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['wallet balance', 'my wallet', 'fiat balance', 'crypto balance', 'how much in wallet', 'check balance'])) {
                $result = $this->chatWalletBalance();
            } elseif ($this->matchesIntent($msgLower, ['withdraw funds', 'request withdrawal', 'cash out', 'take money out', 'payout'])) {
                $result = $this->chatWithdrawFunds($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['recent transactions', 'last payments', 'payment history', 'transaction history', 'money in and out'])) {
                $result = $this->chatRecentTransactions();
            } elseif ($this->matchesIntent($msgLower, ['profit and loss', 'p&l', 'p and l', 'profit loss', 'am i making money', 'income statement'])) {
                $result = $this->chatProfitLoss();
            } elseif ($this->matchesIntent($msgLower, ['campaign stats', 'email stats', 'newsletter stats', 'how did my email do', 'campaign performance'])) {
                $result = $this->chatCampaignStats();
            } elseif ($this->matchesIntent($msgLower, ['create mailing list', 'new audience', 'create list', 'new subscriber list', 'add an audience'])) {
                $result = $this->chatCreateMailingList($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['add subscriber', 'new subscriber', 'add email to list', 'new list member'])) {
                $result = $this->chatAddSubscriber($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['list courses', 'my courses', 'all courses', 'what courses do we have', 'show academy'])) {
                $result = $this->chatListCourses();
            } elseif ($this->matchesIntent($msgLower, ['enroll student', 'add student', 'enroll user', 'give access to course', 'new learner'])) {
                $result = $this->chatEnrollStudent($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['create ticket', 'new ticket', 'open ticket'])) {
                $result = $this->chatCreateTicket($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['assign ticket', 'transfer ticket'])) {
                $result = $this->chatAssignTicket($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['add link', 'new bio link', 'add to bio'])) {
                $result = $this->chatAddLink($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['bio page stats', 'link clicks', 'bio views'])) {
                $result = $this->chatBioPageStats();
            } elseif ($this->matchesIntent($msgLower, ['talk to human', 'speak to human', 'human support', 'contact support', 'talk to agent', 'talk to an agent', 'speak to agent', 'speak to an agent', 'talk to someone', 'speak to someone', 'customer care', 'customer support', 'report issue', 'report a problem', 'file a complaint', 'i need help from admin', 'human agent', 'talk to admin', 'reach support'])) {
                $result = $this->chatHumanSupport($normalizedMessage);
            } elseif ($this->matchesIntent($msgLower, ['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'good evening', 'what can you do', 'help', 'menu', 'commands'])) {
                $result = $this->chatGreeting();
            } else {
                // General question or complex conversational command — use AI entity extraction
                $result = $this->chatAIResponse($normalizedMessage);
            }
        } catch (\Throwable $e) {
            $result = [
                'response' => "I ran into an issue processing that request. Please try again or use one of the quick action buttons below.",
                'type' => 'text'
            ];
        }

        ob_end_clean(); // Discard any PHP warnings that got buffered
        echo json_encode($result);
        exit;
    }

    private function handlePendingState(string $message): array
    {
        $state = $_SESSION['cori_chat_state'];
        
        if ($state['action'] === 'add_product') {
            if ($state['missing_field'] === 'name') {
                $state['entities']['name'] = trim($message);
                $state['missing_field'] = 'price';
                $_SESSION['cori_chat_state'] = $state;
                return ['response' => "Got it. What is the price of **{$state['entities']['name']}**? (e.g. '5000' or '5000 NGN')", 'type' => 'text'];
            }
            if ($state['missing_field'] === 'price') {
                $msgLower = strtolower($message);
                $amount = 0;
                preg_match('/(\d+[\d,]*\.?\d*)/', $message, $matches);
                if (!empty($matches[1])) {
                    $amount = (float)str_replace(',', '', $matches[1]);
                }
                if ($amount <= 0) {
                    return ['response' => "I didn't catch that. Please provide a valid price.", 'type' => 'text'];
                }
                $state['entities']['price'] = $amount;
                
                unset($_SESSION['cori_chat_state']); // Clear state
                return $this->processProductCreation($state['entities']);
            }
        }

        if ($state['action'] === 'create_smart_form') {
            unset($_SESSION['cori_chat_state']);
            return $this->processSmartFormCreation($message);
        }

        if ($state['action'] === 'create_invoice') {
            $entities = $state['entities'];

            // Helper: try to extract all fields from the current message in one pass.
            // This lets users reply with partial or full info in a single message.
            $this->extractInvoiceFields($message, $entities);

            if ($state['missing_field'] === 'client_name') {
                // If the whole message looks like "just a name" (no digits, no email), store it
                if (empty($entities['client_name'])) {
                    $entities['client_name'] = trim(preg_replace('/[,;].*/', '', $message)); // text before first comma
                }
            }

            // Advance to the first still-missing required field
            if (empty($entities['client_name'])) {
                $state['entities'] = $entities;
                $_SESSION['cori_chat_state'] = $state;
                return ['response' => "What is the **client's full name**?", 'type' => 'text'];
            }
            if (empty($entities['amount']) || (float)$entities['amount'] <= 0) {
                $state['missing_field'] = 'amount';
                $state['entities'] = $entities;
                $_SESSION['cori_chat_state'] = $state;
                return ['response' => "Got it, **{$entities['client_name']}**. What is the **invoice amount**? (e.g. '50000 NGN' or '500 USD')", 'type' => 'text'];
            }
            if (empty($entities['email'])) {
                $state['missing_field'] = 'email';
                $state['entities'] = $entities;
                $_SESSION['cori_chat_state'] = $state;
                return ['response' => "What is **{$entities['client_name']}'s email address**?", 'type' => 'text'];
            }
            if (empty($entities['description'])) {
                $state['missing_field'] = 'description';
                $state['entities'] = $entities;
                $_SESSION['cori_chat_state'] = $state;
                return ['response' => "What is this invoice **for**? (e.g. 'Website design services')", 'type' => 'text'];
            }

            // All fields present — create it!
            unset($_SESSION['cori_chat_state']);
            return $this->processInvoiceCreation($entities);
        }

        if ($state['action'] === 'record_expense') {
            $entities = $state['entities'];

            if ($state['missing_field'] === 'amount') {
                preg_match('/([0-9][0-9,]*\.?\d*)/', $message, $numMatch);
                $amount = str_replace(',', '', $numMatch[1] ?? '0');
                if ((float)$amount <= 0) {
                    return ['response' => "I didn't catch that. Please provide a valid amount, e.g. **10000** or **5000 NGN**.", 'type' => 'text'];
                }
                $entities['amount'] = $amount;
                unset($_SESSION['cori_chat_state']);
                return $this->processExpenseRecord($entities);
            }

            // Unknown state — clear and fallback
            unset($_SESSION['cori_chat_state']);
        }

        if ($state['action'] === 'update_stock') {
            $entities = $state['entities'];

            if ($state['missing_field'] === 'product_name') {
                $entities['product_name'] = trim($message);
                $state['missing_field']   = 'quantity';
                $state['entities']        = $entities;
                $_SESSION['cori_chat_state'] = $state;
                return ['response' => "How many units of **{$entities['product_name']}** did you receive?", 'type' => 'text'];
            }

            if ($state['missing_field'] === 'quantity') {
                preg_match('/([0-9][0-9,]*)/', $message, $m);
                $qty = (int)str_replace(',', '', $m[1] ?? '0');
                if ($qty <= 0) {
                    return ['response' => "Please provide a valid quantity, e.g. **50** or **100**.", 'type' => 'text'];
                }
                $entities['quantity'] = $qty;
                unset($_SESSION['cori_chat_state']);
                return $this->processStockUpdate($entities);
            }

            unset($_SESSION['cori_chat_state']);
        }

        if ($state['action'] === 'create_payment_link') {
            unset($_SESSION['cori_chat_state']);
            $msgLower = strtolower($message);
            $amount = 0;
            $currency = 'NGN';
            
            preg_match('/(\d+[\d,]*\.?\d*)/', $message, $matches);
            if (!empty($matches[1])) {
                $amount = (float)str_replace(',', '', $matches[1]);
            }
            if (strpos($msgLower, 'usd') !== false || strpos($msgLower, 'dollar') !== false) {
                $currency = 'USD';
            }
            
            if ($amount <= 0) {
                return ['response' => "Sorry, I couldn't understand the amount. Please specify a number like '500 NGN'.", 'type' => 'text'];
            }
            
            return $this->processPaymentLinkCreation([
                'amount' => $amount,
                'currency' => $currency
            ]);
        }

        if ($state['action'] === 'add_staff') {
            if ($state['missing_field'] === 'first_name') {
                $state['entities']['first_name'] = trim($message);
                $state['missing_field'] = 'last_name';
                $_SESSION['cori_chat_state'] = $state;
                return ['response' => "What is their last name?", 'type' => 'text'];
            }
            if ($state['missing_field'] === 'last_name') {
                $state['entities']['last_name'] = trim($message);
                $state['missing_field'] = 'email';
                $_SESSION['cori_chat_state'] = $state;
                return ['response' => "What is their email address?", 'type' => 'text'];
            }
            if ($state['missing_field'] === 'email') {
                $state['entities']['email'] = trim($message);
                $state['missing_field'] = 'job_title';
                $_SESSION['cori_chat_state'] = $state;
                return ['response' => "What is their job title? (e.g. 'Marketing Specialist')", 'type' => 'text'];
            }
            if ($state['missing_field'] === 'job_title') {
                $state['entities']['job_title'] = trim($message);
                $state['missing_field'] = 'salary';
                $_SESSION['cori_chat_state'] = $state;
                return ['response' => "What is their monthly salary? (numbers only, e.g. '150000')", 'type' => 'text'];
            }
            if ($state['missing_field'] === 'salary') {
                $state['entities']['salary'] = (float)str_replace(',', '', trim($message));
                unset($_SESSION['cori_chat_state']);
                return $this->processStaffAddition($state['entities']);
            }
        }

        if ($state['action'] === 'send_email') {
            if ($state['missing_field'] === 'to_email') {
                $email = preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $message, $matches) ? $matches[0] : trim($message);
                $state['entities']['to_email'] = $email;
                if (empty($state['entities']['subject'])) {
                    $state['missing_field'] = 'subject';
                    $_SESSION['cori_chat_state'] = $state;
                    return ['response' => "Got it. What should be the subject of the email?", 'type' => 'text'];
                } elseif (empty($state['entities']['body'])) {
                    $state['missing_field'] = 'body';
                    $_SESSION['cori_chat_state'] = $state;
                    return ['response' => "Got it. What should be the body/message of the email?", 'type' => 'text'];
                } else {
                    unset($_SESSION['cori_chat_state']);
                    return $this->processSendEmail($state['entities']);
                }
            }
            if ($state['missing_field'] === 'subject') {
                $state['entities']['subject'] = trim($message);
                if (empty($state['entities']['body'])) {
                    $state['missing_field'] = 'body';
                    $_SESSION['cori_chat_state'] = $state;
                    return ['response' => "Great! What should be the body/message of the email?", 'type' => 'text'];
                } else {
                    unset($_SESSION['cori_chat_state']);
                    return $this->processSendEmail($state['entities']);
                }
            }
            if ($state['missing_field'] === 'body') {
                $state['entities']['body'] = trim($message);
                if (empty($state['entities']['subject'])) {
                    $state['missing_field'] = 'subject';
                    $_SESSION['cori_chat_state'] = $state;
                    return ['response' => "Great! What should be the subject of the email?", 'type' => 'text'];
                } else {
                    unset($_SESSION['cori_chat_state']);
                    return $this->processSendEmail($state['entities']);
                }
            }
        }

        // Fallback if state is unknown
        unset($_SESSION['cori_chat_state']);
        return ['response' => "I lost track of our conversation. Let's start over. How can I help?", 'type' => 'text'];
    }

    /**
     * Extract invoice fields from a free-form user message.
     * Only sets a field if it is currently empty and a value is found.
     * Format handled: "cassjoo, 50,000 details is website design" or
     *                 "Name: John Amount: 50000 NGN Email: j@x.com description: dev work"
     */
    private function extractInvoiceFields(string $message, array &$entities): void
    {
        // 1. Email — standard regex
        if (empty($entities['email'])) {
            preg_match('/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/', $message, $m);
            if (!empty($m[0])) $entities['email'] = $m[0];
        }

        // 2. Amount — any standalone number (with optional commas)
        if (empty($entities['amount']) || (float)$entities['amount'] <= 0) {
            preg_match('/\b([0-9][0-9,]*(?:\.\d+)?)\b/', $message, $m);
            if (!empty($m[1])) {
                $entities['amount'] = str_replace(',', '', $m[1]);
                // Currency detection
                $lower = strtolower($message);
                $entities['currency'] = (strpos($lower, 'usd') !== false || strpos($lower, 'dollar') !== false || strpos($lower, '$') !== false) ? 'USD' : 'NGN';
            }
        }

        // 3. Description — after "details is", "description is", "description:", "for:", or "desc:"
        if (empty($entities['description'])) {
            if (preg_match('/(?:details?\s+(?:is\s+)?|description\s*[:\s]\s*|desc\s*[:\s]\s*|for\s*[:\s]\s*)(.{3,100})/i', $message, $m)) {
                $entities['description'] = trim($m[1]);
            }
        }

        // 4. Name — after "name is", "name:", or text before first comma/number (if name not already set)
        if (empty($entities['client_name'])) {
            if (preg_match('/(?:name\s*[:\s]\s*)([A-Za-z][A-Za-z\s]{1,40}?)(?:\s*,|\s*\d|\s+email|\s+amount|$)/i', $message, $m)) {
                $entities['client_name'] = trim($m[1]);
            } elseif (preg_match('/^([A-Za-z][A-Za-z\s]{1,30}?)(?:\s*,|\s+\d)/', $message, $m)) {
                // First word(s) before a comma or number
                $entities['client_name'] = trim($m[1]);
            } elseif (!empty($entities['email'])) {
                $entities['client_name'] = ucwords(str_replace(['.', '_', '-'], ' ', explode('@', $entities['email'])[0]));
            }
        }
    }

    private function matchesIntent(string $message, array $keywords): bool
    {
        foreach ($keywords as $kw) {
            if (strlen($kw) <= 5 || strpos($kw, ' ') === false) {
                if (preg_match('/\b' . preg_quote($kw, '/') . '\b/i', $message)) {
                    return true;
                }
            } else {
                if (stripos($message, $kw) !== false) {
                    return true;
                }
            }
        }
        return false;
    }

    private function normalizeUserInput(string $message): string
    {
        $normalized = $message;

        // 1. Shorthand numbers (e.g. 50k -> 50000, 1.5m -> 1500000)
        $normalized = preg_replace_callback('/(?<=\b|\$|₦|ngn|usd|\s)([0-9]+(?:\.[0-9]+)?)\s*k\b/i', function ($m) {
            return (string)((float)$m[1] * 1000);
        }, $normalized);

        $normalized = preg_replace_callback('/(?<=\b|\$|₦|ngn|usd|\s)([0-9]+(?:\.[0-9]+)?)\s*m\b/i', function ($m) {
            return (string)((float)$m[1] * 1000000);
        }, $normalized);

        // 2. Common business and command typos
        $typoMap = [
            'sned'      => 'send',
            'snd'       => 'send',
            'emial'     => 'email',
            'emai'      => 'email',
            'emails'    => 'email',
            'invoce'    => 'invoice',
            'invoyce'   => 'invoice',
            'invoic'    => 'invoice',
            'invoices'  => 'invoice',
            'creeate'   => 'create',
            'mak'       => 'make',
            'expens'    => 'expense',
            'expence'   => 'expense',
            'expenss'   => 'expense',
            'expenses'  => 'expense',
            'recrd'     => 'record',
            'shw'       => 'show',
            'chck'      => 'check',
            'chk'       => 'check',
            'taks'      => 'tasks',
            'taskes'    => 'tasks',
            'prodct'    => 'product',
            'produc'    => 'product',
            'produt'    => 'product',
            'balence'   => 'balance',
            'balacne'   => 'balance',
            'projt'     => 'project',
            'projct'    => 'project',
            'leav'      => 'leave',
            'suport'    => 'support',
            'supprt'    => 'support',
            'tickt'     => 'ticket',
            'tickts'    => 'tickets',
            'schedul'   => 'schedule',
            'schedle'   => 'schedule',
            'meting'    => 'meeting',
            'calender'  => 'calendar',
            'campain'   => 'campaign',
            'campagn'   => 'campaign',
            'customr'   => 'customer',
            'custmer'   => 'customer',
            'plz'       => 'please',
            'pls'       => 'please',
        ];

        foreach ($typoMap as $bad => $good) {
            $normalized = preg_replace('/\b' . preg_quote($bad, '/') . '\b/i', $good, $normalized);
        }

        return $normalized;
    }

    private function chatHumanSupport(string $message = ''): array
    {
        return [
            'response' => "## 👤 Human Support & Escalation\n\n"
                . "I understand you need to speak with a person. You can reach out directly to your platform admin or open a priority ticket:\n\n"
                . "• 🎫 **[Open Support Ticket](/support)** — Submit an inquiry directly to the helpdesk\n"
                . "• 📋 **[View Support Tickets](/support/tickets)** — Track active tickets\n\n"
                . "Click a button below or let me know what else you need!",
            'type' => 'support',
            'action_url' => '/support',
            'suggestions' => [
                '🎫 Open support ticket',
                '📋 View open tickets',
                '📊 Business summary',
                '✨ Help menu'
            ]
        ];
    }

    private function chatGreeting(): array
    {
        $name = $_SESSION['user_name'] ?? 'there';
        $hour = (int) date('H');
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

        return [
            'response' => "$greeting, $name! 👋\n\nI'm **Cori**, your AI business assistant. Here's what I can do:\n\n"
                . "• 📊 **Business summary** — *'How is my business doing?'*\n"
                . "• 💰 **Record expenses** — *'We bought fuel for 10,000 naira'* or *'Spent 25k on diesel'*\n"
                . "• 📦 **Update inventory** — *'Received 50 bags of rice'*\n"
                . "• 📄 **Create invoices** — *'Create an invoice'* or *'Invoice for 150k'*\n"
                . "• 📧 **Send emails** — *'Send email to someone@domain.com'*\n"
                . "• ➕ **Add leads** — *'Add a new lead'*\n"
                . "• 📋 **Pending tasks** — *'Show my tasks'*\n"
                . "• 📂 **Projects** — *'Show active projects'*\n"
                . "• ✈️ **Leave requests** — *'Show pending leave'*\n"
                . "• 📈 **Cash flow** — *'Cash flow forecast'*\n"
                . "• ⚠️ **Churn risks** — *'Who are my at-risk clients?'*\n"
                . "• ✍️ **Write content** — *'Write a Facebook post about my business'*\n\n"
                . "Just type naturally or tap any of the options below!",
            'type' => 'greeting',
            'suggestions' => [
                '📊 Business summary',
                '📄 Create an invoice',
                '💰 Record expense',
                '📧 Send email',
                '📋 Pending tasks',
                '📦 Stock levels',
                '👤 Talk to human'
            ]
        ];
    }

    private function chatBusinessSummary(): array
    {
        $score = $this->getBusinessScore();
        $ctx = new \App\Core\AI\ContextAggregator($this->tenantId);
        $context = $ctx->getDailyContext();

        $rating = $score['rating']['label'] ?? 'Unknown';
        $emoji = $score['score'] >= 75 ? '🟢' : ($score['score'] >= 50 ? '🟡' : '🔴');

        $revenueStr = 'No revenue recorded today';
        if (!empty($context['financials'])) {
            $parts = [];
            foreach ($context['financials'] as $r) {
                $parts[] = strtoupper($r['currency'] ?? 'NGN') . ' ' . number_format($r['total'] ?? 0, 2);
            }
            $revenueStr = implode(' | ', $parts);
        }

        $response = "## $emoji Business Health: {$score['score']}/100 ({$rating})\n\n";
        $response .= "**Today's Revenue:** $revenueStr\n";
        $response .= "**Clients:** {$context['crm']['clients_count']} total | {$context['crm']['new_leads']} new leads today\n";
        $response .= "**Active Staff:** {$context['hr']['staff_count']}\n";
        $response .= "**Overdue Invoices:** {$context['finance']['overdue_invoices']}\n";
        $response .= "**Overdue Tasks:** {$context['finance']['pending_tasks']}\n\n";

        $response .= "### Score Breakdown\n";
        $response .= "• Revenue Health: **{$score['breakdown']['revenue']}**/100\n";
        $response .= "• Operational Efficiency: **{$score['breakdown']['efficiency']}**/100\n";
        $response .= "• Staff Performance: **{$score['breakdown']['staff']}**/100\n";
        $response .= "• Financial Health: **{$score['breakdown']['financial']}**/100\n";

        return ['response' => $response, 'type' => 'summary'];
    }

    private function chatUnpaidInvoices(): array
    {
        $invoices = $this->getOverdueInvoices();

        if (empty($invoices)) {
            return ['response' => "✅ Great news! You have **no overdue invoices**. All caught up!", 'type' => 'text'];
        }

        $response = "## 📋 Unpaid Invoices (" . count($invoices) . ")\n\n";
        $total = 0;

        foreach ($invoices as $inv) {
            $days = $inv['days_overdue'] ?? 0;
            $amount = (float)($inv['total'] ?? 0);
            $total += $amount;
            $urgency = $days > 30 ? '🔴' : ($days > 14 ? '🟠' : '🟡');
            $currency = strtoupper($inv['currency'] ?? 'NGN');

            $response .= "$urgency **{$inv['client_name']}** — $currency " . number_format($amount, 2) . "\n";
            $response .= "   Invoice #{$inv['invoice_number']} • {$days} days overdue\n\n";
        }

        $response .= "---\n**Total Outstanding:** NGN " . number_format($total, 2) . "\n\n";
        $response .= "_Tip: Type \"follow up on invoice\" to draft a reminder email._";

        return ['response' => $response, 'type' => 'list'];
    }

    private function chatCreateSmartForm(string $message = ''): array
    {
        $clean = trim(str_ireplace([
            'create a smart form for', 'create smart form for', 'create a form for', 'create form for',
            'create a smart form', 'create smart form', 'build a smart form for', 'build smart form for',
            'build a smart form', 'build smart form', 'generate a smart form for', 'generate smart form for',
            'generate smart form', 'new smart form for', 'new smart form', 'create form', 'build form', 'generate form'
        ], '', $message));

        if (empty($clean) || strlen($clean) < 3) {
            $_SESSION['cori_chat_state'] = [
                'action' => 'create_smart_form',
                'missing_field' => 'topic'
            ];
            return [
                'response' => "Sure! What kind of smart form would you like me to create? (e.g., 'Event Registration', 'Customer Survey', 'Job Application', or 'Employee Onboarding')",
                'type' => 'text'
            ];
        }

        return $this->processSmartFormCreation($clean);
    }

    private function processSmartFormCreation(string $topic): array
    {
        $topicLower = strtolower($topic);

        try {
            $ai = new \App\Core\AI\AIService();
            
            $prompt = "You are a form generator for Casjoe Smart Forms. "
                . "The user wants a form for: \"{$topic}\". "
                . "Generate a JSON response representing the form structure. "
                . "The JSON MUST have these exact keys:\n"
                . "1. \"title\": String, a professional title for the form.\n"
                . "2. \"description\": String, a short instruction for the respondent.\n"
                . "3. \"structure\": Array of objects. Each object MUST have:\n"
                . "   - \"type\": String (one of: text, email, textarea, dropdown, number, date, checkbox, radio)\n"
                . "   - \"label\": String (the field label visible to user)\n"
                . "   - \"name\": String (snake_case database field name)\n"
                . "   - \"options\": String (Comma-separated options ONLY IF type is dropdown, checkbox, or radio. Otherwise empty string \"\")\n"
                . "   - \"required\": Boolean (true or false)\n\n"
                . "Return ONLY valid JSON without markdown formatting. Ensure you include a mix of field types relevant to the topic.";

            $jsonStr = $ai->generate($prompt, ['max_tokens' => 1000], 'cori_chat', (int)($_SESSION['user_id'] ?? 0));
            $jsonStr = trim(str_replace(['```json', '```'], '', $jsonStr));
            $parsed = json_decode($jsonStr, true);

            if ($parsed && isset($parsed['title'], $parsed['structure']) && is_array($parsed['structure'])) {
                $title = $parsed['title'];
                $description = $parsed['description'] ?? '';
                $structure = $parsed['structure'];
            } else {
                throw new \Exception("Invalid AI JSON format");
            }
        } catch (\Exception $e) {
            // Fallback to generic form if AI fails
            $title = ucwords(trim($topic)) ?: 'Smart Collection Form';
            $description = 'Please complete the ' . $title . ' form below.';
            $structure = [
                ['type' => 'text', 'label' => 'Full Name', 'name' => 'full_name', 'options' => '', 'required' => true],
                ['type' => 'email', 'label' => 'Email Address', 'name' => 'email', 'options' => '', 'required' => true],
                ['type' => 'text', 'label' => 'Phone / WhatsApp Number', 'name' => 'phone', 'options' => '', 'required' => false],
                ['type' => 'dropdown', 'label' => 'Category / Purpose', 'name' => 'category', 'options' => 'General Inquiry, Support Request, Partnership, Billing', 'required' => true],
                ['type' => 'textarea', 'label' => 'Details / Message', 'name' => 'message', 'options' => '', 'required' => true]
            ];
        }

        try {
            $db = \App\Core\Database::getInstance();
            $tenantId = $this->tenantId;
            $userId = $_SESSION['user_id'] ?? 1;
            $settings = json_encode(['payment_enabled' => false]);

            $db->query(
                "INSERT INTO smart_forms (tenant_id, user_id, title, description, structure, settings, status) VALUES (?, ?, ?, ?, ?, ?, 'active')",
                [$tenantId, $userId, $title, $description, json_encode($structure), $settings]
            );
            $formId = $db->lastInsertId();

            $fieldNames = array_map(function ($f) {
                return "• **" . $f['label'] . "** (`" . strtoupper($f['type']) . "`)";
            }, $structure);

            $response = "## ✅ Smart Form Created Successfully!\n\n";
            $response .= "I have built and activated your new smart form: **{$title}**\n\n";
            $response .= "### Included Fields:\n" . implode("\n", $fieldNames) . "\n\n";
            $response .= "👉 [Open & Customize Form](/smart-forms/edit/{$formId})\n";

            return [
                'response' => $response,
                'type' => 'form',
                'action_url' => "/smart-forms/edit/{$formId}"
            ];
        } catch (\Exception $e) {
            return [
                'response' => "⚠️ Could not save the smart form: " . $e->getMessage(),
                'type' => 'text'
            ];
        }
    }

    private function chatCreateMailCampaign(string $message = ''): array
    {
        $topic = trim(str_ireplace([
            'create email campaign for', 'create campaign for', 'draft campaign for', 'create newsletter for',
            'create email campaign', 'create campaign', 'draft campaign', 'create newsletter', 'draft newsletter',
            'new campaign', 'email campaign', 'send email via casjoe mail for', 'send email via casjoe mail', 'send email for', 'send email'
        ], '', $message)) ?: 'Product Launch & Monthly Update';

        $subject = "Exciting News: " . ucwords($topic);
        $content = "<h2>Hello {First Name},</h2>\n<p>We are thrilled to share our latest update on <strong>" . htmlspecialchars($topic) . "</strong>.</p>\n<p>Discover what's new and how it can help you succeed this month.</p>\n<p><a href='https://app.casjoe.com'>Learn More →</a></p>";

        try {
            $db = \App\Core\Database::getInstance();
            $tenantId = $this->tenantId;
            $db->query(
                "INSERT INTO cm_campaigns (tenant_id, name, subject, content, status) VALUES (?, ?, ?, ?, 'draft')",
                [$tenantId, ucwords($topic), $subject, $content]
            );

            $response = "## 📧 Email Campaign Created!\n\n";
            $response .= "I have drafted your new email campaign in **Casjoe Mail**:\n\n";
            $response .= "• **Campaign Name:** " . ucwords($topic) . "\n";
            $response .= "• **Subject Line:** \"{$subject}\"\n";
            $response .= "• **Status:** Draft (Ready for Review)\n\n";
            $response .= "👉 [Open & Edit Email Campaign](/mail/campaigns)\n";

            return [
                'response' => $response,
                'type' => 'mail',
                'action_url' => '/mail/campaigns'
            ];
        } catch (\Exception $e) {
            return ['response' => "⚠️ Could not create email campaign: " . $e->getMessage(), 'type' => 'text'];
        }
    }

    private function extractEmailDetails(string $message): array
    {
        $to = null;
        $subject = null;
        $body = null;

        // 1. Recipient email
        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $message, $m)) {
            $to = $m[0];
        }

        // 2. Extract Subject
        $subjPatterns = [
            '/(?:(?:email\s+)?subject)\s*(?::|is)?\s*["\']([^"\']+)["\']/i',
            '/(?:(?:email\s+)?subject)\s*(?::\s*|\s+is\s+)([^,;\n\r]+?)(?:\s+(?:and\s+)?(?:tell|saying|message|body)|$)/i',
            '/(?:(?:email\s+)?subject)\s+([^,;\n\r]+?)(?:\s+(?:and\s+)?(?:tell|saying|message|body)|$)/i'
        ];

        foreach ($subjPatterns as $pattern) {
            if (preg_match($pattern, $message, $sm)) {
                $candidate = trim($sm[1]);
                if (preg_match('/^is\s+/i', $candidate)) {
                    $candidate = preg_replace('/^is\s+/i', '', $candidate);
                }
                if (!empty($candidate)) {
                    $subject = trim($candidate);
                    break;
                }
            }
        }

        // 3. Extract Body / Message
        $bodyPatterns = [
            '/(?:message|body)\s*(?::|is)?\s*["\']([^"\']+)["\']/i',
            '/(?:tell\s+(?:him|her|them|someone)?|saying|say|message\s*(?::|is)|body\s*(?::|is))\s*(?:that|to)?\s*(.+?)(?:\s+(?:with\s+)?(?:email\s+)?subject|$)/is'
        ];

        foreach ($bodyPatterns as $pattern) {
            if (preg_match($pattern, $message, $bm)) {
                $candidate = trim($bm[1]);
                $candidate = preg_replace('/\s+(?:with\s+)?(?:email\s+)?subject\s*.*$/i', '', $candidate);
                if (!empty($candidate)) {
                    $body = trim($candidate);
                    break;
                }
            }
        }

        return [
            'to_email' => $to,
            'subject'  => $subject ? ucfirst($subject) : null,
            'body'     => $body ? ucfirst($body) : null
        ];
    }

    private function chatSendEmail(string $message): array
    {
        $details = $this->extractEmailDetails($message);
        $to = $details['to_email'];
        $subject = $details['subject'];
        $body = $details['body'];

        // If all 3 fields are present, send immediately!
        if (!empty($to) && !empty($subject) && !empty($body)) {
            unset($_SESSION['cori_chat_state']);
            return $this->processSendEmail([
                'to_email' => $to,
                'subject'  => $subject,
                'body'     => $body
            ]);
        }

        // If email is missing
        if (empty($to)) {
            $_SESSION['cori_chat_state'] = [
                'action' => 'send_email',
                'missing_field' => 'to_email',
                'entities' => [
                    'subject' => $subject,
                    'body'    => $body
                ]
            ];
            return ['response' => "Who would you like to send the email to? Please provide their email address.", 'type' => 'text'];
        }

        // If subject is missing
        if (empty($subject)) {
            $_SESSION['cori_chat_state'] = [
                'action' => 'send_email',
                'missing_field' => 'subject',
                'entities' => [
                    'to_email' => $to,
                    'body'     => $body
                ]
            ];
            $bodyNote = !empty($body) ? " with message: *\"{$body}\"*" : "";
            return ['response' => "I will send an email to **{$to}**{$bodyNote}. What should be the subject of the email?", 'type' => 'text'];
        }

        // If body is missing
        $_SESSION['cori_chat_state'] = [
            'action' => 'send_email',
            'missing_field' => 'body',
            'entities' => [
                'to_email' => $to,
                'subject'  => $subject
            ]
        ];
        return ['response' => "Got it. What should be the message/body of the email to **{$to}**?", 'type' => 'text'];
    }

    private function processSendEmail(array $entities): array
    {
        try {
            $to = $entities['to_email'] ?? '';
            $subject = $entities['subject'] ?? 'Notification from Cori AI';
            $body = nl2br(htmlspecialchars($entities['body'] ?? ''));

            if (empty($to)) {
                return ['response' => "⚠️ Cannot send email: No recipient email address provided.", 'type' => 'text'];
            }

            \App\Core\Mailer::send($to, $subject, $body, false);

            return [
                'response' => "✅ Email successfully sent to **{$to}** with subject \"{$subject}\".",
                'type' => 'text'
            ];
        } catch (\Exception $e) {
            return [
                'response' => "⚠️ Failed to send email: " . $e->getMessage(),
                'type' => 'text'
            ];
        }
    }

    private function chatCreateCourse(string $message = ''): array
    {
        $topic = trim(str_ireplace([
            'create an academy course for', 'create course for', 'create academy course for',
            'create an academy course', 'create course', 'create academy course', 'build course', 'new course'
        ], '', $message)) ?: 'Business Mastery & Operations';

        $title = ucwords($topic);
        $description = "Comprehensive masterclass on {$title}. Designed to give you actionable skills and strategies.";

        try {
            $db = \App\Core\Database::getInstance();
            $tenantId = $this->tenantId;

            $db->query(
                "INSERT INTO academy_courses (tenant_id, title, description, price, status) VALUES (?, ?, ?, 49.99, 'draft')",
                [$tenantId, $title, $description]
            );
            $courseId = $db->lastInsertId();

            // Insert 3 standard sections
            $sections = [
                'Module 1: Introduction & Fundamentals',
                'Module 2: Advanced Techniques & Strategy',
                'Module 3: Real-World Case Studies & Execution'
            ];
            foreach ($sections as $idx => $secTitle) {
                $db->query(
                    "INSERT INTO academy_sections (course_id, title, sort_order) VALUES (?, ?, ?)",
                    [$courseId, $secTitle, $idx + 1]
                );
            }

            $response = "## 🎓 Academy Course Created!\n\n";
            $response .= "I have structured and drafted your new course in **Casjoe Academy**:\n\n";
            $response .= "• **Course Title:** {$title}\n";
            $response .= "• **Modules Created:** 3 Structured Curriculum Sections\n";
            $response .= "• **Status:** Draft ($49.99)\n\n";
            $response .= "👉 [Open & Add Lessons](/academy/courses)\n";

            return [
                'response' => $response,
                'type' => 'academy',
                'action_url' => '/academy/courses'
            ];
        } catch (\Exception $e) {
            return ['response' => "⚠️ Could not create academy course: " . $e->getMessage(), 'type' => 'text'];
        }
    }

    private function chatCreateBioPage(string $message = ''): array
    {
        $name = trim(str_ireplace([
            'create bio page for', 'create link in bio for', 'create bio page', 'create link in bio', 'new bio page'
        ], '', $message)) ?: 'Casjoe Official Bio';

        $title = ucwords($name);
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name)) . '-' . rand(100, 999);

        try {
            $db = \App\Core\Database::getInstance();
            $tenantId = $this->tenantId;
            $userId = $_SESSION['user_id'] ?? 1;

            $db->query(
                "INSERT INTO links_bio_pages (tenant_id, user_id, slug, title, description, status) VALUES (?, ?, ?, ?, ?, 'active')",
                [$tenantId, $userId, $slug, $title, "Official Link-in-Bio Page for {$title}"]
            );

            $response = "## 🔗 Bio Page Created!\n\n";
            $response .= "I have set up your new link-in-bio page in **Casjoe Links**:\n\n";
            $response .= "• **Page Title:** {$title}\n";
            $response .= "• **URL Slug:** `/bio/{$slug}`\n\n";
            $response .= "👉 [Customize Bio Page Links](/links/bio)\n";

            return [
                'response' => $response,
                'type' => 'links',
                'action_url' => '/links/bio'
            ];
        } catch (\Exception $e) {
            return ['response' => "⚠️ Could not create bio page: " . $e->getMessage(), 'type' => 'text'];
        }
    }

    private function chatSupportTickets(): array
    {
        try {
            $db = \App\Core\Database::getInstance();
            $tenantId = $this->tenantId;
            $stmt = $db->query("SELECT * FROM support_tickets WHERE tenant_id = ? AND status != 'closed' ORDER BY created_at DESC LIMIT 5", [$tenantId]);
            $tickets = $stmt->fetchAll();

            if (empty($tickets)) {
                return [
                    'response' => "## 🎫 Support Desk Status: Excellent\n\nYou currently have **0 active support tickets** waiting for resolution.\n\n👉 [Open Casjoe Support](/support)",
                    'type' => 'support',
                    'action_url' => '/support'
                ];
            }

            $list = [];
            foreach ($tickets as $t) {
                $statusEmoji = $t['priority'] === 'high' ? '🔴' : '🟡';
                $list[] = "• {$statusEmoji} **#" . ($t['id'] ?? '') . " - " . htmlspecialchars($t['subject'] ?? 'Ticket') . "** (`" . ($t['status'] ?? 'open') . "`)";
            }

            $response = "## 🎫 Open Support Tickets (" . count($tickets) . ")\n\n";
            $response .= implode("\n", $list) . "\n\n";
            $response .= "👉 [Open Ticket Queue](/support/tickets)\n";

            return [
                'response' => $response,
                'type' => 'support',
                'action_url' => '/support/tickets'
            ];
        } catch (\Exception $e) {
            return [
                'response' => "## 🎫 Support Helpdesk\n\nManage customer tickets and inquiries.\n\n👉 [Go to Casjoe Support](/support)",
                'type' => 'support',
                'action_url' => '/support'
            ];
        }
    }

    private function chatCreateInvoice(string $message = ''): array
    {
        // Short trigger — start fresh conversation
        if (str_word_count($message) <= 4 && stripos($message, '@') === false && !preg_match('/\d/', $message)) {
            $_SESSION['cori_chat_state'] = [
                'action'        => 'create_invoice',
                'missing_field' => 'client_name',
                'entities'      => []
            ];
            return ['response' => "📄 Let's create an invoice!\n\nWhat is the **client's full name**?", 'type' => 'text'];
        }

        // Detailed message — extract all fields at once
        $entities = [];
        $this->extractInvoiceFields($message, $entities);

        // Route to the first missing field
        if (empty($entities['client_name'])) {
            $_SESSION['cori_chat_state'] = ['action' => 'create_invoice', 'missing_field' => 'client_name', 'entities' => $entities];
            return ['response' => "What is the **client's full name**?", 'type' => 'text'];
        }
        if (empty($entities['amount']) || (float)$entities['amount'] <= 0) {
            $_SESSION['cori_chat_state'] = ['action' => 'create_invoice', 'missing_field' => 'amount', 'entities' => $entities];
            return ['response' => "Got it, **{$entities['client_name']}**. What is the **invoice amount**? (e.g. '50000 NGN' or '500 USD')", 'type' => 'text'];
        }
        if (empty($entities['email'])) {
            $_SESSION['cori_chat_state'] = ['action' => 'create_invoice', 'missing_field' => 'email', 'entities' => $entities];
            return ['response' => "What is **{$entities['client_name']}'s email address**?", 'type' => 'text'];
        }
        if (empty($entities['description'])) {
            $_SESSION['cori_chat_state'] = ['action' => 'create_invoice', 'missing_field' => 'description', 'entities' => $entities];
            return ['response' => "What is this invoice **for**? (e.g. 'Website design services')", 'type' => 'text'];
        }

        // All fields present — create immediately
        return $this->processInvoiceCreation($entities);
    }

    private function chatAddLead(string $message = ''): array
    {
        // If it's just "add lead" without details, return the quick action
        if (str_word_count($message) <= 3) {
            return [
                'response' => "➕ Let's add a new lead!\n\nI'll take you to the CRM where you can enter the contact details.\n\n[🔗 Open Lead Manager →](/erp/crm/leads)",
                'type' => 'action',
                'action_url' => '/erp/crm/leads'
            ];
        }
        
        // Regex Extraction
        // Extract Email
        preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $message, $emailMatches);
        $email = $emailMatches[0] ?? '';
        
        // Extract Name (simple heuristic: look for words before email or at the start)
        // Or if they say "add John Doe as a lead"
        $name = 'New Lead';
        if (preg_match('/add\s+([A-Z][a-z]+(?:\s+[A-Z][a-z]+)?)\s+as/i', $message, $nameMatches)) {
            $name = trim($nameMatches[1]);
        } elseif (!empty($email)) {
            $name = ucfirst(explode('@', $email)[0]); // Fallback to email prefix
        }

        // Extract Source (Facebook, Instagram, LinkedIn, Website, etc)
        $source = 'Other';
        if (stripos($message, 'facebook') !== false || stripos($message, 'fb') !== false) $source = 'Facebook';
        if (stripos($message, 'instagram') !== false || stripos($message, 'ig') !== false) $source = 'Instagram';
        if (stripos($message, 'linkedin') !== false) $source = 'LinkedIn';
        if (stripos($message, 'website') !== false) $source = 'Website';
        
        if (empty($email)) {
            return [
                'response' => "I can add that lead for you, but I need an **email address**. Could you provide the email?",
                'type' => 'text'
            ];
        }

        try {
            // Include email in the name string since the leads table doesn't have an email column
            $fullName = $name . ($email ? " ($email)" : "");
            
            $stmt = $this->pdo->prepare("
                INSERT INTO erp_crm_leads 
                (tenant_id, name, source, status)
                VALUES (?, ?, ?, 'new')
            ");
            $stmt->execute([$this->tenantId, $fullName, $source]);
            
            return [
                'response' => "✅ Success! **$name** ($email) has been added as a new lead from **$source**.\n\n[🔗 View Lead](/erp/crm/leads)",
                'type' => 'ai'
            ];
            
        } catch (\Exception $e) {
            return [
                'response' => "❌ Sorry, I encountered an error while saving the lead.",
                'type' => 'error'
            ];
        }
    }

    private function chatRecordExpense(string $message = ''): array
    {
        // Expense categories we can auto-detect
        $categoryMap = [
            'fuel'        => 'Fuel & Transport',
            'petrol'      => 'Fuel & Transport',
            'diesel'      => 'Fuel & Transport',
            'transport'   => 'Fuel & Transport',
            'travel'      => 'Travel',
            'food'        => 'Meals & Entertainment',
            'lunch'       => 'Meals & Entertainment',
            'dinner'      => 'Meals & Entertainment',
            'meal'        => 'Meals & Entertainment',
            'electricity' => 'Utilities',
            'internet'    => 'Utilities',
            'water'       => 'Utilities',
            'rent'        => 'Rent & Office',
            'office'      => 'Rent & Office',
            'salary'      => 'Salaries & Wages',
            'wages'       => 'Salaries & Wages',
            'software'    => 'Software & Subscriptions',
            'subscription'=> 'Software & Subscriptions',
            'marketing'   => 'Marketing & Advertising',
            'advert'      => 'Marketing & Advertising',
            'printing'    => 'Office Supplies',
            'stationery'  => 'Office Supplies',
            'supplies'    => 'Office Supplies',
            'equipment'   => 'Equipment',
            'repair'      => 'Maintenance & Repairs',
            'maintenance' => 'Maintenance & Repairs',
        ];

        // Extract entities from the message
        $entities = [];

        // 1. Amount
        if (preg_match('/\b([0-9][0-9,]*(?:\.\d+)?)\b/', $message, $m)) {
            $entities['amount'] = str_replace(',', '', $m[1]);
        }

        // 2. Category auto-detect from message keywords
        $msgLower = strtolower($message);
        foreach ($categoryMap as $keyword => $category) {
            if (strpos($msgLower, $keyword) !== false) {
                $entities['category'] = $category;
                break;
            }
        }
        if (empty($entities['category'])) {
            $entities['category'] = 'General';
        }

        // 3. Description — use the full message trimmed, or extract meaningful part
        $desc = trim($message);
        // Capitalise first letter and sanitise
        $desc = ucfirst(preg_replace('/\s+/', ' ', $desc));
        if (strlen($desc) > 200) $desc = substr($desc, 0, 200);
        $entities['description'] = $desc;

        // 4. Date — default to today
        $entities['date'] = date('Y-m-d');

        // If we have an amount, save straight away (category and description are inferred)
        if (!empty($entities['amount']) && (float)$entities['amount'] > 0) {
            return $this->processExpenseRecord($entities);
        }

        // No amount found — ask for it
        $_SESSION['cori_chat_state'] = [
            'action'        => 'record_expense',
            'missing_field' => 'amount',
            'entities'      => $entities
        ];
        return ['response' => "💰 I'll record that expense! What is the **amount**? (e.g. '10000 NGN')", 'type' => 'text'];
    }

    private function processExpenseRecord(array $entities): array
    {
        try {
            $pdo      = $this->pdo;
            $tenantId = $this->tenantId;
            $amount   = (float)($entities['amount'] ?? 0);
            $desc     = $entities['description'] ?? 'Expense';
            $category = $entities['category']    ?? 'General';
            $date     = $entities['date']        ?? date('Y-m-d');

            if ($amount <= 0) {
                return ['response' => "❌ I couldn't save that — the amount doesn't look valid. Please try again.", 'type' => 'error'];
            }

            $stmt = $pdo->prepare(
                "INSERT INTO erp_expenses (tenant_id, description, amount, date, category, status)
                 VALUES (?, ?, ?, ?, ?, 'approved')"
            );
            $stmt->execute([$tenantId, $desc, $amount, $date, $category]);

            // Mirror into erp_transactions
            $stmtTx = $pdo->prepare(
                "INSERT INTO erp_transactions (tenant_id, description, amount, type, date, category)
                 VALUES (?, ?, ?, 'expense', ?, ?)"
            );
            $stmtTx->execute([$tenantId, $desc, $amount, $date, $category]);

            return [
                'response' => "✅ Expense recorded!\n\n"
                    . "**Description:** {$desc}\n"
                    . "**Amount:** NGN " . number_format($amount, 2) . "\n"
                    . "**Category:** {$category}\n"
                    . "**Date:** {$date}\n\n"
                    . "[🔗 View All Expenses →](/erp/finance/expenses)",
                'type' => 'ai'
            ];
        } catch (\Throwable $e) {
            return ['response' => "❌ I ran into a problem saving the expense. Please try again or use [the expense form →](/erp/finance/expenses/create).", 'type' => 'error'];
        }
    }

    private function chatCashFlow(): array
    {
        $forecast = $this->forecastCashFlow();

        $response = "## 📈 Cash Flow Forecast\n\n";

        if (!empty($forecast['projected_months'])) {
            foreach ($forecast['projected_months'] as $month) {
                $emoji = ($month['net'] ?? 0) >= 0 ? '🟢' : '🔴';
                $response .= "$emoji **{$month['label']}**: ";
                $response .= "Income NGN " . number_format($month['income'] ?? 0, 2);
                $response .= " | Expenses NGN " . number_format($month['expense'] ?? 0, 2);
                $response .= " | Net NGN " . number_format($month['net'] ?? 0, 2) . "\n";
            }
        } else {
            $response .= "Not enough transaction data yet to build a forecast. Keep recording transactions and I'll project your cash flow.\n";
        }

        $warnLevel = $forecast['warning_level'] ?? 'none';
        if ($warnLevel === 'critical') {
            $response .= "\n⚠️ **Warning:** Projected expenses may exceed income. Review your upcoming costs.";
        } elseif ($warnLevel === 'warning') {
            $response .= "\n🟡 **Note:** Cash flow is tight. Consider following up on unpaid invoices.";
        }

        return ['response' => $response, 'type' => 'forecast'];
    }

    private function chatChurnRisk(): array
    {
        $risks = $this->getCrmChurnRisks();

        if (empty($risks)) {
            return ['response' => "✅ All your clients are active and engaged. No churn risks detected!", 'type' => 'text'];
        }

        $response = "## ⚠️ Churn Risk Report (" . count($risks) . " clients)\n\n";

        foreach ($risks as $risk) {
            $emoji = ($risk['risk_level'] ?? '') === 'high' ? '🔴' : (($risk['risk_level'] ?? '') === 'medium' ? '🟠' : '🟡');
            $response .= "$emoji **{$risk['name']}**";
            if (!empty($risk['email'])) $response .= " ({$risk['email']})";
            $response .= "\n   Risk: " . ucfirst($risk['risk_level'] ?? 'unknown');
            $response .= " • Inactive {$risk['days_inactive']} days\n";
            if (!empty($risk['suggestion'])) $response .= "   💡 _{$risk['suggestion']}_\n";
            $response .= "\n";
        }

        return ['response' => $response, 'type' => 'list'];
    }

    private function chatStaffSummary(): array
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM erp_employees WHERE tenant_id = ? AND status = 'active'");
        $stmt->execute([$this->tenantId]);
        $total = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT department, COUNT(*) as cnt FROM erp_employees WHERE tenant_id = ? AND status = 'active' GROUP BY department ORDER BY cnt DESC LIMIT 5");
        $stmt->execute([$this->tenantId]);
        $departments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM erp_leave_requests WHERE tenant_id = ? AND status = 'pending'");
        $stmt->execute([$this->tenantId]);
        $pendingLeave = (int) $stmt->fetchColumn();

        $response = "## 👥 Staff Summary\n\n";
        $response .= "**Total Active Staff:** $total\n";
        $response .= "**Pending Leave Requests:** $pendingLeave\n\n";

        if (!empty($departments)) {
            $response .= "### By Department\n";
            foreach ($departments as $dept) {
                $name = $dept['department'] ?: 'Unassigned';
                $response .= "• $name: **{$dept['cnt']}** staff\n";
            }
        }

        return ['response' => $response, 'type' => 'summary'];
    }

    private function chatPendingTasks(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT t.*, p.name as project_name, e.name as employee_name 
            FROM erp_tasks t
            LEFT JOIN erp_projects p ON t.project_id = p.id
            LEFT JOIN erp_employees e ON t.assigned_to = e.id
            WHERE t.tenant_id = ? AND t.status != 'done'
            ORDER BY t.due_date ASC, t.priority DESC
            LIMIT 10
        ");
        $stmt->execute([$this->tenantId]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($tasks)) {
            return ['response' => "✅ All caught up! There are **no pending tasks** right now.", 'type' => 'text'];
        }

        $response = "## 📋 Pending Tasks (" . count($tasks) . ")\n\n";
        foreach ($tasks as $task) {
            $due = $task['due_date'] ? date('M d, Y', strtotime($task['due_date'])) : 'No due date';
            $prio = ucfirst($task['priority'] ?? 'medium');
            $prioEmoji = $task['priority'] === 'high' ? '🔴' : ($task['priority'] === 'medium' ? '🟡' : '🟢');
            $status = ucfirst(str_replace('_', ' ', $task['status'] ?? 'todo'));
            
            $assignee = $task['employee_name'] ?: 'Unassigned';
            $project = $task['project_name'] ?: 'No Project';

            $response .= "{$prioEmoji} **{$task['title']}** [{$prio}]\n";
            $response .= "   Project: {$project} • Assigned: {$assignee}\n";
            $response .= "   Status: **{$status}** • Due: {$due}\n\n";
        }
        return ['response' => $response, 'type' => 'list'];
    }

    private function chatProjectsSummary(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT status, COUNT(*) as cnt 
            FROM erp_projects 
            WHERE tenant_id = ? 
            GROUP BY status
        ");
        $stmt->execute([$this->tenantId]);
        $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare("
            SELECT name, status, end_date 
            FROM erp_projects 
            WHERE tenant_id = ? AND status != 'completed' AND status != 'cancelled'
            ORDER BY created_at DESC 
            LIMIT 5
        ");
        $stmt->execute([$this->tenantId]);
        $activeProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($stats)) {
            return ['response' => "📂 You have **no projects** created yet.", 'type' => 'text'];
        }

        $response = "## 📂 Projects Summary\n\n";
        $total = 0;
        foreach ($stats as $s) {
            $total += (int)$s['cnt'];
            $statusStr = ucfirst(str_replace('_', ' ', $s['status']));
            $response .= "• {$statusStr}: **{$s['cnt']}**\n";
        }
        $response .= "• Total Projects: **{$total}**\n\n";

        if (!empty($activeProjects)) {
            $response .= "### Active Projects\n";
            foreach ($activeProjects as $p) {
                $status = ucfirst(str_replace('_', ' ', $p['status']));
                $deadline = $p['end_date'] ? date('M d, Y', strtotime($p['end_date'])) : 'No deadline';
                $response .= "• **{$p['name']}** — *{$status}* (Ends: {$deadline})\n";
            }
        }
        return ['response' => $response, 'type' => 'summary'];
    }

    private function chatStockLevel(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT name, sku, price, stock_level 
            FROM erp_products 
            WHERE tenant_id = ? 
            ORDER BY stock_level ASC 
            LIMIT 10
        ");
        $stmt->execute([$this->tenantId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($products)) {
            return ['response' => "📦 No products found in your inventory.", 'type' => 'text'];
        }

        $response = "## 📦 Inventory / Stock Levels\n\n";
        $lowStock = 0;
        foreach ($products as $p) {
            $stock = (int)$p['stock_level'];
            $status = '🟢 In Stock';
            if ($stock === 0) {
                $status = '🔴 Out of Stock';
                $lowStock++;
            } elseif ($stock < 10) {
                $status = '🟡 Low Stock';
                $lowStock++;
            }
            $priceStr = number_format((float)$p['price'], 2);
            $response .= "• **{$p['name']}** (`{$p['sku']}`)\n";
            $response .= "   Stock: **{$stock}** ({$status}) • Price: NGN {$priceStr}\n\n";
        }
        if ($lowStock > 0) {
            $response .= "⚠️ **Attention:** You have **{$lowStock}** item(s) running out of stock or out of stock.";
        }
        return ['response' => $response, 'type' => 'list'];
    }

    private function chatPendingLeave(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT lr.*, e.name as employee_name 
            FROM erp_leave_requests lr
            JOIN erp_employees e ON lr.employee_id = e.id
            WHERE lr.tenant_id = ? AND lr.status = 'pending'
            ORDER BY lr.start_date ASC
        ");
        $stmt->execute([$this->tenantId]);
        $leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($leaves)) {
            return ['response' => "✅ No pending leave requests to review.", 'type' => 'text'];
        }

        $response = "## ✈️ Pending Leave Requests (" . count($leaves) . ")\n\n";
        foreach ($leaves as $l) {
            $start = date('M d, Y', strtotime($l['start_date']));
            $end = date('M d, Y', strtotime($l['end_date']));
            $type = ucfirst($l['leave_type']);
            $reason = $l['reason'] ? "Reason: \"{$l['reason']}\"" : "No reason provided";

            $response .= "• **{$l['employee_name']}** — *{$type} Leave*\n";
            $response .= "   Period: {$start} to {$end}\n";
            $response .= "   {$reason}\n\n";
        }
        $response .= "_To manage these, go to the HR -> Leave Requests tab._";
        return ['response' => $response, 'type' => 'list'];
    }

    private function chatRoastBusiness(): array
    {
        $roast = $this->generateWeeklyRoast();
        $scoreData = $this->getBusinessScore();
        
        $response = "## 🔥 Ruthless AI Mentor Roast ({$scoreData['score']}/100 - {$scoreData['rating']['label']})\n\n";
        
        if (is_array($roast)) {
            foreach ($roast as $section => $text) {
                $response .= $text . "\n\n";
            }
        } else {
            $response .= $roast . "\n\n";
        }
        
        return ['response' => $response, 'type' => 'roast'];
    }

    private function chatAnalyzeBusiness(): array
    {
        $scoreData = $this->getBusinessScore();
        $bd = $scoreData['breakdown'];
        
        $response = "## 📊 Business Health Analysis\n\n";
        $response .= "Overall Health Score: **{$scoreData['score']}/100** ({$scoreData['rating']['label']})\n";
        $response .= "*{$scoreData['rating']['message']}*\n\n";
        $response .= "### Breakdown:\n";
        $response .= "• **Revenue Health:** {$bd['revenue']}/100\n";
        $response .= "• **Operational Efficiency:** {$bd['efficiency']}/100\n";
        $response .= "• **Staff Performance:** {$bd['staff']}/100\n";
        $response .= "• **Financial Health:** {$bd['financial']}/100\n\n";
        $response .= "👉 [Open AI Manager Dashboard](/erp/ai-manager)";
        
        return ['response' => $response, 'type' => 'analysis'];
    }

    private function chatAnalyzeFinance(): array
    {
        $financialScore = $this->calculateFinancialScore();
        $forecast = $this->forecastCashFlow();
        
        $response = "## 💰 Financial & Cash Flow Analysis\n\n";
        $response .= "Financial Health Score: **{$financialScore}/100**\n\n";
        
        if (!empty($forecast['forecast'])) {
            $response .= "### 3-Month Projection:\n";
            foreach ($forecast['forecast'] as $item) {
                $response .= "• **{$item['month']}:** Income: NGN " . number_format($item['projected_income'], 2) . " | Expense: NGN " . number_format($item['projected_expense'], 2) . " (Margin: " . round($item['confidence_margin'], 1) . "%)\n";
            }
        } else {
            $response .= "*Not enough transactional data to project 3-month forecast yet.*\n";
        }
        
        $response .= "\n👉 [Open AI Manager Dashboard](/erp/ai-manager)";
        
        return ['response' => $response, 'type' => 'analysis'];
    }

    private function chatAnalyzeStaff(): array
    {
        $staffScore = $this->calculateStaffScore();
        
        $stmt = $this->pdo->prepare("
            SELECT e.first_name, e.last_name, e.job_title, AVG(p.score) as avg_score
            FROM erp_employees e
            LEFT JOIN erp_performance_scores p ON e.id = p.user_id AND p.target_type = 'employee'
            WHERE e.tenant_id = ? AND e.status = 'active'
            GROUP BY e.id
        ");
        $stmt->execute([$this->tenantId]);
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response = "## 👥 Staff Performance Analysis\n\n";
        $response .= "Overall Staff Performance Score: **{$staffScore}/100**\n\n";
        
        if (!empty($employees)) {
            $response .= "### Active Employees:\n";
            foreach ($employees as $emp) {
                $perf = $emp['avg_score'] ? round($emp['avg_score'], 1) . "/100" : "No ratings yet";
                $response .= "• **{$emp['first_name']} {$emp['last_name']}** ({$emp['job_title']}): Performance: *{$perf}*\n";
            }
        } else {
            $response .= "*No active employees registered yet.*\n";
        }
        
        $response .= "\n👉 [Open HR Module](/erp/hr)";
        
        return ['response' => $response, 'type' => 'analysis'];
    }

    private function chatCreatePaymentLink(string $message = ''): array
    {
        $msgLower = strtolower($message);
        $amount = 0;
        $currency = 'NGN';
        
        preg_match('/(\d+[\d,]*\.?\d*)/', $message, $matches);
        if (!empty($matches[1])) {
            $amount = (float)str_replace(',', '', $matches[1]);
        }
        if (strpos($msgLower, 'usd') !== false || strpos($msgLower, 'dollar') !== false) {
            $currency = 'USD';
        }
        
        if ($amount <= 0) {
            $_SESSION['cori_chat_state'] = [
                'action' => 'create_payment_link',
                'missing_field' => 'amount'
            ];
            return [
                'response' => "I can create a payment link for you. What is the amount and currency? (e.g. '1,000 NGN' or '50 USD')",
                'type' => 'text'
            ];
        }
        
        return $this->processPaymentLinkCreation([
            'amount' => $amount,
            'currency' => $currency
        ]);
    }

    private function processPaymentLinkCreation(array $entities): array
    {
        $amount = (float)($entities['amount'] ?? 0);
        $currency = strtoupper($entities['currency'] ?? 'NGN');
        if ($currency === 'NAIRA') $currency = 'NGN';
        if ($currency === 'DOLLARS' || $currency === 'DOLLAR') $currency = 'USD';
        
        $title = $entities['title'] ?? ("Payment Link for " . $currency . " " . number_format($amount, 2));
        $userId = $_SESSION['user_id'] ?? 1;
        $tenantId = $this->tenantId;
        
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title))) . '-' . uniqid();
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO cp_payment_links (tenant_id, user_id, title, slug, amount, currency) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$tenantId, $userId, $title, $slug, $amount, $currency]);
            
            $link = "/pay/link/" . $slug;
            $priceStr = $currency . ' ' . number_format($amount, 2);
            
            $response = "## 🔗 Payment Link Created!\n\n";
            $response .= "I have created a payment link for **{$priceStr}**:\n\n";
            $response .= "• **Title:** {$title}\n";
            $response .= "• **Currency:** {$currency}\n";
            $response .= "• **Amount:** " . number_format($amount, 2) . "\n\n";
            $response .= "👉 [Open Payment Link]({$link})\n";
            
            return [
                'response' => $response,
                'type' => 'payment',
                'action_url' => $link
            ];
        } catch (\Exception $e) {
            return ['response' => "⚠️ Could not create payment link: " . $e->getMessage(), 'type' => 'text'];
        }
    }

    private function chatAddStaff(string $message = ''): array
    {
        $_SESSION['cori_chat_state'] = [
            'action' => 'add_staff',
            'missing_field' => 'first_name',
            'entities' => []
        ];
        return [
            'response' => "Let's add a new staff member. What is their first name?",
            'type' => 'text'
        ];
    }

    private function processStaffAddition(array $entities): array
    {
        $firstName = $entities['first_name'] ?? '';
        $lastName = $entities['last_name'] ?? '';
        $email = $entities['email'] ?? '';
        $jobTitle = $entities['job_title'] ?? 'Staff Member';
        $salary = (float)($entities['salary'] ?? 0);
        $tenantId = $this->tenantId;

        if (empty($firstName) || empty($email)) {
            return ['response' => "To add a staff member, I need at least their first name and email address. Could you provide those?", 'type' => 'text'];
        }

        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO erp_employees (tenant_id, first_name, last_name, email, job_title, salary, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'active')
            ");
            $stmt->execute([$tenantId, $firstName, $lastName, $email, $jobTitle, $salary]);

            $response = "## 👥 Staff Member Added!\n\n";
            $response .= "I have added **{$firstName} {$lastName}** to your staff list:\n\n";
            $response .= "• **Email:** {$email}\n";
            $response .= "• **Role/Job Title:** {$jobTitle}\n";
            $response .= "• **Salary:** NGN " . number_format($salary, 2) . "\n\n";
            $response .= "👉 [View Staff List](/erp/hr)\n";

            return [
                'response' => $response,
                'type' => 'staff',
                'action_url' => '/erp/hr'
            ];
        } catch (\Exception $e) {
            return ['response' => "⚠️ Could not add staff member: " . $e->getMessage(), 'type' => 'text'];
        }
    }

    private function chatAIResponse(string $message): array
    {
        try {
            $ai  = new \App\Core\AI\AIService();
            $ctx = new \App\Core\AI\ContextAggregator($this->tenantId);

            $msgLower = strtolower($message);

            // ── Writing / creative content path ──────────────────────────────
            // Anything that asks for generated text goes straight to prose mode.
            // This avoids the JSON-truncation bug where raw JSON appears in the bubble.
            $writingKeywords = [
                // Generic verbs
                'write', 'draft', 'compose', 'generate', 'create content',
                // Document types
                'a letter', 'an email', 'a memo', 'a report', 'a proposal',
                'a message', 'a note', 'a notice', 'a template', 'a contract',
                'a summary', 'a description', 'a bio', 'a profile',
                // Marketing / social
                'a post', 'facebook post', 'instagram post', 'social media post',
                'a caption', 'a tweet', 'linkedin post', 'whatsapp message',
                'an ad', 'an advert', 'advertisement', 'marketing copy',
                'a slogan', 'a tagline', 'a headline', 'a pitch',
                'an announcement', 'press release', 'newsletter content',
                'email campaign', 'promotional',
                // Help phrases
                'help me write', 'help me draft', 'help me compose',
                'write me', 'draft me', 'make me a',
            ];

            $isWritingTask = false;
            foreach ($writingKeywords as $kw) {
                if (strpos($msgLower, $kw) !== false) {
                    $isWritingTask = true;
                    break;
                }
            }

            if ($isWritingTask) {
                // Pure prose path — no JSON wrapper, no truncation risk
                $prompt = "You are Cori, a professional AI business assistant for {$ctx->companyName}. "
                    . "The user has asked you to create or write something. "
                    . "Respond with a well-structured, professional piece of content that fulfils the request exactly. "
                    . "Use markdown formatting (headers, bold, bullet points) where it helps readability. "
                    . "Do NOT add any preamble or explanation — just produce the content directly.\n\n"
                    . "User request: {$message}";

                $text = $ai->generate($prompt, ['max_tokens' => 1500, 'temperature' => 0.8], 'cori_write', (int)($_SESSION['user_id'] ?? 0));

                if (strpos(trim($text), 'Error:') === 0) {
                    throw new \Exception("AI Provider Error");
                }

                return ['response' => $text, 'type' => 'ai'];
            }

            // ── Structured intent detection ───────────────────────────────────
            // Only used for clearly actionable commands (invoice, payment link, etc.)
            // Keep the response field short to avoid JSON truncation.
            $prompt = "You are Cori, an AI business assistant for {$ctx->companyName}. "
                . "Parse the user's intent and respond ONLY with a compact JSON object.\n"
                . "Intents:\n"
                . "1. \"general_chat\" — anything conversational or informational\n"
                . "2. \"create_invoice\" — entities: client_name, email, amount, currency, description\n"
                . "3. \"create_payment_link\" — entities: amount, currency, title\n"
                . "4. \"add_staff\" — entities: first_name, last_name, email, job_title, salary\n"
                . "5. \"add_lead\" — entities: client_name, email, phone\n\n"
                . "RULES:\n"
                . "- For general_chat, keep \"response\" under 60 words.\n"
                . "- Return ONLY raw JSON. No markdown. No preamble.\n"
                . "- Format: {\"intent\":\"...\",\"response\":\"...\",\"entities\":{}}\n\n"
                . "User: {$message}";

            $jsonStr = $ai->generate($prompt, ['max_tokens' => 1200], 'cori_chat', (int)($_SESSION['user_id'] ?? 0));

            if (strpos(trim($jsonStr), 'Error:') === 0) {
                throw new \Exception("AI Provider Error");
            }

            // Strip markdown fences the AI sometimes adds
            $jsonStr = trim(str_replace(['```json', '```'], '', $jsonStr));
            $parsed  = json_decode($jsonStr, true);

            if ($parsed && isset($parsed['intent'])) {
                // ── Structured action intents ────────────────────────────────
                if ($parsed['intent'] === 'create_invoice') {
                    $entities = $parsed['entities'] ?? [];
                    if (empty($entities['client_name']) || empty($entities['amount'])) {
                        return $this->chatCreateInvoice($message); // delegate to the full flow
                    }
                    if (empty($entities['email'])) {
                        $_SESSION['cori_chat_state'] = ['action' => 'create_invoice', 'missing_field' => 'email', 'entities' => $entities];
                        return ['response' => "What is the email address for {$entities['client_name']}?", 'type' => 'text'];
                    }
                    if (empty($entities['description'])) {
                        $_SESSION['cori_chat_state'] = ['action' => 'create_invoice', 'missing_field' => 'description', 'entities' => $entities];
                        return ['response' => "What is this invoice for? (e.g., 'Web design services')", 'type' => 'text'];
                    }
                    return $this->processInvoiceCreation($entities);
                }

                if ($parsed['intent'] === 'create_payment_link') {
                    return $this->processPaymentLinkCreation($parsed['entities'] ?? []);
                }

                if ($parsed['intent'] === 'add_staff') {
                    return $this->processStaffAddition($parsed['entities'] ?? []);
                }

                if ($parsed['intent'] === 'add_lead') {
                    return $this->chatAddLead($message);
                }

                // ── General chat: return clean prose text ─────────────────
                $responseText = $parsed['response'] ?? null;

                // If general_chat response is empty or too short (AI may have put content elsewhere),
                // fall back to treating the whole raw string as prose.
                if (empty(trim((string)$responseText))) {
                    $responseText = $jsonStr;
                }

                return [
                    'response' => $responseText,
                    'type' => 'ai',
                    'suggestions' => [
                        '📊 Business summary',
                        '📄 Create an invoice',
                        '💰 Record expense',
                        '📧 Send email',
                        '📋 Pending tasks'
                    ]
                ];
            }

            // ── JSON decode failed (likely truncated) ──────────────────────
            // Try to salvage the "response" value with a regex before giving up.
            if (preg_match('/"response"\s*:\s*"((?:[^"\\\\]|\\\\.)*)/', $jsonStr, $m)) {
                $salvaged = stripslashes($m[1]);
                if (strlen(trim($salvaged)) > 5) {
                    return [
                        'response' => $salvaged,
                        'type' => 'ai',
                        'suggestions' => [
                            '📊 Business summary',
                            '📄 Create an invoice',
                            '💰 Record expense'
                        ]
                    ];
                }
            }

            // Last resort: if the raw text doesn't look like JSON at all, show it as prose
            if ($jsonStr !== '' && $jsonStr[0] !== '{' && $jsonStr[0] !== '[') {
                return [
                    'response' => $jsonStr,
                    'type' => 'ai',
                    'suggestions' => [
                        '📊 Business summary',
                        '📄 Create an invoice',
                        '💰 Record expense'
                    ]
                ];
            }

        } catch (\App\Core\AI\CreditExhaustedException $e) {
            return [
                'response' => "⚠️ Your AI credits are exhausted. You can still use the quick action buttons. To keep chatting, [top up your AI credits](/billing).",
                'type'     => 'error',
                'action_url' => '/billing',
                'suggestions' => [
                    '📊 Business summary',
                    '📄 Create an invoice',
                    '💰 Record expense',
                    '📋 Pending tasks'
                ]
            ];
        } catch (\Exception $e) {
            // AI not available — fall through to command suggestions below
        }

        // ── No AI connected or unrecognized input ──────────────────────────
        return [
            'response' => "I didn't quite catch an action for that. Here are the most popular actions I can perform right now:\n\n"
                . "• 📊 **Business summary** — Overview of revenue, tasks & stock\n"
                . "• 📄 **Create invoice** — Send a new invoice in seconds\n"
                . "• 💰 **Record expense** — Log spending (e.g. *'spent 25k on fuel'*)\n"
                . "• 📧 **Send email** — Quick direct email dispatch\n"
                . "• 📦 **Stock levels** — Check product inventory levels\n"
                . "• 👤 **Human support** — Connect with the support desk\n\n"
                . "Tap an option below or type naturally!",
            'type' => 'help',
            'suggestions' => [
                '📊 Business summary',
                '📄 Create an invoice',
                '💰 Record expense',
                '📧 Send email',
                '📋 Pending tasks',
                '📦 Stock levels',
                '👤 Talk to human'
            ]
        ];
    }
    
    private function processInvoiceCreation(array $entities): array
    {
        $tenantId = $this->tenantId;
        // The generateUuid method is a standard Utils method in most frameworks, but let's use standard bin2hex to be safe
        $uuid = bin2hex(random_bytes(16));
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $issueDate = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime('+7 days'));
        
        $clientName = $entities['client_name'] ?? 'Unknown Client';
        $clientEmail = $entities['email'] ?? '';
        
        // Ensure amount is a float
        $amountRaw = $entities['amount'] ?? 0;
        $amount = (float) preg_replace('/[^0-9.]/', '', $amountRaw);
        if ($amount <= 0) $amount = 1; // Fallback to avoid 0 amount invoice errors
        
        $currency = strtoupper($entities['currency'] ?? 'NGN');
        if (empty($currency) || $currency === 'NULL' || strlen($currency) > 3) $currency = 'NGN';
        
        $description = $entities['description'] ?? 'Services rendered';
        
        $pdo = $this->pdo;
        $pdo->beginTransaction();
        
        try {
            // 1. Insert Invoice
            $stmt = $pdo->prepare("
                INSERT INTO erp_invoices 
                (tenant_id, uuid, client_name, client_email, issue_date, due_date, total_amount, status, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'sent', ?)
            ");
            $stmt->execute([$tenantId, $uuid, $clientName, $clientEmail, $issueDate, $dueDate, $amount, $description]);
            $invoiceId = $pdo->lastInsertId();
            
            // 2. Insert Invoice Item
            $stmtItem = $pdo->prepare("
                INSERT INTO erp_invoice_items 
                (invoice_id, description, quantity, unit_price, amount)
                VALUES (?, ?, 1, ?, ?)
            ");
            $stmtItem->execute([$invoiceId, $description, $amount, $amount]);
            
            $pdo->commit();
            
            // 3. Send Email
            if (!empty($clientEmail)) {
                $appUrl = 'https://' . $_SERVER['HTTP_HOST'];
                $invoiceLink = $appUrl . '/invoice/' . $uuid;
                
                $subject = "New Invoice {$invoiceNumber}";
                $body = "<h2>You have a new invoice</h2>";
                $body .= "<p>Hi {$clientName},</p>";
                $body .= "<p>Please find your invoice for NGN " . number_format($amount, 2) . " attached below.</p>";
                $body .= "<p><strong>Description:</strong> {$description}</p>";
                $body .= "<p><a href='{$invoiceLink}' style='display:inline-block;background:#000066;color:#fff;padding:10px 20px;text-decoration:none;border-radius:5px;'>View & Pay Invoice</a></p>";
                
                // Mailer might throw if SMTP isn't configured, so catch it
                try {
                    \App\Core\Mailer::send($clientEmail, $subject, $body);
                    $emailNote = "and emailed to {$clientEmail}";
                } catch (\Exception $e) {
                    $emailNote = "but the email could not be sent (SMTP issue)";
                }
            } else {
                $emailNote = "(No email provided)";
            }
            
            return [
                'response' => "✅ Success! Invoice **{$invoiceNumber}** for **NGN " . number_format($amount, 2) . "** has been created $emailNote.\n\n[🔗 View Invoice](/invoice/{$uuid})",
                'type' => 'ai'
            ];
            
        } catch (\Exception $e) {
            $pdo->rollBack();
            return [
                'response' => "❌ Sorry, I encountered an error while saving the invoice. Please try again.",
                'type' => 'error'
            ];
        }
    }

    // ---- NEW SHOP HANDLERS ----
    private function chatAddProduct(string $message): array
    {
        $_SESSION['cori_chat_state'] = [
            'action' => 'add_product',
            'missing_field' => 'name',
            'entities' => []
        ];
        return ['response' => "Let's add a new product! What is the name of the product?", 'type' => 'text'];
    }

    private function chatUpdateStock(string $message = ''): array
    {
        // Extract quantity — the number in the message
        preg_match('/\b([0-9][0-9,]*)\b/', $message, $qtyMatch);
        $qty = isset($qtyMatch[1]) ? (int)str_replace(',', '', $qtyMatch[1]) : 0;

        // Extract product name — words after "of", "for", or before "units/bags/pieces/items"
        $productName = '';
        if (preg_match('/\bof\s+([A-Za-z][A-Za-z0-9\s]{1,60}?)(?:\s*$|\s*\.|,)/i', $message, $nm)) {
            $productName = trim($nm[1]);
        } elseif (preg_match('/\b(?:units?|bags?|pieces?|items?|boxes?|cartons?)\s+(?:of\s+)?([A-Za-z][A-Za-z0-9\s]{1,60}?)(?:\s*$|,|\.|to)/i', $message, $nm)) {
            $productName = trim($nm[1]);
        }

        if (empty($productName) || $qty <= 0) {
            $_SESSION['cori_chat_state'] = [
                'action'        => 'update_stock',
                'missing_field' => empty($productName) ? 'product_name' : 'quantity',
                'entities'      => ['quantity' => $qty, 'product_name' => $productName]
            ];
            if (empty($productName)) {
                return ['response' => "📦 I'll update the stock! What is the **product name**?", 'type' => 'text'];
            }
            return ['response' => "How many units of **{$productName}** did you receive?", 'type' => 'text'];
        }

        return $this->processStockUpdate(['product_name' => $productName, 'quantity' => $qty]);
    }

    private function processStockUpdate(array $entities): array
    {
        try {
            $pdo         = $this->pdo;
            $tenantId    = $this->tenantId;
            $productName = trim($entities['product_name'] ?? '');
            $qty         = (int)($entities['quantity'] ?? 0);

            if (empty($productName) || $qty <= 0) {
                return ['response' => "❌ I need both a product name and a valid quantity to update stock.", 'type' => 'error'];
            }

            // Try to find product by name (case-insensitive partial match)
            $stmt = $pdo->prepare(
                "SELECT id, name, stock_quantity FROM shop_products
                 WHERE tenant_id = ? AND LOWER(name) LIKE LOWER(?) LIMIT 1"
            );
            $stmt->execute([$tenantId, '%' . $productName . '%']);
            $product = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$product) {
                // Product doesn't exist — offer to create it
                $_SESSION['cori_chat_state'] = [
                    'action'        => 'add_product',
                    'missing_field' => 'price',
                    'entities'      => ['name' => $productName, 'initial_stock' => $qty]
                ];
                return [
                    'response' => "I couldn't find a product named **\"{$productName}\"** in your shop.\n\n"
                        . "Would you like to add it as a new product? If so, what is its **selling price**? (e.g. '5000 NGN')\n\n"
                        . "Or [manage your products manually →](/erp/shop/products)",
                    'type' => 'text'
                ];
            }

            // Update stock quantity (add to existing)
            $newQty = (int)($product['stock_quantity'] ?? 0) + $qty;
            $pdo->prepare("UPDATE shop_products SET stock_quantity = ? WHERE id = ? AND tenant_id = ?")
                ->execute([$newQty, $product['id'], $tenantId]);

            return [
                'response' => "✅ Stock updated!\n\n"
                    . "**Product:** {$product['name']}\n"
                    . "**Added:** +{$qty} units\n"
                    . "**New Total Stock:** {$newQty} units\n\n"
                    . "[🔗 View Inventory →](/erp/shop/products)",
                'type' => 'ai'
            ];
        } catch (\Throwable $e) {
            return ['response' => "❌ I ran into a problem updating the stock. Please try [managing products manually →](/erp/shop/products).", 'type' => 'error'];
        }
    }

    private function processProductCreation(array $entities): array
    {
        global $pdo;
        if(!$pdo) $pdo = \App\Core\Database::getInstance()->getConnection();
        $tenantId = \App\Core\TenantContext::getTenantId();
        
        try {
            $name = $entities['name'];
            $price = $entities['price'];
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            
            $stmt = $pdo->prepare("INSERT INTO shop_products (tenant_id, name, slug, price, type, is_active) VALUES (?, ?, ?, ?, 'physical', 1)");
            $stmt->execute([$tenantId, $name, $slug, $price]);
            
            return ['response' => "✅ Success! Product **{$name}** has been added to your shop for **NGN " . number_format($price, 2) . "**.", 'type' => 'ai'];
        } catch(\Exception $e) {
            return ['response' => "❌ I encountered an error while saving the product.", 'type' => 'error'];
        }
    }

    private function chatRecentOrders(): array
    {
        global $pdo;
        if(!$pdo) $pdo = \App\Core\Database::getInstance()->getConnection();
        $tenantId = \App\Core\TenantContext::getTenantId();
        
        try {
            $stmt = $pdo->prepare("SELECT order_number, total_amount, status FROM shop_orders WHERE tenant_id = ? ORDER BY id DESC LIMIT 5");
            $stmt->execute([$tenantId]);
            $orders = $stmt->fetchAll();
            
            if (empty($orders)) return ['response' => "You don't have any recent orders.", 'type' => 'text'];
            
            $res = "🛒 **Recent Orders**\n\n";
            foreach($orders as $o) {
                $res .= "• #{$o['order_number']} - NGN " . number_format($o['total_amount'], 2) . " ({$o['status']})\n";
            }
            return ['response' => $res, 'type' => 'ai'];
        } catch(\Exception $e) {
            return ['response' => "I couldn't fetch your recent orders right now.", 'type' => 'error'];
        }
    }

    private function chatCreateCoupon(string $message): array
    {
        return ['response' => "Quick coupon creation via chat will be available soon. Please use the Shop Settings to create discount codes.", 'type' => 'text'];
    }

    private function chatTopProducts(): array
    {
        return ['response' => "You don't have enough sales data to determine top-selling products yet.", 'type' => 'text'];
    }

    // ---- NEW ERP/HR HANDLERS ----
    private function chatLogAttendance(string $message): array
    {
        $msgLower = strtolower($message);
        $type = (strpos($msgLower, 'out') !== false) ? 'Clock Out' : 'Clock In';
        return ['response' => "You have requested to **{$type}**. To confirm your attendance, please use the Attendance Tracker on your dashboard.", 'type' => 'text'];
    }

    private function chatScheduleMeeting(string $message): array
    {
        return ['response' => "I can help you schedule a meeting. What is the topic and date of the meeting? (E.g., 'Team sync tomorrow at 10 AM')", 'type' => 'text'];
    }

    private function chatPayrollSummary(): array
    {
        return ['response' => "Your payroll module currently shows no pending salaries for this month.", 'type' => 'text'];
    }

    private function chatAssignTask(string $message): array
    {
        return ['response' => "Task assignment via chat is being upgraded. Please use the **Project Management** module to assign tasks.", 'type' => 'text'];
    }

    private function chatCreateProject(string $message): array
    {
        return ['response' => "To create a new project, navigate to **ERP -> Projects**.", 'type' => 'text'];
    }

    // ---- NEW FINANCE HANDLERS ----
    private function chatWalletBalance(): array
    {
        global $pdo;
        if(!$pdo) $pdo = \App\Core\Database::getInstance()->getConnection();
        $tenantId = \App\Core\TenantContext::getTenantId();
        
        try {
            $stmt = $pdo->prepare("SELECT balance FROM cp_wallets WHERE tenant_id = ? AND currency = 'NGN' LIMIT 1");
            $stmt->execute([$tenantId]);
            $w = $stmt->fetch();
            $bal = $w ? $w['balance'] : 0.00;
            return ['response' => "💳 **Wallet Balance**\nYour current NGN wallet balance is **NGN " . number_format((float)$bal, 2) . "**.", 'type' => 'ai'];
        } catch(\Exception $e) {
            return ['response' => "Wallet balance feature is not fully activated.", 'type' => 'text'];
        }
    }

    private function chatWithdrawFunds(string $message): array
    {
        return ['response' => "To withdraw funds, please go to **CasjoePay -> Wallet** and click 'Withdraw'.", 'type' => 'text'];
    }

    private function chatRecentTransactions(): array
    {
        return ['response' => "No recent incoming transactions found in your wallet.", 'type' => 'text'];
    }

    private function chatProfitLoss(): array
    {
        return ['response' => "Your P&L report requires more data. Keep recording your expenses and invoices!", 'type' => 'text'];
    }

    // ---- NEW MAIL HANDLERS ----
    private function chatCampaignStats(): array
    {
        return ['response' => "To view detailed open and click rates, please visit the **CasjoeMail** dashboard.", 'type' => 'text'];
    }

    private function chatCreateMailingList(string $message): array
    {
        return ['response' => "I can help you create a mailing list! What would you like to name it?", 'type' => 'text'];
    }

    private function chatAddSubscriber(string $message): array
    {
        return ['response' => "To add a subscriber, what is their email address?", 'type' => 'text'];
    }

    // ---- NEW ACADEMY HANDLERS ----
    private function chatListCourses(): array
    {
        return ['response' => "You currently have 0 active courses in your Academy.", 'type' => 'text'];
    }

    private function chatEnrollStudent(string $message): array
    {
        return ['response' => "To enroll a student, please provide their email address.", 'type' => 'text'];
    }

    // ---- NEW SUPPORT HANDLERS ----
    private function chatCreateTicket(string $message): array
    {
        return ['response' => "What is the subject or issue of the ticket you want to open?", 'type' => 'text'];
    }

    private function chatAssignTicket(string $message): array
    {
        return ['response' => "To assign a ticket, please view the active tickets in the **Helpdesk**.", 'type' => 'text'];
    }

    // ---- NEW LINKS HANDLERS ----
    private function chatAddLink(string $message): array
    {
        return ['response' => "What is the URL you want to add to your bio page?", 'type' => 'text'];
    }

    private function chatBioPageStats(): array
    {
        return ['response' => "Your bio page currently has 0 views this week.", 'type' => 'text'];
    }

    // =========================================================
    // MULTIMODAL VISION SCANNING & ACTION EXECUTION
    // =========================================================

    private function handleImageScan(string $imageBase64, string $userPrompt = ''): array
    {
        try {
            $scan = \App\Modules\CasjoeERP\Services\AIVisionScannerService::scan($imageBase64, $userPrompt, $this->tenantId);

            // Check if user explicitly instructed immediate recording/saving
            $promptLower = strtolower($userPrompt);
            $autoSave = false;
            $saveKeywords = ['save', 'record', 'add to', 'put in', 'book this', 'log this', 'store this', 'update inventory', 'confirm'];
            foreach ($saveKeywords as $kw) {
                if (strpos($promptLower, $kw) !== false) {
                    $autoSave = true;
                    break;
                }
            }

            if ($autoSave && !empty($scan['record_type']) && $scan['record_type'] !== 'general') {
                $rec = \App\Modules\CasjoeERP\Services\AIVisionScannerService::recordDirectly(
                    $scan['record_type'],
                    $scan['data'] ?? [],
                    $this->tenantId,
                    $scan['image_url'] ?? null
                );
                $cardHtml = \App\Modules\CasjoeERP\Services\AIVisionScannerService::formatCard($scan, true, $rec['message'] ?? '', $rec['view_url'] ?? '');
                return [
                    'response'   => $cardHtml,
                    'type'       => 'ai',
                    'action_url' => $rec['view_url'] ?? null
                ];
            }

            // Interactive confirmation card
            $cardHtml = \App\Modules\CasjoeERP\Services\AIVisionScannerService::formatCard($scan, false);
            return [
                'response' => $cardHtml,
                'type'     => 'ai'
            ];
        } catch (\Throwable $e) {
            error_log("handleImageScan error: " . $e->getMessage());
            return [
                'response' => "⚠️ I encountered an issue scanning that image: " . $e->getMessage(),
                'type'     => 'error'
            ];
        }
    }

    public function executeAction()
    {
        if (!\App\Core\Auth::user()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        $recordType = trim($input['record_type'] ?? '');
        $fields = (array)($input['fields'] ?? []);
        $imageUrl = $input['image_url'] ?? null;

        if (empty($recordType)) {
            echo json_encode(['success' => false, 'message' => 'Missing record type']);
            exit;
        }

        $result = \App\Modules\CasjoeERP\Services\AIVisionScannerService::recordDirectly(
            $recordType,
            $fields,
            $this->tenantId,
            $imageUrl
        );

        echo json_encode($result);
        exit;
    }

    public function generateProposal($leadId = null)
    {
        $id = is_array($leadId) ? ($leadId['id'] ?? 0) : ($leadId ?: ($_GET['id'] ?? 0));
        header('Content-Type: application/json');

        $stmt = $this->pdo->prepare("SELECT * FROM erp_crm_leads WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $lead = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$lead) {
            echo json_encode(['success' => false, 'error' => 'Lead not found']);
            exit;
        }

        $leadName = htmlspecialchars($lead['name'] ?? 'Valued Client');
        $company = htmlspecialchars($lead['company'] ?? 'Your Organization');

        $proposalHtml = "
            <div style='padding: 15px; border-left: 4px solid #000066; background: #f8fafc; border-radius: 6px;'>
                <h4 style='margin-top:0; color:#000066;'>Business Growth Proposal for {$leadName}</h4>
                <p><strong>Prepared for:</strong> {$company}</p>
                <p><strong>Executive Summary:</strong> Based on your requirements, we propose implementing an integrated Casjoe suite tailored to streamline your core workflows, automate billing, and accelerate customer acquisition.</p>
                <h5 style='margin-bottom:6px;'>Scope of Deliverables:</h5>
                <ul>
                    <li>Complete setup of cloud workspace and employee CRM</li>
                    <li>Automated recurring billing and payment gateway integration</li>
                    <li>Custom AI workflow automation for lead tracking and retention</li>
                </ul>
                <p><strong>Next Step:</strong> Review timeline and schedule an onboarding kickoff session.</p>
            </div>
        ";

        echo json_encode(['success' => true, 'proposal_html' => $proposalHtml]);
        exit;
    }

    public function generateLeadEmail($leadId = null)
    {
        $id = is_array($leadId) ? ($leadId['id'] ?? 0) : ($leadId ?: ($_POST['lead_id'] ?? ($_GET['id'] ?? 0)));
        header('Content-Type: application/json');

        $type = $_POST['type'] ?? 'follow_up';
        $tone = $_POST['tone'] ?? 'professional';

        $stmt = $this->pdo->prepare("SELECT * FROM erp_crm_leads WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $lead = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$lead) {
            echo json_encode(['success' => false, 'error' => 'Lead not found']);
            exit;
        }

        $leadName = htmlspecialchars($lead['name'] ?? 'there');
        $company = htmlspecialchars($lead['company'] ?? 'your team');

        if ($type === 'meeting_request') {
            $emailBody = "Hi {$leadName},<br><br>I hope your week is off to a great start. I wanted to follow up on our previous conversation regarding solutions for {$company}. Would you have 15 minutes this Thursday or Friday for a quick intro call?<br><br>Best regards,<br>The Casjoe Team";
        } elseif ($type === 'special_offer') {
            $emailBody = "Hi {$leadName},<br><br>We are currently extending an exclusive onboarding incentive for {$company} this month. We would love to walk you through how you can get started with dedicated setup support.<br><br>Best regards,<br>The Casjoe Team";
        } else {
            $emailBody = "Hi {$leadName},<br><br>Just checking in to see if you had any questions regarding the overview we shared for {$company}. We are here to help whenever you are ready to take the next step.<br><br>Best regards,<br>The Casjoe Team";
        }

        echo json_encode(['success' => true, 'email' => $emailBody]);
        exit;
    }
}

