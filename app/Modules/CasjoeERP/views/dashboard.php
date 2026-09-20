<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Dashboard | Casjoe BOS</title>
    <meta name="description" content="Enterprise Resource Planning dashboard with AI Manager, staff recognition, and business intelligence.">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        :root {
            --erp-bg: #030014;
            --erp-bg-text: #ffffff;
            --erp-bg-text-muted: #94a3b8;
            --erp-surface: #ffffff;
            --erp-primary: #000066;
            --erp-primary-rgb: 0,0,102;
            --erp-gold: #FFA600;
            --erp-gold-rgb: 255,166,0;
            --erp-text: #0f172a;
            --erp-text-muted: #475569;
            --erp-border: rgba(0,0,0,0.08);
            --erp-radius: 20px;
            --erp-shadow: 0 4px 20px rgba(0,0,0,0.1);
            --erp-shadow-hover: 0 10px 30px rgba(0,0,0,0.15);
            --erp-green: #10b981;
            --erp-red: #ef4444;
            --erp-blue: #3b82f6;
            --erp-purple: #8b5cf6;
        }

        body { background: var(--erp-bg); color: var(--erp-bg-text); font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }

        .app-container { min-height: 100vh; display: flex; }
        a { text-decoration: none; }
        .sidebar { background-color: #030014 !important; border-right: 2px solid var(--erp-gold) !important; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8) !important; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #ffffff !important; background: rgba(255,255,255,0.1); }
        .sidebar .nav-link ion-icon { color: inherit !important; }

        @media (min-width: 992px) {
            .erp-main {
                margin-left: var(--sidebar-width, 260px);
            }
        }
        .erp-main { flex: 1; padding: 0; overflow-y: auto; }

        /* ── Hero Banner ── */
        .erp-hero {
            background: linear-gradient(135deg, #000044 0%, #000077 50%, #1a1aaa 100%);
            color: white;
            padding: 32px 32px 28px;
            position: relative;
            overflow: hidden;
        }
        .erp-hero::before {
            content: '';
            position: absolute;
            top: -60%; right: -10%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(255,166,0,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        .erp-hero-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            position: relative;
            z-index: 1;
        }
        .erp-hero .label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 2px; font-weight: 600; margin-bottom: 4px; }
        .erp-hero h1 { font-size: 1.5rem; font-weight: 800; margin: 0; letter-spacing: -0.5px; }
        .erp-hero .subtitle { font-size: 0.85rem; margin-top: 4px; }
        .erp-hero-actions {
            display: flex;
            gap: 10px;
        }
        .erp-hero-actions a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 18px;
            border-radius: 12px;
            font-size: 0.82rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-ai {
            background: linear-gradient(135deg, var(--erp-gold), #ff8c00);
            color: #000;
        }
        .btn-ai:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255,166,0,0.4); }
        .btn-secondary-hero {
            background: rgba(255,255,255,0.1);
            color: white;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.15);
        }
        .btn-secondary-hero:hover { background: rgba(255,255,255,0.2); }

        /* ── Quick Stats Row ── */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 8px;
            position: relative;
            z-index: 1;
        }
        .qs-card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: all 0.2s;
        }
        .qs-card:hover { background: rgba(255,255,255,0.14); transform: translateY(-1px); }
        .qs-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }
        .qs-data h4 { margin: 0; font-size: 1.3rem; font-weight: 800; line-height: 1; }
        .qs-data span { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }

        /* ── Dashboard Grid ── */
        .erp-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            padding: 28px 32px;
        }

        /* ── Cards ── */
        .erp-card {
            background: var(--erp-surface);
            border-radius: var(--erp-radius);
            box-shadow: var(--erp-shadow);
            overflow: hidden;
            transition: box-shadow 0.2s;
        }
        .erp-card:hover { box-shadow: var(--erp-shadow-hover); }
        .erp-card-header {
            padding: 20px 24px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .erp-card-header h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: var(--erp-text);
        }
        .erp-card-header .badge {
            font-size: 0.7rem;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
        }
        .erp-card-body { padding: 16px 24px 24px; }

        /* ── Module Cards Grid ── */
        .module-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 14px;
        }
        .mod-card {
            background: var(--erp-surface);
            border: 1px solid var(--erp-border);
            border-radius: 16px;
            padding: 22px 16px;
            text-align: center;
            transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
            cursor: pointer;
            display: block;
            color: var(--erp-text);
            position: relative;
            overflow: hidden;
        }
        .mod-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            transition: height 0.3s;
        }
        .mod-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--erp-shadow-hover);
        }
        .mod-card:hover::before { height: 6px; }
        .mod-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 12px;
            color: white;
        }
        .mod-title { font-size: 0.88rem; font-weight: 700; margin-bottom: 4px; }
        .mod-stat { font-size: 0.72rem; color: var(--erp-text-muted); }

        /* ── Staff of the Month ── */
        .staff-card {
            background: linear-gradient(135deg, #f8fafc, #eef2ff);
            border-radius: var(--erp-radius);
            padding: 28px 24px;
            text-align: center;
            border: 1px solid rgba(var(--erp-primary-rgb), 0.08);
            position: relative;
            overflow: hidden;
        }
        .staff-card::before {
            content: '⭐';
            position: absolute;
            top: 12px; right: 16px;
            font-size: 1.5rem;
            animation: starPulse 2s ease-in-out infinite;
        }
        @keyframes starPulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.15); opacity: 1; }
        }
        .staff-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--erp-gold), #ff8c00);
            color: #000;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
        }
        .staff-avatar {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--erp-primary), #1a1aaa);
            color: white;
            font-size: 1.8rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            box-shadow: 0 4px 16px rgba(var(--erp-primary-rgb), 0.3);
            border: 3px solid var(--erp-gold);
        }
        .staff-name { font-size: 1.1rem; font-weight: 800; color: var(--erp-text); margin-bottom: 2px; }
        .staff-role { font-size: 0.8rem; color: var(--erp-text-muted); }

        /* ── Goals ── */
        .goal-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid var(--erp-border);
        }
        .goal-item:last-child { border-bottom: none; }
        .goal-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: white;
            flex-shrink: 0;
        }
        .goal-info { flex: 1; }
        .goal-title { font-size: 0.85rem; font-weight: 600; color: var(--erp-text); margin-bottom: 6px; }
        .goal-bar {
            height: 6px;
            background: #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }
        .goal-bar-fill {
            height: 100%;
            border-radius: 6px;
            transition: width 1.2s ease;
            min-width: 2px;
        }
        .goal-pct { font-size: 0.78rem; font-weight: 700; color: var(--erp-text-muted); flex-shrink: 0; }

        /* ── Quick Actions ── */
        .qa-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .qa-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid var(--erp-border);
            color: var(--erp-text);
            font-size: 0.82rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .qa-btn:hover {
            background: rgba(var(--erp-primary-rgb), 0.04);
            border-color: rgba(var(--erp-primary-rgb), 0.15);
            transform: translateY(-1px);
        }
        .qa-btn ion-icon { font-size: 1.2rem; color: #FFA600 !important; }

        /* ── Activity Feed ── */
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--erp-border);
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            margin-top: 6px;
            flex-shrink: 0;
        }
        .activity-text { font-size: 0.82rem; color: var(--erp-text); line-height: 1.4; }
        .activity-time { font-size: 0.7rem; color: var(--erp-text-muted); margin-top: 2px; }

        /* ── Alerts Row ── */
        .alerts-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-bottom: 4px;
        }
        .alert-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid;
        }
        .alert-chip ion-icon { font-size: 1.1rem; }
        .alert-warn { background: #fffbeb; border-color: #fbbf24; color: #92400e; }
        .alert-warn ion-icon { color: #f59e0b; }
        .alert-info { background: #eff6ff; border-color: #93c5fd; color: #1e40af; }
        .alert-info ion-icon { color: #3b82f6; }
        .alert-success { background: #f0fdf4; border-color: #86efac; color: #166534; }
        .alert-success ion-icon { color: #22c55e; }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .erp-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 991px) {
            .app-container { flex-direction: column; }
            .erp-main { margin-left: 0 !important; width: 100% !important; }
            .erp-hero { padding: 20px 16px; }
            .erp-hero-top { flex-direction: column; gap: 16px; }
            .erp-grid { padding: 16px; gap: 16px; }
            .quick-stats { grid-template-columns: repeat(2, 1fr); }
            .module-grid { grid-template-columns: repeat(2, 1fr); }
            .qa-grid { grid-template-columns: 1fr; }
        }
    
        /* ── Light Theme Overrides ── */
        html.light-theme body {
            background: #f8fafc !important;
            color: #0f172a !important;
        }

        html.light-theme .erp-main {
            background: #f8fafc !important;
        }

        html.light-theme .erp-hero {
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%) !important;
            color: #0f172a !important;
            border-bottom: 1px solid #e2e8f0;
        }

        html.light-theme .erp-hero .label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 2px; font-weight: 600; margin-bottom: 4px; }

        html.light-theme .erp-hero h1 { font-size: 1.5rem; font-weight: 800; margin: 0; letter-spacing: -0.5px; }

        html.light-theme .erp-hero .subtitle { font-size: 0.85rem; margin-top: 4px; }

        html.light-theme .btn-secondary-hero {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        html.light-theme .btn-secondary-hero:hover {
            background: #f1f5f9 !important;
        }

        html.light-theme .qs-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03) !important;
        }

        html.light-theme .qs-card:hover {
            background: #ffffff !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06) !important;
        }

        html.light-theme .qs-data h4 { margin: 0; font-size: 1.3rem; font-weight: 800; line-height: 1; }

        html.light-theme .qs-data span { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }

        html.light-theme .erp-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02) !important;
        }

        html.light-theme .erp-card-header h3 {
            color: #0f172a !important;
        }

        html.light-theme .mod-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #0f172a !important;
        }

        html.light-theme .mod-title {
            color: #0f172a !important;
        }

        html.light-theme .mod-stat {
            color: #64748b !important;
        }

        html.light-theme .qa-btn {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            color: #0f172a !important;
        }

        html.light-theme .qa-btn:hover {
            background: #edf2f7 !important;
        }

        html.light-theme .staff-card {
            background: linear-gradient(135deg, #ffffff, #f1f5f9) !important;
            border: 1px solid #e2e8f0 !important;
        }

        html.light-theme .staff-name {
            color: #0f172a !important;
        }

        html.light-theme .staff-role {
            color: #64748b !important;
        }

        html.light-theme .goal-title {
            color: #0f172a !important;
        }

        html.light-theme .goal-pct {
            color: #64748b !important;
        }

        html.light-theme .activity-text {
            color: #0f172a !important;
        }

        html.light-theme .activity-time {
            color: #64748b !important;
        }
    
        /* ── Dark Theme Explicit Overrides ── */
        html.dark-theme body,
        html.dark-theme .erp-main {
            background: #030014 !important;
            color: #ffffff !important;
        }

        html.dark-theme .erp-hero {
            background: linear-gradient(135deg, #000044 0%, #000077 50%, #1a1aaa 100%) !important;
            color: #ffffff !important;
        }

        html.dark-theme .erp-hero .label {
            color: #a0aec0 !important;
            opacity: 1 !important;
        }

        html.dark-theme .erp-hero h1 {
            color: #ffffff !important;
        }

        html.dark-theme .erp-hero .subtitle {
            color: #cbd5e1 !important;
            opacity: 1 !important;
        }

        html.dark-theme .btn-secondary-hero {
            background: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
        }

        html.dark-theme .qs-card {
            background: rgba(255, 255, 255, 0.08) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        html.dark-theme .qs-data h4 {
            color: #ffffff !important;
        }

        html.dark-theme .qs-data span {
            color: #94a3b8 !important;
            opacity: 1 !important;
        }

        html.dark-theme .erp-card {
            background: #090d1f !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        html.dark-theme .erp-card-header h3 {
            color: #ffffff !important;
        }

        html.dark-theme .mod-card {
            background: #090d1f !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
        }

        html.dark-theme .mod-title {
            color: #ffffff !important;
        }

        html.dark-theme .mod-stat {
            color: #94a3b8 !important;
        }

        html.dark-theme .qa-btn {
            background: #090d1f !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
        }

        html.dark-theme .staff-card {
            background: linear-gradient(135deg, #090d1f, #11182e) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        html.dark-theme .staff-name {
            color: #ffffff !important;
        }

        html.dark-theme .staff-role {
            color: #94a3b8 !important;
        }

        html.dark-theme .goal-title {
            color: #ffffff !important;
        }

        html.dark-theme .activity-text {
            color: #ffffff !important;
        }
    
        /* ── Dark Theme (Default & Explicit) ── */
        html:not(.light-theme) body,
        html.dark-theme body,
        html:not(.light-theme) .erp-main,
        html.dark-theme .erp-main {
            background: #030014 !important;
            color: #ffffff !important;
        }

        html:not(.light-theme) .erp-hero,
        html.dark-theme .erp-hero {
            background: linear-gradient(135deg, #000044 0%, #000077 50%, #1a1aaa 100%) !important;
            color: #ffffff !important;
            border-bottom: none !important;
        }

        html:not(.light-theme) .erp-hero .label,
        html.dark-theme .erp-hero .label {
            color: #a0aec0 !important;
            opacity: 1 !important;
        }

        html:not(.light-theme) .erp-hero h1,
        html.dark-theme .erp-hero h1 {
            color: #ffffff !important;
        }

        html:not(.light-theme) .erp-hero .subtitle,
        html.dark-theme .erp-hero .subtitle {
            color: #cbd5e1 !important;
            opacity: 1 !important;
        }

        html:not(.light-theme) .btn-secondary-hero,
        html.dark-theme .btn-secondary-hero {
            background: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
        }

        html:not(.light-theme) .qs-card,
        html.dark-theme .qs-card {
            background: rgba(255, 255, 255, 0.08) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        html:not(.light-theme) .qs-data h4,
        html.dark-theme .qs-data h4 {
            color: #ffffff !important;
        }

        html:not(.light-theme) .qs-data span,
        html.dark-theme .qs-data span {
            color: #94a3b8 !important;
            opacity: 1 !important;
        }

        html:not(.light-theme) .erp-card,
        html.dark-theme .erp-card {
            background: #090d1f !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        html:not(.light-theme) .erp-card-header h3,
        html.dark-theme .erp-card-header h3 {
            color: #ffffff !important;
        }

        html:not(.light-theme) .mod-card,
        html.dark-theme .mod-card {
            background: #090d1f !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
        }

        html:not(.light-theme) .mod-title,
        html.dark-theme .mod-title {
            color: #ffffff !important;
        }

        html:not(.light-theme) .mod-stat,
        html.dark-theme .mod-stat {
            color: #94a3b8 !important;
        }

        html:not(.light-theme) .qa-btn,
        html.dark-theme .qa-btn {
            background: #090d1f !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
        }

        html:not(.light-theme) .staff-card,
        html.dark-theme .staff-card {
            background: linear-gradient(135deg, #090d1f, #11182e) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        html:not(.light-theme) .staff-name,
        html.dark-theme .staff-name {
            color: #ffffff !important;
        }

        html:not(.light-theme) .staff-role,
        html.dark-theme .staff-role {
            color: #94a3b8 !important;
        }

        html:not(.light-theme) .goal-title,
        html.dark-theme .goal-title {
            color: #ffffff !important;
        }

        html:not(.light-theme) .activity-text,
        html.dark-theme .activity-text {
            color: #ffffff !important;
        }

        /* ── Light Theme Explicit Overrides ── */
        html.light-theme body,
        html.light-theme .erp-main {
            background: #f8fafc !important;
            color: #0f172a !important;
        }

        html.light-theme .erp-hero {
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%) !important;
            color: #0f172a !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        html.light-theme .erp-hero .label {
            color: #64748b !important;
            opacity: 1 !important;
        }

        html.light-theme .erp-hero h1 {
            color: #0f172a !important;
        }

        html.light-theme .erp-hero .subtitle {
            color: #475569 !important;
            opacity: 1 !important;
        }

        html.light-theme .btn-secondary-hero {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02) !important;
        }

        html.light-theme .btn-secondary-hero:hover {
            background: #f1f5f9 !important;
        }

        html.light-theme .qs-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03) !important;
        }

        html.light-theme .qs-card:hover {
            background: #ffffff !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06) !important;
        }

        html.light-theme .qs-data h4 {
            color: #0f172a !important;
        }

        html.light-theme .qs-data span {
            color: #64748b !important;
            opacity: 1 !important;
        }

        html.light-theme .erp-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02) !important;
        }

        html.light-theme .erp-card-header h3 {
            color: #0f172a !important;
        }

        html.light-theme .mod-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #0f172a !important;
        }

        html.light-theme .mod-title {
            color: #0f172a !important;
        }

        html.light-theme .mod-stat {
            color: #64748b !important;
        }

        html.light-theme .qa-btn {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            color: #0f172a !important;
        }

        html.light-theme .qa-btn:hover {
            background: #edf2f7 !important;
        }

        html.light-theme .staff-card {
            background: linear-gradient(135deg, #ffffff, #f1f5f9) !important;
            border: 1px solid #e2e8f0 !important;
        }

        html.light-theme .staff-name {
            color: #0f172a !important;
        }

        html.light-theme .staff-role {
            color: #64748b !important;
        }

        html.light-theme .goal-title {
            color: #0f172a !important;
        }

        html.light-theme .goal-pct {
            color: #64748b !important;
        }

        html.light-theme .activity-text {
            color: #0f172a !important;
        }

        html.light-theme .activity-time {
            color: #64748b !important;
        }
    
        /* ── Universal Yellow Borders & Gold Glow Overlay (Both Themes) ── */
        .qs-card,
        .mod-card,
        .erp-card,
        .staff-card,
        .qa-btn {
            border: 1.5px solid #FFA600 !important;
            box-shadow: 0 4px 18px rgba(255, 166, 0, 0.18), inset 0 0 10px rgba(255, 166, 0, 0.04) !important;
            transition: all 0.25s ease-in-out !important;
        }

        .qs-card:hover,
        .mod-card:hover,
        .erp-card:hover,
        .staff-card:hover,
        .qa-btn:hover {
            border-color: #FFB800 !important;
            box-shadow: 0 6px 24px rgba(255, 166, 0, 0.35), inset 0 0 15px rgba(255, 166, 0, 0.08) !important;
        }

        /* Emojis & Icon Yellow Outline Glow */
        .qs-icon,
        .mod-icon,
        .goal-icon,
        .staff-avatar,
        .staff-badge {
            border: 2px solid #FFA600 !important;
            box-shadow: 0 0 12px rgba(255, 166, 0, 0.4) !important;
        }

        /* ── Dark Theme Scoped Colors ── */
        html:not(.light-theme) body,
        html.dark-theme body,
        html:not(.light-theme) .erp-main,
        html.dark-theme .erp-main {
            background: #030014 !important;
            color: #ffffff !important;
        }

        html:not(.light-theme) .erp-hero,
        html.dark-theme .erp-hero {
            background: linear-gradient(135deg, #000044 0%, #000077 50%, #1a1aaa 100%) !important;
            color: #ffffff !important;
            border-bottom: 1.5px solid #FFA600 !important;
        }

        html:not(.light-theme) .erp-hero .label,
        html.dark-theme .erp-hero .label {
            color: #a0aec0 !important;
            opacity: 1 !important;
        }

        html:not(.light-theme) .erp-hero h1,
        html.dark-theme .erp-hero h1 {
            color: #ffffff !important;
        }

        html:not(.light-theme) .erp-hero .subtitle,
        html.dark-theme .erp-hero .subtitle {
            color: #cbd5e1 !important;
            opacity: 1 !important;
        }

        html:not(.light-theme) .btn-secondary-hero,
        html.dark-theme .btn-secondary-hero {
            background: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
            border: 1.5px solid #FFA600 !important;
        }

        html:not(.light-theme) .qs-card,
        html.dark-theme .qs-card {
            background: rgba(255, 255, 255, 0.08) !important;
        }

        html:not(.light-theme) .qs-data h4,
        html.dark-theme .qs-data h4 {
            color: #ffffff !important;
        }

        html:not(.light-theme) .qs-data span,
        html.dark-theme .qs-data span {
            color: #94a3b8 !important;
            opacity: 1 !important;
        }

        html:not(.light-theme) .erp-card,
        html.dark-theme .erp-card {
            background: #090d1f !important;
        }

        html:not(.light-theme) .erp-card-header h3,
        html.dark-theme .erp-card-header h3 {
            color: #ffffff !important;
        }

        html:not(.light-theme) .mod-card,
        html.dark-theme .mod-card {
            background: #090d1f !important;
            color: #ffffff !important;
        }

        html:not(.light-theme) .mod-title,
        html.dark-theme .mod-title {
            color: #ffffff !important;
        }

        html:not(.light-theme) .mod-stat,
        html.dark-theme .mod-stat {
            color: #94a3b8 !important;
        }

        html:not(.light-theme) .qa-btn,
        html.dark-theme .qa-btn {
            background: #090d1f !important;
            color: #ffffff !important;
        }

        html:not(.light-theme) .staff-card,
        html.dark-theme .staff-card {
            background: linear-gradient(135deg, #090d1f, #11182e) !important;
        }

        html:not(.light-theme) .staff-name,
        html.dark-theme .staff-name {
            color: #ffffff !important;
        }

        html:not(.light-theme) .staff-role,
        html.dark-theme .staff-role {
            color: #94a3b8 !important;
        }

        html:not(.light-theme) .goal-title,
        html.dark-theme .goal-title {
            color: #ffffff !important;
        }

        html:not(.light-theme) .activity-text,
        html.dark-theme .activity-text {
            color: #ffffff !important;
        }

        /* ── Light Theme Scoped Colors ── */
        html.light-theme body,
        html.light-theme .erp-main {
            background: #f8fafc !important;
            color: #0f172a !important;
        }

        html.light-theme .erp-hero {
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%) !important;
            color: #0f172a !important;
            border-bottom: 1.5px solid #FFA600 !important;
        }

        html.light-theme .erp-hero .label {
            color: #64748b !important;
            opacity: 1 !important;
        }

        html.light-theme .erp-hero h1 {
            color: #0f172a !important;
        }

        html.light-theme .erp-hero .subtitle {
            color: #475569 !important;
            opacity: 1 !important;
        }

        html.light-theme .btn-secondary-hero {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1.5px solid #FFA600 !important;
        }

        html.light-theme .qs-card {
            background: #ffffff !important;
        }

        html.light-theme .qs-data h4 {
            color: #0f172a !important;
        }

        html.light-theme .qs-data span {
            color: #64748b !important;
            opacity: 1 !important;
        }

        html.light-theme .erp-card {
            background: #ffffff !important;
        }

        html.light-theme .erp-card-header h3 {
            color: #0f172a !important;
        }

        html.light-theme .mod-card {
            background: #ffffff !important;
            color: #0f172a !important;
        }

        html.light-theme .mod-title {
            color: #0f172a !important;
        }

        html.light-theme .mod-stat {
            color: #64748b !important;
        }

        html.light-theme .qa-btn {
            background: #f8fafc !important;
            color: #0f172a !important;
        }

        html.light-theme .staff-card {
            background: linear-gradient(135deg, #ffffff, #f1f5f9) !important;
        }

        html.light-theme .staff-name {
            color: #0f172a !important;
        }

        html.light-theme .staff-role {
            color: #64748b !important;
        }

        html.light-theme .goal-title {
            color: #0f172a !important;
        }

        html.light-theme .goal-pct {
            color: #64748b !important;
        }

        html.light-theme .activity-text {
            color: #0f172a !important;
        }

        html.light-theme .activity-time {
            color: #64748b !important;
        }
    
        /* ── Card Grid Yellow Border Differentiation (Both Light & Dark Modes) ── */
        .qs-card,
        .mod-card,
        .erp-card,
        .staff-card,
        .qa-btn {
            border: 1.5px solid #FFA600 !important;
        }

        .qs-card:hover,
        .mod-card:hover,
        .erp-card:hover,
        .staff-card:hover,
        .qa-btn:hover {
            border-color: #FFB800 !important;
        }

        /* ── Dark Theme Scoped Colors ── */
        html:not(.light-theme) body,
        html.dark-theme body,
        html:not(.light-theme) .erp-main,
        html.dark-theme .erp-main {
            background: #030014 !important;
            color: #ffffff !important;
        }

        html:not(.light-theme) .erp-hero,
        html.dark-theme .erp-hero {
            background: linear-gradient(135deg, #000044 0%, #000077 50%, #1a1aaa 100%) !important;
            color: #ffffff !important;
            border-bottom: 1.5px solid #FFA600 !important;
        }

        html:not(.light-theme) .erp-hero .label,
        html.dark-theme .erp-hero .label {
            color: #a0aec0 !important;
            opacity: 1 !important;
        }

        html:not(.light-theme) .erp-hero h1,
        html.dark-theme .erp-hero h1 {
            color: #ffffff !important;
        }

        html:not(.light-theme) .erp-hero .subtitle,
        html.dark-theme .erp-hero .subtitle {
            color: #cbd5e1 !important;
            opacity: 1 !important;
        }

        html:not(.light-theme) .btn-secondary-hero,
        html.dark-theme .btn-secondary-hero {
            background: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
            border: 1.5px solid #FFA600 !important;
        }

        html:not(.light-theme) .qs-card,
        html.dark-theme .qs-card {
            background: rgba(255, 255, 255, 0.08) !important;
        }

        html:not(.light-theme) .qs-data h4,
        html.dark-theme .qs-data h4 {
            color: #ffffff !important;
        }

        html:not(.light-theme) .qs-data span,
        html.dark-theme .qs-data span {
            color: #94a3b8 !important;
            opacity: 1 !important;
        }

        html:not(.light-theme) .erp-card,
        html.dark-theme .erp-card {
            background: #090d1f !important;
        }

        html:not(.light-theme) .erp-card-header h3,
        html.dark-theme .erp-card-header h3 {
            color: #ffffff !important;
        }

        html:not(.light-theme) .mod-card,
        html.dark-theme .mod-card {
            background: #090d1f !important;
            color: #ffffff !important;
        }

        html:not(.light-theme) .mod-title,
        html.dark-theme .mod-title {
            color: #ffffff !important;
        }

        html:not(.light-theme) .mod-stat,
        html.dark-theme .mod-stat {
            color: #94a3b8 !important;
        }

        html:not(.light-theme) .qa-btn,
        html.dark-theme .qa-btn {
            background: #090d1f !important;
            color: #ffffff !important;
        }

        html:not(.light-theme) .staff-card,
        html.dark-theme .staff-card {
            background: linear-gradient(135deg, #090d1f, #11182e) !important;
        }

        html:not(.light-theme) .staff-name,
        html.dark-theme .staff-name {
            color: #ffffff !important;
        }

        html:not(.light-theme) .staff-role,
        html.dark-theme .staff-role {
            color: #94a3b8 !important;
        }

        html:not(.light-theme) .goal-title,
        html.dark-theme .goal-title {
            color: #ffffff !important;
        }

        html:not(.light-theme) .activity-text,
        html.dark-theme .activity-text {
            color: #ffffff !important;
        }

        /* ── Light Theme Scoped Colors ── */
        html.light-theme body,
        html.light-theme .erp-main {
            background: #f8fafc !important;
            color: #0f172a !important;
        }

        html.light-theme .erp-hero {
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%) !important;
            color: #0f172a !important;
            border-bottom: 1.5px solid #FFA600 !important;
        }

        html.light-theme .erp-hero .label {
            color: #64748b !important;
            opacity: 1 !important;
        }

        html.light-theme .erp-hero h1 {
            color: #0f172a !important;
        }

        html.light-theme .erp-hero .subtitle {
            color: #475569 !important;
            opacity: 1 !important;
        }

        html.light-theme .btn-secondary-hero {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1.5px solid #FFA600 !important;
        }

        html.light-theme .qs-card {
            background: #ffffff !important;
        }

        html.light-theme .qs-data h4 {
            color: #0f172a !important;
        }

        html.light-theme .qs-data span {
            color: #64748b !important;
            opacity: 1 !important;
        }

        html.light-theme .erp-card {
            background: #ffffff !important;
        }

        html.light-theme .erp-card-header h3 {
            color: #0f172a !important;
        }

        html.light-theme .mod-card {
            background: #ffffff !important;
            color: #0f172a !important;
        }

        html.light-theme .mod-title {
            color: #0f172a !important;
        }

        html.light-theme .mod-stat {
            color: #64748b !important;
        }

        html.light-theme .qa-btn {
            background: #f8fafc !important;
            color: #0f172a !important;
        }

        html.light-theme .staff-card {
            background: linear-gradient(135deg, #ffffff, #f1f5f9) !important;
        }

        html.light-theme .staff-name {
            color: #0f172a !important;
        }

        html.light-theme .staff-role {
            color: #64748b !important;
        }

        html.light-theme .goal-title {
            color: #0f172a !important;
        }

        html.light-theme .goal-pct {
            color: #64748b !important;
        }

        html.light-theme .activity-text {
            color: #0f172a !important;
        }

        html.light-theme .activity-time {
            color: #64748b !important;
        }
    
        /* Quick Action Yellow Icons & Card Borders */
        .qa-btn ion-icon {
            color: #FFA600 !important;
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/layout/mobile_nav.php'; ?>
    <?php require __DIR__ . '/layout/sidebar.php'; ?>

    <main class="erp-main">
        <!-- ── Hero Banner ── -->
        <div class="erp-hero">
            <div class="erp-hero-top">
                <div>
                    <div class="label">Enterprise Resource Planning</div>
                    <h1>Welcome back, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></h1>
                    <div class="subtitle"><?= date('l, F j, Y') ?></div>
                </div>
                <div class="erp-hero-actions">
                    <?php if (isset($activeAttendance) && $activeAttendance): ?>
                        <form method="POST" action="/erp/my-portal/checkout" style="margin: 0;">
                            <button type="submit" class="btn-secondary-hero" style="border-color: var(--erp-red); background: rgba(239, 68, 68, 0.15); color: #fff; cursor: pointer; padding: 10px 18px; border-radius: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                <ion-icon name="log-out-outline"></ion-icon> Check Out
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="/erp/my-portal/checkin" style="margin: 0;">
                            <button type="submit" class="btn-secondary-hero" style="border-color: var(--erp-green); background: rgba(16, 185, 129, 0.15); color: #fff; cursor: pointer; padding: 10px 18px; border-radius: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                <ion-icon name="log-in-outline"></ion-icon> Check In
                            </button>
                        </form>
                    <?php endif; ?>
                    <a href="/erp/ai-manager" class="btn-ai">
                        <ion-icon name="sparkles"></ion-icon> AI Manager
                    </a>
                    <a href="/erp/finance" class="btn-secondary-hero">
                        <ion-icon name="stats-chart-outline"></ion-icon> Reports
                    </a>
                </div>
            </div>

            <div class="quick-stats">
                <div class="qs-card">
                    <div class="qs-icon" style="background: rgba(16,185,129,0.2);">
                        <ion-icon name="people" style="color: #10b981;"></ion-icon>
                    </div>
                    <div class="qs-data">
                        <h4><?= $stats['employees'] ?></h4>
                        <span>Employees</span>
                    </div>
                </div>
                <div class="qs-card">
                    <div class="qs-icon" style="background: rgba(59,130,246,0.2);">
                        <ion-icon name="briefcase" style="color: #3b82f6;"></ion-icon>
                    </div>
                    <div class="qs-data">
                        <h4><?= $stats['customers'] ?></h4>
                        <span>Clients</span>
                    </div>
                </div>
                <div class="qs-card">
                    <div class="qs-icon" style="background: rgba(139,92,246,0.2);">
                        <ion-icon name="folder-open" style="color: #8b5cf6;"></ion-icon>
                    </div>
                    <div class="qs-data">
                        <h4><?= $stats['open_projects'] ?></h4>
                        <span>Projects</span>
                    </div>
                </div>
                <div class="qs-card">
                    <div class="qs-icon" style="background: rgba(255,166,0,0.2);">
                        <ion-icon name="checkmark-done" style="color: var(--erp-gold);"></ion-icon>
                    </div>
                    <div class="qs-data">
                        <h4><?= $stats['pending_tasks'] ?>/<?= $stats['total_tasks'] ?></h4>
                        <span>Tasks Active</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Alerts Row ── -->
        <?php if ($stats['pending_leave'] > 0 || $stats['overdue_invoices'] > 0): ?>
        <div style="padding: 20px 32px 0;">
            <div class="alerts-row">
                <?php if ($stats['pending_leave'] > 0): ?>
                <div class="alert-chip alert-warn">
                    <ion-icon name="time-outline"></ion-icon>
                    <?= $stats['pending_leave'] ?> pending leave request<?= $stats['pending_leave'] > 1 ? 's' : '' ?>
                </div>
                <?php endif; ?>
                <?php if ($stats['overdue_invoices'] > 0): ?>
                <div class="alert-chip alert-info">
                    <ion-icon name="alert-circle-outline"></ion-icon>
                    <?= $stats['overdue_invoices'] ?> overdue invoice<?= $stats['overdue_invoices'] > 1 ? 's' : '' ?>
                </div>
                <?php endif; ?>
                <div class="alert-chip alert-success">
                    <ion-icon name="sparkles-outline"></ion-icon>
                    AI Manager ready
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="erp-grid">
            <!-- ── LEFT COLUMN ── -->
            <div style="display: flex; flex-direction: column; gap: 24px;">

                <!-- Modules -->
                <div class="erp-card">
                    <div class="erp-card-header">
                        <h3>ERP Modules</h3>
                        <span class="badge" style="background: rgba(var(--erp-primary-rgb),0.08); color: var(--erp-primary);">Active</span>
                    </div>
                    <div class="erp-card-body">
                        <div class="module-grid">
                            <a href="/erp/hr" class="mod-card" style="--c: var(--erp-primary);">
                                <div style="position:absolute;top:0;left:0;right:0;height:4px;background:var(--erp-primary);"></div>
                                <div class="mod-icon" style="background: var(--erp-primary);">
                                    <ion-icon name="people-outline"></ion-icon>
                                </div>
                                <div class="mod-title">Human Resources</div>
                                <div class="mod-stat"><?= $stats['employees'] ?> Employees</div>
                            </a>
                            <a href="/erp/finance" class="mod-card">
                                <div style="position:absolute;top:0;left:0;right:0;height:4px;background:var(--erp-gold);"></div>
                                <div class="mod-icon" style="background: var(--erp-gold);">
                                    <ion-icon name="cash-outline"></ion-icon>
                                </div>
                                <div class="mod-title">Finance</div>
                                <div class="mod-stat">Accounting & GL</div>
                            </a>
                            <a href="/erp/crm" class="mod-card">
                                <div style="position:absolute;top:0;left:0;right:0;height:4px;background:var(--erp-blue);"></div>
                                <div class="mod-icon" style="background: var(--erp-blue);">
                                    <ion-icon name="people-circle-outline"></ion-icon>
                                </div>
                                <div class="mod-title">CRM</div>
                                <div class="mod-stat"><?= $stats['customers'] ?> Clients</div>
                            </a>
                            <a href="/erp/inventory" class="mod-card">
                                <div style="position:absolute;top:0;left:0;right:0;height:4px;background:var(--erp-green);"></div>
                                <div class="mod-icon" style="background: var(--erp-green);">
                                    <ion-icon name="cube-outline"></ion-icon>
                                </div>
                                <div class="mod-title">Inventory</div>
                                <div class="mod-stat">Stock Mgmt</div>
                            </a>
                            <a href="/erp/projects" class="mod-card">
                                <div style="position:absolute;top:0;left:0;right:0;height:4px;background:var(--erp-purple);"></div>
                                <div class="mod-icon" style="background: var(--erp-purple);">
                                    <ion-icon name="folder-outline"></ion-icon>
                                </div>
                                <div class="mod-title">Projects</div>
                                <div class="mod-stat"><?= $stats['open_projects'] ?> Active</div>
                            </a>
                            <a href="/erp/scheduler" class="mod-card">
                                <div style="position:absolute;top:0;left:0;right:0;height:4px;background:#00cec9;"></div>
                                <div class="mod-icon" style="background: #00cec9;">
                                    <ion-icon name="calendar-outline"></ion-icon>
                                </div>
                                <div class="mod-title">Scheduler</div>
                                <div class="mod-stat"><?= $stats['upcoming_bookings'] ?? 0 ?> Bookings</div>
                            </a>
                            <a href="/erp/ai-manager" class="mod-card">
                                <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--erp-gold),#ff8c00);"></div>
                                <div class="mod-icon" style="background: linear-gradient(135deg, var(--erp-gold), #ff8c00);">
                                    <ion-icon name="sparkles"></ion-icon>
                                </div>
                                <div class="mod-title">AI Manager</div>
                                <div class="mod-stat">Business Intel</div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Goals Progress -->
                <div class="erp-card">
                    <div class="erp-card-header">
                        <h3>Goals & Targets</h3>
                        <span class="badge" style="background: rgba(var(--erp-gold-rgb),0.12); color: #b45309;"><?= date('Y') ?></span>
                    </div>
                    <div class="erp-card-body">
                        <?php foreach ($goals as $goal): ?>
                        <?php $pct = $goal['target'] > 0 ? min(100, round(($goal['current'] / $goal['target']) * 100, 1)) : 0; ?>
                        <div class="goal-item">
                            <div class="goal-icon" style="background: <?= $goal['color'] ?>;">
                                <ion-icon name="trending-up"></ion-icon>
                            </div>
                            <div class="goal-info">
                                <div class="goal-title"><?= htmlspecialchars($goal['title']) ?></div>
                                <div class="goal-bar">
                                    <div class="goal-bar-fill" style="width: <?= $pct ?>%; background: <?= $goal['color'] ?>;"></div>
                                </div>
                            </div>
                            <div class="goal-pct"><?= $pct ?>%</div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="erp-card">
                    <div class="erp-card-header">
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="erp-card-body">
                        <div class="qa-grid">
                            <a href="/erp/employees/create" class="qa-btn">
                                <ion-icon name="person-add-outline"></ion-icon> Add Employee
                            </a>
                            <a href="/erp/finance/invoices/create" class="qa-btn">
                                <ion-icon name="receipt-outline"></ion-icon> New Invoice
                            </a>
                            <a href="/erp/projects/create" class="qa-btn">
                                <ion-icon name="add-circle-outline"></ion-icon> Create Project
                            </a>
                            <a href="/erp/leave" class="qa-btn">
                                <ion-icon name="calendar-outline"></ion-icon> Leave Requests
                            </a>
                            <a href="/erp/attendance" class="qa-btn">
                                <ion-icon name="finger-print-outline"></ion-icon> Attendance
                            </a>
                            <a href="/erp/ai-manager" class="qa-btn">
                                <ion-icon name="sparkles-outline"></ion-icon> AI Insights
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── RIGHT COLUMN ── -->
            <div style="display: flex; flex-direction: column; gap: 24px;">

                <!-- Staff of the Month -->
                <div class="staff-card">
                    <div class="staff-badge">⭐ Staff of the Month</div>
                    <div class="staff-avatar">
                        <?php
                        $initials = strtoupper(substr($staffOfMonth['first_name'] ?? 'U', 0, 1) . substr($staffOfMonth['last_name'] ?? '', 0, 1));
                        echo htmlspecialchars($initials);
                        ?>
                    </div>
                    <div class="staff-name"><?= htmlspecialchars(($staffOfMonth['first_name'] ?? '') . ' ' . ($staffOfMonth['last_name'] ?? '')) ?></div>
                    <div class="staff-role"><?= htmlspecialchars($staffOfMonth['job_title'] ?? 'Team Member') ?></div>
                    <div style="margin-top: 16px; padding-top: 14px; border-top: 1px solid rgba(0,0,0,0.06); display: flex; justify-content: center; gap: 20px;">
                        <div style="text-align: center;">
                            <div style="font-size: 1.1rem; font-weight: 800; color: var(--erp-primary);">98%</div>
                            <div style="font-size: 0.68rem; color: var(--erp-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Attendance</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.1rem; font-weight: 800; color: var(--erp-green);">A+</div>
                            <div style="font-size: 0.68rem; color: var(--erp-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Rating</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.1rem; font-weight: 800; color: var(--erp-gold);">12</div>
                            <div style="font-size: 0.68rem; color: var(--erp-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Tasks Done</div>
                        </div>
                    </div>
                </div>

                <!-- AI Manager Mini -->
                <div class="erp-card" style="background: linear-gradient(135deg, #1e293b, #334155); color: white;">
                    <div class="erp-card-body" style="padding: 24px;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                            <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, var(--erp-gold), #ff8c00); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                                <ion-icon name="sparkles"></ion-icon>
                            </div>
                            <div>
                                <h3 style="margin: 0; font-size: 1rem; font-weight: 700;">AI Business Manager</h3>
                                <span style="font-size: 0.72rem; opacity: 0.6;">Powered by intelligence</span>
                            </div>
                        </div>
                        <p style="font-size: 0.82rem; opacity: 0.7; margin: 0 0 18px; line-height: 1.5;">
                            Get AI-powered insights, cash flow forecasts, churn risk alerts, and automated weekly business roasts.
                        </p>
                        <a href="/erp/ai-manager" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 10px; background: linear-gradient(135deg, var(--erp-gold), #ff8c00); color: #000; font-size: 0.82rem; font-weight: 700; transition: all 0.2s;">
                            <ion-icon name="arrow-forward"></ion-icon> Open AI Dashboard
                        </a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="erp-card">
                    <div class="erp-card-header">
                        <h3>Recent Activity</h3>
                    </div>
                    <div class="erp-card-body">
                        <?php if (!empty($recentActivity)): ?>
                            <?php foreach ($recentActivity as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-dot" style="background: var(--erp-primary);"></div>
                                <div>
                                    <div class="activity-text"><?= htmlspecialchars($activity['description'] ?? $activity['action'] ?? '') ?></div>
                                    <div class="activity-time"><?= date('M j, g:i A', strtotime($activity['created_at'])) ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="activity-item">
                                <div class="activity-dot" style="background: var(--erp-green);"></div>
                                <div>
                                    <div class="activity-text">ERP system initialized and ready</div>
                                    <div class="activity-time"><?= date('M j, g:i A') ?></div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-dot" style="background: var(--erp-blue);"></div>
                                <div>
                                    <div class="activity-text">AI Manager insights available</div>
                                    <div class="activity-time"><?= date('M j, g:i A') ?></div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-dot" style="background: var(--erp-gold);"></div>
                                <div>
                                    <div class="activity-text">Dashboard premium features activated</div>
                                    <div class="activity-time"><?= date('M j, g:i A') ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>

    </main>
</div>
</body>
</html>
