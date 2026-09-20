<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Scheduler &amp; Availability Studio | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        :root {
            --sched-bg: #030712;
            --sched-card-bg: rgba(17, 24, 39, 0.9);
            --sched-card-hover: rgba(31, 41, 55, 0.95);
            --sched-card-subtle: rgba(255, 255, 255, 0.03);
            --sched-border: rgba(255, 255, 255, 0.1);
            --sched-border-strong: rgba(255, 166, 0, 0.35);
            --sched-text: #f8fafc;
            --sched-text-secondary: #cbd5e1;
            --sched-text-muted: #94a3b8;
            --sched-gold: #FFA600;
            --sched-gold-hover: #ffb733;
            --sched-gold-soft: rgba(255, 166, 0, 0.12);
            --sched-gold-glow: rgba(255, 166, 0, 0.25);
            --sched-blue: #3b82f6;
            --sched-blue-soft: rgba(59, 130, 246, 0.15);
            --sched-green: #10b981;
            --sched-green-soft: rgba(16, 185, 129, 0.15);
            --sched-red: #ef4444;
            --sched-red-soft: rgba(239, 68, 68, 0.15);
            --sched-input-bg: rgba(15, 23, 42, 0.7);
            --sched-input-border: rgba(255, 255, 255, 0.15);
        }

        html.light-theme {
            --sched-bg: #f8fafc;
            --sched-card-bg: #ffffff;
            --sched-card-hover: #f1f5f9;
            --sched-card-subtle: #f8fafc;
            --sched-border: #e2e8f0;
            --sched-border-strong: #cbd5e1;
            --sched-text: #0f172a;
            --sched-text-secondary: #334155;
            --sched-text-muted: #64748b;
            --sched-gold: #d97706;
            --sched-gold-hover: #b45309;
            --sched-gold-soft: rgba(217, 119, 6, 0.1);
            --sched-gold-glow: rgba(217, 119, 6, 0.2);
            --sched-input-bg: #ffffff;
            --sched-input-border: #cbd5e1;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--sched-bg);
            color: var(--sched-text);
        }

        /* ── Header & Command Strip ── */
        .sched-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--sched-border);
        }
        .sched-title-box h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--sched-text);
            margin: 0 0 6px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sched-title-box h2 ion-icon {
            color: var(--sched-gold);
            filter: drop-shadow(0 0 8px var(--sched-gold-glow));
        }
        .sched-title-box p {
            margin: 0;
            color: var(--sched-text-muted);
            font-size: 0.95rem;
        }
        .sched-header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .status-pill.active {
            background: var(--sched-green-soft);
            color: var(--sched-green);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .status-pill.inactive {
            background: var(--sched-gold-soft);
            color: var(--sched-gold);
            border: 1px solid rgba(255, 166, 0, 0.3);
        }
        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: currentColor;
            box-shadow: 0 0 8px currentColor;
            animation: pulseAnim 2s infinite;
        }
        @keyframes pulseAnim {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }

        /* ── Share Hub Strip ── */
        .share-hub-card {
            background: var(--sched-card-bg);
            border: 1px solid var(--sched-border-strong);
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 25px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }
        .share-hub-info {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 260px;
        }
        .share-hub-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--sched-gold-soft);
            color: var(--sched-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            border: 1px solid rgba(255, 166, 0, 0.25);
        }
        .share-hub-link {
            flex: 1;
            min-width: 280px;
            display: flex;
            align-items: center;
            background: var(--sched-input-bg);
            border: 1px solid var(--sched-border);
            border-radius: 10px;
            padding: 4px 6px 4px 14px;
            transition: border-color 0.2s;
        }
        .share-hub-link:focus-within {
            border-color: var(--sched-gold);
            box-shadow: 0 0 0 3px var(--sched-gold-glow);
        }
        .share-hub-link input {
            flex: 1;
            background: transparent !important;
            border: none !important;
            color: var(--sched-text) !important;
            font-size: 0.95rem;
            outline: none !important;
            font-family: inherit;
            padding: 8px 0;
        }
        .share-hub-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn-gold {
            background: linear-gradient(135deg, var(--sched-gold), #ff8c00);
            color: #000000 !important;
            font-weight: 700;
            border: none;
            padding: 10px 18px;
            border-radius: 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            transition: all 0.2s;
            box-shadow: 0 4px 15px var(--sched-gold-glow);
            text-decoration: none;
        }
        .btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 166, 0, 0.4);
        }
        .btn-outline {
            background: var(--sched-card-subtle);
            border: 1px solid var(--sched-border);
            color: var(--sched-text);
            font-weight: 600;
            padding: 10px 16px;
            border-radius: 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9rem;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--sched-gold);
            color: var(--sched-gold);
        }

        /* ── Tabs Navigation ── */
        .scheduler-tabs {
            display: flex;
            gap: 8px;
            background: var(--sched-card-subtle);
            padding: 6px;
            border-radius: 14px;
            border: 1px solid var(--sched-border);
            margin-bottom: 25px;
            overflow-x: auto;
        }
        .scheduler-tab {
            color: var(--sched-text-muted);
            font-weight: 600;
            cursor: pointer;
            padding: 10px 22px;
            border-radius: 10px;
            transition: all 0.25s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            font-size: 0.95rem;
        }
        .scheduler-tab:hover {
            color: var(--sched-text);
            background: rgba(255, 255, 255, 0.05);
        }
        .scheduler-tab.active {
            background: var(--sched-card-bg);
            color: var(--sched-gold);
            border: 1px solid var(--sched-border-strong);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .tab-badge {
            background: var(--sched-gold-soft);
            color: var(--sched-gold);
            font-size: 0.75rem;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 700;
        }

        .tab-pane { display: none; }
        .tab-pane.active { display: block; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

        /* ── Cards & Sections ── */
        .sched-section {
            background: var(--sched-card-bg);
            border: 1px solid var(--sched-border);
            border-radius: 18px;
            padding: 28px;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            backdrop-filter: blur(16px);
        }
        .sched-section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid var(--sched-border);
            padding-bottom: 16px;
            margin-bottom: 22px;
            gap: 15px;
            flex-wrap: wrap;
        }
        .sched-section-header h3 {
            margin: 0 0 4px 0;
            color: var(--sched-text);
            font-size: 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .sched-section-header p {
            margin: 0;
            color: var(--sched-text-muted);
            font-size: 0.88rem;
        }

        /* ── Stats Grid ── */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: var(--sched-card-bg);
            border: 1px solid var(--sched-border);
            border-radius: 16px;
            padding: 22px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            border-color: var(--sched-border-strong);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        .stat-card .stat-icon {
            font-size: 2rem;
            color: var(--sched-gold);
            margin-bottom: 12px;
        }
        .stat-card .number {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--sched-text);
            line-height: 1.1;
        }
        .stat-card .label {
            font-size: 0.9rem;
            color: var(--sched-text-muted);
            margin-top: 6px;
            font-weight: 500;
        }

        /* ── Form Controls & Two Column Layout ── */
        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--sched-text-secondary);
            font-size: 0.9rem;
        }
        .form-control-custom {
            width: 100%;
            background: var(--sched-input-bg) !important;
            border: 1px solid var(--sched-input-border) !important;
            border-radius: 10px !important;
            padding: 12px 16px !important;
            color: var(--sched-text) !important;
            font-size: 0.95rem !important;
            font-family: inherit;
            outline: none;
            transition: all 0.2s;
            box-sizing: border-box;
        }
        .form-control-custom:focus {
            border-color: var(--sched-gold) !important;
            box-shadow: 0 0 0 3px var(--sched-gold-glow) !important;
        }
        .input-hint {
            display: block;
            margin-top: 6px;
            font-size: 0.82rem;
            color: var(--sched-text-muted);
        }

        /* Slug input with prefix */
        .slug-input-wrap {
            display: flex;
            align-items: center;
            background: var(--sched-input-bg);
            border: 1px solid var(--sched-input-border);
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.2s;
        }
        .slug-input-wrap:focus-within {
            border-color: var(--sched-gold);
            box-shadow: 0 0 0 3px var(--sched-gold-glow);
        }
        .slug-prefix {
            padding: 12px 14px;
            background: rgba(255, 255, 255, 0.04);
            color: var(--sched-text-muted);
            font-size: 0.9rem;
            font-weight: 500;
            border-right: 1px solid var(--sched-border);
            user-select: none;
        }
        .slug-input-wrap input {
            border: none !important;
            background: transparent !important;
            flex: 1;
            padding: 12px 14px;
            color: var(--sched-text);
            font-weight: 600;
            outline: none;
            font-size: 0.95rem;
            font-family: inherit;
        }

        /* Duration Selector Pills */
        .duration-pills {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .duration-pill {
            padding: 9px 18px;
            border-radius: 10px;
            background: var(--sched-input-bg);
            border: 1px solid var(--sched-border);
            color: var(--sched-text-secondary);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .duration-pill:hover {
            border-color: var(--sched-gold);
            color: var(--sched-text);
        }
        .duration-pill.active {
            background: var(--sched-gold-soft);
            border-color: var(--sched-gold);
            color: var(--sched-gold);
            box-shadow: 0 2px 10px var(--sched-gold-glow);
        }

        /* iOS Toggle Switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 46px;
            height: 26px;
            flex-shrink: 0;
        }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(255, 255, 255, 0.15);
            transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 26px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        input:checked + .slider {
            background-color: var(--sched-gold);
        }
        input:checked + .slider:before {
            transform: translateX(20px);
        }

        /* ── Weekly Availability Studio ── */
        .preset-toolbar {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            padding: 12px 16px;
            background: var(--sched-card-subtle);
            border-radius: 12px;
            border: 1px solid var(--sched-border);
        }
        .preset-label {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--sched-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-right: 4px;
        }
        .preset-btn {
            background: var(--sched-input-bg);
            border: 1px solid var(--sched-border);
            color: var(--sched-text-secondary);
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 0.84rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .preset-btn:hover {
            background: var(--sched-gold-soft);
            border-color: var(--sched-gold);
            color: var(--sched-gold);
        }

        .avail-day-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 14px 20px;
            border-radius: 14px;
            background: var(--sched-card-subtle);
            border: 1px solid var(--sched-border);
            margin-bottom: 12px;
            transition: all 0.25s ease;
        }
        .avail-day-card:hover {
            border-color: rgba(255, 166, 0, 0.25);
        }
        .avail-day-card.disabled-day {
            opacity: 0.55;
            background: transparent;
        }
        .avail-day-card.disabled-day input[type="time"] {
            pointer-events: none;
            opacity: 0.6;
        }
        .day-lead {
            display: flex;
            align-items: center;
            gap: 14px;
            width: 220px;
        }
        .day-name {
            font-weight: 700;
            font-size: 1rem;
            color: var(--sched-text);
            text-transform: capitalize;
            min-width: 100px;
        }
        .day-badge {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .day-badge.avail {
            background: var(--sched-green-soft);
            color: var(--sched-green);
        }
        .day-badge.unavail {
            background: rgba(255, 255, 255, 0.08);
            color: var(--sched-text-muted);
        }

        .day-times {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            flex: 1;
        }
        .time-picker-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .time-picker-wrap input[type="time"] {
            background: var(--sched-input-bg);
            border: 1px solid var(--sched-input-border);
            color: var(--sched-text);
            padding: 8px 14px;
            border-radius: 8px;
            font-family: inherit;
            font-weight: 600;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }
        .time-picker-wrap input[type="time"]:focus {
            border-color: var(--sched-gold);
        }
        .time-sep {
            color: var(--sched-text-muted);
            font-size: 0.9rem;
            font-weight: 500;
        }
        .day-hours-calc {
            font-size: 0.82rem;
            color: var(--sched-gold);
            font-weight: 600;
            background: var(--sched-gold-soft);
            padding: 4px 10px;
            border-radius: 6px;
            margin-left: 8px;
        }
        .btn-copy-day {
            background: transparent;
            border: 1px solid transparent;
            color: var(--sched-text-muted);
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.82rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }
        .btn-copy-day:hover {
            border-color: var(--sched-border);
            color: var(--sched-gold);
            background: rgba(255, 255, 255, 0.04);
        }

        .capacity-summary-bar {
            margin-top: 18px;
            padding: 14px 20px;
            border-radius: 12px;
            background: var(--sched-card-subtle);
            border: 1px solid var(--sched-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.9rem;
            color: var(--sched-text-secondary);
        }
        .capacity-pill {
            font-weight: 700;
            color: var(--sched-gold);
        }

        /* ── Google Calendar Sync Card ── */
        .gcal-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 24px;
            border-radius: 16px;
            border: 1px solid var(--sched-border);
            background: var(--sched-card-subtle);
            flex-wrap: wrap;
        }
        .gcal-card.connected {
            border-color: rgba(16, 185, 129, 0.35);
            background: rgba(16, 185, 129, 0.05);
        }
        .gcal-lead {
            display: flex;
            align-items: center;
            gap: 16px;
            max-width: 600px;
        }
        .gcal-icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--sched-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            flex-shrink: 0;
        }
        .gcal-details h4 {
            margin: 0 0 4px 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--sched-text);
        }
        .gcal-details p {
            margin: 0;
            color: var(--sched-text-muted);
            font-size: 0.88rem;
            line-height: 1.5;
        }

        /* ── Action Sticky Bar ── */
        .action-save-bar {
            position: sticky;
            bottom: 20px;
            background: var(--sched-card-bg);
            border: 1px solid var(--sched-border-strong);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            border-radius: 16px;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            z-index: 100;
            margin-top: 30px;
            backdrop-filter: blur(20px);
        }

        /* ── Booking Cards (Overview & Log) ── */
        .booking-card {
            background: var(--sched-card-bg);
            border: 1px solid var(--sched-border);
            border-radius: 16px;
            padding: 22px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            transition: all 0.25s ease;
        }
        .booking-card:hover {
            transform: translateY(-2px);
            border-color: var(--sched-border-strong);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        .guest-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--sched-gold), #ff8c00);
            color: #000;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }
        .booking-info-main {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            flex: 1;
        }
        .booking-meta-right {
            text-align: right;
            min-width: 180px;
        }
        .booking-date-pill {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--sched-text);
        }
        .booking-time-pill {
            font-size: 0.9rem;
            color: var(--sched-gold);
            font-weight: 600;
            margin-top: 4px;
        }

        /* ── Search & Filter Bar ── */
        .log-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 22px;
            flex-wrap: wrap;
        }
        .log-filter-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .filter-btn {
            background: var(--sched-input-bg);
            border: 1px solid var(--sched-border);
            color: var(--sched-text-secondary);
            padding: 8px 18px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .filter-btn:hover {
            border-color: var(--sched-gold);
            color: var(--sched-text);
        }
        .filter-btn.active {
            background: var(--sched-gold);
            color: #000;
            border-color: var(--sched-gold);
            font-weight: 700;
        }
        .log-search-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--sched-input-bg);
            border: 1px solid var(--sched-border);
            border-radius: 10px;
            padding: 8px 14px;
            min-width: 240px;
        }
        .log-search-box input {
            border: none !important;
            background: transparent !important;
            color: var(--sched-text) !important;
            font-size: 0.9rem;
            outline: none;
            width: 100%;
        }

        /* ── Modals ── */
        .sched-modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            background: rgba(3, 7, 18, 0.75);
            backdrop-filter: blur(8px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }
        .sched-modal.active { display: flex; }
        .sched-modal-dialog {
            background: var(--sched-card-bg);
            border: 1px solid var(--sched-border-strong);
            border-radius: 20px;
            padding: 30px;
            width: 92%;
            max-width: 500px;
            color: var(--sched-text);
            box-shadow: 0 25px 70px rgba(0,0,0,0.5);
            animation: modalPop 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes modalPop {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .sched-modal-dialog h3 {
            margin: 0 0 10px 0;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--sched-text);
        }
        .sched-modal-dialog p {
            margin: 0 0 20px 0;
            color: var(--sched-text-muted);
            font-size: 0.92rem;
            line-height: 1.5;
        }

        /* ── Alerts ── */
        .alert-custom {
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 22px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
        }
        .alert-custom.success {
            background: var(--sched-green-soft);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: var(--sched-green);
        }
        .alert-custom.error {
            background: var(--sched-red-soft);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: var(--sched-red);
        }

        /* ── Empty State ── */
        .sched-empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--sched-card-subtle);
            border: 1px dashed var(--sched-border);
            border-radius: 18px;
            margin: 20px 0;
        }
        .sched-empty-state ion-icon {
            font-size: 3.8rem;
            color: var(--sched-gold);
            opacity: 0.8;
            margin-bottom: 16px;
        }
        .sched-empty-state h3 {
            color: var(--sched-text);
            font-size: 1.3rem;
            margin: 0 0 8px 0;
            font-weight: 700;
        }
        .sched-empty-state p {
            color: var(--sched-text-muted);
            max-width: 440px;
            margin: 0 auto 20px auto;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        @media (max-width: 860px) {
            .form-grid-2 { grid-template-columns: 1fr; }
            .booking-card { flex-direction: column; align-items: stretch; text-align: left; }
            .booking-meta-right { text-align: left; margin-top: 10px; }
            .avail-day-card { flex-direction: column; align-items: stretch; gap: 12px; }
            .day-lead { width: 100%; justify-content: space-between; }
            .share-hub-card { flex-direction: column; align-items: stretch; }
            .share-hub-link { min-width: 100%; }
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../layout/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <!-- ── Header ── -->
        <div class="sched-header">
            <div class="sched-title-box">
                <h2><ion-icon name="calendar-outline"></ion-icon> Booking Scheduler &amp; Availability Studio</h2>
                <p>Manage your client booking page, weekly time slots, Google Calendar sync, and appointments.</p>
            </div>
            <div class="sched-header-actions">
                <?php if ($profile && ($profile['is_active'] ?? 0)): ?>
                    <div class="status-pill active">
                        <span class="pulse-dot"></span> Live &amp; Bookable
                    </div>
                    <a href="/book/<?= htmlspecialchars($profile['slug']) ?>" target="_blank" class="btn-outline">
                        <ion-icon name="open-outline"></ion-icon> View Booking Page
                    </a>
                <?php else: ?>
                    <div class="status-pill inactive">
                        <ion-icon name="pause-circle-outline"></ion-icon> Booking Page Paused
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── Alerts ── -->
        <?php if (isset($_GET['saved'])): ?>
            <div class="alert-custom success"><ion-icon name="checkmark-circle"></ion-icon> Availability and event settings saved successfully!</div>
        <?php endif; ?>
        <?php if (isset($_GET['google_connected'])): ?>
            <div class="alert-custom success"><ion-icon name="logo-google"></ion-icon> Google Calendar connected successfully! 2-way sync is active.</div>
        <?php endif; ?>
        <?php if (isset($_GET['google_disconnected'])): ?>
            <div class="alert-custom success"><ion-icon name="information-circle"></ion-icon> Google Calendar disconnected.</div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert-custom error"><ion-icon name="warning"></ion-icon> <?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <!-- ── Public Booking Link Hero Bar ── -->
        <?php if ($profile): 
            $publicUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'app.casjoe.com') . '/book/' . htmlspecialchars($profile['slug']);
        ?>
        <div class="share-hub-card">
            <div class="share-hub-info">
                <div class="share-hub-icon">
                    <ion-icon name="link-outline"></ion-icon>
                </div>
                <div>
                    <strong style="display:block; color: var(--sched-text); font-size: 0.95rem;">Your Public Booking URL</strong>
                    <span style="color: var(--sched-text-muted); font-size: 0.82rem;">Share this link with clients or embed it on your website</span>
                </div>
            </div>
            <div class="share-hub-link">
                <ion-icon name="globe-outline" style="color: var(--sched-gold); margin-right: 8px; font-size: 1.1rem;"></ion-icon>
                <input type="text" value="<?= $publicUrl ?>" id="publicShareUrlInput" readonly>
            </div>
            <div class="share-hub-buttons">
                <button type="button" class="btn-gold" id="copyShareBtn" onclick="copyShareLink()">
                    <ion-icon name="copy-outline"></ion-icon> <span>Copy Link</span>
                </button>
                <a href="<?= $publicUrl ?>" target="_blank" class="btn-outline" title="Open live booking page in new tab">
                    <ion-icon name="open-outline"></ion-icon> Test Link
                </a>
                <button type="button" class="btn-outline" onclick="openQrModal()" title="View QR code for offline or mobile sharing">
                    <ion-icon name="qr-code-outline"></ion-icon> QR Code
                </button>
                <button type="button" class="btn-outline" onclick="openEmbedModal()" title="Get embed code for website">
                    <ion-icon name="code-slash-outline"></ion-icon> Embed
                </button>
            </div>
        </div>
        <?php endif; ?>

        <!-- ── Segmented Tabs ── -->
        <div class="scheduler-tabs">
            <div class="scheduler-tab" data-tab="overview">
                <ion-icon name="analytics-outline"></ion-icon> Overview
            </div>
            <div class="scheduler-tab" data-tab="bookings">
                <ion-icon name="calendar-number-outline"></ion-icon> Appointments Log
                <?php if (!empty($stats['upcoming'])): ?>
                    <span class="tab-badge"><?= $stats['upcoming'] ?> Upcoming</span>
                <?php endif; ?>
            </div>
            <div class="scheduler-tab active" data-tab="settings">
                <ion-icon name="options-outline"></ion-icon> Availability &amp; Settings
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- ── TAB 1: OVERVIEW ── -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div class="tab-pane" id="tab-overview">
            <?php if ($profile): ?>
                <!-- Stat Cards -->
                <div class="stat-cards">
                    <div class="stat-card">
                        <div class="stat-icon"><ion-icon name="time-outline"></ion-icon></div>
                        <div class="number"><?= (int)($stats['today'] ?? 0) ?></div>
                        <div class="label">Today's Appointments</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><ion-icon name="calendar-outline"></ion-icon></div>
                        <div class="number"><?= (int)($stats['upcoming'] ?? 0) ?></div>
                        <div class="label">Upcoming Active Bookings</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><ion-icon name="checkmark-done-circle-outline"></ion-icon></div>
                        <div class="number"><?= (int)($stats['total'] ?? 0) ?></div>
                        <div class="label">Total Historical Appointments</div>
                    </div>
                </div>

                <!-- Next Upcoming Section -->
                <div class="sched-section">
                    <div class="sched-section-header">
                        <div>
                            <h3><ion-icon name="alarm-outline" style="color: var(--sched-gold);"></ion-icon> Next Upcoming Meetings</h3>
                            <p>Upcoming confirmed client consultations</p>
                        </div>
                        <a onclick="switchTab('bookings')" style="color: var(--sched-gold); font-weight: 600; cursor: pointer; text-decoration: none; font-size: 0.92rem;">
                            View All Bookings Log →
                        </a>
                    </div>

                    <?php if (empty($bookings)): ?>
                        <div class="sched-empty-state">
                            <ion-icon name="calendar-clear-outline"></ion-icon>
                            <h3>No upcoming meetings scheduled</h3>
                            <p>Share your booking link with customers or embed it on your funnels to receive client appointments!</p>
                            <button type="button" class="btn-gold" onclick="copyShareLink()">
                                <ion-icon name="copy-outline"></ion-icon> Copy Booking Link
                            </button>
                        </div>
                    <?php else: ?>
                        <?php foreach ($bookings as $b): 
                            $initials = strtoupper(substr($b['guest_name'] ?? 'G', 0, 2));
                        ?>
                        <div class="booking-card">
                            <div class="booking-info-main">
                                <div class="guest-avatar"><?= htmlspecialchars($initials) ?></div>
                                <div>
                                    <h4 style="margin: 0 0 6px 0; font-size: 1.15rem; color: var(--sched-text); font-weight: 700;">
                                        <?= htmlspecialchars($b['guest_name']) ?>
                                    </h4>
                                    <div style="display: flex; gap: 16px; flex-wrap: wrap; font-size: 0.88rem; color: var(--sched-text-muted);">
                                        <span style="display: flex; align-items: center; gap: 4px;">
                                            <ion-icon name="mail-outline"></ion-icon> 
                                            <a href="mailto:<?= htmlspecialchars($b['guest_email']) ?>" style="color: inherit; text-decoration: none;"><?= htmlspecialchars($b['guest_email']) ?></a>
                                        </span>
                                        <?php if (!empty($b['guest_phone'])): ?>
                                            <span style="display: flex; align-items: center; gap: 4px;">
                                                <ion-icon name="call-outline"></ion-icon>
                                                <a href="tel:<?= htmlspecialchars($b['guest_phone']) ?>" style="color: inherit; text-decoration: none;"><?= htmlspecialchars($b['guest_phone']) ?></a>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($b['guest_notes'])): ?>
                                        <div style="margin-top: 10px; background: var(--sched-card-subtle); border: 1px solid var(--sched-border); padding: 8px 12px; border-radius: 8px; font-size: 0.85rem; color: var(--sched-text-secondary); max-width: 600px;">
                                            <strong style="color: var(--sched-gold);"><ion-icon name="document-text-outline"></ion-icon> Client Note:</strong> 
                                            <?= htmlspecialchars($b['guest_notes']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <div style="margin-top: 12px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                        <?php if (!empty($b['meet_link'])): ?>
                                            <a href="<?= htmlspecialchars($b['meet_link']) ?>" target="_blank" class="btn-gold" style="padding: 6px 14px; font-size: 0.84rem;">
                                                <ion-icon name="videocam-outline"></ion-icon> Join Video Call
                                            </a>
                                        <?php endif; ?>
                                        <button type="button" onclick="openCancelModal(<?= (int)$b['id'] ?>)" class="btn-outline" style="padding: 6px 12px; font-size: 0.84rem; color: var(--sched-red); border-color: rgba(239,68,68,0.3);">
                                            <ion-icon name="close-circle-outline"></ion-icon> Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="booking-meta-right">
                                <div class="booking-date-pill"><?= date('D, M j, Y', strtotime($b['booking_date'])) ?></div>
                                <div class="booking-time-pill"><?= date('g:i A', strtotime($b['start_time'])) ?> &ndash; <?= date('g:i A', strtotime($b['end_time'])) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            <?php else: ?>
                <div class="sched-empty-state">
                    <ion-icon name="calendar-outline"></ion-icon>
                    <h3>Set Up Your Scheduler Profile</h3>
                    <p>You haven't configured your scheduler profile yet. Customize your availability slots, meeting duration, and slug to launch your booking page.</p>
                    <button type="button" onclick="switchTab('settings')" class="btn-gold" style="padding: 12px 30px; font-size: 1rem;">
                        <ion-icon name="rocket-outline"></ion-icon> Configure Profile Now
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- ── TAB 2: APPOINTMENTS LOG ── -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div class="tab-pane" id="tab-bookings">
            <div class="sched-section">
                <div class="sched-section-header">
                    <div>
                        <h3><ion-icon name="list-outline" style="color: var(--sched-gold);"></ion-icon> Appointments Management Log</h3>
                        <p>Complete record of scheduled, completed, and cancelled client meetings</p>
                    </div>
                </div>

                <!-- Filters & Real-time Search -->
                <div class="log-toolbar">
                    <div class="log-filter-pills">
                        <a href="/erp/scheduler?filter=all&tab=bookings" class="filter-btn <?= $filter === 'all' ? 'active' : '' ?>">All Appointments</a>
                        <a href="/erp/scheduler?filter=upcoming&tab=bookings" class="filter-btn <?= $filter === 'upcoming' ? 'active' : '' ?>">Upcoming Active</a>
                        <a href="/erp/scheduler?filter=cancelled&tab=bookings" class="filter-btn <?= $filter === 'cancelled' ? 'active' : '' ?>">Cancelled</a>
                    </div>
                    <div class="log-search-box">
                        <ion-icon name="search-outline" style="color: var(--sched-text-muted);"></ion-icon>
                        <input type="text" id="logSearchInput" placeholder="Filter by guest name, email..." oninput="filterBookingList(this.value)">
                    </div>
                </div>

                <div id="bookingListContainer">
                    <?php if (empty($allBookings)): ?>
                        <div class="sched-empty-state">
                            <ion-icon name="folder-open-outline"></ion-icon>
                            <h3>No appointments found</h3>
                            <p>There are no bookings matching the selected filter.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($allBookings as $b): 
                            $initials = strtoupper(substr($b['guest_name'] ?? 'G', 0, 2));
                            $isCancelled = ($b['status'] === 'cancelled');
                            $isFuture = (strtotime($b['booking_date'] . ' ' . $b['start_time']) >= time());
                        ?>
                        <div class="booking-card item-booking-row" data-guest="<?= strtolower(htmlspecialchars($b['guest_name'] . ' ' . $b['guest_email'])) ?>">
                            <div class="booking-info-main">
                                <div class="guest-avatar" style="<?= $isCancelled ? 'filter: grayscale(1); opacity:0.6;' : '' ?>">
                                    <?= htmlspecialchars($initials) ?>
                                </div>
                                <div>
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px; flex-wrap: wrap;">
                                        <h4 style="margin: 0; font-size: 1.15rem; color: var(--sched-text); font-weight: 700;">
                                            <?= htmlspecialchars($b['guest_name']) ?>
                                        </h4>
                                        <?php if ($isCancelled): ?>
                                            <span class="status-pill" style="background: var(--sched-red-soft); color: var(--sched-red); font-size: 0.72rem; padding: 2px 10px;">
                                                Cancelled
                                            </span>
                                        <?php elseif ($isFuture): ?>
                                            <span class="status-pill active" style="font-size: 0.72rem; padding: 2px 10px;">
                                                Upcoming Active
                                            </span>
                                        <?php else: ?>
                                            <span class="status-pill" style="background: rgba(255,255,255,0.08); color: var(--sched-text-muted); font-size: 0.72rem; padding: 2px 10px;">
                                                Completed
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="display: flex; gap: 16px; flex-wrap: wrap; font-size: 0.88rem; color: var(--sched-text-muted);">
                                        <span style="display: flex; align-items: center; gap: 4px;">
                                            <ion-icon name="mail-outline"></ion-icon> 
                                            <a href="mailto:<?= htmlspecialchars($b['guest_email']) ?>" style="color: inherit; text-decoration: none;"><?= htmlspecialchars($b['guest_email']) ?></a>
                                        </span>
                                        <?php if (!empty($b['guest_phone'])): ?>
                                            <span style="display: flex; align-items: center; gap: 4px;">
                                                <ion-icon name="call-outline"></ion-icon>
                                                <a href="tel:<?= htmlspecialchars($b['guest_phone']) ?>" style="color: inherit; text-decoration: none;"><?= htmlspecialchars($b['guest_phone']) ?></a>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!empty($b['guest_notes'])): ?>
                                        <div style="margin-top: 8px; background: var(--sched-card-subtle); border: 1px solid var(--sched-border); padding: 6px 12px; border-radius: 8px; font-size: 0.84rem; color: var(--sched-text-secondary);">
                                            <strong>Client Note:</strong> <?= htmlspecialchars($b['guest_notes']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($isCancelled && !empty($b['cancel_reason'])): ?>
                                        <div style="margin-top: 8px; color: var(--sched-red); font-size: 0.84rem; font-weight: 500; display: flex; align-items: center; gap: 6px;">
                                            <ion-icon name="alert-circle-outline"></ion-icon> 
                                            Reason: <?= htmlspecialchars($b['cancel_reason']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <div style="margin-top: 10px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                        <?php if (!$isCancelled && !empty($b['meet_link'])): ?>
                                            <a href="<?= htmlspecialchars($b['meet_link']) ?>" target="_blank" class="btn-gold" style="padding: 6px 14px; font-size: 0.84rem;">
                                                <ion-icon name="videocam-outline"></ion-icon> Join Video Call
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!$isCancelled && $isFuture): ?>
                                            <button type="button" onclick="openCancelModal(<?= (int)$b['id'] ?>)" class="btn-outline" style="padding: 6px 12px; font-size: 0.84rem; color: var(--sched-red); border-color: rgba(239,68,68,0.3);">
                                                <ion-icon name="close-circle-outline"></ion-icon> Cancel Appointment
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="booking-meta-right">
                                <div class="booking-date-pill"><?= date('D, M j, Y', strtotime($b['booking_date'])) ?></div>
                                <div class="booking-time-pill"><?= date('g:i A', strtotime($b['start_time'])) ?> &ndash; <?= date('g:i A', strtotime($b['end_time'])) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- ── TAB 3: AVAILABILITY & SETTINGS ── -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div class="tab-pane active" id="tab-settings">
            <form method="POST" action="/erp/scheduler/settings/save" id="schedulerForm">
                <?= \App\Core\Services\CsrfService::getTokenField() ?>

                <!-- 1. Event Profile Card -->
                <div class="sched-section">
                    <div class="sched-section-header">
                        <div>
                            <h3><ion-icon name="id-card-outline" style="color: var(--sched-gold);"></ion-icon> Event Profile &amp; Booking Page</h3>
                            <p>Configure what clients see when booking appointments with you</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <label class="switch">
                                <input type="checkbox" name="is_active" id="isActiveInput" <?= ($profile['is_active'] ?? 1) ? 'checked' : '' ?> onchange="updateActiveToggle(this)">
                                <span class="slider"></span>
                            </label>
                            <span id="activeToggleLabel" style="font-weight: 700; font-size: 0.9rem; color: <?= ($profile['is_active'] ?? 1) ? 'var(--sched-green)' : 'var(--sched-gold)' ?>;">
                                <?= ($profile['is_active'] ?? 1) ? 'Page is Live' : 'Page is Paused' ?>
                            </span>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label><ion-icon name="text-outline" style="vertical-align: middle;"></ion-icon> Event Booking Title</label>
                            <input type="text" name="title" class="form-control-custom" value="<?= htmlspecialchars($profile['title'] ?? '30-Minute Discovery Call') ?>" placeholder="e.g. 30-Minute Discovery Call" required>
                            <span class="input-hint">The main headline shown to clients on the booking page</span>
                        </div>

                        <div class="form-group">
                            <label><ion-icon name="link-outline" style="vertical-align: middle;"></ion-icon> Public Booking Slug</label>
                            <div class="slug-input-wrap">
                                <span class="slug-prefix">casjoe.com/book/</span>
                                <input type="text" name="slug" id="slugInput" value="<?= htmlspecialchars($profile['slug'] ?? 'meeting') ?>" placeholder="your-name" required oninput="updateSlugPreview(this.value)">
                            </div>
                            <span class="input-hint">Your unique public calendar URL</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><ion-icon name="document-text-outline" style="vertical-align: middle;"></ion-icon> Description &amp; Client Instructions</label>
                        <textarea name="description" class="form-control-custom" rows="3" placeholder="A brief introductory call to understand your business requirements and discuss how Casjoe LLC can support your growth..."><?= htmlspecialchars($profile['description'] ?? '') ?></textarea>
                        <span class="input-hint">Included in the booking page header and calendar invite details</span>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label><ion-icon name="timer-outline" style="vertical-align: middle;"></ion-icon> Appointment Duration</label>
                            <input type="hidden" name="duration" id="durationHiddenInput" value="<?= (int)($profile['duration'] ?? 30) ?>">
                            <div class="duration-pills">
                                <div class="duration-pill <?= ($profile['duration'] ?? 30) == 15 ? 'active' : '' ?>" onclick="selectDuration(15, this)">
                                    ⏱ 15 min
                                </div>
                                <div class="duration-pill <?= ($profile['duration'] ?? 30) == 30 ? 'active' : '' ?>" onclick="selectDuration(30, this)">
                                    ⏱ 30 min
                                </div>
                                <div class="duration-pill <?= ($profile['duration'] ?? 30) == 45 ? 'active' : '' ?>" onclick="selectDuration(45, this)">
                                    ⏱ 45 min
                                </div>
                                <div class="duration-pill <?= ($profile['duration'] ?? 30) == 60 ? 'active' : '' ?>" onclick="selectDuration(60, this)">
                                    ⏱ 60 min
                                </div>
                                <div class="duration-pill <?= ($profile['duration'] ?? 30) == 90 ? 'active' : '' ?>" onclick="selectDuration(90, this)">
                                    ⏱ 90 min
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <label style="margin-bottom: 0;"><ion-icon name="globe-outline" style="vertical-align: middle;"></ion-icon> Host Timezone</label>
                                <button type="button" class="btn-outline" style="padding: 4px 10px; font-size: 0.78rem;" onclick="autoDetectTimezone()">
                                    <ion-icon name="locate-outline"></ion-icon> Detect My Timezone
                                </button>
                            </div>
                            <select name="timezone" id="timezoneSelect" class="form-control-custom">
                                <option value="Africa/Lagos" <?= ($profile['timezone'] ?? '') == 'Africa/Lagos' ? 'selected' : '' ?>>Africa/Lagos (WAT, GMT+1)</option>
                                <option value="Africa/Accra" <?= ($profile['timezone'] ?? '') == 'Africa/Accra' ? 'selected' : '' ?>>Africa/Accra (GMT)</option>
                                <option value="Africa/Johannesburg" <?= ($profile['timezone'] ?? '') == 'Africa/Johannesburg' ? 'selected' : '' ?>>Africa/Johannesburg (SAST, GMT+2)</option>
                                <option value="Africa/Nairobi" <?= ($profile['timezone'] ?? '') == 'Africa/Nairobi' ? 'selected' : '' ?>>Africa/Nairobi (EAT, GMT+3)</option>
                                <option value="Europe/London" <?= ($profile['timezone'] ?? '') == 'Europe/London' ? 'selected' : '' ?>>Europe/London (GMT/BST)</option>
                                <option value="Europe/Paris" <?= ($profile['timezone'] ?? '') == 'Europe/Paris' ? 'selected' : '' ?>>Europe/Paris (CET, GMT+1)</option>
                                <option value="America/New_York" <?= ($profile['timezone'] ?? '') == 'America/New_York' ? 'selected' : '' ?>>America/New York (EST/EDT)</option>
                                <option value="America/Chicago" <?= ($profile['timezone'] ?? '') == 'America/Chicago' ? 'selected' : '' ?>>America/Chicago (CST/CDT)</option>
                                <option value="America/Denver" <?= ($profile['timezone'] ?? '') == 'America/Denver' ? 'selected' : '' ?>>America/Denver (MST/MDT)</option>
                                <option value="America/Los_Angeles" <?= ($profile['timezone'] ?? '') == 'America/Los_Angeles' ? 'selected' : '' ?>>America/Los Angeles (PST/PDT)</option>
                                <option value="America/Toronto" <?= ($profile['timezone'] ?? '') == 'America/Toronto' ? 'selected' : '' ?>>America/Toronto (EST/EDT)</option>
                                <option value="Asia/Dubai" <?= ($profile['timezone'] ?? '') == 'Asia/Dubai' ? 'selected' : '' ?>>Asia/Dubai (GST, GMT+4)</option>
                                <option value="Asia/Singapore" <?= ($profile['timezone'] ?? '') == 'Asia/Singapore' ? 'selected' : '' ?>>Asia/Singapore (SGT, GMT+8)</option>
                                <option value="Asia/Tokyo" <?= ($profile['timezone'] ?? '') == 'Asia/Tokyo' ? 'selected' : '' ?>>Asia/Tokyo (JST, GMT+9)</option>
                                <option value="Australia/Sydney" <?= ($profile['timezone'] ?? '') == 'Australia/Sydney' ? 'selected' : '' ?>>Australia/Sydney (AEST, GMT+10)</option>
                            </select>
                            <span class="input-hint">Slots will be converted automatically to the visitor's local timezone</span>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label><ion-icon name="videocam-outline" style="vertical-align: middle;"></ion-icon> Custom Video Conference Link (Optional Fallback)</label>
                        <input type="url" name="meeting_link" class="form-control-custom" value="<?= htmlspecialchars($profile['meeting_link'] ?? '') ?>" placeholder="https://zoom.us/j/123456789 or https://meet.google.com/xyz">
                        <span class="input-hint">If provided, this Zoom / Teams / Meet link will be attached to guest confirmation emails. (If empty and Google Calendar is linked, Google Meet will be auto-generated).</span>
                    </div>
                </div>

                <!-- 2. Weekly Availability Studio -->
                <div class="sched-section">
                    <div class="sched-section-header">
                        <div>
                            <h3><ion-icon name="time-outline" style="color: var(--sched-gold);"></ion-icon> Weekly Availability Studio</h3>
                            <p>Set the days and hours when clients are allowed to book appointments with you</p>
                        </div>
                    </div>

                    <!-- Quick Preset Toolbar -->
                    <div class="preset-toolbar">
                        <span class="preset-label">⚡ Quick Presets:</span>
                        <button type="button" class="preset-btn" onclick="applyPreset('standard')">
                            💼 Mon–Fri (9:00 AM – 5:00 PM)
                        </button>
                        <button type="button" class="preset-btn" onclick="applyPreset('morning')">
                            🌅 Morning Shift (8:00 AM – 1:00 PM)
                        </button>
                        <button type="button" class="preset-btn" onclick="applyPreset('afternoon')">
                            🌇 Afternoon Shift (1:00 PM – 7:00 PM)
                        </button>
                        <button type="button" class="preset-btn" onclick="copyMondayToAll()">
                            🔄 Copy Monday to All Active Days
                        </button>
                        <button type="button" class="preset-btn" onclick="applyPreset('clear_weekend')">
                            🚫 Clear Weekends
                        </button>
                    </div>

                    <!-- 7 Day Cards -->
                    <div id="availabilityCardsContainer">
                        <?php
                        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                        foreach ($days as $day):
                            $dayData = $availability[$day] ?? ['enabled' => false, 'start' => '09:00', 'end' => '17:00'];
                            $isEnabled = !empty($dayData['enabled']);
                            $startTime = $dayData['start'] ?? '09:00';
                            $endTime = $dayData['end'] ?? '17:00';
                        ?>
                        <div class="avail-day-card <?= $isEnabled ? '' : 'disabled-day' ?>" id="card_<?= $day ?>">
                            <div class="day-lead">
                                <label class="switch">
                                    <input type="checkbox" name="avail_<?= $day ?>" id="toggle_<?= $day ?>" <?= $isEnabled ? 'checked' : '' ?> onchange="toggleDayAvailability('<?= $day ?>', this)">
                                    <span class="slider"></span>
                                </label>
                                <span class="day-name"><?= $day ?></span>
                                <span class="day-badge <?= $isEnabled ? 'avail' : 'unavail' ?>" id="badge_<?= $day ?>">
                                    <?= $isEnabled ? 'Available' : 'Unavailable' ?>
                                </span>
                            </div>

                            <div class="day-times">
                                <div class="time-picker-wrap">
                                    <input type="time" name="avail_<?= $day ?>_start" id="start_<?= $day ?>" value="<?= htmlspecialchars($startTime) ?>" onchange="recalculateDayHours('<?= $day ?>')">
                                    <span class="time-sep">to</span>
                                    <input type="time" name="avail_<?= $day ?>_end" id="end_<?= $day ?>" value="<?= htmlspecialchars($endTime) ?>" onchange="recalculateDayHours('<?= $day ?>')">
                                </div>
                                <span class="day-hours-calc" id="calc_<?= $day ?>">8 hrs</span>
                                <button type="button" class="btn-copy-day" onclick="copySingleDayHoursToAll('<?= $day ?>')" title="Copy <?= ucfirst($day) ?> hours to all other enabled days">
                                    <ion-icon name="copy-outline"></ion-icon> Apply to all
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Capacity Summary -->
                    <div class="capacity-summary-bar">
                        <span><ion-icon name="speedometer-outline" style="color: var(--sched-gold); vertical-align: middle;"></ion-icon> Total Weekly Capacity:</span>
                        <span class="capacity-pill" id="capacityPill">5 Days Active &bull; 40 Hours Total Availability</span>
                    </div>
                </div>

                <!-- 3. Google Calendar Sync Card -->
                <div class="sched-section">
                    <div class="sched-section-header">
                        <div>
                            <h3><ion-icon name="logo-google" style="color: #4285f4;"></ion-icon> Google Calendar 2-Way Synchronization</h3>
                            <p>Seamlessly check personal calendar conflicts and auto-generate Google Meet attachments</p>
                        </div>
                    </div>

                    <?php if (!$googleConfigured): ?>
                        <div class="gcal-card">
                            <div class="gcal-lead">
                                <div class="gcal-icon-wrap" style="color: var(--sched-gold);">
                                    <ion-icon name="warning-outline"></ion-icon>
                                </div>
                                <div class="gcal-details">
                                    <h4>Google OAuth Not Configured</h4>
                                    <p>Google Client ID and Client Secret must be configured in your environment or Admin settings to activate real-time calendar synchronization.</p>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($googleConnected): ?>
                        <div class="gcal-card connected">
                            <div class="gcal-lead">
                                <div class="gcal-icon-wrap" style="color: var(--sched-green); background: rgba(16,185,129,0.1);">
                                    <ion-icon name="checkmark-circle"></ion-icon>
                                </div>
                                <div class="gcal-details">
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                                        <h4>Google Calendar Connected &amp; Synchronized</h4>
                                        <span class="status-pill active" style="font-size: 0.72rem; padding: 2px 8px;">Active</span>
                                    </div>
                                    <p>New appointments automatically create Google Calendar events with Google Meet links. Conflicting busy times in your Google Calendar are automatically removed from your bookable slots.</p>
                                </div>
                            </div>
                            <div>
                                <a href="/erp/scheduler/google/disconnect" class="btn-outline" style="color: var(--sched-red); border-color: rgba(239,68,68,0.3);" onclick="return confirm('Are you sure you want to disconnect Google Calendar?');">
                                    <ion-icon name="unlink-outline"></ion-icon> Disconnect Google Calendar
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="gcal-card">
                            <div class="gcal-lead">
                                <div class="gcal-icon-wrap" style="color: #4285f4;">
                                    <ion-icon name="logo-google"></ion-icon>
                                </div>
                                <div class="gcal-details">
                                    <h4>Connect Your Google Calendar</h4>
                                    <p>Link your Google account to prevent double-bookings, block busy slots automatically, and generate Google Meet links for every appointment.</p>
                                </div>
                            </div>
                            <div>
                                <a href="/erp/scheduler/google/auth" class="btn-gold" style="background: #4285f4; color: #fff !important; box-shadow: 0 4px 15px rgba(66,133,244,0.3);">
                                    <ion-icon name="logo-google"></ion-icon> Connect Google Calendar
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ── Action Save Bar ── -->
                <div class="action-save-bar">
                    <div style="color: var(--sched-text-muted); font-size: 0.9rem;">
                        <ion-icon name="shield-checkmark-outline" style="color: var(--sched-green); vertical-align: middle;"></ion-icon> 
                        All changes apply instantly to your live booking link.
                    </div>
                    <div>
                        <button type="submit" class="btn-gold" style="padding: 12px 34px; font-size: 1rem;" id="saveBtn">
                            <ion-icon name="save-outline"></ion-icon> <span>Save Availability &amp; Settings</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- ── MODALS ── -->
<!-- ══════════════════════════════════════════════════════════ -->

<!-- QR Code Modal -->
<div class="sched-modal" id="qrModal" onclick="if(event.target===this)closeQrModal()">
    <div class="sched-modal-dialog">
        <h3><ion-icon name="qr-code-outline" style="color: var(--sched-gold); vertical-align: middle;"></ion-icon> Booking Page QR Code</h3>
        <p>Scan this QR code with any smartphone camera to immediately open your booking page. Perfect for business cards, flyers, and client presentations.</p>
        <div style="text-align: center; margin: 20px 0; padding: 20px; background: #ffffff; border-radius: 14px; display: inline-block; width: 100%; box-sizing: border-box;">
            <img id="qrCodeImg" src="" alt="QR Code" style="max-width: 220px; height: auto; display: block; margin: 0 auto;">
            <div style="margin-top: 10px; color: #0f172a; font-weight: 700; font-size: 0.9rem;" id="qrCodeUrlText"></div>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="btn-outline" onclick="closeQrModal()">Close</button>
            <a id="qrDownloadLink" href="" download="booking_qr_code.png" class="btn-gold">
                <ion-icon name="download-outline"></ion-icon> Download QR
            </a>
        </div>
    </div>
</div>

<!-- Embed Modal -->
<div class="sched-modal" id="embedModal" onclick="if(event.target===this)closeEmbedModal()">
    <div class="sched-modal-dialog">
        <h3><ion-icon name="code-slash-outline" style="color: var(--sched-gold); vertical-align: middle;"></ion-icon> Embed Booking Calendar</h3>
        <p>Paste this HTML iframe snippet into your website, landing page, or client portal to embed your live booking calendar directly.</p>
        <div class="form-group">
            <textarea id="embedCodeSnippet" class="form-control-custom" rows="4" readonly style="font-family: monospace; font-size: 0.85rem; background: rgba(0,0,0,0.3);"></textarea>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="btn-outline" onclick="closeEmbedModal()">Close</button>
            <button type="button" class="btn-gold" id="copyEmbedBtn" onclick="copyEmbedCode()">
                <ion-icon name="copy-outline"></ion-icon> <span>Copy Embed Code</span>
            </button>
        </div>
    </div>
</div>

<!-- Cancellation Reason Modal -->
<div class="sched-modal" id="cancelModal" onclick="if(event.target===this)closeCancelModal()">
    <div class="sched-modal-dialog">
        <h3 style="color: var(--sched-red);"><ion-icon name="alert-circle-outline" style="vertical-align: middle;"></ion-icon> Cancel Client Appointment</h3>
        <p>Please enter a brief reason for cancelling this appointment. A professional cancellation notice will be immediately dispatched to the guest's email address.</p>
        <form method="POST" action="/erp/scheduler/bookings/cancel">
            <?= \App\Core\Services\CsrfService::getTokenField() ?>
            <input type="hidden" name="id" id="cancelBookingId" value="">
            <div class="form-group">
                <label>Cancellation Reason / Note to Guest</label>
                <textarea name="cancel_reason" class="form-control-custom" rows="3" placeholder="e.g. Unforeseen schedule conflict, please reschedule via our booking link." required></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn-outline" onclick="closeCancelModal()">Dismiss</button>
                <button type="submit" class="btn-gold" style="background: var(--sched-red); color: #fff !important; box-shadow: 0 4px 15px var(--sched-red-soft);">
                    <ion-icon name="close-circle-outline"></ion-icon> Confirm Cancellation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- ── CLIENT LOGIC ── -->
<!-- ══════════════════════════════════════════════════════════ -->
<script>
// 1. Tab Switching Engine
const tabs = document.querySelectorAll('.scheduler-tab');
const panes = document.querySelectorAll('.tab-pane');

tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        const tabName = tab.getAttribute('data-tab');
        switchTab(tabName);
    });
});

