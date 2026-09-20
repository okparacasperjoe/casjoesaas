<?php $active = 'campaigns'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consent Warning | Casjoe</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .warning-container {
            max-width: 600px;
            margin: 40px auto;
            text-align: center;
        }
        
        .warning-icon-wrapper {
            width: 80px;
            height: 80px;
            background: #fef2f2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            border: 4px solid #fee2e2;
        }
        
        .warning-icon {
            font-size: 2.5rem;
            color: #ef4444;
        }

        .warning-title {
            color: #000066;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .warning-text {
            color: #475569;
            font-size: 1.05rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 40px;
        }

        .btn-outline-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: 2px solid #cbd5e1;
            color: #475569;
            background: transparent;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-outline-back:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #1e293b;
        }

        .btn-primary-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #000066;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary-action:hover {
            background: #000044;
            opacity: 0.9;
        }

        .explanation-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            text-align: left;
        }

        .explanation-box h4 {
            color: #1e293b;
            font-size: 1.1rem;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .explanation-box ul {
            margin: 0;
            padding-left: 20px;
            color: #475569;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .explanation-box li {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar via partial -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <main class="main-content" style="display: flex; align-items: center; justify-content: center;">
            <div class="warning-container">
                <div class="card app-card-white" style="padding: 50px 40px; border-top: 4px solid #ef4444;">
                    
                    <div class="warning-icon-wrapper">
                        <i class="bi bi-shield-lock-fill warning-icon"></i>
                    </div>
                    
                    <h1 class="warning-title">Action Required: No Valid Subscribers</h1>
                    
                    <p class="warning-text">
                        <strong>These people did not subscribe to your email.</strong><br><br>
                        The subscribers in your selected list do not have a legally valid consent record. To protect your sender reputation and comply with GDPR/anti-spam policies, Casjoe has blocked this campaign from sending.
                    </p>

                    <div class="action-buttons">
                        <a href="/mail/campaigns" class="btn-outline-back">
                            <i class="bi bi-arrow-left"></i> Back to Campaigns
                        </a>
                        <a href="/mail/lists/view?id=<?= htmlspecialchars($campaign['list_id']) ?>" class="btn-primary-action">
                            <i class="bi bi-people-fill"></i> View Subscriber List
                        </a>
                    </div>

                    <div class="explanation-box">
                        <h4><i class="bi bi-info-circle-fill" style="color: #000066;"></i> How to get valid consent?</h4>
                        <ul>
                            <li>Use the <strong>Public Signup Link</strong> or <strong>Embed Form</strong> (found in your List Tools) to let users sign themselves up. This captures their IP and timestamp as legal proof.</li>
                            <li>If you imported these contacts from another platform, ensure they explicitly opted-in to marketing communications before migrating them.</li>
                        </ul>
                    </div>

                </div>
            </div>
        </main>
    </div>
</body>
</html>
