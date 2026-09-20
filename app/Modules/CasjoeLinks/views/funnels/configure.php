<?php
// Icon mapping for funnel types
$iconMap = [
    'lead' => 'people-outline',
    'service' => 'briefcase-outline',
    'product' => 'cart-outline',
    'event' => 'calendar-outline'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configure Funnel | Casjoe Links</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/casjoe_links.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .wizard-container {
            max-width: 800px;
            margin: 20px auto 40px;
            padding: 0 10px;
        }
        .wizard-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .wizard-header h1 {
            color: #000066;
            margin-bottom: 8px;
            font-size: 1.8rem;
        }
        .wizard-header .funnel-type {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            background: linear-gradient(135deg, #FFA600, #000066);
            color: white;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.88rem;
            margin-bottom: 16px;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            gap: 12px;
        }
        .step {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #64748b;
            font-size: 14px;
        }
        .step.active {
            background: #FFA600;
            color: #000066;
            box-shadow: 0 0 0 4px rgba(255, 166, 0, 0.2);
        }
        .step.completed {
            background: #10b981;
            color: white;
        }
        .form-card {
            background: white;
            padding: 36px;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .form-group {
            margin-bottom: 22px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1e293b;
            font-size: 0.92rem;
        }
        .form-group label .required {
            color: #dc3545;
            margin-left: 4px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14.5px;
            font-family: inherit;
            background: #fff;
            color: #1e293b;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #FFA600;
            box-shadow: 0 0 0 3px rgba(255,166,0,0.15);
        }
        .form-group small {
            display: block;
            margin-top: 6px;
            color: #64748b;
            font-size: 12.5px;
        }
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 11px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-secondary:hover {
            background: #e2e8f0;
        }
        .btn-primary {
            background: #FFA600;
            color: #000066;
            border: none;
            padding: 11px 28px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 14px rgba(255,166,0,0.3);
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background: #e69500;
            transform: translateY(-1px);
        }
        .info-box {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 22px;
        }
        .info-box p {
            margin: 0;
            color: #1e40af;
            font-size: 13.5px;
            line-height: 1.5;
        }
        html.dark-theme .form-card {
            background: #1e293b;
            border-color: #334155;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        html.dark-theme .form-group label {
            color: #f8fafc;
        }
        html.dark-theme .form-group input, html.dark-theme .form-group select {
            background: #0f172a;
            border-color: #334155;
            color: #f8fafc;
        }
        html.dark-theme .step {
            background: #334155;
            color: #94a3b8;
        }
        html.dark-theme .wizard-header h1 {
            color: #f8fafc;
        }
        html.dark-theme .btn-secondary {
            background: #334155;
            border-color: #475569;
            color: #f8fafc;
        }
        html.dark-theme .info-box {
            background: #172554;
            border-left-color: #60a5fa;
        }
        html.dark-theme .info-box p {
            color: #93c5fd;
        }
        html.dark-theme .form-actions {
            border-top-color: #334155;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php $active = 'funnels'; include dirname(__DIR__) . '/partials/sidebar_links.php'; ?>
        
        <main class="main-content">
            <div class="wizard-container">
                <div class="wizard-header">
                    <span class="funnel-type">
                        <ion-icon name="<?= $iconMap[(string)$type] ?? 'funnel-outline' ?>" style="font-size: 16px;"></ion-icon>
                        <?= ucfirst($type) ?> Funnel
                    </span>
                    <h1>Configure CRM &amp; Pipeline Integration</h1>
                    <p style="color: #64748b; font-size: 0.95rem;">Connect your funnel to your CRM for automatic lead tracking, deal scoring, and stage updates</p>
                </div>

                <div class="step-indicator">
                    <div class="step completed" title="Funnel Architecture">1</div>
                    <div class="step active" title="CRM Configuration">2</div>
                    <div class="step" title="Canvas &amp; Upsells">3</div>
                </div>

                <?php if (!empty($_GET['error'])): ?>
                    <div style="background: #fee2e2; border: 1px solid #f87171; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 13.5px;">
                        <strong>Notice:</strong> <?= htmlspecialchars($_GET['error']) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/links/funnels/store" class="form-card">
                    <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?>">
                    <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">

                    <div class="info-box">
                        <p>
                            <ion-icon name="information-circle" style="vertical-align: -2px; font-size: 16px;"></ion-icon> 
                            <strong>Automated Pipeline Sync:</strong> When visitors submit forms or purchase on this funnel, Casjoe will instantly score the lead, create an opportunity in your selected pipeline stage, and assign the deal owner.
                        </p>
                    </div>

                    <!-- Funnel Name -->
                    <div class="form-group">
                        <label>Funnel Name <span class="required">*</span></label>
                        <input type="text" name="name" required placeholder="e.g., High-Ticket Client Accelerator" value="<?= ucfirst($type) ?> Funnel">
                        <small>Give your funnel a memorable title for analytics and reporting</small>
                    </div>

                    <!-- Default Deal Stage -->
                    <div class="form-group">
                        <label>Default CRM Deal Stage <span class="required">*</span></label>
                        <select name="default_stage_id" id="stageSelect" required>
                            <?php if (empty($stages)): ?>
                                <option value="1">Default Stage</option>
                            <?php else: ?>
                                <?php $sIdx = 0; foreach ($stages as $stage): ?>
                                    <option value="<?= $stage['id'] ?>" <?= $sIdx === 0 ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($stage['name']) ?>
                                    </option>
                                <?php $sIdx++; endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small>New opportunities created from this funnel will land in this CRM stage</small>
                    </div>

                    <!-- Lead Source -->
                    <div class="form-group">
                        <label>Lead Source Tag <span class="required">*</span></label>
                        <input type="text" name="lead_source" required placeholder="e.g., Facebook Ads, Organic Funnel, Email" value="<?= ucfirst($type) ?> Funnel" list="leadSources">
                        <datalist id="leadSources">
                            <option value="Sales Funnel">
                            <option value="Facebook Ads">
                            <option value="Google Ads">
                            <option value="TikTok Ads">
                            <option value="Instagram Bio">
                            <option value="Email Campaign">
                            <option value="Organic Referral">
                        </datalist>
                        <small>Attribution tag passed directly to CRM leads &amp; attribution reports</small>
                    </div>

                    <!-- Owner Selection -->
                    <div class="form-group">
                        <label>Assigned Funnel &amp; Deal Owner <span class="required">*</span></label>
                        <select name="owner_id" required>
                            <?php if (empty($users)): ?>
                                <option value="<?= (int)$currentUserId ?>">You (Default)</option>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>" <?= $user['id'] == $currentUserId ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($user['name']) ?> <?= $user['id'] == $currentUserId ? '(You)' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small>Team member notified and assigned when new leads or deals are created</small>
                    </div>

                    <div class="form-actions">
                        <a href="/links/funnels/create" class="btn-secondary">
                            <ion-icon name="arrow-back-outline"></ion-icon> Back
                        </a>
                        <button type="submit" class="btn-primary">
                            Continue to Visual Canvas <ion-icon name="arrow-forward-outline"></ion-icon>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
