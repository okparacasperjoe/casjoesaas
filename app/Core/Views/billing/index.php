<?php
$user = \App\Core\Auth::user();
$currencySvc = new \App\Core\Services\CurrencyService();
$tenant = \App\Core\TenantContext::getTenant();
$currency = $_SESSION['currency'] ?? ($user['currency'] ?? ($tenant['currency'] ?? 'NGN'));
if (empty($currency)) $currency = 'NGN';
$isNaira = (strtoupper($currency) === 'NGN');
$currencySymbol = $isNaira ? '₦' : '$';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Billing & Subscriptions | Casjoe Apps</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --brand-blue: #000066;
            --brand-gold: #FFA600;
            --brand-white: #FFFFFF;
            
            --bg-color: #F8FAFC;
            --card-bg: #FFFFFF;
            --card-hover: #F1F5F9;
            --border-color: #E2E8F0;
            --border-strong: #CBD5E1;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --radius: 16px;
        }

        * { box-sizing: border-box; }
        
        body {
            margin: 0; padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-color);
            color: var(--text-main);
            overflow-x: hidden;
            min-height: 100vh;
        }

        a { text-decoration: none; color: inherit; }

        /* --- Layout --- */
        .app-container { display: flex; min-height: 100vh; position: relative; z-index: 1; }
        
        /* ── UNIFIED CASJOE EXECUTIVE SIDEBAR ── */
        .sidebar {
            width: 270px !important;
            min-width: 270px !important;
            background: rgba(3, 4, 20, 0.85) !important;
            backdrop-filter: blur(30px) !important;
            -webkit-backdrop-filter: blur(30px) !important;
            border-right: 1px solid rgba(255, 166, 0, 0.25) !important;
            box-shadow: 10px 0 35px rgba(0, 0, 0, 0.4) !important;
            position: fixed !important;
            top: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            height: 100vh !important;
            z-index: 1000 !important;
            padding: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            overflow-y: auto;
        }
        .sidebar .brand {
            padding: 24px 20px !important;
            margin: 0 0 12px 0 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            display: flex !important;
            align-items: center !important;
        }
        .sidebar .brand img { height: 36px; }
        .nav-menu { list-style: none; padding: 0; margin: 0; }
        .nav-item { margin: 0; }
        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 13px 18px;
            color: rgba(255, 255, 255, 0.72);
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.25s ease;
            margin: 6px 14px;
            border-radius: 14px;
            border: 1px solid transparent;
            background: transparent;
        }
        .nav-link:hover, .nav-link.active {
            color: #ffffff;
            background: linear-gradient(90deg, rgba(255, 166, 0, 0.18) 0%, rgba(0, 0, 102, 0.35) 100%);
            border: 1px solid rgba(255, 166, 0, 0.3);
            box-shadow: 0 4px 15px rgba(255, 166, 0, 0.12);
        }
        .nav-link ion-icon { font-size: 1.35rem; color: #FFA600; flex-shrink: 0; }

        .main-content {
            flex: 1;
            margin-left: 270px;
            padding: 30px 36px;
            max-width: 1100px;
        }

        /* --- Components --- */
        .page-header { margin-bottom: 24px; }
        .page-header h2 { margin: 0; font-size: 1.65rem; font-weight: 800; color: #0F172A; }
        .page-header p { margin: 4px 0 0; color: #64748B; font-size: 0.88rem; }

        .card {
            background: linear-gradient(135deg, #000066 0%, #060E36 100%);
            border: 1px solid rgba(255, 166, 0, 0.45);
            border-radius: var(--radius);
            padding: 22px;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 102, 0.18), 0 0 15px rgba(255, 166, 0, 0.1);
            color: #FFFFFF;
        }

        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 9px 18px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
        }
        .btn-gold {
            background: linear-gradient(135deg, var(--brand-gold) 0%, #ff8c00 100%);
            color: #000000;
            box-shadow: 0 4px 12px rgba(255, 166, 0, 0.35);
        }
        .btn-gold:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(255, 166, 0, 0.5);
        }
        
        .btn-outline {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 166, 0, 0.45);
            color: #FFFFFF;
        }
        .btn-outline:hover {
            background: rgba(255, 166, 0, 0.18);
            border-color: var(--brand-gold);
            transform: translateY(-1px);
        }

        .btn-danger-outline {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.45);
            color: #fca5a5;
            padding: 7px 14px;
        }
        .btn-danger-outline:hover {
            background: rgba(239, 68, 68, 0.22);
        }

        /* --- Current Plan Section --- */
        .plan-status-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #000066 0%, #060E36 100%);
            border: 1px solid rgba(255, 166, 0, 0.45);
            border-radius: var(--radius);
            padding: 16px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 102, 0.18), 0 0 15px rgba(255, 166, 0, 0.1);
            color: #FFFFFF;
        }
        .plan-title { font-size: 1.15rem; font-weight: 800; color: #FFFFFF; margin-bottom: 4px; }
        .plan-status { display: inline-flex; align-items: center; gap: 4px; font-weight: 700; padding: 4px 10px; border-radius: 12px; font-size: 0.72rem; }
        .status-active { background: rgba(16, 185, 129, 0.18); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); }
        .status-inactive { background: rgba(239, 68, 68, 0.18); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.4); }
        .status-trial { background: rgba(255, 166, 0, 0.2); color: var(--brand-gold); border: 1px solid var(--brand-gold); }

        /* --- AI Credits Card --- */
        .ai-engine-card {
            background: linear-gradient(135deg, #000066 0%, #060E36 100%);
            border-radius: var(--radius);
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            border: 1px solid rgba(255, 166, 0, 0.45);
            border-left: 5px solid var(--brand-gold);
            box-shadow: 0 10px 30px rgba(0, 0, 102, 0.18), 0 0 15px rgba(255, 166, 0, 0.1);
            margin-bottom: 20px;
            color: #FFFFFF;
        }
        .ai-main-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ai-info { display: flex; align-items: center; gap: 14px; }
        .ai-icon { width: 44px; height: 44px; background: rgba(255, 166, 0, 0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--brand-gold); font-size: 1.5rem; color: var(--brand-gold); }
        .ai-credits-value { font-size: 1.8rem; font-weight: 800; line-height: 1; color: #FFFFFF; }
        .ai-credits-label { font-size: 0.75rem; color: var(--brand-gold); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .ai-usage-bar { margin-top: 4px; background: rgba(0, 0, 0, 0.35); padding: 4px 10px; border-radius: 8px; display: inline-flex; align-items: center; gap: 4px; font-size: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.15); color: #E2E8F0; }

        /* AI Topup Buttons Row */
        .ai-topup-section {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .ai-topup-label {
            font-size: 0.85rem;
            color: #E2E8F0;
            font-weight: 600;
        }
        .ai-topup-btns {
            display: flex;
            gap: 8px;
        }

        /* --- Pricing Grid --- */
        .pricing-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 24px; }
        .pricing-card {
            background: linear-gradient(135deg, #000066 0%, #060E36 100%);
            border: 1px solid rgba(255, 166, 0, 0.45);
            border-radius: var(--radius); padding: 16px 14px; text-align: center;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 102, 0.18), 0 0 15px rgba(255, 166, 0, 0.1);
            color: #FFFFFF;
        }
        .pricing-card:hover {
            transform: translateY(-5px);
            background: linear-gradient(135deg, #020448 0%, #0B1445 100%);
            border-color: var(--brand-gold);
            box-shadow: 0 18px 40px rgba(0, 0, 102, 0.35), 0 0 25px rgba(255, 166, 0, 0.25);
        }
        .pricing-card.active-plan {
            border: 2px solid var(--brand-gold);
            background: linear-gradient(135deg, #000066 0%, #09134D 100%);
        }
        .active-badge { position: absolute; top: -10px; left: 50%; transform: translateX(-50%); background: var(--brand-gold); color: #000; font-size: 0.65rem; font-weight: 800; padding: 3px 10px; border-radius: 10px; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(255, 166, 0, 0.4); }
        .pricing-name { font-size: 0.95rem; color: #FFFFFF; font-weight: 800; margin-bottom: 4px; }
        .pricing-price { font-size: 1.6rem; font-weight: 800; color: var(--brand-gold); margin-bottom: 2px; }
        .pricing-cycle { font-size: 0.75rem; color: rgba(255, 255, 255, 0.75); }
        .pricing-desc { font-size: 0.8rem; color: rgba(255, 255, 255, 0.8); margin: 10px 0; min-height: 36px; line-height: 1.4; }

        /* --- Data Table --- */
        .table-responsive { overflow-x: auto; }
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .data-table th { text-align: left; padding: 12px 10px; color: #475569; font-weight: 700; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #E2E8F0; }
        .data-table td { padding: 12px 10px; border-bottom: 1px solid #E2E8F0; font-size: 0.85rem; color: #0F172A; }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: #F8FAFC; }
        .badge-paid { background: #ECFDF5; color: #059669; padding: 4px 8px; border-radius: 8px; font-size: 0.72rem; font-weight: 700; border: 1px solid #A7F3D0; }
        .badge-pending { background: #FEF2F2; color: #DC2626; padding: 4px 8px; border-radius: 8px; font-size: 0.72rem; font-weight: 700; border: 1px solid #FECACA; }

        /* --- Mobile Nav --- */
        .mobile-bottom-nav { display: none; }

        /* --- Responsive --- */
        @media (max-width: 1024px) {
            .sidebar { width: 200px !important; min-width: 200px !important; }
            .main-content { margin-left: 200px; padding: 20px; }
        }
        @media (max-width: 768px) {
            .sidebar { display: none !important; }
            .main-content { margin-left: 0; padding: 16px 16px 80px; }
            .page-header h2 { font-size: 1.3rem; }
            .ai-main-row { flex-direction: column; align-items: flex-start; gap: 10px; }
            .ai-topup-section { flex-direction: column; align-items: flex-start; }
            .ai-topup-btns { width: 100%; }
            
            .mobile-bottom-nav {
                display: flex; justify-content: space-around; position: fixed; bottom: 0; left: 0; right: 0;
                background: rgba(0, 0, 51, 0.9); backdrop-filter: blur(20px); border-top: 1px solid var(--glass-border);
                z-index: 9999; padding: 8px 10px 20px;
            }
            .mobile-bottom-nav a {
                display: flex; flex-direction: column; align-items: center; color: rgba(255, 255, 255, 0.5);
                font-size: 0.6rem; font-weight: 600; padding: 4px 10px;
            }
            .mobile-bottom-nav a.active { color: var(--brand-gold); }
            .mobile-bottom-nav ion-icon { font-size: 1.2rem; margin-bottom: 2px; }
        }
    </style>
</head>
<body>

<div class="app-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="brand">
            <a href="/dashboard" style="text-decoration:none; display:flex; align-items:center;">
                <img src="/assets/casjoe_logo.webp" alt="Casjoe Apps" style="height: 40px; width: auto;">
            </a>
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="/dashboard" class="nav-link"><ion-icon name="grid-outline"></ion-icon> Dashboard</a></li>
            <li class="nav-item"><a href="/profile" class="nav-link"><ion-icon name="person-circle-outline"></ion-icon> My Profile</a></li>
            <li class="nav-item"><a href="/billing" class="nav-link active"><ion-icon name="card-outline"></ion-icon> Billing</a></li>
            <li class="nav-item"><a href="/support" class="nav-link"><ion-icon name="help-buoy-outline"></ion-icon> Casjoe Support</a></li>
            <li class="nav-item" style="margin-top: 40px;"><a href="/logout" class="nav-link"><ion-icon name="log-out-outline"></ion-icon> Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <h2>Billing & Subscription</h2>
            <p>Manage your ecosystem modules, payments, and AI usage.</p>
        </div>

        <?php if (isset($_GET['success']) || isset($_GET['msg'])): ?>
            <div style="padding: 14px 18px; background: rgba(16,185,129,0.18); color: #34d399; border-radius: 12px; margin-bottom: 20px; border: 1px solid rgba(16,185,129,0.4); font-size: 0.92rem; font-weight: 600;">
                <ion-icon name="checkmark-circle" style="vertical-align: middle; font-size: 1.2rem; margin-right: 6px;"></ion-icon> 
                <?= htmlspecialchars($_GET['msg'] ?? ($_GET['success'] === 'ai_topup' ? 'AI Credits successfully topped up!' : 'Success! Your subscription has been updated.')) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
             <div style="padding: 14px 18px; background: rgba(239,68,68,0.18); color: #fca5a5; border-radius: 12px; margin-bottom: 20px; border: 1px solid rgba(239,68,68,0.4); font-size: 0.92rem; font-weight: 600;">
                <ion-icon name="alert-circle" style="vertical-align: middle; font-size: 1.2rem; margin-right: 6px;"></ion-icon> 
                <?= htmlspecialchars($_GET['error'] == 'subscription_required' ? 'Access Denied: You need an active subscription to access that feature. Please upgrade below.' : $_GET['error']) ?>
            </div>
        <?php endif; ?>

        <!-- Current Plan (Minimized) -->
        <div class="plan-status-card">
            <div>
                <div class="plan-title"><?= ucfirst($sub['plan'] ?? 'None') ?> Plan</div>
                <div style="margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                    <span class="plan-status <?= $isActive ? 'status-active' : 'status-inactive' ?>">
                        <ion-icon name="<?= $isActive ? 'checkmark-circle' : 'close-circle' ?>"></ion-icon> 
                        <?= ucfirst($sub['status'] ?? 'inactive') ?>
                    </span>
                    <?php if (isset($sub['status']) && $sub['status'] === 'trial'): ?>
                        <span style="color: var(--text-muted); font-size: 0.75rem;">
                            <ion-icon name="time-outline" style="vertical-align: middle; margin-right: 2px;"></ion-icon> Trial Ends: <?= date('M j, Y', strtotime($sub['trial_ends_at'])) ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div>
                <?php if (!$isActive || (isset($sub['status']) && $sub['status'] === 'trial')): ?>
                    <a href="/billing/upgrade" class="btn btn-gold">Upgrade to Premium</a>
                <?php else: ?>
                    <form method="POST" action="/billing/cancel" onsubmit="return confirm('Are you sure you want to cancel your subscription?')" style="margin: 0;">
                        <button type="submit" class="btn btn-danger-outline">Cancel Subscription</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>



        <!-- AI Credits Engine (Minimized with specific Top-up Pack buttons) -->
        <div class="ai-engine-card">
            <div class="ai-main-row">
                <div class="ai-info">
                    <div class="ai-icon"><ion-icon name="hardware-chip"></ion-icon></div>
                    <div>
                        <div class="ai-credits-label">Cori AI Token</div>
                        <div class="ai-credits-value"><?= number_format($totalTokens ?? 0) ?></div>
                        <div class="ai-usage-bar">
                            <span style="color: var(--brand-gold); font-weight: 700;"><ion-icon name="analytics-outline" style="vertical-align: middle;"></ion-icon> <?= number_format($aiTokensUsed ?? 0) ?></span> / <?= number_format($aiTokensLimit ?? 0) ?> Tokens Used
                        </div>
                    </div>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); text-align: right; display: block; line-height: 1.3;">
                        <strong>Plan Allowance:</strong> <?= number_format(max(0, $aiTokensLimit - $aiTokensUsed)) ?> Remaining<br>
                        <strong>Top-Up Credits:</strong> <?= number_format($aiCredits) ?> <?= $currency ?>
                    </span>
                </div>
            </div>
            
            <!-- AI Credit Top-Up Options (With Select Gateway and Dynamic Currency) -->
            <div class="ai-topup-section">
                <div class="ai-topup-label">
                    <ion-icon name="flash-outline" style="vertical-align: middle; color: var(--brand-gold); margin-right: 4px;"></ion-icon>
                    Top up tokens (Pay-as-you-go):
                </div>
                <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <span style="font-size: 0.75rem; color: var(--text-muted);">Gateway:</span>
                        <select id="ai-gateway-select" style="background: rgba(0,0,102,0.9); border: 1px solid #FFA600; color: #fff; padding: 6px 10px; border-radius: 6px; font-size: 0.78rem; outline: none; cursor: pointer;">
                            <option value="paystack" selected>Paystack (Card/Transfer)</option>
                            <option value="flutterwave">Flutterwave</option>
                            <option value="bank_transfer">Direct Bank Transfer</option>
                            <option value="coupon">Redeem Coupon Code</option>
                        </select>
                    </div>
                    <div class="ai-topup-btns">
                        <?php
                            $price5k = $isNaira ? 5000 : round($currencySvc->convert(5, 'USD', $currency)['amount'] ?? 5, 2);
                            $price10k = $isNaira ? 10000 : round($currencySvc->convert(10, 'USD', $currency)['amount'] ?? 10, 2);
                        ?>
                        <button onclick="payAI('ai_5k')" class="btn btn-outline">Top Up <?= $currencySvc->getSymbol($currency) ?><?= number_format($price5k, $isNaira?0:2) ?></button>
                        <button onclick="payAI('ai_10k')" class="btn btn-gold">Top Up <?= $currencySvc->getSymbol($currency) ?><?= number_format($price10k, $isNaira?0:2) ?></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Employee Subscriptions -->
        <h3 style="font-size: 1.1rem; margin-bottom: 12px; font-weight: 800; color: var(--brand-white); margin-top: 24px;">AI Office Add-ons</h3>
        <div class="pricing-grid">
            <?php 
                $aiRoles = [
                    'sales_manager' => ['name' => 'AI Sales Manager', 'desc' => 'Draft emails, analyze pipeline, propose follow-ups'],
                    'accountant' => ['name' => 'AI Accountant', 'desc' => 'Track invoices, summarize expenses, generate reports']
                ];
                
                // Fetch active AI Employees
                $db = \App\Core\Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT employee_type FROM tenant_ai_employees WHERE tenant_id = ? AND status = 'active'");
                $stmt->execute([$tenant['id'] ?? 0]);
                $activeAiEmployees = $stmt->fetchAll(\PDO::FETCH_COLUMN);

                $aiPrice = $isNaira ? AI_EMPLOYEE_PRICE_NGN : round($currencySvc->convert(AI_EMPLOYEE_PRICE_USD, 'USD', $currency)['amount'] ?? AI_EMPLOYEE_PRICE_USD, 2);
            ?>
            <?php foreach ($aiRoles as $roleKey => $roleData): ?>
                <?php $isActiveRole = in_array($roleKey, $activeAiEmployees); ?>
                <div class="pricing-card <?= $isActiveRole ? 'active-plan' : '' ?>">
                    <?php if ($isActiveRole): ?>
                        <div class="active-badge">ACTIVE ADD-ON</div>
                    <?php endif; ?>
                    <div class="pricing-name"><?= htmlspecialchars($roleData['name']) ?></div>
                    <div class="pricing-price"><?= $currencySvc->getSymbol($currency) ?><?= number_format($aiPrice, $isNaira?0:2) ?></div>
                    <div class="pricing-cycle">per monthly</div>
                    <div class="pricing-desc"><?= htmlspecialchars($roleData['desc']) ?></div>
                    
                    <?php if (!$isActiveRole): ?>
                        <button onclick="payAIEmployee('<?= $roleKey ?>')" class="btn btn-outline" style="width: 100%;">Subscribe</button>
                    <?php else: ?>
                        <button class="btn" style="width: 100%; background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.3); border: 1px solid rgba(255,255,255,0.1); cursor: not-allowed;" disabled>Active</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Coupon Section -->
        <div id="couponSection" class="card" style="margin-top: 24px;">
            <h3 style="margin-top: 0; margin-bottom: 12px; font-size: 1rem; font-weight: 800;">Redeem Coupon Code</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 16px;">Have a coupon code for AI Tokens or a Subscription? Enter it below to redeem it.</p>
            <form method="POST" action="/billing/redeem-coupon" style="display: flex; gap: 10px; max-width: 500px;">
                <input type="hidden" name="context" value="ai_topup">
                <input type="text" name="code" placeholder="Enter Coupon Code" required class="form-control" style="flex: 1; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px;">
                <button type="submit" class="btn btn-gold" style="white-space: nowrap;">Apply Coupon</button>
            </form>
        </div>

        <!-- Subscription Modules -->
        <h3 style="font-size: 1.1rem; margin-bottom: 12px; font-weight: 800; color: var(--brand-white);">Available Modules</h3>
        <div class="pricing-grid">
            <?php foreach ($plans ?? [] as $p): ?>
                <?php 
                $isCurrent = (strtolower($sub['plan'] ?? '') === strtolower($p['slug'] ?? '')); 
                $priceVal = (float)($isNaira ? ($p['price_ngn'] ?? (($p['price'] ?? 0) * 1500)) : ($p['price'] ?? 0));
                ?>
                <div class="pricing-card <?= $isCurrent ? 'active-plan' : '' ?>">
                    <?php if ($isCurrent): ?>
                        <div class="active-badge">CURRENT PLAN</div>
                    <?php endif; ?>
                    <div class="pricing-name"><?= htmlspecialchars((string)($p['name'] ?? 'Module')) ?></div>
                    <div class="pricing-price"><?= $currencySymbol ?><?= number_format($priceVal, 0) ?></div>
                    <div class="pricing-cycle">per <?= htmlspecialchars((string)($p['billing_cycle'] ?? 'monthly')) ?></div>
                    <div class="pricing-desc"><?= htmlspecialchars((string)($p['description'] ?? 'Enterprise platform module for your ecosystem.')) ?></div>
                    
                    <?php if (!$isCurrent): ?>
                        <a href="/billing/upgrade?slug=<?= urlencode((string)($p['slug'] ?? '')) ?>" class="btn btn-outline" style="width: 100%;">Select Module</a>
                    <?php else: ?>
                        <button class="btn" style="width: 100%; background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.3); border: 1px solid rgba(255,255,255,0.1); cursor: not-allowed;" disabled>Active</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Invoice History -->
        <div class="card">
            <h3 style="margin-top: 0; margin-bottom: 12px; font-size: 1rem; font-weight: 800;">Invoice History</h3>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($invoices)): ?>
                            <?php foreach ($invoices as $inv): ?>
                                <?php 
                                $invSymbol = (strtoupper($inv['currency'] ?? 'NGN') === 'NGN') ? '₦' : '$';
                                ?>
                                <tr>
                                    <td><?= date('M j, Y', strtotime($inv['date'])) ?></td>
                                    <td style="font-family: monospace; color: var(--brand-gold); font-weight: 600;"><?= htmlspecialchars($inv['reference'] ?? '') ?></td>
                                    <td style="font-weight: 700; color: var(--brand-white);"><?= $invSymbol ?><?= number_format($inv['amount'], 2) ?></td>
                                    <td>
                                        <span class="<?= $inv['status'] === 'paid' ? 'badge-paid' : 'badge-pending' ?>">
                                            <?= ucfirst($inv['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 15px;">No invoices found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<!-- Mobile Bottom Navigation -->
<nav class="mobile-bottom-nav">
    <a href="/dashboard">
        <ion-icon name="grid-outline"></ion-icon>
        <span>Platform</span>
    </a>
    <a href="/profile">
        <ion-icon name="person-circle-outline"></ion-icon>
        <span>Profile</span>
    </a>
    <a href="/billing" class="active">
        <ion-icon name="card-outline"></ion-icon>
        <span>Billing</span>
    </a>
    <a href="/support">
        <ion-icon name="headset-outline"></ion-icon>
        <span>Support</span>
    </a>
</nav>

<script>
    const userCurrency = "<?= $currencyCode ?? $currency ?? 'NGN' ?>";
    
    function getGateway() {
        const select = document.getElementById('ai-gateway-select');
        if (select) {
            const val = select.value;
            if (val === 'coupon') return 'coupon';
            if (val === 'bank_transfer') return 'bank_transfer';
        }
        // Force gateway based on currency
        return (userCurrency === 'NGN') ? 'paystack' : 'flutterwave';
    }

    // Auto-select correct gateway in dropdown if it exists
    document.addEventListener("DOMContentLoaded", () => {
        const select = document.getElementById('ai-gateway-select');
        if (select) {
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].value === 'paystack' && userCurrency !== 'NGN') select.options[i].disabled = true;
                if (select.options[i].value === 'flutterwave' && userCurrency === 'NGN') select.options[i].disabled = true;
            }
            if (userCurrency !== 'NGN') select.value = 'flutterwave';
        }
    });

    function payAI(pack) {
        const gateway = getGateway();
        if (gateway === 'coupon') {
            document.getElementById('couponSection').scrollIntoView({ behavior: 'smooth' });
            const input = document.querySelector('input[name=code]');
            if (input) input.focus();
            return;
        }
        submitPaymentForm('ai_topup', pack, gateway);
    }
    
    function payAIEmployee(pack) {
        const gateway = getGateway();
        if (gateway === 'coupon') {
            document.getElementById('couponSection').scrollIntoView({ behavior: 'smooth' });
            const input = document.querySelector('input[name=code]');
            const contextInput = document.querySelector('input[name=context]');
            if (contextInput) contextInput.value = 'ai_employee_' + pack;
            if (input) input.focus();
            return;
        }
        submitPaymentForm('ai_employee', pack, gateway);
    }
    
    function submitPaymentForm(type, pack, gateway) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/billing/pay';
        
        const typeInput = document.createElement('input');
        typeInput.type = 'hidden';
        typeInput.name = 'type';
        typeInput.value = type;
        
        const packInput = document.createElement('input');
        packInput.type = 'hidden';
        packInput.name = 'pack';
        packInput.value = pack;
        
        const gatewayInput = document.createElement('input');
        gatewayInput.type = 'hidden';
        gatewayInput.name = 'gateway';
        gatewayInput.value = gateway;
        
        form.appendChild(typeInput);
        form.appendChild(packInput);
        form.appendChild(gatewayInput);
        document.body.appendChild(form);
        form.submit();
    }
</script>

</body>
</html>
