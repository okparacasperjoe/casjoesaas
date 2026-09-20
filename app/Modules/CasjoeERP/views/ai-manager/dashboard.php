<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Cori AI | Casjoe BOS</title>
    <meta name="description" content="Cori AI — Your intelligent business assistant. Create invoices, track expenses, manage clients, and get business insights.">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Markdown parser for AI responses -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        :root {
            --cori-bg: #030014;
            --cori-surface: #0a0a2e;
            --cori-primary: #000066;
            --cori-gold: #FFA600;
            --cori-gold-rgb: 255,166,0;
            --cori-blue: #2563eb;
            --cori-blue-light: #3b82f6;
            --cori-text: #ffffff;
            --cori-text-muted: #94a3b8;
            --cori-bubble-ai: #111140;
            --cori-bubble-user: linear-gradient(135deg, #1d4ed8, #2563eb);
            --cori-input-bg: #111140;
            --cori-radius: 20px;
        }

        html.light-theme {
            --cori-bg: #f8fafc;
            --cori-surface: #ffffff;
            --cori-text: #0f172a;
            --cori-text-muted: #475569;
            --cori-bubble-ai: #ffffff;
            --cori-input-bg: #ffffff;
        }

        html.light-theme .cori-header, 
        html.light-theme .cori-input-container {
            background: #ffffff !important;
            border-color: rgba(0,0,0,0.08) !important;
        }

        html.light-theme .cori-header h2 { color: #0f172a !important; }
        html.light-theme .cori-header-info p { color: #475569 !important; }

        
        
        

        html.light-theme .msg-bubble.ai-bubble {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02) !important;
        }

        html.light-theme .msg-bubble.ai-bubble strong,
        html.light-theme .msg-bubble.ai-bubble h2,
        html.light-theme .msg-bubble.ai-bubble h3 {
            color: #0f172a !important;
        }

        html.light-theme .quick-chip {
            background: #ffffff !important;
            border-color: rgba(0,0,0,0.08) !important;
            color: #475569 !important;
        }
        html.light-theme .quick-chip:hover {
            color: #000066 !important;
            border-color: rgba(0,0,102,0.2) !important;
            background: rgba(0,0,102,0.05) !important;
        }

        html.light-theme .cori-input {
            color: #0f172a !important;
        }
        html.light-theme .cori-input::placeholder {
            color: #94a3b8 !important;
        }

        html.light-theme .cori-tabs {
            background: #ffffff !important;
            border-bottom-color: rgba(0,0,0,0.08) !important;
        }
        html.light-theme .cori-tab { color: #475569 !important; }
        html.light-theme .cori-tab.active,
        html.light-theme .cori-tab:hover { color: #000066 !important; }

        html.light-theme .crm-card {
            background: #ffffff !important;
            border-color: rgba(0,0,0,0.08) !important;
            color: #0f172a !important;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05) !important;
        }
        html.light-theme .cori-container table,
        html.light-theme .cori-container .crm-table { color: #0f172a !important; }
        html.light-theme th { color: #475569 !important; border-bottom-color: rgba(0,0,0,0.08) !important; }
        html.light-theme td { border-bottom-color: rgba(0,0,0,0.05) !important; }
        html.light-theme h3, html.light-theme h4, html.light-theme h5 { color: #0f172a !important; }
        html.light-theme .text-white { color: #0f172a !important; }

        * { box-sizing: border-box; }
        html, body, .app-container, .cori-container, .cori-messages, .tab-content, .crm-panel {
            background: var(--cori-bg) !important;
            background-color: var(--cori-bg) !important;
            color: var(--cori-text) !important;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0; padding: 0;
            overflow: hidden;
            height: 100vh;
        }

        .app-container { min-height: 100vh; display: flex; }
        a { text-decoration: none; }
        .sidebar { background-color: #030014 !important; border-right: 2px solid var(--cori-gold) !important; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8) !important; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #ffffff !important; background: rgba(255,255,255,0.1) !important; }
        .sidebar .nav-link ion-icon { color: inherit !important; }

        /* ── Chat Layout ── */
        @media (min-width: 992px) {
            .cori-container {
                margin-left: var(--sidebar-width, 260px);
            }
        }
        .cori-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
            background: var(--cori-bg) !important;
        }

        /* ── Chat Header ── */
        .cori-header {
            background: linear-gradient(135deg, #0a0a3a 0%, #14145a 100%);
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            flex-shrink: 0;
            z-index: 10;
        }
        .cori-avatar {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--cori-gold), #e69500);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            box-shadow: 0 4px 15px rgba(255,166,0,0.3);
        }
        .cori-header-info h2 {
            margin: 0; font-size: 1.1rem; font-weight: 800;
            color: #fff;
        }
        .cori-header-info p {
            margin: 2px 0 0; font-size: 0.78rem;
            color: var(--cori-text-muted);
        }
        .cori-header-close {
            margin-left: auto;
            width: 36px; height: 36px;
            border-radius: 50%;
            border: none;
            background: rgba(255,255,255,0.06);
            color: var(--cori-text-muted);
            font-size: 1.2rem;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
        }
        .cori-header-close:hover {
            background: rgba(255,255,255,0.12);
            color: #fff;
        }

        /* ── Messages Area ── */
        .cori-messages {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            scroll-behavior: smooth;
        }
        .cori-messages::-webkit-scrollbar { width: 4px; }
        .cori-messages::-webkit-scrollbar-track { background: transparent; }
        .cori-messages::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

        /* ── Message Bubbles ── */
        .msg-row {
            display: flex;
            gap: 10px;
            max-width: 85%;
            animation: msgFadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes msgFadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .msg-row.ai { align-self: flex-start; }
        .msg-row.user { align-self: flex-end; flex-direction: row-reverse; }

        .msg-avatar {
            width: 32px; height: 32px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
            align-self: flex-end;
        }
        .msg-avatar.ai-av {
            background: linear-gradient(135deg, var(--cori-gold), #e69500);
            box-shadow: 0 2px 8px rgba(255,166,0,0.25);
        }
        .msg-avatar.user-av {
            background: var(--cori-blue);
            color: #fff;
            font-weight: 700;
            font-size: 0.75rem;
        }

        .msg-bubble {
            padding: 14px 18px;
            border-radius: 18px;
            line-height: 1.6;
            font-size: 0.92rem;
            word-wrap: break-word;
        }
        .msg-bubble.ai-bubble {
            background: var(--cori-bubble-ai);
            border: 1px solid rgba(255,255,255,0.06);
            border-bottom-left-radius: 6px;
            color: #e2e8f0;
        }
        .msg-bubble.user-bubble {
            background: var(--cori-bubble-user);
            border-bottom-right-radius: 6px;
            color: #fff;
        }

        /* Markdown rendering inside bubbles */
        .msg-bubble h2 { font-size: 1.05rem; margin: 0 0 10px; color: var(--cori-gold); font-weight: 800; }
        .msg-bubble h3 { font-size: 0.92rem; margin: 12px 0 6px; color: #93c5fd; font-weight: 700; }
        .msg-bubble p { margin: 0 0 8px; }
        .msg-bubble p:last-child { margin-bottom: 0; }
        .msg-bubble strong { color: #fff; }
        .msg-bubble em { color: var(--cori-text-muted); font-style: italic; }
        .msg-bubble ul, .msg-bubble ol { margin: 6px 0; padding-left: 20px; }
        .msg-bubble li { margin-bottom: 4px; }
        .msg-bubble hr { border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 12px 0; }
        .msg-bubble a {
            color: var(--cori-gold);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        .msg-bubble a:hover { color: #ffbe4d; text-decoration: underline; }
        .msg-bubble code {
            background: rgba(255,255,255,0.08);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.85em;
        }

        /* ── Typing Indicator ── */
        .typing-indicator {
            display: none;
            align-self: flex-start;
            gap: 10px;
            align-items: flex-end;
        }
        .typing-indicator.show { display: flex; }
        .typing-dots {
            background: var(--cori-bubble-ai);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 18px;
            border-bottom-left-radius: 6px;
            padding: 14px 20px;
            display: flex;
            gap: 5px;
        }
        .typing-dots span {
            width: 7px; height: 7px;
            background: var(--cori-text-muted);
            border-radius: 50%;
            animation: typingBounce 1.4s ease-in-out infinite;
        }
        .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
        .typing-dots span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typingBounce {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
            30% { transform: translateY(-6px); opacity: 1; }
        }

        /* ── Quick Actions ── */
        .cori-quick-actions {
            padding: 12px 24px 6px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            flex-shrink: 0;
        }
        .quick-chip {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 50px;
            padding: 8px 16px;
            font-size: 0.82rem;
            font-weight: 600;
            color: #cbd5e1;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        .quick-chip:hover {
            background: rgba(var(--cori-gold-rgb), 0.12);
            border-color: rgba(var(--cori-gold-rgb), 0.4);
            color: var(--cori-gold);
            transform: translateY(-1px);
        }
        .quick-chip:active {
            transform: scale(0.96);
        }

        /* ── Input Bar ── */
        .cori-input-bar {
            padding: 12px 24px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
            background: linear-gradient(to top, var(--cori-bg), transparent);
        }
        .cori-input-wrap {
            flex: 1;
            background: var(--cori-input-bg);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 50px;
            display: flex;
            align-items: center;
            padding: 4px 6px 4px 20px;
            transition: border-color 0.2s;
        }
        .cori-input-wrap:focus-within {
            border-color: rgba(var(--cori-gold-rgb), 0.4);
        }
        .cori-input {
            flex: 1;
            background: none;
            border: none;
            outline: none;
            color: #fff;
            font-family: inherit;
            font-size: 0.92rem;
            padding: 10px 0;
        }
        .cori-input::placeholder { color: #4a5568; }
        .cori-send-btn {
            width: 40px; height: 40px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(135deg, var(--cori-gold), #e69500);
            color: #000;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(255,166,0,0.3);
        }
        .cori-send-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 18px rgba(255,166,0,0.4);
        }
        .cori-send-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
        }
        .cori-cam-btn {
            width: 40px; height: 40px;
            border-radius: 50%;
            border: 1px solid rgba(255, 166, 0, 0.35);
            background: rgba(255, 166, 0, 0.15);
            color: var(--cori-gold);
            font-size: 1.3rem;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .cori-cam-btn:hover {
            background: rgba(255, 166, 0, 0.3);
            color: #fff;
            transform: scale(1.06);
        }

        /* ── Responsive ── */
        @media (max-width: 991px) {
            .app-container { flex-direction: column; }
            .sidebar { display: none !important; }
            .cori-container { margin-left: 0 !important; width: 100% !important; }
            .cori-messages { padding: 16px; }
            .cori-quick-actions { padding: 10px 16px 4px; }
            .cori-input-bar { padding: 10px 16px 16px; }
            .msg-row { max-width: 92%; }
            .cori-header { padding: 12px 16px; }
        }

        /* ── Tabs & CRM UI ── */
        .cori-tabs {
            display: flex; background: var(--cori-bg); border-bottom: 1px solid rgba(255,255,255,0.06);
            padding: 0 24px; gap: 20px; flex-shrink: 0;
        }
        .cori-tab {
            background: none; border: none; color: var(--cori-text-muted); font-size: 0.95rem;
            font-weight: 600; padding: 14px 4px; cursor: pointer; border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }
        .cori-tab:hover { color: #fff; }
        .cori-tab.active { color: var(--cori-gold); border-bottom-color: var(--cori-gold); }
        .tab-content { display: none; flex: 1; overflow-y: auto; flex-direction: column; }
        .tab-content.active { display: flex; }

        .crm-panel { padding: 24px; }
        .crm-card {
            background: var(--cori-surface) !important;
            border: 1px solid rgba(255,255,255,0.08) !important;
            border-radius: 16px !important;
            padding: 20px !important;
            margin-bottom: 20px !important;
            color: #ffffff !important;
        }
        .cori-container table, .cori-container .crm-table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-top: 10px !important;
            background: transparent !important;
            color: #ffffff !important;
            border: none !important;
        }
        .cori-container table th, .cori-container .crm-table th {
            padding: 14px 12px !important;
            text-align: left !important;
            border-bottom: 2px solid rgba(255,255,255,0.12) !important;
            font-size: 0.86rem !important;
            color: var(--cori-gold) !important;
            background: rgba(255,255,255,0.03) !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
        }
        .cori-container table td, .cori-container .crm-table td {
            padding: 14px 12px !important;
            text-align: left !important;
            border-bottom: 1px solid rgba(255,255,255,0.06) !important;
            font-size: 0.92rem !important;
            color: #ffffff !important;
            background: transparent !important;
        }
        .cori-container table tr:hover td, .cori-container .crm-table tr:hover td {
            background: rgba(255,255,255,0.04) !important;
        }
        .cori-container h1, .cori-container h2, .cori-container h3, .cori-container h4, .cori-container h5 {
            color: #ffffff !important;
        }
        .ai-badge { background: rgba(var(--cori-gold-rgb), 0.15) !important; color: var(--cori-gold) !important; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: bold; }
        .btn-ai { background: linear-gradient(135deg, var(--cori-gold), #e69500) !important; color: #000 !important; border: none; padding: 8px 16px; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 0.85rem; }
        .btn-ai:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(255,166,0,0.3); }
        .tool-form { display: flex; flex-direction: column; gap: 12px; max-width: 600px; }
        .tool-form h4 { color: #93c5fd !important; }
        .tool-input { background: var(--cori-input-bg) !important; border: 1px solid rgba(255,255,255,0.1) !important; color: #fff !important; padding: 12px; border-radius: 8px; font-size: 0.9rem; width: 100%; }
        .tool-result { margin-top: 16px; background: #000 !important; color: #fff !important; padding: 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); display: none; font-size: 0.9rem; line-height: 1.5; white-space: pre-wrap; }

        /* ── Supercharged AI Pipeline Styles ── */
        .ai-metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .ai-metric-box {
            background: rgba(255, 255, 255, 0.03) !important;
            border: 1px solid rgba(255, 166, 0, 0.22) !important;
            border-radius: 14px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            position: relative;
            overflow: hidden;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .ai-metric-box:hover {
            transform: translateY(-3px);
            border-color: var(--cori-gold) !important;
            box-shadow: 0 8px 24px rgba(255, 166, 0, 0.15);
        }
        .ai-metric-title {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--cori-text-muted) !important;
            font-weight: 600;
        }
        .ai-metric-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: #ffffff !important;
        }
        .ai-metric-sub {
            font-size: 0.75rem;
            color: var(--cori-gold) !important;
            font-weight: 500;
        }
        .ai-filter-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.02) !important;
            padding: 12px 16px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.06) !important;
        }
        .filter-pill {
            background: rgba(255, 255, 255, 0.06) !important;
            color: #cbd5e1 !important;
            border: 1px solid transparent !important;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .filter-pill:hover, .filter-pill.active {
            background: rgba(255, 166, 0, 0.15) !important;
            color: #FFA600 !important;
            border-color: #FFA600 !important;
        }
        .pipeline-search {
            margin-left: auto;
            background: var(--cori-input-bg) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            color: #fff !important;
            padding: 7px 14px;
            border-radius: 20px;
            font-size: 0.82rem;
            min-width: 230px;
        }
        .pipeline-search:focus {
            outline: none;
            border-color: var(--cori-gold) !important;
        }
        .score-progress-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .score-progress-bar {
            flex: 1;
            background: rgba(255, 255, 255, 0.1) !important;
            border-radius: 10px;
            height: 6px;
            min-width: 65px;
            overflow: hidden;
        }
        .score-progress-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
        }
        .score-progress-fill.hot { background: linear-gradient(90deg, #FFA600, #ff4500) !important; }
        .score-progress-fill.warm { background: linear-gradient(90deg, #3b82f6, #60a5fa) !important; }
        .score-progress-fill.cold { background: rgba(255, 255, 255, 0.3) !important; }
        .inline-ai-btn {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.76rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .inline-ai-btn:hover {
            background: var(--cori-gold) !important;
            color: #000000 !important;
            border-color: var(--cori-gold) !important;
        }
        .contact-icon {
            color: var(--cori-gold) !important;
            font-size: 1.05rem;
            vertical-align: middle;
            margin-left: 6px;
            transition: transform 0.2s;
        }
        .contact-icon:hover {
            transform: scale(1.2);
        }
        .source-pill {
            display: inline-block;
            background: rgba(59, 130, 246, 0.18) !important;
            color: #93c5fd !important;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/mobile_nav.php'; ?>
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <div class="cori-container">
        <!-- Header -->
        <div class="cori-header">
            <div class="cori-avatar">✨</div>
            <div class="cori-header-info">
                <h2>Cori AI</h2>
                <p>Your Business Assistant</p>
            </div>
            <button onclick="toggleAiSettings()" class="cori-header-close" title="AI Settings" style="margin-left: auto; margin-right: 8px;">
                <ion-icon name="settings-outline"></ion-icon>
            </button>
            <a href="/erp" class="cori-header-close" title="Back to ERP" style="margin-left: 0;">
                <ion-icon name="close-outline"></ion-icon>
            </a>
        </div>

        <!-- Tabs Navigation -->
        <div class="cori-tabs">
            <button class="cori-tab active" onclick="switchTab('chat', this)">Cori Chat</button>
            <button class="cori-tab" onclick="switchTab('pipeline', this)">AI Pipeline</button>
            <button class="cori-tab" onclick="switchTab('tools', this)">AI Toolset</button>
            <button class="cori-tab" onclick="switchTab('roast', this)">Weekly Roast 🔥</button>
        </div>

        <!-- Settings Modal ... (keep this unchanged) -->
        <div id="aiSettingsModal" style="display:none; position:absolute; top:70px; right:20px; width:300px; background:var(--cori-surface); border:1px solid rgba(255,255,255,0.1); border-radius:16px; padding:20px; z-index:100; box-shadow:0 10px 30px rgba(0,0,0,0.5);">
            <h3 style="margin-top:0; color:#fff; font-size:1.1rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:10px;">AI Sales Manager</h3>
            <div style="margin-top:15px; display:flex; align-items:center; justify-content:space-between;">
                <label style="color:#cbd5e1; font-size:0.9rem;">Auto-Pilot Sequences</label>
                <label class="switch" style="position:relative; display:inline-block; width:44px; height:24px;">
                    <input type="checkbox" id="autoPilotToggle" onchange="saveAiSettings()" style="opacity:0; width:0; height:0;" <?= (isset($aiSettings['auto_pilot_enabled']) && $aiSettings['auto_pilot_enabled'] == '1') ? 'checked' : '' ?>>
                    <span class="slider round" style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:#111140; transition:.4s; border-radius:24px; border:1px solid rgba(255,255,255,0.2);"></span>
                </label>
            </div>
            <p style="font-size:0.75rem; color:#94a3b8; margin-top:8px;">Automatically sends follow-up emails and WhatsApp messages for unpaid invoices.</p>
        </div>

        <style>
            .switch input:checked + .slider { background-color: var(--cori-gold); }
            .switch input:checked + .slider:before { transform: translateX(20px); }
            .slider:before { position:absolute; content:""; height:16px; width:16px; left:3px; bottom:3px; background-color:white; transition:.4s; border-radius:50%; }
        </style>
        
        <script>
            function toggleAiSettings() {
                const m = document.getElementById('aiSettingsModal');
                m.style.display = m.style.display === 'none' ? 'block' : 'none';
            }
            function saveAiSettings() {
                const enabled = document.getElementById('autoPilotToggle').checked ? 1 : 0;
                fetch('/erp/ai-manager/settings/save', {
                    method: 'POST',
                    headers: {'Content-Type':'application/x-www-form-urlencoded'},
                    body: 'auto_pilot_enabled=' + enabled
                }).then(res => res.json()).then(data => {
                    if(data.success) {
                        alert('AI Settings saved!');
                    } else {
                        alert('Error saving settings.');
                    }
                });
            }
        </script>

        <div id="tab-chat" class="tab-content active">
            <!-- Messages -->
            <div class="cori-messages" id="coriMessages">
            <!-- Welcome message -->
            <div class="msg-row ai">
                <div class="msg-avatar ai-av">✨</div>
                <div class="msg-bubble ai-bubble">
                    👋 Hi! I'm <strong>Cori</strong>, your AI business assistant.<br><br>
                    I can create invoices, send emails, manage tasks, and answer questions about your business. What can I do for you?
                </div>
            </div>
        </div>

        <!-- Typing Indicator -->
        <div class="typing-indicator" id="typingIndicator">
            <div class="msg-avatar ai-av">✨</div>
            <div class="typing-dots">
                <span></span><span></span><span></span>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="cori-quick-actions" id="quickActions">
            <div class="quick-chip" onclick="sendQuick('Business summary')">📊 Business summary</div>
            <div class="quick-chip" onclick="sendQuick('Pending tasks')">📋 Pending tasks</div>
            <div class="quick-chip" onclick="sendQuick('Stock levels')">📦 Stock levels</div>
            <div class="quick-chip" onclick="sendQuick('Create an invoice')">📄 Create an invoice</div>
            <div class="quick-chip" onclick="sendQuick('Show unpaid invoices')">📋 Show unpaid invoices</div>
            <div class="quick-chip" onclick="sendQuick('Add a lead')">➕ Add a lead</div>
            <div class="quick-chip" onclick="sendQuick('Record expense')">💰 Record expense</div>
            <div class="quick-chip" onclick="sendQuick('Add product')">🛍️ Add product</div>
            <div class="quick-chip" onclick="sendQuick('Wallet balance')">💳 Wallet balance</div>
            <div class="quick-chip" onclick="sendQuick('Create ticket')">🎟️ Create ticket</div>
            <div class="quick-chip" onclick="sendQuick('Campaign stats')">📧 Campaign stats</div>
            <div class="quick-chip" onclick="sendQuick('List courses')">🎓 List courses</div>
            <div class="quick-chip" onclick="sendQuick('Recent orders')">🛒 Recent orders</div>
        </div>

        <!-- Image Attachment Preview Bar -->
        <div id="coriImagePreview" style="display:none; padding:10px 24px; background:rgba(255,166,0,0.12); border-top:1px solid rgba(255,166,0,0.25); align-items:center; gap:12px;">
            <img id="coriPreviewImg" src="" style="width:40px; height:40px; border-radius:8px; object-fit:cover; border:1px solid rgba(255,166,0,0.4);" alt="Preview">
            <div style="flex:1; overflow:hidden;">
                <div id="coriFileName" style="font-size:0.85rem; color:#fff; font-weight:600; white-space:nowrap; text-overflow:ellipsis; overflow:hidden;">Image attached</div>
                <div style="font-size:0.75rem; color:rgba(255,255,255,0.6);">Ready to scan with AI Vision</div>
            </div>
            <button type="button" onclick="clearCoriImage()" style="background:none; border:none; color:rgba(255,255,255,0.7); cursor:pointer; font-size:1.3rem; display:flex; align-items:center;">
                <ion-icon name="close-circle"></ion-icon>
            </button>
        </div>

        <!-- Input -->
        <div class="cori-input-bar">
            <input type="file" id="coriFileInput" accept="image/*" capture="environment" style="display:none;" onchange="handleCoriFileSelect(event)">
            <button type="button" class="cori-cam-btn" id="coriCamBtn" onclick="document.getElementById('coriFileInput').click()" title="Snap or upload image to scan">
                <ion-icon name="camera-outline"></ion-icon>
            </button>
            <div class="cori-input-wrap">
                <input type="text" class="cori-input" id="coriInput" placeholder="Ask or snap a photo..." autocomplete="off">
            </div>
            <button class="cori-send-btn" id="coriSend" onclick="sendMessage()">
                <ion-icon name="send"></ion-icon>
            </button>
        </div>
        </div> <!-- End input bar -->
        </div> <!-- End tab-chat -->

        <!-- Tab: Pipeline -->
        <div id="tab-pipeline" class="tab-content crm-panel">
            <?php
                $totalLeads = count($aiCrmLeads ?? []);
                $hotLeads = 0;
                $warmLeads = 0;
                $coldLeads = 0;
                $totalScoreSum = 0;
                $totalBudgetSum = 0;

                if (!empty($aiCrmLeads)) {
                    foreach ($aiCrmLeads as $l) {
                        $sc = (int)($l['ai_score'] ?? $l['score'] ?? 85);
                        $totalScoreSum += $sc;
                        if ($sc >= 80) $hotLeads++;
                        elseif ($sc >= 50) $warmLeads++;
                        else $coldLeads++;

                        $b = $l['budget'] ?? 0;
                        if (is_numeric($b)) $totalBudgetSum += (float)$b;
                        elseif (preg_match('/[0-9,\.]+/', (string)$b, $matches)) {
                            $totalBudgetSum += (float)str_replace(',', '', $matches[0]);
                        }
                    }
                }
                $avgScore = $totalLeads > 0 ? round($totalScoreSum / $totalLeads) : 0;
            ?>

            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px; margin-bottom:18px;">
                <div>
                    <h3 style="margin:0; font-size:1.35rem; color:#fff !important; font-weight:800; display:flex; align-items:center; gap:8px;">
                        <span>AI Lead Pipeline & Intelligence Hub</span>
                        <span class="ai-badge" style="background:var(--cori-gold) !important; color:#000 !important;">PRO ENGINE</span>
                    </h3>
                    <p style="margin:4px 0 0; font-size:0.84rem; color:var(--cori-text-muted) !important;">Real-time predictive scoring, automated qualification, and AI outreach workflows.</p>
                </div>
                <button class="btn-ai" onclick="switchTab('tools', document.querySelectorAll('.cori-tab')[2])" style="display:flex; align-items:center; gap:6px;">
                    <ion-icon name="flash-outline"></ion-icon> Launch AI Toolset
                </button>
            </div>

            <!-- AI Metrics Grid -->
            <div class="ai-metrics-grid">
                <div class="ai-metric-box">
                    <span class="ai-metric-title">📡 Total Active Leads</span>
                    <span class="ai-metric-value"><?= number_format($totalLeads) ?></span>
                    <span class="ai-metric-sub">✨ Real-time capture & sync</span>
                </div>
                <div class="ai-metric-box">
                    <span class="ai-metric-title">🔥 Hot AI Qualified</span>
                    <span class="ai-metric-value" style="color:#FFA600 !important;"><?= number_format($hotLeads) ?></span>
                    <span class="ai-metric-sub">Score ≥ 80% High Intent</span>
                </div>
                <div class="ai-metric-box">
                    <span class="ai-metric-title">🎯 Avg Readiness Score</span>
                    <span class="ai-metric-value" style="color:#60a5fa !important;"><?= $avgScore ?>%</span>
                    <span class="ai-metric-sub">Predictive conversion chance</span>
                </div>
                <div class="ai-metric-box">
                    <span class="ai-metric-title">💰 Tracked Deal Volume</span>
                    <span class="ai-metric-value" style="color:#10b981 !important;">$<?= number_format($totalBudgetSum > 0 ? $totalBudgetSum : ($totalLeads * 4500)) ?></span>
                    <span class="ai-metric-sub"><?= $totalBudgetSum > 0 ? 'Extracted from leads' : 'Estimated AI valuation' ?></span>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="ai-filter-bar">
                <span style="font-size:0.8rem; color:var(--cori-gold) !important; font-weight:700;">FILTER BY INTENT:</span>
                <button class="filter-pill active" onclick="filterPipeline('all', this)">All Leads (<?= $totalLeads ?>)</button>
                <button class="filter-pill" onclick="filterPipeline('hot', this)">🔥 Hot Intent (<?= $hotLeads ?>)</button>
                <button class="filter-pill" onclick="filterPipeline('warm', this)">⚡ Warm (<?= $warmLeads ?>)</button>
                <button class="filter-pill" onclick="filterPipeline('cold', this)">❄️ Nurture (<?= $coldLeads ?>)</button>
                <input type="text" id="pipelineSearchInput" class="pipeline-search" placeholder="🔍 Search leads, company, source..." onkeyup="searchPipelineRows()">
            </div>

            <!-- Supercharged Table -->
            <div class="crm-card" style="padding:0 !important; overflow:hidden; border:1px solid rgba(255,255,255,0.08) !important;">
                <div style="overflow-x:auto;">
                    <table class="crm-table" id="pipelineTable" style="margin:0 !important;">
                        <thead>
                            <tr>
                                <th>Lead Profile & Contact</th>
                                <th>Source & Industry</th>
                                <th>AI Readiness & Intent</th>
                                <th>Est. Budget / Timeline</th>
                                <th>Current Status</th>
                                <th style="text-align:right !important;">AI Quick Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($aiCrmLeads)): ?>
                                <?php foreach($aiCrmLeads as $lead): 
                                    $lname = !empty($lead['name']) ? $lead['name'] : (!empty($lead['email']) ? $lead['email'] : 'Lead #' . ($lead['id'] ?? ''));
                                    $lcomp = !empty($lead['company']) ? $lead['company'] : 'Direct Contact';
                                    $lscore = (int)($lead['ai_score'] ?? $lead['score'] ?? 85);
                                    $lstatus = !empty($lead['status']) ? ucfirst($lead['status']) : 'New';
                                    $lsource = !empty($lead['source']) ? $lead['source'] : 'AI Capture';
                                    $lindustry = !empty($lead['industry']) ? $lead['industry'] : 'General';
                                    $lbudget = !empty($lead['budget']) ? $lead['budget'] : 'Not specified';
                                    $ltimeline = !empty($lead['timeline']) ? $lead['timeline'] : 'Immediate';
                                    
                                    $tier = $lscore >= 80 ? 'hot' : ($lscore >= 50 ? 'warm' : 'cold');
                                    $scoreLabel = $lscore >= 80 ? '🔥 High Intent' : ($lscore >= 50 ? '⚡ Medium' : '❄️ Nurture');
                                ?>
                                <tr class="pipeline-row" data-score-tier="<?= $tier ?>" data-search="<?= strtolower(htmlspecialchars((string)$lname . ' ' . (string)$lcomp . ' ' . (string)$lsource . ' ' . (string)$lindustry . ' ' . (string)$lstatus)) ?>">
                                    <td>
                                        <div style="font-weight:700; font-size:0.95rem; color:#ffffff !important;">
                                            <?= htmlspecialchars((string)$lname) ?>
                                            <?php if(!empty($lead['email'])): ?>
                                                <a href="mailto:<?= htmlspecialchars($lead['email']) ?>" class="contact-icon" title="Email <?= htmlspecialchars($lead['email']) ?>">
                                                    <ion-icon name="mail-outline"></ion-icon>
                                                </a>
                                            <?php endif; ?>
                                            <?php if(!empty($lead['phone'])): ?>
                                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $lead['phone']) ?>" target="_blank" class="contact-icon" title="WhatsApp <?= htmlspecialchars($lead['phone']) ?>">
                                                    <ion-icon name="logo-whatsapp"></ion-icon>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        <div style="font-size:0.8rem; color:var(--cori-text-muted) !important; margin-top:2px;">
                                            🏢 <?= htmlspecialchars((string)$lcomp) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div><span class="source-pill"><?= htmlspecialchars((string)$lsource) ?></span></div>
                                        <div style="font-size:0.76rem; color:#cbd5e1 !important; margin-top:4px;">🏷️ <?= htmlspecialchars((string)$lindustry) ?></div>
                                    </td>
                                    <td>
                                        <div class="score-progress-wrap">
                                            <div class="score-progress-bar">
                                                <div class="score-progress-fill <?= $tier ?>" style="width: <?= $lscore ?>%;"></div>
                                            </div>
                                            <span style="font-weight:800; font-size:0.9rem; color:#fff !important;"><?= $lscore ?>%</span>
                                        </div>
                                        <div style="font-size:0.74rem; color:var(--cori-gold) !important; margin-top:4px; font-weight:600;">
                                            <?= $scoreLabel ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight:700; color:#10b981 !important;">
                                            <?= is_numeric($lbudget) ? '$' . number_format((float)$lbudget) : htmlspecialchars((string)$lbudget) ?>
                                        </div>
                                        <div style="font-size:0.76rem; color:var(--cori-text-muted) !important; margin-top:2px;">
                                            ⏱️ <?= htmlspecialchars((string)$ltimeline) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="display:inline-block; padding:4px 10px; border-radius:12px; font-size:0.75rem; font-weight:700; background:rgba(255,255,255,0.08); color:#e2e8f0; border:1px solid rgba(255,255,255,0.12);">
                                            <?= htmlspecialchars((string)$lstatus) ?>
                                        </span>
                                    </td>
                                    <td style="text-align:right !important; white-space:nowrap;">
                                        <button class="inline-ai-btn" onclick="triggerResearch('<?= $lead['id'] ?? '' ?>')" title="AI Company Research">
                                            <ion-icon name="search-outline"></ion-icon> Research
                                        </button>
                                        <button class="inline-ai-btn" onclick="triggerProposal('<?= $lead['id'] ?? '' ?>')" title="Draft AI Proposal">
                                            <ion-icon name="document-text-outline"></ion-icon> Proposal
                                        </button>
                                        <button class="inline-ai-btn" onclick="triggerEmail('<?= $lead['id'] ?? '' ?>')" title="Draft AI Outreach">
                                            <ion-icon name="mail-unread-outline"></ion-icon> Outreach
                                        </button>
                                        <button class="inline-ai-btn" onclick="triggerChatStrategy('<?= htmlspecialchars((string)addslashes($lname)) ?>', '<?= htmlspecialchars((string)addslashes($lcomp)) ?>')" title="Ask Cori AI Strategy" style="border-color:var(--cori-gold) !important; color:var(--cori-gold) !important;">
                                            <ion-icon name="sparkles-outline"></ion-icon> Strategy
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align:center; padding:30px !important; color:var(--cori-text-muted) !important;">No leads captured yet. Add a lead or trigger AI Webhook capture.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab: Tools -->
        <div id="tab-tools" class="tab-content crm-panel">
            <h3 style="margin-top:0; color:#fff !important;">AI Tools</h3>
            
            <div class="crm-card tool-form">
                <h4 style="margin:0; color:#93c5fd !important;">🔍 Research Lead</h4>
                <p style="margin:0; font-size:0.8rem; color:var(--cori-text-muted) !important;">Enter a company URL to generate an AI Research Brief.</p>
                <select id="researchLeadId" class="tool-input">
                    <option value="">Select a lead...</option>
                    <?php foreach($aiCrmLeads as $lead): 
                        $lname = !empty($lead['name']) ? $lead['name'] : (!empty($lead['email']) ? $lead['email'] : 'Lead #' . ($lead['id'] ?? ''));
                        $lcomp = !empty($lead['company']) ? ' (' . $lead['company'] . ')' : '';
                    ?>
                        <option value="<?= $lead['id'] ?? '' ?>"><?= htmlspecialchars((string)$lname . (string)$lcomp) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="url" id="researchUrl" class="tool-input" placeholder="https://example.com">
                <button class="btn-ai" onclick="runResearch()">Research Company</button>
                <div id="researchResult" class="tool-result"></div>
            </div>

            <div class="crm-card tool-form">
                <h4 style="margin:0; color:#93c5fd !important;">📝 Proposal Generator</h4>
                <select id="proposalLeadId" class="tool-input">
                    <option value="">Select a lead...</option>
                    <?php foreach($aiCrmLeads as $lead): 
                        $lname = !empty($lead['name']) ? $lead['name'] : (!empty($lead['email']) ? $lead['email'] : 'Lead #' . ($lead['id'] ?? ''));
                    ?>
                        <option value="<?= $lead['id'] ?? '' ?>"><?= htmlspecialchars((string)$lname) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn-ai" onclick="runProposal()">Generate Proposal</button>
                <div id="proposalResult" class="tool-result"></div>
            </div>

            <div class="crm-card tool-form">
                <h4 style="margin:0; color:#93c5fd !important;">📧 Email Writer</h4>
                <select id="emailLeadId" class="tool-input">
                    <option value="">Select a lead...</option>
                    <?php foreach($aiCrmLeads as $lead): 
                        $lname = !empty($lead['name']) ? $lead['name'] : (!empty($lead['email']) ? $lead['email'] : 'Lead #' . ($lead['id'] ?? ''));
                    ?>
                        <option value="<?= $lead['id'] ?? '' ?>"><?= htmlspecialchars((string)$lname) ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="emailType" class="tool-input">
                    <option value="cold">Cold Outreach</option>
                    <option value="follow_up">Follow-up</option>
                    <option value="welcome">Welcome</option>
                    <option value="thank_you">Thank You</option>
                </select>
                <select id="emailTone" class="tool-input">
                    <option value="professional">Professional</option>
                    <option value="friendly">Friendly</option>
                    <option value="persuasive">Persuasive</option>
                </select>
                <button class="btn-ai" onclick="runEmail()">Draft Email</button>
                <div id="emailResult" class="tool-result"></div>
            </div>
        </div>

        <!-- Tab: Weekly Roast -->
        <div id="tab-roast" class="tab-content crm-panel">
            <h3 style="margin-top:0; color:#fff !important; display:flex; align-items:center; gap:8px;">
                Weekly Roast 🔥 
                <span class="ai-badge" style="background:var(--cori-gold) !important; color:#000 !important;">AI PERFORMANCE AUDIT</span>
            </h3>

            <!-- AI Overall Roast -->
            <div class="crm-card" style="margin-bottom: 20px;">
                <h4 style="margin:0 0 10px 0; color:#f87171 !important;">Brutal Business Assessment</h4>
                <div style="color: #e2e8f0; line-height: 1.6; font-size: 0.95rem;">
                    <?= nl2br(htmlspecialchars($weeklyRoast['overall'] ?? 'Awaiting data...')) ?>
                </div>
            </div>

            <!-- Financial Forecast Roast -->
            <div class="crm-card" style="margin-bottom: 20px;">
                <h4 style="margin:0 0 10px 0; color:#60a5fa !important;">Financial Trajectory</h4>
                <div style="color: #e2e8f0; line-height: 1.6; font-size: 0.95rem;">
                    <?php if (!empty($cashForecast['projected_months'])): ?>
                        <?php foreach ($cashForecast['projected_months'] as $month): ?>
                            <div style="margin-bottom:5px;">
                                <?= ($month['net'] ?? 0) >= 0 ? '🟢' : '🔴' ?> 
                                <strong><?= htmlspecialchars($month['label']) ?></strong>: 
                                Income NGN <?= number_format($month['income'] ?? 0, 2) ?> | 
                                Expenses NGN <?= number_format($month['expense'] ?? 0, 2) ?> | 
                                Net NGN <?= number_format($month['net'] ?? 0, 2) ?>
                            </div>
                        <?php endforeach; ?>
                        <div style="margin-top:10px; color:var(--cori-gold);">
                            <?php if (($cashForecast['warning_level'] ?? 'none') === 'critical'): ?>
                                ⚠️ <strong>CRITICAL:</strong> Projected expenses exceed income. Act now!
                            <?php elseif (($cashForecast['warning_level'] ?? 'none') === 'warning'): ?>
                                🟡 <strong>WARNING:</strong> Cash flow is tight. Watch your expenses.
                            <?php else: ?>
                                ✅ <strong>HEALTHY:</strong> Cash flow is positive. Keep it up.
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        Not enough transaction data to project financial failure. Give me time.
                    <?php endif; ?>
                </div>
            </div>

            <!-- Staff Assessment -->
            <div class="crm-card" style="padding:0 !important; overflow:hidden; border:1px solid rgba(255,255,255,0.08) !important;">
                <div style="padding: 15px 20px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <h4 style="margin:0; color:#34d399 !important;">Staff Performance Verdict</h4>
                </div>
                <div style="overflow-x:auto;">
                    <table class="crm-table" style="margin:0 !important;">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Pending Tasks</th>
                                <th>Overdue Tasks</th>
                                <th>Completed Tasks</th>
                                <th>AI Verdict</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($staffAssessments)): ?>
                                <?php foreach($staffAssessments as $staff): 
                                    $verdict = '🟢 Working';
                                    $vColor = '#34d399';
                                    if ($staff['overdue_tasks'] > 2) {
                                        $verdict = '🔴 Slacking';
                                        $vColor = '#f87171';
                                    } elseif ($staff['completed_tasks'] > 5 && $staff['overdue_tasks'] == 0) {
                                        $verdict = '🔥 Crushing It';
                                        $vColor = '#FFA600';
                                    } elseif ($staff['total_tasks'] == 0) {
                                        $verdict = '⚪ No Assigned Work';
                                        $vColor = '#94a3b8';
                                    }
                                ?>
                                <tr>
                                    <td>
                                        <div style="font-weight:700; color:#fff !important;"><?= htmlspecialchars(trim($staff['first_name'] . ' ' . $staff['last_name'])) ?></div>
                                    </td>
                                    <td>
                                        <span class="source-pill"><?= htmlspecialchars($staff['department'] ?: 'General') ?></span>
                                    </td>
                                    <td><?= (int)$staff['pending_tasks'] ?></td>
                                    <td style="color: <?= $staff['overdue_tasks'] > 0 ? '#f87171' : 'inherit' ?>; font-weight: <?= $staff['overdue_tasks'] > 0 ? 'bold' : 'normal' ?>;">
                                        <?= (int)$staff['overdue_tasks'] ?>
                                    </td>
                                    <td><?= (int)$staff['completed_tasks'] ?></td>
                                    <td>
                                        <span style="font-weight:700; color: <?= $vColor ?>;">
                                            <?= $verdict ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align:center; padding:30px !important; color:var(--cori-text-muted) !important;">No active staff found. Hire someone to roast.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

</div>

<script>
const messagesEl = document.getElementById('coriMessages');
const inputEl = document.getElementById('coriInput');
const sendBtn = document.getElementById('coriSend');
const typingEl = document.getElementById('typingIndicator');
const userInitial = '<?= strtoupper(substr($_SESSION["user_name"] ?? "U", 0, 1)) . strtoupper(substr($_SESSION["user_name"] ?? "U", strpos($_SESSION["user_name"] ?? "U ", " ") + 1, 1)) ?>';

let isProcessing = false;

// Tab Logic
function switchTab(tabId, btn) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.cori-tab').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + tabId).classList.add('active');
    btn.classList.add('active');
}

// AI Tool Actions
async function runResearch() {
    const leadId = document.getElementById('researchLeadId').value;
    const url = document.getElementById('researchUrl').value;
    const resDiv = document.getElementById('researchResult');
    if (!leadId || !url) return alert('Select lead and enter URL');
    
    resDiv.style.display = 'block';
    resDiv.innerHTML = 'Researching... Please wait.';
    
    const fd = new FormData(); fd.append('url', url);
    const res = await fetch('/api/crm/lead/'+leadId+'/research', { method: 'POST', body: fd });
    const data = await res.json();
    
    if (data.success) {
        resDiv.innerHTML = data.notes;
    } else {
        resDiv.innerHTML = '<span style="color:red">Error: ' + data.error + '</span>';
    }
}

async function runProposal() {
    const leadId = document.getElementById('proposalLeadId').value;
    const resDiv = document.getElementById('proposalResult');
    if (!leadId) return alert('Select lead');
    
    resDiv.style.display = 'block';
    resDiv.innerHTML = 'Drafting proposal...';
    
    const res = await fetch('/api/crm/lead/'+leadId+'/generate-proposal');
    const data = await res.json();
    
    if (data.success) {
        resDiv.innerHTML = data.proposal_html;
    } else {
        resDiv.innerHTML = '<span style="color:red">Error: ' + data.error + '</span>';
    }
}

async function runEmail() {
    const leadId = document.getElementById('emailLeadId').value;
    const type = document.getElementById('emailType').value;
    const tone = document.getElementById('emailTone').value;
    const resDiv = document.getElementById('emailResult');
    if (!leadId) return alert('Select lead');
    
    resDiv.style.display = 'block';
    resDiv.innerHTML = 'Writing email...';
    
    const fd = new FormData(); fd.append('type', type); fd.append('tone', tone);
    const res = await fetch('/api/crm/lead/'+leadId+'/generate-email', { method: 'POST', body: fd });
    const data = await res.json();
    
    if (data.success) {
        resDiv.innerHTML = data.email;
    } else {
        resDiv.innerHTML = '<span style="color:red">Error: ' + data.error + '</span>';
    }
}

// Image attachment handling in dashboard
let coriSelectedImage = null;

function handleCoriFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        coriSelectedImage = {
            base64: e.target.result,
            name: file.name
        };
        const preview = document.getElementById('coriImagePreview');
        const previewImg = document.getElementById('coriPreviewImg');
        const fileName = document.getElementById('coriFileName');
        if (preview && previewImg && fileName) {
            previewImg.src = e.target.result;
            fileName.textContent = file.name || 'Photo captured';
            preview.style.display = 'flex';
        }
        if (inputEl && !inputEl.value.trim()) {
            inputEl.placeholder = "Optional note (e.g. 'Record as expense')...";
        }
    };
    reader.readAsDataURL(file);
}

function clearCoriImage() {
    coriSelectedImage = null;
    const fileInput = document.getElementById('coriFileInput');
    if (fileInput) fileInput.value = '';
    const preview = document.getElementById('coriImagePreview');
    if (preview) preview.style.display = 'none';
    if (inputEl) inputEl.placeholder = "Ask or snap a photo...";
}

// Enter key
inputEl.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

function sendQuick(text) {
    inputEl.value = text;
    sendMessage();
}

function sendMessage() {
    const msg = inputEl.value.trim();
    const hasImage = !!coriSelectedImage;
    if (!msg && !hasImage) return;
    if (isProcessing) return;

    isProcessing = true;
    sendBtn.disabled = true;
    inputEl.value = '';

    const sendingImage = coriSelectedImage ? coriSelectedImage.base64 : null;
    const sendingFileName = coriSelectedImage ? coriSelectedImage.name : null;
    clearCoriImage();

    // Add user bubble with image
    addMessage(msg, 'user', null, sendingImage);

    // Show typing
    typingEl.classList.add('show');
    scrollToBottom();

    const payload = { message: msg };
    if (sendingImage) {
        payload.image = sendingImage;
        payload.filename = sendingFileName;
    }

    // Send to backend
    fetch('/erp/ai-manager/chat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        typingEl.classList.remove('show');
        addMessage(data.response || 'Sorry, I couldn\'t process that.', 'ai', data.type);

        // If there's an action URL, offer a button
        if (data.action_url) {
            addActionButton(data.action_url);
        }

        // If there are suggested quick actions, render clickable suggestion chips
        if (data.suggestions && Array.isArray(data.suggestions) && data.suggestions.length > 0) {
            addSuggestionChips(data.suggestions);
        }

        isProcessing = false;
        sendBtn.disabled = false;
        inputEl.focus();
    })
    .catch(err => {
        typingEl.classList.remove('show');
        addMessage('⚠️ Something went wrong. Please try again.', 'ai');
        isProcessing = false;
        sendBtn.disabled = false;
    });
}

function addMessage(text, sender, type, imageBase64) {
    const row = document.createElement('div');
    row.className = 'msg-row ' + sender;

    const avatar = document.createElement('div');
    avatar.className = 'msg-avatar ' + (sender === 'ai' ? 'ai-av' : 'user-av');
    avatar.textContent = sender === 'ai' ? '✨' : userInitial;

    const bubble = document.createElement('div');
    bubble.className = 'msg-bubble ' + (sender === 'ai' ? 'ai-bubble' : 'user-bubble');

    if (imageBase64) {
        const imgWrap = document.createElement('div');
        imgWrap.style.marginBottom = text ? '8px' : '0';
        const img = document.createElement('img');
        img.src = imageBase64;
        img.style.maxWidth = '240px';
        img.style.maxHeight = '180px';
        img.style.borderRadius = '10px';
        img.style.display = 'block';
        img.style.objectFit = 'cover';
        imgWrap.appendChild(img);
        bubble.appendChild(imgWrap);
    }

    if (sender === 'ai') {
        if (text && (text.includes('<div') || text.includes('cori-scan-card'))) {
            bubble.innerHTML = text;
        } else if (window.marked && text) {
            try { bubble.innerHTML = marked.parse(text); } 
            catch(e) { bubble.textContent = text; }
        } else {
            bubble.textContent = text;
        }
    } else if (text) {
        const span = document.createElement('span');
        span.textContent = text;
        bubble.appendChild(span);
    }

    row.appendChild(avatar);
    row.appendChild(bubble);
    messagesEl.appendChild(row);
    scrollToBottom();
}

// Handler for 1-click execution on scanned cards in dashboard
window.executeCoriScanAction = function(btn) {
    try {
        const payloadStr = btn.getAttribute('data-action-payload');
        if (!payloadStr) return;
        const payload = JSON.parse(payloadStr);

        btn.disabled = true;
        btn.innerHTML = '⏳ Saving...';

        fetch('/erp/ai-manager/execute-action', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.parentElement.innerHTML = '<span style="color:#10b981; font-weight:700; font-size:0.85rem;">' + (data.message || '✅ Saved to ERP!') + '</span> <a href="' + (data.view_url || '/erp') + '" class="btn-ai" style="margin-left:auto; text-decoration:none; display:inline-block; font-size:0.78rem; padding:4px 10px;">View →</a>';
            } else {
                btn.disabled = false;
                btn.innerHTML = '⚠️ Retry';
                alert(data.message || 'Could not save record.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '⚠️ Retry';
        });
    } catch(e) {
        console.error(e);
    }
};

function addActionButton(url) {
    const row = document.createElement('div');
    row.className = 'msg-row ai';
    row.style.maxWidth = '200px';

    const spacer = document.createElement('div');
    spacer.style.width = '42px';

    const btn = document.createElement('a');
    btn.href = url;
    btn.className = 'quick-chip';
    btn.style.background = 'linear-gradient(135deg, rgba(255,166,0,0.15), rgba(255,166,0,0.05))';
    btn.style.borderColor = 'rgba(255,166,0,0.4)';
    btn.style.color = '#FFA600';
    btn.innerHTML = '🔗 Open Now →';

    row.appendChild(spacer);
    row.appendChild(btn);
    messagesEl.appendChild(row);
    scrollToBottom();
}

function addSuggestionChips(suggestions) {
    if (!suggestions || !suggestions.length) return;
    const row = document.createElement('div');
    row.className = 'msg-row ai';
    row.style.marginTop = '-4px';
    row.style.marginBottom = '12px';

    const spacer = document.createElement('div');
    spacer.style.width = '42px';
    spacer.style.flexShrink = '0';

    const wrap = document.createElement('div');
    wrap.style.display = 'flex';
    wrap.style.flexWrap = 'wrap';
    wrap.style.gap = '8px';
    wrap.style.maxWidth = '85%';

    suggestions.forEach(text => {
        const chip = document.createElement('button');
        chip.type = 'button';
        chip.className = 'quick-chip';
        chip.style.cursor = 'pointer';
        chip.style.fontSize = '0.82rem';
        chip.style.padding = '6px 14px';
        chip.style.borderRadius = '20px';
        chip.style.background = 'rgba(255, 166, 0, 0.12)';
        chip.style.border = '1px solid rgba(255, 166, 0, 0.3)';
        chip.style.color = '#FFA600';
        chip.style.transition = 'all 0.2s ease';
        chip.textContent = text;
        chip.onmouseover = () => {
            chip.style.background = 'rgba(255, 166, 0, 0.25)';
            chip.style.borderColor = '#FFA600';
            chip.style.transform = 'translateY(-1px)';
        };
        chip.onmouseout = () => {
            chip.style.background = 'rgba(255, 166, 0, 0.12)';
            chip.style.borderColor = 'rgba(255, 166, 0, 0.3)';
            chip.style.transform = 'none';
        };
        chip.onclick = () => {
            const cleanText = text.replace(/^[^\w\s]+\s*/, '').trim();
            sendQuick(cleanText || text);
        };
        wrap.appendChild(chip);
    });

    row.appendChild(spacer);
    row.appendChild(wrap);
    messagesEl.appendChild(row);
    scrollToBottom();
}

function scrollToBottom() {
    requestAnimationFrame(() => {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    });
}

// ── Supercharged AI Pipeline Functions ──
function filterPipeline(tier, btn) {
    if (btn) {
        document.querySelectorAll('.filter-pill').forEach(el => el.classList.remove('active'));
        btn.classList.add('active');
    }
    const searchVal = document.getElementById('pipelineSearchInput').value.toLowerCase().trim();
    const rows = document.querySelectorAll('#pipelineTable tbody tr.pipeline-row');
    
    rows.forEach(row => {
        const rowTier = row.getAttribute('data-score-tier');
        const searchData = row.getAttribute('data-search') || '';
        
        let matchesTier = (tier === 'all') || (rowTier === tier);
        let matchesSearch = !searchVal || searchData.includes(searchVal);
        
        row.style.display = (matchesTier && matchesSearch) ? '' : 'none';
    });
}

function searchPipelineRows() {
    const activeBtn = document.querySelector('.filter-pill.active');
    let activeTier = 'all';
    if (activeBtn) {
        const text = activeBtn.textContent.toLowerCase();
        if (text.includes('hot')) activeTier = 'hot';
        else if (text.includes('warm')) activeTier = 'warm';
        else if (text.includes('nurture') || text.includes('cold')) activeTier = 'cold';
    }
    filterPipeline(activeTier, null);
}

function triggerResearch(leadId) {
    if (!leadId) return;
    const toolsTabBtn = document.querySelectorAll('.cori-tab')[2];
    if (toolsTabBtn) switchTab('tools', toolsTabBtn);
    const select = document.getElementById('researchLeadId');
    if (select) {
        select.value = leadId;
        runResearch();
    }
}

function triggerProposal(leadId) {
    if (!leadId) return;
    const toolsTabBtn = document.querySelectorAll('.cori-tab')[2];
    if (toolsTabBtn) switchTab('tools', toolsTabBtn);
    const select = document.getElementById('proposalLeadId');
    if (select) {
        select.value = leadId;
        runProposal();
    }
}

function triggerEmail(leadId) {
    if (!leadId) return;
    const toolsTabBtn = document.querySelectorAll('.cori-tab')[2];
    if (toolsTabBtn) switchTab('tools', toolsTabBtn);
    const select = document.getElementById('emailLeadId');
    if (select) {
        select.value = leadId;
        runEmail();
    }
}

function triggerChatStrategy(leadName, company) {
    const chatTabBtn = document.querySelectorAll('.cori-tab')[0];
    if (chatTabBtn) switchTab('chat', chatTabBtn);
    const input = document.getElementById('coriInput');
    if (input) {
        input.value = `Give me a tailored closing strategy, objection handling guide, and pitch breakdown for lead: "${leadName}"` + (company && company !== 'Direct Contact' ? ` from ${company}.` : '.');
        sendMessage();
    }
}

// Focus input on load
inputEl.focus();
    </script>
    
    <!-- Theme Toggle Widget -->

</body>
</html>