function switchTab(tabName) {
    tabs.forEach(t => t.classList.remove('active'));
    panes.forEach(p => p.classList.remove('active'));

    const activeTab = document.querySelector(`.scheduler-tab[data-tab="${tabName}"]`);
    const activePane = document.getElementById(`tab-${tabName}`);
    
    if (activeTab && activePane) {
        activeTab.classList.add('active');
        activePane.classList.add('active');
        
        // Sync URL query without page reload
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.replaceState({}, '', url);
    }
}

// 2. Share Hub & Copy
function copyShareLink() {
    const input = document.getElementById('publicShareUrlInput');
    if (!input) return;
    
    input.select();
    input.setSelectionRange(0, 99999);
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(input.value);
    } else {
        document.execCommand('copy');
    }
    
    const btn = document.getElementById('copyShareBtn');
    if (btn) {
        const span = btn.querySelector('span');
        const oldText = span ? span.textContent : 'Copy Link';
        if (span) span.textContent = '✓ Copied!';
        btn.style.background = 'var(--sched-green)';
        setTimeout(() => {
            if (span) span.textContent = oldText;
            btn.style.background = '';
        }, 2200);
    }
}

// 3. Slug Preview Sync
function updateSlugPreview(slugVal) {
    const clean = slugVal.toLowerCase().replace(/[^a-z0-9_-]/g, '-');
    const input = document.getElementById('slugInput');
    if (input && input.value !== clean) {
        input.value = clean;
    }
    const publicInput = document.getElementById('publicShareUrlInput');
    if (publicInput) {
        const host = window.location.host || 'app.casjoe.com';
        publicInput.value = 'https://' + host + '/book/' + clean;
    }
}

