<!DOCTYPE html>
<html lang="en">

<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#020314">
    <title>Executive Ecosystem Dashboard | Casjoe BOS</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/assets/casjoe_logo.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="apple-mobile-web-app-title" content="Casjoe">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').catch(function() {});
            });
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --gd-bg: #030413;
            --gd-primary: #000066;
            --gd-primary-light: rgba(0, 0, 102, 0.4);
            --gd-gold: #FFA600;
            --gd-gold-glow: rgba(255, 166, 0, 0.25);
            --gd-glass-bg: rgba(255, 255, 255, 0.055);
            --gd-glass-bg-hover: rgba(255, 255, 255, 0.09);
            --gd-glass-border: rgba(255, 255, 255, 0.12);
            --gd-glass-border-hover: rgba(255, 166, 0, 0.45);
            --gd-text: #ffffff;
            --gd-text-muted: #94A3B8;
            --gd-radius: 24px;
            --gd-shadow: 0 16px 40px rgba(0, 0, 0, 0.45);
        }

        * {
            box-sizing: border-box;
        }

        html {
            overflow-x: hidden !important;
            width: 100% !important;
            max-width: 100vw !important;
            -webkit-text-size-adjust: 100%;
        }

        body {
            background: var(--gd-bg);
            background-image:
                radial-gradient(circle at 15% 10%, rgba(0, 0, 102, 0.65) 0%, transparent 45%),
                radial-gradient(circle at 85% 20%, rgba(255, 166, 0, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 50% 80%, rgba(10, 15, 60, 0.8) 0%, transparent 60%);
            background-attachment: fixed;
            color: var(--gd-text);
            font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            width: 100% !important;
            max-width: 100vw !important;
            overflow-x: hidden !important;
            position: relative;
            touch-action: pan-y pinch-zoom;
        }

        .app-container {
            min-height: 100vh;
            display: flex;
            width: 100% !important;
            max-width: 100vw !important;
            overflow-x: hidden !important;
            position: relative;
        }

        /* ── Sidebar ── */
        a { text-decoration: none; }
        .sidebar {
            width: 270px;
            background: rgba(3, 4, 20, 0.85) !important;
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-right: 1px solid rgba(255, 166, 0, 0.25) !important;
            box-shadow: 10px 0 35px rgba(0, 0, 0, 0.4);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }
        .sidebar .brand {
            padding: 24px 20px;
            margin-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.72) !important;
            border-radius: 14px;
            margin: 6px 14px;
            padding: 13px 18px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.25s ease;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #ffffff !important;
            background: linear-gradient(90deg, rgba(255, 166, 0, 0.18) 0%, rgba(0, 0, 102, 0.35) 100%);
            border: 1px solid rgba(255, 166, 0, 0.3);
            box-shadow: 0 4px 15px rgba(255, 166, 0, 0.12);
        }
        .sidebar .nav-link ion-icon {
            color: var(--gd-gold) !important;
            font-size: 1.35rem;
            margin-right: 12px;
        }

        .main-content {
            flex: 1;
            min-width: 0;
            width: 100%;
            max-width: 100%;
            margin-left: 270px;
            padding: 28px 40px 60px 40px;
            box-sizing: border-box;
            overflow-x: hidden;
        }

        /* ── Top Executive Header Banner ── */
        .exec-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--gd-glass-bg);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid var(--gd-glass-border);
            border-radius: 20px;
            padding: 16px 28px;
            margin-bottom: 28px;
            box-shadow: var(--gd-shadow);
        }
        .exec-topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .exec-badge {
            background: linear-gradient(135deg, var(--gd-gold) 0%, #ff8c00 100%);
            color: #000;
            font-weight: 800;
            font-size: 0.72rem;
            padding: 5px 12px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            box-shadow: 0 0 15px rgba(255, 166, 0, 0.4);
        }
        .exec-status-text {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
        }
        .exec-topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .status-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #34d399;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 999px;
        }
        .status-pill .dot {
            width: 8px;
            height: 8px;
            background: #34d399;
            border-radius: 50%;
            box-shadow: 0 0 10px #34d399;
        }

        /* ── Glass Welcome Hero ── */
        .welcome-hero {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(0, 0, 102, 0.35) 100%);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 166, 0, 0.3);
            color: white;
            padding: 38px 36px;
            border-radius: var(--gd-radius);
            position: relative;
            overflow: hidden;
            box-shadow: 0 24px 50px rgba(0, 0, 0, 0.5);
            margin-bottom: 28px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }
        .welcome-hero::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, rgba(255, 166, 0, 0.18) 0%, transparent 65%);
            pointer-events: none;
        }
        .welcome-label {
            font-size: 0.85rem;
            color: var(--gd-gold);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            margin-bottom: 6px;
        }
        .welcome-name {
            font-size: 2.4rem;
            font-weight: 800;
            margin: 0 0 16px 0;
            letter-spacing: -0.5px;
            line-height: 1.15;
            position: relative;
            z-index: 1;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .hero-org-card {
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid rgba(255, 166, 0, 0.45);
            padding: 10px 18px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            max-width: 100%;
            box-sizing: border-box;
        }
        .hero-org-card img {
            max-height: 56px;
            max-width: 140px;
            object-fit: contain;
            border-radius: 6px;
            flex-shrink: 0;
        }

        /* ── Cori AI Briefing Glass Banner ── */
        .ai-briefing-glass {
            padding: 20px 24px;
            background: rgba(0, 0, 0, 0.45);
            border: 1px solid rgba(255, 166, 0, 0.45);
            border-radius: 18px;
            display: flex;
            gap: 18px;
            align-items: flex-start;
            max-width: 100%;
            width: 100%;
            box-sizing: border-box;
            position: relative;
            z-index: 2;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            overflow: hidden;
        }
        .ai-briefing-glass > ion-icon {
            color: var(--gd-gold);
            font-size: 1.8rem;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .btn-briefing {
            background: linear-gradient(135deg, var(--gd-gold) 0%, #ff8c00 100%);
            color: #000;
            font-weight: 800;
            font-size: 0.82rem;
            padding: 9px 18px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(255, 166, 0, 0.3);
            max-width: 100%;
            box-sizing: border-box;
            text-align: center;
            justify-content: center;
            white-space: normal;
            word-break: break-word;
        }

        /* ── Attendance Glass Widget ── */
        .attendance-card {
            background: var(--gd-glass-bg);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid var(--gd-glass-border);
            border-radius: var(--gd-radius);
            padding: 22px 30px;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--gd-shadow);
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }
        .attendance-info h3 {
            margin: 0 0 4px 0;
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .attendance-info p {
            margin: 0;
            font-size: 0.95rem;
            color: var(--gd-text-muted);
        }
        .status-on { color: #34d399; font-weight: 700; }
        .status-off { color: #f87171; font-weight: 700; }

        .att-btn {
            padding: 12px 28px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            transition: all 0.25s;
        }
        .att-btn-in {
            background: linear-gradient(135deg, var(--gd-gold) 0%, #ff8c00 100%);
            color: #000;
            box-shadow: 0 8px 20px rgba(255, 166, 0, 0.3);
        }
        .att-btn-in:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(255, 166, 0, 0.45);
        }
        .att-btn-out {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.5);
            color: #fca5a5;
        }

        /* ── Section Header ── */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }
        .section-title {
            font-size: 1.45rem;
            font-weight: 800;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .section-subtitle {
            font-size: 0.9rem;
            color: var(--gd-text-muted);
        }

        /* ── Ecosystem Grid ── */
        .app-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(215px, 1fr));
            gap: 16px;
            margin-bottom: 50px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        /* ── Glass Module Tile (Compact & Sleek) ── */
        .module-tile {
            position: relative;
            background: var(--gd-glass-bg);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border-radius: 18px;
            padding: 18px 16px 16px;
            text-align: center;
            box-shadow: var(--gd-shadow);
            border: 1px solid var(--gd-glass-border);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            min-width: 0;
        }
        .module-tile:hover {
            transform: translateY(-5px);
            background: var(--gd-glass-bg-hover);
            border-color: var(--gd-glass-border-hover);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6), 0 0 20px rgba(255, 166, 0, 0.15);
        }
        .module-tile.inactive {
            background: rgba(15, 23, 42, 0.4);
            border-color: rgba(255, 255, 255, 0.05);
        }

        .module-toggle {
            position: absolute;
            top: 14px;
            right: 14px;
            z-index: 20;
        }

        /* Custom ON/OFF Pill Switch matching uploaded image exactly */
        .pill-switch {
            position: relative;
            display: inline-flex;
            align-items: center;
            width: 64px;
            height: 30px;
            border-radius: 30px;
            cursor: pointer;
            user-select: none;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        /* When OFF (Unchecked) - Yellow/Amber color */
        .pill-switch.pill-off {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: 2px solid #b45309;
        }

        /* When ON (Checked) - Vibrant Blue color */
        .pill-switch.pill-on {
            background: linear-gradient(135deg, #000066 0%, #0052cc 100%);
            border: 2px solid #00004d;
        }

        .pill-switch .pill-text {
            font-size: 0.75rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 0.5px;
            line-height: 1;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .pill-switch.pill-on .pill-text {
            margin-left: 10px;
        }
        .pill-switch.pill-off .pill-text {
            margin-left: 26px;
        }

        .pill-switch .pill-knob {
            position: absolute;
            top: 3px;
            width: 20px;
            height: 20px;
            background: #ffffff;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.25);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 3;
        }

        .pill-switch.pill-on .pill-knob {
            left: 38px;
        }
        .pill-switch.pill-off .pill-knob {
            left: 3px;
        }

        .module-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: var(--gd-text);
            width: 100%;
            transition: all 0.3s;
        }
        .module-tile.inactive .module-link {
            opacity: 0.45;
            filter: grayscale(85%);
            pointer-events: none;
        }

        .module-icon-box {
            width: 56px;
            height: 56px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            background: linear-gradient(135deg, rgba(0, 0, 102, 0.75) 0%, rgba(10, 15, 55, 0.95) 100%);
            border: 1px solid rgba(255, 166, 0, 0.28);
            box-shadow: 0 8px 18px rgba(0, 0, 102, 0.4);
            transition: all 0.3s;
        }
        .module-tile:hover .module-icon-box {
            transform: scale(1.06);
            border-color: var(--gd-gold);
        }
        .module-icon-box img {
            width: 36px;
            height: 36px;
            object-fit: contain;
        }
        .module-icon-box ion-icon {
            font-size: 1.8rem;
            color: var(--gd-gold);
        }

        .module-name {
            font-size: 0.98rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 4px;
        }
        .module-desc {
            font-size: 0.78rem;
            color: var(--gd-text-muted);
            line-height: 1.35;
            margin-bottom: 12px;
            min-height: 32px;
        }

        .module-status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            margin-bottom: 12px;
        }
        .module-status.active {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.35);
        }
        .module-status.inactive {
            background: rgba(245, 158, 11, 0.12);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .btn-open {
            width: 100%;
            padding: 8px 0;
            border-radius: 11px;
            font-weight: 700;
            font-size: 0.8rem;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.04) 100%);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #ffffff;
            cursor: pointer;
            transition: all 0.25s;
        }
        .module-tile:hover .btn-open {
            background: linear-gradient(135deg, var(--gd-gold) 0%, #ff8c00 100%);
            border-color: var(--gd-gold);
            color: #000000;
            box-shadow: 0 6px 20px rgba(255, 166, 0, 0.35);
        }

        /* ── LIGHT THEME OVERRIDES ── */
        html.light-theme {
            background: #f8fafc;
            background-image: none;
            color: #0f172a;
        }
        html.light-theme .exec-topbar, html.light-theme .attendance-card, html.light-theme .module-tile {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            color: #0f172a;
        }
        html.light-theme .exec-status-text, html.light-theme .attendance-info p, html.light-theme .section-subtitle, html.light-theme .module-desc {
            color: #475569;
        }
        html.light-theme .welcome-hero {
            background: linear-gradient(135deg, #000066 0%, #1a1aaa 100%);
            color: #ffffff;
        }
        html.light-theme .ai-briefing-glass {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.2);
        }
        html.light-theme .module-name {
            color: #0f172a;
        }
        html.light-theme .module-icon-box {
            background: #f1f5f9;
            border-color: #e2e8f0;
            box-shadow: none;
        }
        html.light-theme .module-tile.inactive {
            background: #f8fafc;
        }
        html.light-theme .module-tile:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            border-color: var(--gd-gold);
        }
        html.light-theme .btn-open {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
        }
        html.light-theme .pill-switch .pill-text {
            color: #ffffff;
        }

        /* ── Responsive ── */
        @media (max-width: 992px) {
            .sidebar {
                display: flex !important;
                position: fixed !important;
                top: 0 !important;
                bottom: 0 !important;
                left: -285px !important;
                width: 275px !important;
                height: 100vh !important;
                z-index: 10000 !important;
                transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                box-shadow: 10px 0 30px rgba(0, 0, 0, 0.5) !important;
            }
            .sidebar.active {
                left: 0 !important;
            }
            .main-content {
                margin-left: 0 !important;
                padding-top: calc(72px + env(safe-area-inset-top, 0px)) !important;
                padding-bottom: calc(96px + env(safe-area-inset-bottom, 0px)) !important;
                padding-left: 14px !important;
                padding-right: 14px !important;
                width: 100% !important;
                max-width: 100vw !important;
                box-sizing: border-box !important;
                overflow-x: hidden !important;
            }
            .exec-topbar {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 12px !important;
                padding: 14px 16px !important;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
            }
            .exec-status-text {
                font-size: 0.82rem !important;
                word-break: break-word;
            }
            .status-pill {
                font-size: 0.75rem !important;
                padding: 5px 12px !important;
                white-space: normal !important;
                word-break: break-word !important;
            }
            .welcome-hero {
                padding: 20px 14px !important;
                border-radius: 18px !important;
                margin-bottom: 20px !important;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
                overflow: hidden !important;
            }
            .welcome-hero::after {
                display: none !important;
            }
            .welcome-name {
                font-size: 1.6rem !important;
                word-break: break-word;
            }
            .attendance-card {
                flex-direction: column !important;
                gap: 14px !important;
                text-align: center !important;
                padding: 18px 14px !important;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
            }
            .hero-org-card {
                width: 100% !important;
                padding: 10px 14px !important;
                gap: 12px !important;
            }
            .hero-org-card img {
                max-height: 44px !important;
                max-width: 90px !important;
            }
            .ai-briefing-glass {
                padding: 14px 12px !important;
                gap: 12px !important;
                border-radius: 14px !important;
            }
            .ai-briefing-glass > ion-icon {
                font-size: 1.4rem !important;
            }
            .btn-briefing {
                width: 100% !important;
            }
        }

        @media (max-width: 480px) {
            .app-grid {
                grid-template-columns: 1fr !important;
                gap: 14px !important;
            }
            .main-content {
                padding-left: 10px !important;
                padding-right: 10px !important;
            }
            .welcome-hero {
                padding: 16px 12px !important;
            }
            .hero-org-card {
                padding: 8px 10px !important;
                gap: 8px !important;
            }
            .hero-org-card img {
                max-height: 38px !important;
                max-width: 70px !important;
            }
        }
    </style>
</head>

<body>
    <?php require __DIR__ . '/partials/mobile_nav.php'; ?>

    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="brand">
                <a href="/dashboard" style="display: flex; align-items: center;">
                    <img src="/assets/casjoe_logo.webp" alt="Casjoe BOS" style="height: 42px; width: auto;">
                </a>
            </div>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li><a href="/dashboard" class="nav-link active" style="display: flex; align-items: center;"><ion-icon name="grid"></ion-icon> Ecosystem Hub</a></li>
                <li><a href="/ai-office" class="nav-link" style="display: flex; align-items: center; border-left: 3px solid #FFA600; background: rgba(255,166,0,0.05);"><ion-icon name="briefcase"></ion-icon> AI Office</a></li>
                <li><a href="/profile" class="nav-link" style="display: flex; align-items: center;"><ion-icon name="person-circle-outline"></ion-icon> My Profile</a></li>
                <li><a href="/billing" class="nav-link" style="display: flex; align-items: center;"><ion-icon name="card-outline"></ion-icon> Billing & Subscription</a></li>
                <li><a href="/support" class="nav-link" style="display: flex; align-items: center;"><ion-icon name="headset-outline"></ion-icon> Priority Support</a></li>
                <li style="margin-top: 30px;"><a href="/logout" class="nav-link" style="display: flex; align-items: center; color: #fca5a5 !important;"><ion-icon name="log-out-outline" style="color: #f87171 !important;"></ion-icon> Secure Logout</a></li>
            </ul>

            <!-- Old Theme Toggle removed -->
        </aside>

        <!-- Main Dashboard Content -->
        <main class="main-content">
            <div class="welcome-hero">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 20px; width: 100%; max-width: 100%;">
                    <div style="min-width: 0; max-width: 100%;">
                        <div class="welcome-label">ENTERPRISE COMMAND CENTER</div>
                        <h1 class="welcome-name" style="margin-bottom: 6px;"><?= htmlspecialchars(\App\Core\Auth::user()['name'] ?? 'Executive Partner') ?></h1>
                        <?php if (!empty($tenantData['name'])): ?>
                        <div style="color: var(--gd-gold); font-size: 1.05rem; font-weight: 700; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <ion-icon name="business"></ion-icon> <?= htmlspecialchars($tenantData['name']) ?>
                            <?php if (!empty($tenantData['country'])): ?>
                                <span style="font-size: 0.8rem; background: rgba(255,166,0,0.15); padding: 2px 8px; border-radius: 6px; border: 1px solid rgba(255,166,0,0.3);"><?= htmlspecialchars($tenantData['country']) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($tenantData['name'])): ?>
                    <div class="hero-org-card">
                        <?php if (!empty($tenantData['logo'])): ?>
                            <img src="<?= htmlspecialchars($tenantData['logo']) ?>" alt="Company Logo">
                        <?php else: ?>
                            <div style="width: 44px; height: 44px; flex-shrink: 0; background: rgba(255, 166, 0, 0.15); border: 1px solid rgba(255, 166, 0, 0.4); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--gd-gold); font-size: 1.4rem;">
                                <ion-icon name="business-outline"></ion-icon>
                            </div>
                        <?php endif; ?>
                        <div style="min-width: 0; flex: 1;">
                            <div style="font-size: 0.72rem; color: var(--gd-text-muted); text-transform: uppercase; letter-spacing: 1px; font-weight: 700; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                Active Organization
                                <?php if (!empty($tenantData['currency'])): ?>
                                    <span style="background: rgba(255,255,255,0.12); padding: 1px 6px; border-radius: 4px; font-size: 0.65rem; color: var(--gd-gold);"><?= htmlspecialchars($tenantData['currency']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div style="font-weight: 800; color: #fff; font-size: 1rem; word-break: break-word;"><?= htmlspecialchars($tenantData['name']) ?></div>
                            <a href="/erp/settings" style="font-size: 0.73rem; color: var(--gd-gold); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; margin-top: 2px;">
                                <ion-icon name="settings-outline"></ion-icon> Edit Logo &amp; Info
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($aiBriefing)): ?>
                <div class="ai-briefing-glass">
                    <ion-icon name="sparkles"></ion-icon>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-wrap: wrap; gap: 8px;">
                            <strong style="color: var(--gd-gold); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1.2px;">
                                <ion-icon name="analytics-outline" style="vertical-align: middle; font-size: 1.1rem; margin-right: 4px;"></ion-icon>
                                Cori AI Daily Executive Briefing
                            </strong>
                            <span style="font-size: 0.72rem; background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); padding: 3px 10px; border-radius: 999px; font-weight: 700;">
                                ● LIVE TELEMETRY
                            </span>
                        </div>
                        <div style="font-size: 0.93rem; color: rgba(255, 255, 255, 0.92); line-height: 1.6; margin-bottom: 14px; word-break: break-word;">
                            <?= $aiBriefing ?>
                        </div>
                        <div>
                            <a href="/erp/ai-manager" class="btn btn-briefing">
                                Read More &amp; Launch Full AI Manager <ion-icon name="arrow-forward-outline"></ion-icon>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Staff Attendance Glass Widget -->
            <?php if (isset($employee) && $employee): ?>
            <div class="attendance-card">
                <div class="attendance-info">
                    <h3><ion-icon name="time-outline" style="color: var(--gd-gold);"></ion-icon> Staff Portal Attendance</h3>
                    <p>
                        <?= ($isCheckedIn ?? false)
                            ? 'Current Status: <span class="status-on">Checked In</span> since '.date('H:i', strtotime($checkInTime))
                            : 'Current Status: <span class="status-off">Checked Out</span>'
                        ?>
                    </p>
                </div>
                <div>
                    <?php if (isset($isCheckedIn) && $isCheckedIn): ?>
                        <form method="POST" action="/erp/my-portal/checkout" style="margin:0;">
                            <input type="hidden" name="attendance_id" value="<?= $activeAttendanceId ?>">
                            <button type="submit" class="att-btn att-btn-out">Check Out Now</button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="/erp/my-portal/checkin" style="margin:0;">
                            <button type="submit" class="att-btn att-btn-in">Check In Now</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Ecosystem Grid Section -->
            <div class="section-header">
                <div>
                    <h2 class="section-title">
                        <ion-icon name="apps" style="color: var(--gd-gold);"></ion-icon>
                        Your Casjoe BOS Suite
                    </h2>
                </div>
            </div>

            <div class="app-grid">
                <?php foreach ($modules as $module): ?>
                    <?php $isActive = (isset($module['status']) && $module['status'] === 'active'); ?>
                    <div class="module-tile <?= $isActive ? '' : 'inactive' ?>">
                        
                        <!-- Instant ON/OFF Pill Toggle -->
                        <div class="module-toggle">
                            <div class="pill-switch <?= $isActive ? 'pill-on' : 'pill-off' ?>" onclick="handleDashboardPillClick(event, this, '<?= htmlspecialchars($module['slug']) ?>', '<?= htmlspecialchars(addslashes($module['name'])) ?>')" title="Toggle <?= htmlspecialchars($module['name']) ?>">
                                <span class="pill-text"><?= $isActive ? 'ON' : 'OFF' ?></span>
                                <span class="pill-knob"></span>
                                <input type="checkbox" class="d-none module-checkbox" <?= $isActive ? 'checked' : '' ?>>
                            </div>
                        </div>

                        <a href="<?= $isActive ? htmlspecialchars($module['url']) : '#' ?>" class="module-link card-link">
                            <div class="module-icon-box">
                                <?php if (isset($module['image'])): ?>
                                    <img src="<?= htmlspecialchars($module['image']) ?>" alt="<?= htmlspecialchars($module['name']) ?>">
                                <?php else: ?>
                                    <ion-icon name="<?= htmlspecialchars($module['icon']) ?>"></ion-icon>
                                <?php endif; ?>
                            </div>
                            <span class="module-name"><?= htmlspecialchars($module['name']) ?></span>
                            <div class="module-desc"><?= htmlspecialchars($module['description']) ?></div>
                            <span class="module-status <?= $isActive ? 'active' : 'inactive' ?>">
                                <?= $isActive ? '● Active' : '○ Deactivated' ?>
                            </span>
                            <button class="btn-open"><?= $isActive ? 'Launch Module' : 'Activate to Launch' ?></button>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Executive Glass Footer Bar below modules -->
            <div class="exec-topbar" style="margin-top: 36px; margin-bottom: 24px;">
                <div class="exec-topbar-left">
                    <span class="exec-badge">CASJOE BOS v4.8</span>
                    <span class="exec-status-text">Unified Enterprise Business Operating System</span>
                </div>
                <div class="exec-topbar-right">
                    <div class="status-pill">
                        <span class="dot"></span> All Enterprise Systems Online (99.99%)
                    </div>
                </div>
            </div>

            <script>
                function handleDashboardPillClick(e, pillEl, slug, modName) {
                    if (e) {
                        e.stopPropagation();
                        e.preventDefault();
                    }
                    const checkbox = pillEl.querySelector('.module-checkbox');
                    checkbox.checked = !checkbox.checked;
                    toggleModule(checkbox, slug, pillEl, modName);
                }

                function toggleModule(checkbox, slug, pillEl = null, modName = '') {
                    const isActive = checkbox.checked;
                    const card = checkbox.closest('.module-tile');
                    const link = card.querySelector('.card-link');
                    const statusBadge = card.querySelector('.module-status');
                    const btnOpen = card.querySelector('.btn-open');
                    if (!pillEl) {
                        pillEl = card.querySelector('.pill-switch');
                    }

                    updateUI(isActive);

                    fetch('/Modules/toggle', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ slug: slug, active: isActive })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            Swal.fire({
                                title: 'Notice',
                                text: data.error,
                                icon: 'warning',
                                background: '#0a1128',
                                color: '#fff'
                            });
                            checkbox.checked = !isActive;
                            updateUI(!isActive);
                        } else {
                            const nameToUse = data.module_name || modName || 'Module';
                            if (isActive) {
                                // Pop up green check mark good when module is activated
                                Swal.fire({
                                    title: '<span style="color: #10B981; font-weight: 800;">Module Activated!</span>',
                                    html: `<div style="text-align: center; margin-top: 10px;">
                                               <p style="color: #e2e8f0; font-size: 1.1rem; margin-bottom: 10px;">
                                                   <b>${nameToUse}</b> is now active in your workspace!
                                               </p>
                                           </div>`,
                                    icon: 'success',
                                    iconColor: '#10B981',
                                    confirmButtonText: 'Got it, let\'s go!',
                                    confirmButtonColor: '#10B981',
                                    background: '#0a1128',
                                    color: '#fff',
                                    customClass: {
                                        popup: 'rounded-4 border border-secondary border-opacity-25 shadow-lg'
                                    }
                                });
                            } else {
                                // When a user deactivates a module, no email should be sent
                                Swal.fire({
                                    title: 'Module Deactivated',
                                    text: `${nameToUse} has been turned off. No email notification was sent.`,
                                    icon: 'info',
                                    timer: 2600,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end',
                                    background: '#0a1128',
                                    color: '#fff'
                                });
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Toggle error:', error);
                        checkbox.checked = !isActive;
                        updateUI(!isActive);
                    });

                    function updateUI(active) {
                        if (pillEl) {
                            const pillText = pillEl.querySelector('.pill-text');
                            if (active) {
                                pillEl.className = 'pill-switch pill-on';
                                if (pillText) pillText.textContent = 'ON';
                            } else {
                                pillEl.className = 'pill-switch pill-off';
                                if (pillText) pillText.textContent = 'OFF';
                            }
                        }
                        if (active) {
                            card.classList.remove('inactive');
                            link.style.pointerEvents = 'auto';
                            link.style.opacity = '1';
                            link.style.filter = 'none';
                            statusBadge.textContent = '● Active';
                            statusBadge.className = 'module-status active';
                            btnOpen.textContent = 'Launch Module';
                            link.setAttribute('href', getModuleUrl(slug));
                        } else {
                            card.classList.add('inactive');
                            link.style.pointerEvents = 'none';
                            link.style.opacity = '0.45';
                            link.style.filter = 'grayscale(85%)';
                            statusBadge.textContent = '○ Deactivated';
                            statusBadge.className = 'module-status inactive';
                            btnOpen.textContent = 'Activate to Launch';
                            link.setAttribute('href', '#');
                        }
                    }

                    function getModuleUrl(slug) {
                        const map = {
                            'casjoe-bos': '/erp',
                            'casjoe-pay': '/pay',
                            'casjoe-academy': '/academy',
                            'casjoe-mail': '/mail',
                            'casjoe-cloud': '/cloud',
                            'casjoe-links': '/links',
                            'casjoe-smart-forms': '/smart-forms',
                            'casjoe-mart': '/shop',
                            'casjoe-ai': '/ai'
                        };
                        return map[slug] || '/dashboard';
                    }
                }
            </script>
            <?php require __DIR__ . '/partials/floating_theme_widget.php'; ?>
        </main>
    </div>
    <?php if (file_exists(__DIR__ . '/../../Views/partials/footer.php')) require __DIR__ . '/../../Views/partials/footer.php'; ?>
</body>
</html>
