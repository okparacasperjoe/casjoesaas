<?php
// Executive Casjoe 403 Security Perimeter Alert View
$user = $user ?? \App\Core\Auth::user();
$clientIp = htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
$targetResource = htmlspecialchars($resource ?? ($_SERVER['REQUEST_URI'] ?? '/casper-joe'));
$incidentCode = $incidentCode ?? ('SEC-' . strtoupper(substr(md5($clientIp . $targetResource . time()), 0, 8)));
$timestamp = gmdate('Y-m-d H:i:s') . ' UTC';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Security Perimeter Alert | Casjoe</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        :root {
            --bg-base: #05060f;
            --card-bg: rgba(12, 14, 28, 0.88);
            --card-border: rgba(255, 68, 68, 0.28);
            --accent-red: #ff334b;
            --accent-amber: #ffa600;
            --accent-blue: #0066ff;
            --text-primary: #ffffff;
            --text-muted: #8b92a5;
            --mono-font: 'JetBrains Mono', monospace;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-base);
            background-image: 
                radial-gradient(at 15% 15%, rgba(255, 51, 75, 0.12) 0px, transparent 50%),
                radial-gradient(at 85% 85%, rgba(0, 0, 102, 0.25) 0px, transparent 60%),
                radial-gradient(at 50% 50%, rgba(5, 6, 15, 1) 0px, transparent 100%);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow-x: hidden;
        }

        /* Subtle grid pattern */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: 0;
            pointer-events: none;
        }

        /* Main Container */
        .security-card {
            position: relative;
            z-index: 1;
            max-width: 620px;
            width: 100%;
            background: var(--card-bg);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--card-border);
            border-radius: 28px;
            padding: 44px 40px;
            box-shadow: 
                0 30px 80px rgba(0, 0, 0, 0.7),
                0 0 40px rgba(255, 51, 75, 0.08);
            text-align: center;
            animation: cardAppear 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes cardAppear {
            from { opacity: 0; transform: translateY(24px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Perimeter Status Pill */
        .security-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 51, 75, 0.12);
            border: 1px solid rgba(255, 51, 75, 0.35);
            padding: 7px 16px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #ff6b7e;
            margin-bottom: 24px;
        }

        .pulsing-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ff334b;
            box-shadow: 0 0 10px #ff334b;
            animation: pulseGlow 1.8s infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        /* Shield Icon Animation */
        .icon-wrapper {
            position: relative;
            width: 88px;
            height: 88px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-circle {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 51, 75, 0.25) 0%, rgba(255, 51, 75, 0.03) 70%);
            border: 1px dashed rgba(255, 51, 75, 0.4);
            animation: rotateRadar 14s linear infinite;
        }

        @keyframes rotateRadar {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .icon-center {
            position: relative;
            z-index: 2;
            width: 62px;
            height: 62px;
            border-radius: 18px;
            background: linear-gradient(135deg, #220912 0%, #12050b 100%);
            border: 1px solid rgba(255, 51, 75, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ff4d64;
            font-size: 32px;
            box-shadow: 0 10px 25px rgba(255, 51, 75, 0.2);
        }

        /* Headings */
        .error-badge {
            font-family: var(--mono-font);
            font-size: 13px;
            font-weight: 700;
            color: var(--accent-amber);
            letter-spacing: 2px;
            margin-bottom: 6px;
        }

        h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
            background: linear-gradient(135deg, #ffffff 30%, #ff8a99 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p.lead-desc {
            font-size: 15px;
            line-height: 1.6;
            color: var(--text-muted);
            margin-bottom: 24px;
        }

        /* Audit Box */
        .audit-box {
            background: rgba(0, 0, 0, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 24px;
            text-align: left;
            font-family: var(--mono-font);
            font-size: 12px;
        }

        .audit-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }

        .audit-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .audit-label {
            color: #6c748c;
            font-weight: 500;
        }

        .audit-val {
            color: #d1d5db;
            font-weight: 600;
        }

        .audit-val.alert {
            color: #ff5266;
        }

        .audit-val.status {
            color: var(--accent-amber);
        }

        /* Notice message */
        .security-notice {
            background: rgba(255, 166, 0, 0.06);
            border-left: 3px solid var(--accent-amber);
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 28px;
            font-size: 12.5px;
            line-height: 1.5;
            color: #d5d9e5;
            text-align: left;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .security-notice ion-icon {
            font-size: 18px;
            color: var(--accent-amber);
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #0052cc 0%, #003399 100%);
            color: white;
            padding: 13px 26px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 10px 25px rgba(0, 82, 204, 0.35);
            transition: all 0.2s ease;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(0, 82, 204, 0.5);
            background: linear-gradient(135deg, #0062f5 0%, #003db8 100%);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: #d1d5db;
            padding: 13px 22px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Auto-redirect footer */
        .auto-redirect {
            margin-top: 22px;
            font-size: 12px;
            color: #636b80;
        }

        .auto-redirect span {
            color: var(--accent-amber);
            font-weight: 600;
        }

        @media (max-width: 600px) {
            .security-card {
                padding: 32px 24px;
                border-radius: 22px;
            }
            h1 {
                font-size: 24px;
            }
            .action-buttons {
                flex-direction: column;
            }
            .btn-primary, .btn-secondary {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <div class="security-card">
        <!-- Perimeter Indicator -->
        <div class="security-pill">
            <span class="pulsing-dot"></span>
            SOC Defense Perimeter Active
        </div>

        <!-- Radar Shield Icon -->
        <div class="icon-wrapper">
            <div class="icon-circle"></div>
            <div class="icon-center">
                <ion-icon name="shield-half-outline"></ion-icon>
            </div>
        </div>

        <div class="error-badge">ERROR 403 &bull; RESTRICTED ZONE</div>
        <h1>Access Denied: Super Admin Area</h1>
        <p class="lead-desc">
            This administrative control system is restricted exclusively to authorized Casjoe platform Super Administrators. Your current session does not possess the requisite governance credentials.
        </p>

        <!-- Threat / Audit Diagnostic Box -->
        <div class="audit-box">
            <div class="audit-row">
                <span class="audit-label">Incident Reference:</span>
                <span class="audit-val alert"><?= $incidentCode ?></span>
            </div>
            <div class="audit-row">
                <span class="audit-label">Client IP Address:</span>
                <span class="audit-val"><?= $clientIp ?></span>
            </div>
            <div class="audit-row">
                <span class="audit-label">Requested Target:</span>
                <span class="audit-val"><?= $targetResource ?></span>
            </div>
            <div class="audit-row">
                <span class="audit-label">Timestamp (UTC):</span>
                <span class="audit-val"><?= $timestamp ?></span>
            </div>
            <div class="audit-row">
                <span class="audit-label">Firewall Disposition:</span>
                <span class="audit-val status">Logged &bull; Monitoring Active</span>
            </div>
        </div>

        <!-- Warning Deterrent Notice -->
        <div class="security-notice">
            <ion-icon name="warning-outline"></ion-icon>
            <div>
                <strong>Security Notice:</strong> All unauthorized attempts to probe, access, or manipulate governance endpoints are recorded by the Casjoe SOC Firewall. Repeated policy infractions result in immediate, automated IP blacklisting across the network.
            </div>
        </div>

        <!-- Action Controls -->
        <div class="action-buttons">
            <a href="/dashboard" class="btn-primary" id="btnDashboard">
                <ion-icon name="home-outline"></ion-icon>
                Return to Safe Dashboard
            </a>
            <a href="/logout" class="btn-secondary">
                <ion-icon name="log-out-outline"></ion-icon>
                Sign Out
            </a>
        </div>

        <!-- Auto Return Countdown -->
        <div class="auto-redirect">
            Auto-redirecting to safe dashboard in <span id="countdown">20</span>s. 
            <a href="javascript:void(0)" onclick="cancelRedirect()" style="color: #8b92a5; text-decoration: underline; margin-left: 4px;">Cancel</a>
        </div>
    </div>

    <script>
        let timeLeft = 20;
        let timer = setInterval(function() {
            timeLeft--;
            const el = document.getElementById('countdown');
            if (el) el.innerText = timeLeft;
            if (timeLeft <= 0) {
                clearInterval(timer);
                window.location.href = '/dashboard';
            }
        }, 1000);

        function cancelRedirect() {
            clearInterval(timer);
            const parent = document.querySelector('.auto-redirect');
            if (parent) {
                parent.innerHTML = 'Auto-redirect paused.';
            }
        }
    </script>
</body>
</html>