// 4. Duration Pills
function selectDuration(mins, pillEl) {
    document.querySelectorAll('.duration-pill').forEach(p => p.classList.remove('active'));
    pillEl.classList.add('active');
    document.getElementById('durationHiddenInput').value = mins;
}

// 5. Active Status Switch
function updateActiveToggle(checkbox) {
    const label = document.getElementById('activeToggleLabel');
    if (checkbox.checked) {
        label.textContent = 'Page is Live';
        label.style.color = 'var(--sched-green)';
    } else {
        label.textContent = 'Page is Paused';
        label.style.color = 'var(--sched-gold)';
    }
}

// 6. Timezone Auto-Detect
function autoDetectTimezone() {
    try {
        const detected = Intl.DateTimeFormat().resolvedOptions().timeZone;
        const select = document.getElementById('timezoneSelect');
        if (select) {
            let found = false;
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].value === detected) {
                    select.selectedIndex = i;
                    found = true;
                    break;
                }
            }
            if (!found) {
                // Add new option and select
                const opt = document.createElement('option');
                opt.value = detected;
                opt.textContent = detected + ' (Detected)';
                opt.selected = true;
                select.appendChild(opt);
            }
            alert('Detected and selected timezone: ' + detected);
        }
    } catch (e) {
        alert('Could not auto-detect timezone: ' + e.message);
    }
}

