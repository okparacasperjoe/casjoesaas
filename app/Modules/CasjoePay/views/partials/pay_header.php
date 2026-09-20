<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Casjoe Pay' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* ── Pay Dashboard Premium Theme ── */
        :root {
            --pay-bg: #050510;
            --pay-surface: #0c0c1e;
            --pay-surface-2: #111128;
            --pay-border: rgba(255,255,255,0.06);
            --pay-gold: #FFA600;
            --pay-gold-dim: rgba(255,166,0,0.15);
            --pay-green: #00d68f;
            --pay-red: #ff4d6a;
            --pay-blue: #4361ee;
            --pay-text: #e8e8f0;
            --pay-text-muted: #7a7a9a;
            --pay-radius: 20px;
        }

        body {
            background-color: var(--pay-bg);
            color: var(--pay-text);
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        /* ── Form Inputs Premium Style ── */
        .pay-form-card {
            background: var(--pay-surface);
            border: 1px solid var(--pay-border);
            border-radius: var(--pay-radius);
            padding: 30px;
            max-width: 500px;
            margin: 40px auto;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .pay-form-header {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 25px;
            color: #fff;
            text-align: center;
        }
        .pay-input-group {
            margin-bottom: 20px;
        }
        .pay-label {
            display: block;
            font-size: 0.8rem;
            color: var(--pay-text-muted);
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .pay-input, .pay-select {
            width: 100%;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--pay-border);
            color: #fff;
            padding: 14px 16px;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
            box-sizing: border-box;
        }
        .pay-input:focus, .pay-select:focus {
            outline: none;
            border-color: var(--pay-gold);
            background: rgba(255,166,0,0.02);
            box-shadow: 0 0 0 3px rgba(255,166,0,0.1);
        }
        .pay-select option {
            background: var(--pay-surface);
            color: #fff;
        }
        .pay-btn-primary {
            width: 100%;
            background: var(--pay-gold);
            color: #000;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }
        .pay-btn-primary:hover {
            background: #ffb52e;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255,166,0,0.25);
        }

        /* ── Input with Addon (Currency) ── */
        .pay-input-with-addon {
            display: flex;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--pay-border);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s;
        }
        .pay-input-with-addon:focus-within {
            border-color: var(--pay-gold);
            box-shadow: 0 0 0 3px rgba(255,166,0,0.1);
        }
        .pay-addon-select {
            background: rgba(255,255,255,0.05);
            border: none;
            border-right: 1px solid var(--pay-border);
            color: #fff;
            padding: 0 15px;
            font-weight: 600;
            outline: none;
            cursor: pointer;
        }
        .pay-addon-select option { background: var(--pay-surface); color: #fff; }
        .pay-addon-input {
            flex: 1;
            background: transparent;
            border: none;
            color: #fff;
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
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--pay-border);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s;
            color: var(--pay-text-muted);
            margin-bottom: 10px;
        }
        .pay-method-label:hover {
            background: rgba(255,255,255,0.04);
        }
        .pay-method-label:has(input:checked) {
            border-color: var(--pay-gold);
            background: var(--pay-gold-dim);
            color: #fff;
        }
        .pay-method-label input[type="radio"] {
            accent-color: var(--pay-gold);
            width: 18px;
            height: 18px;
            margin-right: 15px;
            cursor: pointer;
        }
        .pay-method-icon {
            font-size: 1.4rem;
            color: var(--pay-gold);
            margin-right: 10px;
        }

        /* ── Modern Tabs (Transfer) ── */
        .pay-tabs {
            display: flex;
            background: rgba(255,255,255,0.03);
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
            transition: all 0.3s;
            font-size: 0.85rem;
        }
        .pay-tab-btn:hover { color: #fff; }
        .pay-tab-btn.active {
            background: var(--pay-gold-dim);
            color: var(--pay-gold);
            box-shadow: 0 2px 8px rgba(255,166,0,0.12);
        }

        /* ── Sidebar Overrides ── */
        .app-container { display: flex !important; min-height: 100vh; position: relative; }
        .sidebar { width: 270px !important; min-width: 270px !important; background: rgba(3, 4, 20, 0.85) !important; backdrop-filter: blur(30px) !important; -webkit-backdrop-filter: blur(30px) !important; border-right: 1px solid rgba(255, 166, 0, 0.25) !important; box-shadow: 10px 0 35px rgba(0, 0, 0, 0.4) !important; position: fixed !important; top: 0 !important; bottom: 0 !important; left: 0 !important; height: 100vh !important; z-index: 1000 !important; padding: 0 !important; display: flex !important; flex-direction: column !important; }
        .main-content {
            flex: 1 !important;
            margin-left: 270px !important;
            padding: 30px;
            width: calc(100% - 270px);
            min-height: 100vh;
        }

        a { text-decoration: none; }
        .sidebar .brand { padding: 24px 20px !important; margin: 0 0 12px 0 !important; border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important; display: flex !important; align-items: center !important; font-size: 1.3rem !important; font-weight: 700 !important; color: #ffffff !important; }
        .sidebar .nav-link { color: rgba(255, 255, 255, 0.72) !important; border-radius: 14px !important; margin: 6px 14px !important; padding: 13px 18px !important; font-weight: 600 !important; font-size: 0.95rem !important; transition: all 0.25s ease !important; display: flex !important; align-items: center !important; gap: 12px !important; border: 1px solid transparent !important; border-left: 1px solid transparent !important; background: transparent !important; text-decoration: none !important; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #ffffff !important; background: linear-gradient(90deg, rgba(255, 166, 0, 0.18) 0%, rgba(0, 0, 102, 0.35) 100%) !important; border: 1px solid rgba(255, 166, 0, 0.3) !important; box-shadow: 0 4px 15px rgba(255, 166, 0, 0.12) !important; }
        .sidebar .nav-link ion-icon { color: #FFA600 !important; font-size: 1.35rem !important; margin-right: 0 !important; }
        .sidebar .nav-menu { list-style: none; padding: 0 !important; margin: 0 !important; }
        .sidebar .nav-item { margin: 0 !important; }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .top-bar h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
        }
        .wallet-id-badge {
            font-size: 0.75rem;
            background: var(--pay-gold-dim);
            color: var(--pay-gold);
            padding: 6px 16px;
            border-radius: 30px;
            font-weight: 600;
            border: 1px solid rgba(255,166,0,0.2);
        }

        /* ── Responsive ── */
        @media (max-width: 992px) {
            .sidebar { left: -260px !important; transition: left 0.3s ease; }
            .sidebar.active { left: 0 !important; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 80px; }
        }
        @media (max-width: 576px) {
            .pay-form-card { padding: 20px; margin: 20px 10px; }
            .pay-tabs { flex-direction: column; }
            .top-bar { flex-direction: column; align-items: flex-start; gap: 10px; }
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
            <div class="wallet-id-badge">Wallet ID: <?= $_SESSION['user_id'] ?? '' ?></div>
        </div>
