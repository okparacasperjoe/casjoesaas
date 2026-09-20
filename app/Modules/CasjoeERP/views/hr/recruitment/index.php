<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talent Acquisition & Requisitions | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        /* ── EXECUTIVE RECRUITMENT & ATS THEME POLISH ── */
        :root {
            --rec-primary: #000066;
            --rec-primary-hover: #000044;
            --rec-accent: #FFA600;
            --rec-border: #e2e8f0;
            --rec-bg-main: #ffffff;
            --rec-bg-subtle: #f8fafc;
            --rec-text-main: #0f172a;
            --rec-text-muted: #64748b;
        }

        body, html {
            background: #ffffff !important;
            color: var(--rec-text-main);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .main-content {
            background: #ffffff !important;
            padding: 32px 40px !important;
            min-height: 100vh;
        }

        .breadcrumb-nav {
            font-size: 0.85rem;
            color: var(--rec-text-muted);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }
        .breadcrumb-nav span {
            color: var(--rec-primary);
            font-weight: 700;
        }

        /* Hero Header */
        .rec-hero {
            background: #ffffff;
            border: 1px solid var(--rec-border);
            border-radius: 20px;
            padding: 28px 32px;
            margin-bottom: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 102, 0.04);
            position: relative;
            overflow: hidden;
        }
        .rec-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #000066 0%, #3b82f6 50%, #FFA600 100%);
        }
        .rec-hero h1 {
            margin: 0;
            font-size: 2.1rem;
            color: #000066 !important;
            font-weight: 900;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .rec-hero p {
            margin: 6px 0 0;
            color: var(--rec-text-muted);
            font-size: 0.96rem;
            max-width: 680px;
            line-height: 1.5;
        }

        /* Metrics Board */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: #ffffff !important;
            border: 1px solid var(--rec-border) !important;
            border-radius: 18px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.03) !important;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 34px rgba(0, 0, 102, 0.08) !important;
            border-color: #cbd5e1 !important;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: var(--stat-accent, #000066);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
            flex-shrink: 0;
            background: var(--icon-bg, #f1f5f9);
            color: var(--icon-color, #000066);
            transition: transform 0.3s ease;
        }
        .stat-card:hover .stat-icon {
            transform: scale(1.08) rotate(4deg);
        }
        .stat-info h4 {
            margin: 0;
            font-size: 0.82rem;
            color: var(--rec-text-muted) !important;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            font-weight: 800;
        }
        .stat-info .stat-value {
            font-size: 2rem;
            font-weight: 900;
            color: #000066 !important;
            margin: 4px 0 2px;
            line-height: 1.1;
        }
        .stat-info .stat-sub {
            font-size: 0.8rem;
            color: #10b981;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Main Roster Card */
        .rec-main-card {
            background: #ffffff !important;
            border: 1px solid var(--rec-border) !important;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 8px 30px rgba(0, 0, 102, 0.04) !important;
            margin-bottom: 36px;
        }

        /* Filter & Search Bar */
        .filter-header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--rec-border);
        }
        .filter-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .filter-tab {
            background: #f1f5f9;
            border: 1px solid transparent;
            color: var(--rec-text-muted);
            padding: 9px 18px;
            border-radius: 12px;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-tab:hover {
            background: #e2e8f0;
            color: var(--rec-text-main);
        }
        .filter-tab.active {
            background: var(--rec-primary);
            color: #ffffff !important;
            box-shadow: 0 6px 16px rgba(0, 0, 102, 0.22);
        }
        .filter-badge {
            background: rgba(255, 255, 255, 0.2);
            color: inherit;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.76rem;
        }
        .filter-tab:not(.active) .filter-badge {
            background: #cbd5e1;
            color: #334155;
        }

        .search-box {
            position: relative;
            width: 320px;
            max-width: 100%;
        }
        .search-box input {
            width: 100%;
            padding: 11px 16px 11px 42px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 12px !important;
            font-size: 0.92rem !important;
            background: #ffffff !important;
            color: var(--rec-text-main) !important;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .search-box input:focus {
            border-color: var(--rec-primary) !important;
            box-shadow: 0 0 0 4px rgba(0, 0, 102, 0.1) !important;
            outline: none;
        }
        .search-box ion-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.25rem;
        }

        /* Requisition Table */
        .req-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin: 0 !important;
        }
        .req-table th {
            font-size: 0.8rem;
            font-weight: 800;
            color: #000066 !important;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 12px 16px;
            border-bottom: 2px solid var(--rec-border);
        }
        .req-table tbody tr {
            background: #ffffff;
            transition: all 0.2s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }
        .req-table tbody tr:hover {
            background: #f8fafc;
            transform: scale(1.003);
            box-shadow: 0 6px 18px rgba(0, 0, 102, 0.06);
        }
        .req-table td {
            padding: 18px 16px;
            vertical-align: middle;
            border-top: 1px solid var(--rec-border);
            border-bottom: 1px solid var(--rec-border);
        }
        .req-table td:first-child {
            border-left: 1px solid var(--rec-border);
            border-top-left-radius: 14px;
            border-bottom-left-radius: 14px;
        }
        .req-table td:last-child {
            border-right: 1px solid var(--rec-border);
            border-top-right-radius: 14px;
            border-bottom-right-radius: 14px;
        }

        /* Status Pills & Badges */
        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.4px;
        }
        .badge-open {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .badge-closed {
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }
        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            animation: pulse-ring 1.8s infinite cubic-bezier(0.66, 0, 0, 1);
        }
        @keyframes pulse-ring {
            to { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
        }

        .dept-badge {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #000066;
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 0.84rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Action Buttons */
        .btn-primary-exec {
            background: #000066 !important;
            color: #ffffff !important;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 0.94rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(0, 0, 102, 0.25);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none;
        }
        .btn-primary-exec:hover {
            background: #000044 !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(0, 0, 102, 0.35);
        }
        .btn-outline-exec {
            background: #ffffff;
            color: #000066 !important;
            border: 1.5px solid #cbd5e1;
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.86rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-outline-exec:hover {
            background: #f8fafc;
            border-color: #000066;
            transform: translateY(-1px);
        }

        /* Candidate Sandbox Card */
        .sandbox-card {
            background: #ffffff !important;
            border: 1px solid var(--rec-border) !important;
            border-top: 5px solid #000066 !important;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 10px 30px rgba(0, 0, 102, 0.04) !important;
            position: relative;
            overflow: hidden;
        }
        .sandbox-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }
        .autofill-btn {
            background: #f1f5f9;
            color: #000066;
            border: 1px solid #cbd5e1;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
        }
        .autofill-btn:hover {
            background: #000066;
            color: #ffffff;
            border-color: #000066;
        }

        /* Modal Polish */
        dialog {
            border: 1px solid var(--rec-border);
            border-radius: 22px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
            padding: 32px;
            background: #ffffff;
        }
        dialog::backdrop {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(5px);
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <!-- Breadcrumb -->
        <div class="breadcrumb-nav">
            <ion-icon name="apps-outline"></ion-icon> Casjoe ERP / Talent Acquisition / <span>Job Requisitions</span>
        </div>

        <!-- Top Hero Bar -->
        <div class="rec-hero">
            <div>
                <span style="display:inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.76rem; font-weight: 800; background: rgba(0,0,102,0.08); color: #000066; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 8px;">
                    ⚡ Enterprise ATS Engine
                </span>
                <h1>
                    <ion-icon name="people-circle" style="color: #000066; font-size: 2.3rem;"></ion-icon>
                    Talent Acquisition & Requisition Hub
                </h1>
                <p>
                    Manage active job openings, track candidate velocity across global departments, and accelerate your structured interview pipeline.
                </p>
            </div>
            <div>
                <button onclick="document.getElementById('newJobModal').showModal()" class="btn-primary-exec">
                    <ion-icon name="add-circle-outline" style="font-size: 1.4rem;"></ion-icon> Post New Requisition
                </button>
            </div>
        </div>

        <?php
        // Safe Metrics Calculation
        $activeCount = 0;
        $closedCount = 0;
        $deptList = [];
        foreach ($jobs as $j) {
            $st = strtolower($j['status'] ?? 'open');
            if ($st === 'open' || $st === 'active') {
                $activeCount++;
            } else {
                $closedCount++;
            }
            if (!empty($j['department'])) {
                $deptList[$j['department']] = true;
            }
        }
        $deptCount = count($deptList);
        ?>

        <!-- 4-Column Executive Metrics Board -->
        <div class="stat-grid">
            <div class="stat-card" style="--stat-accent: #000066;">
                <div class="stat-icon" style="--icon-bg: rgba(0, 0, 102, 0.08); --icon-color: #000066;">
                    <ion-icon name="briefcase-outline"></ion-icon>
                </div>
                <div class="stat-info">
                    <h4>Active Postings</h4>
                    <div class="stat-value"><?= $activeCount ?></div>
                    <div class="stat-sub"><ion-icon name="trending-up-outline"></ion-icon> Open requisitions</div>
                </div>
            </div>
            <div class="stat-card" style="--stat-accent: #FFA600;">
                <div class="stat-icon" style="--icon-bg: rgba(255, 166, 0, 0.14); --icon-color: #d84315;">
                    <ion-icon name="business-outline"></ion-icon>
                </div>
                <div class="stat-info">
                    <h4>Hiring Departments</h4>
                    <div class="stat-value"><?= $deptCount ?></div>
                    <div class="stat-sub" style="color:#64748b;"><ion-icon name="checkmark-circle-outline"></ion-icon> Active business units</div>
                </div>
            </div>
            <div class="stat-card" style="--stat-accent: #10b981;">
                <div class="stat-icon" style="--icon-bg: rgba(16, 185, 129, 0.12); --icon-color: #059669;">
                    <ion-icon name="people-outline"></ion-icon>
                </div>
                <div class="stat-info">
                    <h4>Total Requisitions</h4>
                    <div class="stat-value"><?= count($jobs) ?></div>
                    <div class="stat-sub"><ion-icon name="flash-outline"></ion-icon> All-time postings</div>
                </div>
            </div>
            <div class="stat-card" style="--stat-accent: #6366f1;">
                <div class="stat-icon" style="--icon-bg: rgba(99, 102, 241, 0.12); --icon-color: #4f46e5;">
                    <ion-icon name="timer-outline"></ion-icon>
                </div>
                <div class="stat-info">
                    <h4>Time to Fill</h4>
                    <div class="stat-value" style="font-size:1.6rem;">18 <span style="font-size:0.9rem; font-weight:700; color:#64748b;">Days Avg</span></div>
                    <div class="stat-sub"><ion-icon name="checkmark-done-circle-outline"></ion-icon> Target: &lt; 21 days</div>
                </div>
            </div>
        </div>

        <!-- Main Requisition Roster Card -->
        <div class="rec-main-card">
            <!-- Filter Bar & Search -->
            <div class="filter-header-bar">
                <div class="filter-tabs">
                    <button class="filter-tab active" onclick="setJobFilter('all', this)">
                        <span>All Requisitions</span>
                        <span class="filter-badge"><?= count($jobs) ?></span>
                    </button>
                    <button class="filter-tab" onclick="setJobFilter('open', this)">
                        <span>Active / Open</span>
                        <span class="filter-badge"><?= $activeCount ?></span>
                    </button>
                    <button class="filter-tab" onclick="setJobFilter('closed', this)">
                        <span>Closed / Filled</span>
                        <span class="filter-badge"><?= $closedCount ?></span>
                    </button>
                </div>
                <div class="search-box">
                    <ion-icon name="search-outline"></ion-icon>
                    <input type="text" id="jobSearchInput" placeholder="🔍 Search position, department, or scope..." onkeyup="filterJobTable()">
                </div>
            </div>
            
            <div style="overflow-x: auto;">
                <table class="req-table" id="jobsTable">
                    <thead>
                        <tr>
                            <th style="width: 36%;">Position Title & Scope Overview</th>
                            <th style="width: 20%;">Department & Unit</th>
                            <th style="width: 15%;">Requisition Status</th>
                            <th style="width: 14%;">Published Date</th>
                            <th style="width: 15%; text-align: right;">Candidate Pipeline & Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($jobs)): ?>
                        <tr id="emptyRow">
                            <td colspan="5" style="text-align: center; padding: 60px 20px; color: #64748b;">
                                <div style="width: 80px; height: 80px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                    <ion-icon name="briefcase-outline" style="font-size: 3rem; color: #94a3b8;"></ion-icon>
                                </div>
                                <div style="font-size: 1.25rem; font-weight: 800; color: #000066;">No Job Requisitions Found</div>
                                <p style="margin: 8px 0 22px; font-size: 0.94rem; max-width: 480px; margin-left: auto; margin-right: auto;">Launch your talent acquisition pipeline right now by publishing your first open position.</p>
                                <button onclick="document.getElementById('newJobModal').showModal()" class="btn-primary-exec">
                                    <ion-icon name="add-circle-outline" style="font-size: 1.3rem;"></ion-icon> Post Requisition Now
                                </button>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($jobs as $job): ?>
                            <?php 
                            $rawStatus = strtolower($job['status'] ?? 'open');
                            $isOpen = ($rawStatus === 'open' || $rawStatus === 'active');
                            ?>
                            <tr class="job-row" data-status="<?= $isOpen ? 'open' : 'closed' ?>">
                                <td>
                                    <div style="font-weight: 800; color: #000066; font-size: 1.12rem; display: flex; align-items: center; gap: 8px;">
                                        <?= htmlspecialchars($job['title'] ?? '') ?>
                                    </div>
                                    <?php if (!empty($job['description'])): ?>
                                        <div style="color: #64748b; font-size: 0.88rem; margin-top: 5px; max-width: 440px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.4;">
                                            <?= htmlspecialchars($job['description']) ?>
                                        </div>
                                    <?php else: ?>
                                        <div style="color: #94a3b8; font-size: 0.84rem; margin-top: 4px; font-style: italic;">Standard executive requisition profile</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="dept-badge">
                                        <ion-icon name="folder-open-outline" style="color: #000066; font-size: 1.1rem;"></ion-icon>
                                        <?= htmlspecialchars($job['department'] ?? 'General') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($isOpen): ?>
                                        <span class="badge-pill badge-open">
                                            <span class="pulse-dot"></span> Active & Open
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-pill badge-closed">
                                            <ion-icon name="lock-closed-outline"></ion-icon> Closed
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="color: #475569; font-size: 0.92rem; font-weight: 600;">
                                    <div style="display:flex; align-items:center; gap:6px;">
                                        <ion-icon name="calendar-outline" style="color:#94a3b8;"></ion-icon>
                                        <?= !empty($job['created_at']) ? date('M j, Y', strtotime($job['created_at'])) : 'Recent' ?>
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; justify-content: flex-end; gap: 10px; align-items: center;">
                                        <button onclick="copyPublicLink(<?= $job['id'] ?>)" class="btn-outline-exec" title="Copy Public Link" style="background: #f8fafc; border-color: #cbd5e1; color: #475569;">
                                            <ion-icon name="link-outline" style="font-size: 1.15rem; color: #000066;"></ion-icon> Copy Link
                                        </button>
                                        <a href="/erp/recruitment/applications?job_id=<?= $job['id'] ?>" class="btn-outline-exec" title="Review Candidates in Pipeline">
                                            <ion-icon name="people" style="font-size: 1.15rem; color: #000066;"></ion-icon> Review Pipeline →
                                        </a>
                                        <form action="/erp/recruitment/delete" method="POST" style="margin: 0; padding: 0;" onsubmit="return confirm('Delete job posting?');">
                                            <input type="hidden" name="id" value="<?= $job['id'] ?>">
                                            <button type="submit" class="btn-outline-exec" style="color: #ef4444 !important; border-color: #ef4444; padding: 8px 12px;" title="Delete">
                                                <ion-icon name="trash" style="font-size: 1.15rem;"></ion-icon>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Candidate Intake & Pipeline Sandbox Card -->
        <div class="sandbox-card">
            <div class="sandbox-header">
                <div>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <h3 style="margin:0; font-size: 1.35rem; color: #000066; font-weight: 900;">Simulate Candidate Application Intake</h3>
                        <span style="background: rgba(0,0,102,0.1); color: #000066; font-size: 0.75rem; font-weight: 800; padding: 4px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;">⚡ ATS Intake Sandbox</span>
                    </div>
                    <p style="margin:6px 0 0; color: #64748b; font-size: 0.94rem;">
                        Manually submit candidate profiles to verify automated ATS intake triggers, AI screening workflows, and interview stages.
                    </p>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="button" class="autofill-btn" onclick="autofillSampleCandidate('Alexander Wright', 'alexander.wright@techlead.io')">
                        ⚡ Sample Engineer
                    </button>
                    <button type="button" class="autofill-btn" onclick="autofillSampleCandidate('Sophia Chen', 'sophia.c@executive-suite.org')">
                        ⚡ Sample VP
                    </button>
                </div>
            </div>
             
            <form action="/erp/recruitment/apply" method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; align-items: flex-end;">
                <div>
                    <label style="display:block; font-size:0.86rem; font-weight:800; color:#000066; margin-bottom:8px;">Target Job Requisition *</label>
                    <select name="job_id" id="intakeJobSelect" class="form-control" required style="width:100%; padding: 12px 16px; border-radius: 10px; border: 1px solid #cbd5e1; font-weight: 600;">
                        <?php if(empty($jobs)): ?>
                           <option value="">No active jobs available</option>
                        <?php else: ?>
                            <?php foreach ($jobs as $job): ?>
                               <option value="<?= $job['id'] ?>"><?= htmlspecialchars($job['title']) ?> — <?= htmlspecialchars($job['department']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:0.86rem; font-weight:800; color:#000066; margin-bottom:8px;">Candidate Full Name *</label>
                    <input type="text" name="candidate_name" id="intakeCandidateName" placeholder="e.g. Alexander Wright" class="form-control" required style="width:100%; padding: 12px 16px; border-radius: 10px; border: 1px solid #cbd5e1; font-weight: 600;">
                </div>
                <div>
                    <label style="display:block; font-size:0.86rem; font-weight:800; color:#000066; margin-bottom:8px;">Candidate Email Address *</label>
                    <input type="email" name="email" id="intakeCandidateEmail" placeholder="e.g. alexander.wright@example.com" class="form-control" required style="width:100%; padding: 12px 16px; border-radius: 10px; border: 1px solid #cbd5e1; font-weight: 600;">
                </div>
                <div>
                    <button type="submit" class="btn-primary-exec" style="width:100%; justify-content:center; padding: 13px 20px; font-size: 0.95rem;">
                        <ion-icon name="send-outline" style="font-size: 1.15rem;"></ion-icon> Submit Profile to ATS
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Post New Job Requisition Dialog -->
    <dialog id="newJobModal" style="width: 600px; max-width: 94vw;">
        <form action="/erp/recruitment/store" method="POST">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; border-bottom:1px solid var(--rec-border); padding-bottom:16px;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(0,0,102,0.08); display:flex; align-items:center; justify-content:center; color:#000066;">
                        <ion-icon name="briefcase" style="font-size:1.5rem;"></ion-icon>
                    </div>
                    <div>
                        <h3 style="margin:0; font-size: 1.35rem; color:#000066; font-weight: 900;">Create Job Requisition</h3>
                        <p style="margin:2px 0 0; font-size:0.84rem; color:#64748b;">Publish a new role to your talent portal & career page</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('newJobModal').close()" style="background:none; border:none; font-size:1.8rem; cursor:pointer; color:#94a3b8; transition:color 0.2s;" onmouseover="this.style.color='#0f172a'" onmouseout="this.style.color='#94a3b8'">
                    <ion-icon name="close-outline"></ion-icon>
                </button>
            </div>
            
            <div style="margin-bottom:20px;">
                <label style="display:block; font-weight:800; font-size:0.9rem; color:#000066; margin-bottom:6px;">Position Title *</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Senior Full-Stack Cloud Architect" required style="width:100%; padding: 12px 16px; border-radius: 10px; border: 1px solid #cbd5e1; font-weight: 600;">
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block; font-weight:800; font-size:0.9rem; color:#000066; margin-bottom:6px;">Department & Unit *</label>
                <input type="text" name="department" class="form-control" placeholder="e.g. Engineering & Platform Architecture" required style="width:100%; padding: 12px 16px; border-radius: 10px; border: 1px solid #cbd5e1; font-weight: 600;">
            </div>
            <div style="margin-bottom:28px;">
                <label style="display:block; font-weight:800; font-size:0.9rem; color:#000066; margin-bottom:6px;">Job Description & Candidate Requirements</label>
                <textarea name="description" class="form-control" rows="5" placeholder="Detail the primary responsibilities, must-have technical qualifications, compensation structure, and team culture..." style="width:100%; padding: 14px 16px; border-radius: 10px; border: 1px solid #cbd5e1; line-height: 1.5; font-size: 0.92rem;"></textarea>
            </div>

            <div style="margin-bottom:28px;">
                <label style="display:flex; justify-content:space-between; align-items:center; font-weight:800; font-size:0.9rem; color:#000066; margin-bottom:6px;">
                    Custom Questions
                    <button type="button" onclick="addCustomQuestion()" style="background:none; border:none; color:#10b981; font-weight:bold; cursor:pointer; font-size:0.85rem; display:flex; align-items:center; gap:4px;">
                        <ion-icon name="add-circle"></ion-icon> Add Question
                    </button>
                </label>
                <div id="customQuestionsContainer" style="display:flex; flex-direction:column; gap:10px;">
                    <!-- Dynamically added questions go here -->
                </div>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:12px; border-top:1px solid var(--rec-border); padding-top:20px;">
                <button type="button" onclick="document.getElementById('newJobModal').close()" class="btn-outline-exec" style="padding: 11px 20px;">Cancel</button>
                <button type="submit" class="btn-primary-exec" style="padding: 11px 26px;">
                    <ion-icon name="checkmark-circle-outline" style="font-size: 1.2rem;"></ion-icon> Publish Requisition
                </button>
            </div>
        </form>
    </dialog>
</div>

<script>
let activeStatusFilter = 'all';

function setJobFilter(status, btnElement) {
    activeStatusFilter = status;
    document.querySelectorAll('.filter-tab').forEach(btn => btn.classList.remove('active'));
    btnElement.classList.add('active');
    filterJobTable();
}

function filterJobTable() {
    const searchFilter = document.getElementById('jobSearchInput').value.toLowerCase();
    const rows = document.querySelectorAll('.job-row');

    rows.forEach(row => {
        const rowText = row.textContent.toLowerCase();
        const rowStatus = row.getAttribute('data-status');
        
        const matchesSearch = rowText.includes(searchFilter);
        const matchesStatus = (activeStatusFilter === 'all') || (rowStatus === activeStatusFilter);

        if (matchesSearch && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function autofillSampleCandidate(name, email) {
    document.getElementById('intakeCandidateName').value = name;
    document.getElementById('intakeCandidateEmail').value = email;
    const select = document.getElementById('intakeJobSelect');
    if (select && select.options.length > 0 && select.value === "") {
        select.selectedIndex = 0;
    }
}

function copyPublicLink(jobId) {
    const url = window.location.origin + '/careers/job?id=' + jobId;
    navigator.clipboard.writeText(url).then(() => {
        alert("Public job link copied to clipboard:\n" + url);
    }).catch(err => {
        console.error('Could not copy text: ', err);
        prompt("Copy this link:", url);
    });
}

function addCustomQuestion() {
    const container = document.getElementById('customQuestionsContainer');
    const questionDiv = document.createElement('div');
    questionDiv.style.display = 'flex';
    questionDiv.style.gap = '10px';
    questionDiv.style.alignItems = 'center';

    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'questions[]';
    input.className = 'form-control';
    input.placeholder = 'e.g. How many years of experience do you have?';
    input.style.width = '100%';
    input.style.padding = '10px 14px';
    input.style.borderRadius = '8px';
    input.style.border = '1px solid #cbd5e1';

    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.innerHTML = '<ion-icon name="trash"></ion-icon>';
    removeBtn.style.background = 'none';
    removeBtn.style.border = 'none';
    removeBtn.style.color = '#ef4444';
    removeBtn.style.cursor = 'pointer';
    removeBtn.style.fontSize = '1.2rem';
    removeBtn.onclick = function() {
        container.removeChild(questionDiv);
    };

    questionDiv.appendChild(input);
    questionDiv.appendChild(removeBtn);
    container.appendChild(questionDiv);
}
</script>
</body>
</html>