// 7. Day Toggle & Hours Recalculation
const daysList = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

function toggleDayAvailability(day, checkbox) {
    const card = document.getElementById('card_' + day);
    const badge = document.getElementById('badge_' + day);
    
    if (checkbox.checked) {
        card.classList.remove('disabled-day');
        badge.className = 'day-badge avail';
        badge.textContent = 'Available';
    } else {
        card.classList.add('disabled-day');
        badge.className = 'day-badge unavail';
        badge.textContent = 'Unavailable';
    }
    recalculateDayHours(day);
    updateWeeklyCapacitySummary();
}

function parseTimeToMinutes(t) {
    if (!t) return 0;
    const parts = t.split(':');
    return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
}

function recalculateDayHours(day) {
    const startInput = document.getElementById('start_' + day);
    const endInput = document.getElementById('end_' + day);
    const calcEl = document.getElementById('calc_' + day);
    const toggle = document.getElementById('toggle_' + day);
    
    if (!toggle.checked) {
        calcEl.textContent = 'Off';
        calcEl.style.opacity = '0.5';
        updateWeeklyCapacitySummary();
        return;
    }
    
    calcEl.style.opacity = '1';
    const startM = parseTimeToMinutes(startInput.value);
    const endM = parseTimeToMinutes(endInput.value);
    
    let diff = endM - startM;
    if (diff <= 0) {
        calcEl.textContent = 'Invalid';
        calcEl.style.color = 'var(--sched-red)';
    } else {
        const hrs = Math.floor(diff / 60);
        const mins = diff % 60;
        calcEl.style.color = 'var(--sched-gold)';
        calcEl.textContent = hrs + (mins > 0 ? '.' + Math.round((mins/60)*10) : '') + ' hrs';
    }
    updateWeeklyCapacitySummary();
}

