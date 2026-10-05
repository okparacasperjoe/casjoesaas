<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Casjoe Pay' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/assets/casjoe_logo.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Casjoe">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').catch(function() {});
            });
        }
    </script>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* ── Pay Dashboard Premium Theme Variables ── */
        :root {
            /* Default Dark: Modern Deep Slate / Midnight Obsidian (Crisp, High Contrast) */
            --pay-bg: #0b0f19;
            --pay-surface: #151c2e;
            --pay-surface-2: #1e293b;
            --pay-input-bg: rgba(255, 255, 255, 0.05);
            --pay-border: rgba(255, 255, 255, 0.12);
            --pay-border-hover: rgba(255, 255, 255, 0.25);
            --pay-gold: #FFA600;
            --pay-gold-dim: rgba(255, 166, 0, 0.15);
            --pay-green: #10b981;
            --pay-green-dim: rgba(16, 185, 129, 0.15);
            --pay-red: #ef4444;
            --pay-blue: #3b82f6;
            --pay-text: #f8fafc;
            --pay-text-secondary: #cbd5e1;
            --pay-text-muted: #94a3b8;
            --pay-radius: 20px;
            --pay-card-shadow: 0 20px 45px rgba(0, 0, 0, 0.35);
            --pay-header-color: #ffffff;
            --pay-warning-bg: rgba(245, 158, 11, 0.12);
            --pay-warning-border: rgba(245, 158, 11, 0.35);
            --pay-warning-text: #fef3c7;
        }

        /* ── Light Theme Overrides (Bright, Clean, Crisp Modern Fintech) ── */
        html.light-theme,
        html[data-theme='light'],
        body.light-theme,
        body[data-theme='light'] {
            --pay-bg: #f8fafc;
            --pay-surface: #ffffff;
            --pay-surface-2: #f1f5f9;
            --pay-input-bg: #ffffff;
            --pay-border: #e2e8f0;
            --pay-border-hover: #cbd5e1;
            --pay-gold: #000066;
            --pay-gold-dim: rgba(0, 0, 102, 0.08);
            --pay-green: #059669;
            --pay-green-dim: rgba(5, 150, 105, 0.1);
            --pay-red: #dc2626;
            --pay-blue: #2563eb;
            --pay-text: #0f172a;
            --pay-text-secondary: #334155;
            --pay-text-muted: #64748b;
            --pay-card-shadow: 0 15px 35px rgba(0, 0, 0, 0.06);
            --pay-header-color: #000066;
            --pay-warning-bg: #fffbeb;
            --pay-warning-border: #fde68a;
            --pay-warning-text: #92400e;
        }

        body,
        .main-content,
        .app-container {
            background: var(--pay-bg) !important;
            background-image: none !important;
            color: var(--pay-text);
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* ── Light Theme Global Polish ── */
        html.light-theme body,
        html[data-theme='light'] body,
        body.light-theme,
        body[data-theme='light'],
        html.light-theme .main-content,
        html[data-theme='light'] .main-content,
        html.light-theme .app-container,
        html[data-theme='light'] .app-container {
            background: #f8fafc !important;
            background-image: none !important;
            color: #0f172a !important;
        }

        html.light-theme .sidebar,
        html[data-theme='light'] .sidebar {
            background: #ffffff !important;
            border-right: 1px solid #e2e8f0 !important;
            box-shadow: 5px 0 25px rgba(0, 0, 0, 0.04) !important;
        }
        html.light-theme .sidebar .brand span,
        html.light-theme .pay-brand span,
        html[data-theme='light'] .sidebar .brand span,
        html[data-theme='light'] .pay-brand span {
            color: #000066 !important;
        }
        html.light-theme .sidebar .nav-link,
        html.light-theme .sidebar .pay-link,
        html[data-theme='light'] .sidebar .nav-link,
        html[data-theme='light'] .sidebar .pay-link {
            color: #475569 !important;
        }
        html.light-theme .sidebar .nav-link:hover,
        html.light-theme .sidebar .nav-link.active,
        html.light-theme .sidebar .pay-link:hover,
        html.light-theme .sidebar .pay-link.active,
        html[data-theme='light'] .sidebar .nav-link:hover,
        html[data-theme='light'] .sidebar .nav-link.active,
        html[data-theme='light'] .sidebar .pay-link:hover,
        html[data-theme='light'] .sidebar .pay-link.active {
            background: rgba(0, 0, 102, 0.08) !important;
            color: #000066 !important;
            border-color: rgba(0, 0, 102, 0.18) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 102, 0.08) !important;
        }
        html.light-theme .top-bar h2,
        html[data-theme='light'] .top-bar h2,
        body.light-theme .top-bar h2,
        body[data-theme='light'] .top-bar h2 {
            color: #000066 !important;
            opacity: 1 !important;
            visibility: visible !important;
            display: block !important;
        }
        html.light-theme .pay-form-header,
        html[data-theme='light'] .pay-form-header,
        body.light-theme .pay-form-header,
        body[data-theme='light'] .pay-form-header {
            color: #000066 !important;
            opacity: 1 !important;
            visibility: visible !important;
            display: block !important;
        }
        html.light-theme .wallet-id-badge,
        html[data-theme='light'] .wallet-id-badge,
        body.light-theme .wallet-id-badge,
        body[data-theme='light'] .wallet-id-badge {
            background: #ffffff !important;
            color: #000066 !important;
            border: 1.5px solid #cbd5e1 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
            opacity: 1 !important;
            visibility: visible !important;
            display: inline-flex !important;
        }
        html.light-theme .wallet-id-badge span,
        html[data-theme='light'] .wallet-id-badge span,
        body.light-theme .wallet-id-badge span,
        body[data-theme='light'] .wallet-id-badge span {
            color: #000066 !important;
            font-weight: 700 !important;
        }
        html.light-theme .wallet-id-badge ion-icon,
        html[data-theme='light'] .wallet-id-badge ion-icon,
        body.light-theme .wallet-id-badge ion-icon,
        body[data-theme='light'] .wallet-id-badge ion-icon {
            color: #FFA600 !important;
        }

        /* ── Form Inputs Premium Style ── */
        .pay-form-card {
            background: var(--pay-surface);
            border: 1px solid var(--pay-border);
            border-radius: var(--pay-radius);
            padding: 32px;
            max-width: 520px;
            margin: 30px auto;
            box-shadow: var(--pay-card-shadow);
            transition: background 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
        }
        .pay-form-header {
            font-size: 1.45rem;
            font-weight: 800;
            margin-bottom: 25px;
            color: var(--pay-header-color);
            text-align: center;
            letter-spacing: -0.3px;
        }
        .pay-input-group {
            margin-bottom: 20px;
        }
        .pay-label {
            display: block;
            font-size: 0.82rem;
            color: var(--pay-text-muted);
            font-weight: 700;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        .pay-input, .pay-select {
            width: 100%;
            background: var(--pay-input-bg);
            border: 1.5px solid var(--pay-border);
            color: var(--pay-text);
            padding: 13px 16px;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.25s;
            box-sizing: border-box;
        }
        .pay-input:focus, .pay-select:focus {
            outline: none;
            border-color: #FFA600;
            background: rgba(255,166,0,0.03);
            box-shadow: 0 0 0 3px rgba(255,166,0,0.15);
        }
        .pay-select option {
            background: var(--pay-surface);
            color: var(--pay-text);
        }
        .pay-btn-primary {
            width: 100%;
            background: linear-gradient(135deg, #FFA600 0%, #FF8800 100%);
            color: #000000;
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.25s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            box-shadow: 0 4px 16px rgba(255,166,0,0.3);
        }
        .pay-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255,166,0,0.4);
        }

        /* ── Input with Addon (Currency) ── */
        .pay-input-with-addon {
            display: flex;
            background: var(--pay-input-bg);
            border: 1.5px solid var(--pay-border);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.25s;
        }
        .pay-input-with-addon:focus-within {
            border-color: #FFA600;
            box-shadow: 0 0 0 3px rgba(255,166,0,0.15);
        }
        .pay-addon-select {
            background: var(--pay-surface-2);
            border: none;
            border-right: 1.5px solid var(--pay-border);
            color: var(--pay-text);
            padding: 0 16px;
            font-weight: 700;
            outline: none;
            cursor: pointer;
        }
        .pay-addon-select option { background: var(--pay-surface); color: var(--pay-text); }
        .pay-addon-input {
            flex: 1;
            background: transparent;
            border: none;
            color: var(--pay-text);
            padding: 14px 16px;
            font-size: 1.1rem;
            font-weight: 600;
            outline: none;
            font-family: 'JetBrains Mono', monospace;
        }

        /* ── Radio Methods (Cards, Mobile Money) ── */
        .pay-method-label {
            display: flex;
            align-items: center;
            padding: 16px;
            background: var(--pay-input-bg);
            border: 1.5px solid var(--pay-border);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s;
            color: var(--pay-text-muted);
            margin-bottom: 10px;
        }
        .pay-method-label:hover {
            background: var(--pay-surface-2);
            border-color: var(--pay-border-hover);
        }
        .pay-method-label:has(input:checked) {
            border-color: #FFA600;
            background: rgba(255,166,0,0.1);
            color: var(--pay-text);
        }
        .pay-method-label input[type="radio"] {
            accent-color: #FFA600;
            width: 18px;
            height: 18px;
            margin-right: 15px;
            cursor: pointer;
        }
        .pay-method-icon {
            font-size: 1.4rem;
            color: #FFA600;
            margin-right: 10px;
        }

        /* ── Modern Tabs ── */
        .pay-tabs {
            display: flex;
            background: var(--pay-surface-2);
            border: 1px solid var(--pay-border);
            border-radius: 14px;
            padding: 4px;
            margin-bottom: 25px;
        }
        .pay-tab-btn {
            flex: 1;
            padding: 10px;
            border: none;
            background: transparent;
            color: var(--pay-text-muted);
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.25s;
            font-size: 0.85rem;
        }
        .pay-tab-btn:hover { color: var(--pay-text); }
        .pay-tab-btn.active {
            background: rgba(255,166,0,0.15);
            color: #FFA600;
            box-shadow: 0 2px 8px rgba(255,166,0,0.15);
        }

        /* ── Sidebar Overrides ── */
        .app-container { display: flex !important; min-height: 100vh; position: relative; }
        .sidebar { width: 270px !important; min-width: 270px !important; background: rgba(3, 4, 20, 0.88) !important; backdrop-filter: blur(30px) !important; -webkit-backdrop-filter: blur(30px) !important; border-right: 1px solid rgba(255, 166, 0, 0.25) !important; box-shadow: 10px 0 35px rgba(0, 0, 0, 0.4) !important; position: fixed !important; top: 0 !important; bottom: 0 !important; left: 0 !important; height: 100vh !important; z-index: 1000 !important; padding: 0 !important; display: flex !important; flex-direction: column !important; }
        .main-content {
            flex: 1 !important;
            margin-left: 270px !important;
            padding: 30px;
            width: calc(100% - 270px);
            min-height: 100vh;
        }

        a { text-decoration: none; }
        .sidebar .brand { padding: 24px 20px !important; margin: 0 0 12px 0 !important; border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important; display: flex !important; align-items: center !important; font-size: 1.3rem !important; font-weight: 700 !important; color: #ffffff !important; }
        .sidebar .nav-link { color: rgba(255, 255, 255, 0.72) !important; border-radius: 14px !important; margin: 6px 14px !important; padding: 13px 18px !important; font-weight: 600 !important; font-size: 0.95rem !important; transition: all 0.25s ease !important; display: flex !important; align-items: center !important; gap: 12px !important; border: 1px solid transparent !important; background: transparent !important; text-decoration: none !important; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #ffffff !important; background: linear-gradient(90deg, rgba(255, 166, 0, 0.18) 0%, rgba(0, 0, 102, 0.35) 100%) !important; border: 1px solid rgba(255, 166, 0, 0.3) !important; box-shadow: 0 4px 15px rgba(255, 166, 0, 0.12) !important; }
        .sidebar .nav-link ion-icon { color: #FFA600 !important; font-size: 1.35rem !important; margin-right: 0 !important; }
        .sidebar .nav-menu { list-style: none; padding: 0 !important; margin: 0 !important; }
        .sidebar .nav-item { margin: 0 !important; }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            position: relative;
            z-index: 10;
        }
        .top-bar h2 {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--pay-header-color);
            letter-spacing: -0.4px;
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
        .wallet-id-badge {
            font-size: 0.8rem;
            background: rgba(255, 166, 0, 0.14);
            color: #FFA600;
            padding: 8px 18px;
            border-radius: 30px;
            font-weight: 700;
            border: 1.5px solid rgba(255, 166, 0, 0.35);
            display: inline-flex;
            align-items: center;
            gap: 7px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
            opacity: 1 !important;
            visibility: visible !important;
        }
        .wallet-id-badge ion-icon {
            font-size: 1.15rem;
            color: #FFA600;
            flex-shrink: 0;
        }
        .wallet-id-badge span {
            font-weight: 700;
        }

        /* ── Responsive ── */
        @media (max-width: 992px) {
            .sidebar { left: -260px !important; transition: left 0.3s ease; }
            .sidebar.active { left: 0 !important; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 80px; }
        }
        @media (max-width: 576px) {
            .pay-form-card { padding: 22px 18px; margin: 20px 10px; }
            .pay-tabs { flex-direction: column; }
            .top-bar { flex-direction: column; align-items: flex-start; gap: 12px; }
        }
    </style>
</head>
<body data-theme="pay-premium">
<?php require_once dirname(__DIR__, 4) . '/Core/Views/partials/mobile_nav.php'; ?>
<div class="app-container">
    <aside class="sidebar">
    <?php include __DIR__ . '/../partials/sidebar_pay_css.php'; ?>

        <div class="pay-brand">
            <a href="/dashboard" style="text-decoration:none; display:flex; align-items:center; gap:8px;">
                <img src="/assets/casjoe_logo.webp" alt="Casjoe" style="height: 32px; flex-shrink:0;">
                <span style="color:#fff; font-weight:800; font-size:1.1rem; letter-spacing:1px;">Pay</span>
            </a>
        </div>
        <ul class="pay-menu">
            <li class="pay-item"><a href="/dashboard" class="pay-link"><ion-icon name="speedometer-outline"></ion-icon> Dashboard</a></li>
            <li class="pay-item"><a href="/pay" class="pay-link <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>"><ion-icon name="wallet-outline"></ion-icon> Casjoe Pay</a></li>
            <li class="pay-item"><a href="/pay/fund" class="pay-link <?= ($activeMenu ?? '') === 'fund' ? 'active' : '' ?>"><ion-icon name="add-circle-outline"></ion-icon> Fund Wallet</a></li>
            <li class="pay-item"><a href="/pay/transfer" class="pay-link <?= ($activeMenu ?? '') === 'transfer' ? 'active' : '' ?>"><ion-icon name="paper-plane-outline"></ion-icon> Transfer</a></li>
            <li class="pay-item"><a href="/pay/cards" class="pay-link <?= ($activeMenu ?? '') === 'cards' ? 'active' : '' ?>"><ion-icon name="card-outline"></ion-icon> Virtual Card</a></li>
            <li class="pay-item"><a href="/pay/naira-cards" class="pay-link <?= ($activeMenu ?? '') === 'naira-cards' ? 'active' : '' ?>"><ion-icon name="card-outline"></ion-icon> Naira Card</a></li>
            <li class="pay-item"><a href="/pay/virtual-bank" class="pay-link <?= ($activeMenu ?? '') === 'virtual-bank' ? 'active' : '' ?>"><ion-icon name="business-outline"></ion-icon> Virtual Bank Account</a></li>
            <li class="pay-item"><a href="/pay/links" class="pay-link <?= ($activeMenu ?? '') === 'links' ? 'active' : '' ?>"><ion-icon name="link-outline"></ion-icon> Payment Links</a></li>
            <li class="pay-item"><a href="/pay/convert" class="pay-link <?= ($activeMenu ?? '') === 'convert' ? 'active' : '' ?>"><ion-icon name="swap-horizontal-outline"></ion-icon> Convert Currency</a></li>
            <li class="pay-item"><a href="/logout" class="pay-link"><ion-icon name="log-out-outline"></ion-icon> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2><?= $pageTitle ?? 'Casjoe Pay' ?></h2>
            <div class="wallet-id-badge">
                <ion-icon name="wallet-outline"></ion-icon>
                <span>Wallet ID: <?= $_SESSION['user_id'] ?? '' ?></span>
            </div>
        </div>