function updateWeeklyCapacitySummary() {
    let activeDays = 0;
    let totalMinutes = 0;
    
    daysList.forEach(day => {
        const toggle = document.getElementById('toggle_' + day);
        if (toggle && toggle.checked) {
            activeDays++;
            const s = parseTimeToMinutes(document.getElementById('start_' + day).value);
            const e = parseTimeToMinutes(document.getElementById('end_' + day).value);
            if (e > s) totalMinutes += (e - s);
        }
    });
    
    const totalHours = Math.round((totalMinutes / 60) * 10) / 10;
    const pill = document.getElementById('capacityPill');
    if (pill) {
        pill.textContent = `${activeDays} Days Active • ${totalHours} Hours Total Availability / Week`;
    }
}

// 8. Presets Engine
function applyPreset(type) {
    if (type === 'standard') {
        daysList.forEach(day => {
            const isWeekday = (day !== 'saturday' && day !== 'sunday');
            setDayConfig(day, isWeekday, '09:00', '17:00');
        });
    } else if (type === 'morning') {
        daysList.forEach(day => {
            const isWeekday = (day !== 'saturday' && day !== 'sunday');
            setDayConfig(day, isWeekday, '08:00', '13:00');
        });
    } else if (type === 'afternoon') {
        daysList.forEach(day => {
            const isWeekday = (day !== 'saturday' && day !== 'sunday');
            setDayConfig(day, isWeekday, '13:00', '19:00');
        });
    } else if (type === 'clear_weekend') {
        setDayConfig('saturday', false, '09:00', '17:00');
        setDayConfig('sunday', false, '09:00', '17:00');
    }
}

function setDayConfig(day, enabled, start, end) {
    const toggle = document.getElementById('toggle_' + day);
    const startInput = document.getElementById('start_' + day);
    const endInput = document.getElementById('end_' + day);
    
    if (toggle) toggle.checked = enabled;
    if (startInput) startInput.value = start;
    if (endInput) endInput.value = end;
    
    toggleDayAvailability(day, toggle);
}

function copyMondayToAll() {
    const mStart = document.getElementById('start_monday').value;
    const mEnd = document.getElementById('end_monday').value;
    
    daysList.forEach(day => {
        const toggle = document.getElementById('toggle_' + day);
        if (toggle && toggle.checked) {
            document.getElementById('start_' + day).value = mStart;
            document.getElementById('end_' + day).value = mEnd;
            recalculateDayHours(day);
        }
    });
    alert('Monday hours (' + mStart + ' to ' + mEnd + ') applied to all active days.');
}

function copySingleDayHoursToAll(sourceDay) {
    const s = document.getElementById('start_' + sourceDay).value;
    const e = document.getElementById('end_' + sourceDay).value;
    
    daysList.forEach(day => {
        const toggle = document.getElementById('toggle_' + day);
        if (toggle && toggle.checked) {
            document.getElementById('start_' + day).value = s;
            document.getElementById('end_' + day).value = e;
            recalculateDayHours(day);
        }
    });
}

// 9. Appointments Realtime Filter
function filterBookingList(query) {
    const q = (query || '').trim().toLowerCase();
    const rows = document.querySelectorAll('.item-booking-row');
    rows.forEach(r => {
        const guestData = r.getAttribute('data-guest') || '';
        if (!q || guestData.indexOf(q) !== -1) {
            r.style.display = 'flex';
        } else {
            r.style.display = 'none';
        }
    });
}

// 10. Modals Management
function openQrModal() {
    const input = document.getElementById('publicShareUrlInput');
    const url = input ? input.value : window.location.origin;
    const qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' + encodeURIComponent(url);
    
    document.getElementById('qrCodeImg').src = qrApiUrl;
    document.getElementById('qrCodeUrlText').textContent = url;
    document.getElementById('qrDownloadLink').href = qrApiUrl;
    document.getElementById('qrModal').classList.add('active');
}

function closeQrModal() {
    document.getElementById('qrModal').classList.remove('active');
}

function openEmbedModal() {
    const input = document.getElementById('publicShareUrlInput');
    const url = input ? input.value : window.location.origin;
    const snippet = `<iframe src="${url}" width="100%" height="750" frameborder="0" style="border:1px solid #e2e8f0; border-radius:16px; box-shadow:0 8px 30px rgba(0,0,0,0.1);"></iframe>`;
    document.getElementById('embedCodeSnippet').value = snippet;
    document.getElementById('embedModal').classList.add('active');
}

function closeEmbedModal() {
    document.getElementById('embedModal').classList.remove('active');
}

function copyEmbedCode() {
    const ta = document.getElementById('embedCodeSnippet');
    ta.select();
    document.execCommand('copy');
    const btn = document.getElementById('copyEmbedBtn');
    const span = btn.querySelector('span');
    if (span) span.textContent = '✓ Copied to Clipboard!';
    setTimeout(() => {
        if (span) span.textContent = 'Copy Embed Code';
    }, 2000);
}

function openCancelModal(bookingId) {
    document.getElementById('cancelBookingId').value = bookingId;
    document.getElementById('cancelModal').classList.add('active');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.remove('active');
}

// 11. Initialization & URL Parameter Sync
document.addEventListener('DOMContentLoaded', () => {
    // Recalculate hours for all days on load
    daysList.forEach(day => recalculateDayHours(day));
    updateWeeklyCapacitySummary();

    // Check URL tab parameter: e.g. ?tab=settings
    const params = new URLSearchParams(window.location.search);
    const requestedTab = params.get('tab');
    if (requestedTab) {
        switchTab(requestedTab);
    } else {
        // Default to settings if navigated directly or overview
        switchTab('settings');
    }
});
</script>
</body>
</html>
