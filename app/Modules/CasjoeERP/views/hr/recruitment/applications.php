<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Pipeline & Applications | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        /* ── EXECUTIVE APPLICATIONS PIPELINE THEME ── */
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
            padding: 26px 32px;
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

        /* Main Card */
        .rec-main-card {
            background: #ffffff !important;
            border: 1px solid var(--rec-border) !important;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 8px 30px rgba(0, 0, 102, 0.04) !important;
        }

        /* Candidate Table */
        .apps-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin: 0 !important;
        }
        .apps-table th {
            font-size: 0.8rem;
            font-weight: 800;
            color: #000066 !important;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 12px 16px;
            border-bottom: 2px solid var(--rec-border);
        }
        .apps-table tbody tr {
            background: #ffffff;
            transition: all 0.2s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }
        .apps-table tbody tr:hover {
            background: #f8fafc;
            transform: scale(1.003);
            box-shadow: 0 6px 18px rgba(0, 0, 102, 0.06);
        }
        .apps-table td {
            padding: 18px 16px;
            vertical-align: middle;
            border-top: 1px solid var(--rec-border);
            border-bottom: 1px solid var(--rec-border);
        }
        .apps-table td:first-child {
            border-left: 1px solid var(--rec-border);
            border-top-left-radius: 14px;
            border-bottom-left-radius: 14px;
        }
        .apps-table td:last-child {
            border-right: 1px solid var(--rec-border);
            border-top-right-radius: 14px;
            border-bottom-right-radius: 14px;
        }

        /* Avatar & Candidate Details */
        .candidate-card-cell {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .candidate-avatar {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(0,0,102,0.08);
            color: #000066;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 1.15rem;
            flex-shrink: 0;
            letter-spacing: 0.5px;
            border: 1px solid rgba(0,0,102,0.12);
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
        .badge-received { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
        .badge-shortlisted { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
        .badge-interviewing { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
        .badge-hired { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-rejected { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
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

        /* Action Buttons */
        .btn-primary-exec {
            background: #000066 !important;
            color: #ffffff !important;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(0, 0, 102, 0.22);
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-primary-exec:hover {
            background: #000044 !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(0, 0, 102, 0.32);
        }
        .btn-outline-exec {
            background: #ffffff;
            color: #000066 !important;
            border: 1.5px solid #cbd5e1;
            padding: 9px 18px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.88rem;
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
        .btn-action-chip {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #334155;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }
        .btn-action-chip:hover {
            background: #000066;
            color: #ffffff;
            border-color: #000066;
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <!-- Breadcrumb -->
        <div class="breadcrumb-nav">
            <ion-icon name="apps-outline"></ion-icon> Casjoe ERP / Talent Acquisition / <span>Candidate Pipeline</span>
        </div>

        <!-- Hero Header -->
        <div class="rec-hero">
            <div>
                <span style="display:inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.76rem; font-weight: 800; background: rgba(0,0,102,0.08); color: #000066; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 8px;">
                    🎯 Applicant Tracking Roster
                </span>
                <h1>
                    <ion-icon name="people" style="color: #000066; font-size: 2.3rem;"></ion-icon>
                    Candidate Pipeline & Applications
                </h1>
                <p>
                    Review applicant submissions, evaluate executive talent profiles, and advance candidates through structured interview stages.
                </p>
            </div>
            <div>
                <a href="/erp/recruitment" class="btn-outline-exec">
                    <ion-icon name="arrow-back-outline" style="font-size: 1.15rem;"></ion-icon> Back to Job Requisitions
                </a>
            </div>
        </div>

        <?php
        // Count statuses
        $counts = ['received' => 0, 'shortlisted' => 0, 'interviewing' => 0, 'hired' => 0, 'rejected' => 0];
        foreach ($applications as $a) {
            $st = strtolower($a['status'] ?? 'received');
            if (isset($counts[$st])) {
                $counts[$st]++;
            } else {
                $counts['received']++;
            }
        }
        ?>

        <div class="rec-main-card">
            <!-- Filter & Search Bar -->
            <div class="filter-header-bar">
                <div class="filter-tabs">
                    <button class="filter-tab active" onclick="setAppFilter('all', this)">
                        <span>All Candidates</span>
                        <span class="filter-badge"><?= count($applications) ?></span>
                    </button>
                    <button class="filter-tab" onclick="setAppFilter('received', this)">
                        <span>New / Received</span>
                        <span class="filter-badge"><?= $counts['received'] ?></span>
                    </button>
                    <button class="filter-tab" onclick="setAppFilter('shortlisted', this)">
                        <span>Shortlisted</span>
                        <span class="filter-badge"><?= $counts['shortlisted'] ?></span>
                    </button>
                    <button class="filter-tab" onclick="setAppFilter('interviewing', this)">
                        <span>Interviewing</span>
                        <span class="filter-badge"><?= $counts['interviewing'] ?></span>
                    </button>
                    <button class="filter-tab" onclick="setAppFilter('hired', this)">
                        <span>Hired</span>
                        <span class="filter-badge"><?= $counts['hired'] ?></span>
                    </button>
                </div>
                <div class="search-box">
                    <ion-icon name="search-outline"></ion-icon>
                    <input type="text" id="appSearchInput" placeholder="🔍 Search candidate name or email..." onkeyup="filterAppTable()">
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="apps-table" id="appsTable">
                    <thead>
                        <tr>
                            <th style="width: 32%;">Candidate Profile & Contact</th>
                            <th style="width: 26%;">Applied Requisition</th>
                            <th style="width: 16%;">Pipeline Stage</th>
                            <th style="width: 12%;">Date Received</th>
                            <th style="width: 14%; text-align: right;">Structured Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($applications)): ?>
                        <tr id="emptyRow">
                            <td colspan="5" style="text-align: center; padding: 60px 20px; color: #64748b;">
                                <div style="width: 80px; height: 80px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                    <ion-icon name="people-outline" style="font-size: 3rem; color: #94a3b8;"></ion-icon>
                                </div>
                                <div style="font-size: 1.25rem; font-weight: 800; color: #000066;">No Candidates in Pipeline</div>
                                <p style="margin: 8px 0 0; font-size: 0.94rem; max-width: 480px; margin-left: auto; margin-right: auto;">Applications submitted for this requisition will appear here instantly via our ATS intake triggers.</p>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($applications as $app): ?>
                            <?php 
                            $status = strtolower($app['status'] ?? 'received');
                            $badgeClass = 'badge-received';
                            if ($status === 'shortlisted') $badgeClass = 'badge-shortlisted';
                            elseif ($status === 'interviewing') $badgeClass = 'badge-interviewing';
                            elseif ($status === 'hired') $badgeClass = 'badge-hired';
                            elseif ($status === 'rejected') $badgeClass = 'badge-rejected';
                            
                            // Generate initials
                            $nameParts = explode(' ', trim($app['candidate_name'] ?? 'C'));
                            $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
                            ?>
                            <tr class="app-row" data-status="<?= $status ?>">
                                <td>
                                    <div class="candidate-card-cell">
                                        <div class="candidate-avatar"><?= htmlspecialchars($initials) ?></div>
                                        <div>
                                            <div style="font-weight: 800; color: #000066; font-size: 1.1rem;"><?= htmlspecialchars($app['candidate_name'] ?? '') ?></div>
                                            <div style="color: #64748b; font-size: 0.86rem; margin-top: 3px; display: flex; align-items: center; gap: 6px;">
                                                <ion-icon name="mail-outline" style="color: #94a3b8;"></ion-icon> 
                                                <a href="mailto:<?= htmlspecialchars($app['email'] ?? '') ?>" style="color: inherit; text-decoration: none;"><?= htmlspecialchars($app['email'] ?? '') ?></a>
                                            </div>
                                            <?php 
                                            $answers = !empty($app['custom_answers']) ? json_decode($app['custom_answers'], true) : [];
                                            if (!empty($answers) && is_array($answers)): 
                                            ?>
                                            <div style="margin-top: 10px; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid var(--rec-border);">
                                                <div style="font-size: 0.82rem; font-weight: 800; color: #000066; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Custom Answers</div>
                                                <?php foreach ($answers as $ans): ?>
                                                    <div style="margin-bottom: 8px;">
                                                        <div style="font-size: 0.84rem; font-weight: 700; color: #475569; margin-bottom: 2px;">Q: <?= htmlspecialchars($ans['question']) ?></div>
                                                        <div style="font-size: 0.9rem; color: #0f172a; line-height: 1.4;">A: <?= nl2br(htmlspecialchars($ans['answer'])) ?></div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 800; color: #0f172a; font-size: 1rem;"><?= htmlspecialchars($app['title'] ?? 'General Application') ?></div>
                                    <div style="color: #64748b; font-size: 0.82rem; margin-top: 3px; font-weight: 600;">🏷️ Direct ATS Submission</div>
                                </td>
                                <td>
                                    <span class="badge-pill <?= $badgeClass ?>">
                                        <?php if ($status === 'interviewing' || $status === 'hired'): ?>
                                            <span class="pulse-dot" style="width: 6px; height: 6px;"></span>
                                        <?php endif; ?>
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                                <td style="color: #475569; font-size: 0.92rem; font-weight: 600;">
                                    <div style="display:flex; align-items:center; gap:6px;">
                                        <ion-icon name="calendar-outline" style="color:#94a3b8;"></ion-icon>
                                        <?= !empty($app['created_at']) ? date('M j, Y', strtotime($app['created_at'])) : 'Recent' ?>
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; justify-content: flex-end; gap: 8px; align-items: center;">
                                        <button onclick="advanceCandidate('<?= htmlspecialchars(addslashes($app['candidate_name'] ?? '')) ?>')" class="btn-action-chip" title="Advance Stage">
                                            ⚡ Advance
                                        </button>
                                        <button onclick="scheduleInterview('<?= htmlspecialchars(addslashes($app['candidate_name'] ?? '')) ?>')" class="btn-primary-exec" title="Schedule Structured Interview">
                                            <ion-icon name="calendar-outline" style="font-size: 1.1rem;"></ion-icon> Schedule
                                        </button>
                                        <form action="/erp/recruitment/applications/delete" method="POST" style="margin: 0; padding: 0;" onsubmit="return confirm('Delete application?');">
                                            <input type="hidden" name="id" value="<?= $app['id'] ?>">
                                            <button type="submit" class="btn-outline-exec" style="color: #ef4444 !important; border-color: #ef4444; padding: 6px 10px;" title="Delete">
                                                <ion-icon name="trash" style="font-size: 1.1rem;"></ion-icon>
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
    </main>
</div>

<script>
let activeStatusFilter = 'all';

function setAppFilter(status, btnElement) {
    activeStatusFilter = status;
    document.querySelectorAll('.filter-tab').forEach(btn => btn.classList.remove('active'));
    btnElement.classList.add('active');
    filterAppTable();
}

function filterAppTable() {
    const searchFilter = document.getElementById('appSearchInput').value.toLowerCase();
    const rows = document.querySelectorAll('.app-row');

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

function advanceCandidate(name) {
    alert(`⚡ Stage Progression: Candidate "${name}" has been marked for Shortlisting & Structured Review.`);
}

function scheduleInterview(name) {
    alert(`📅 Interview Calendar: Launching booking scheduler and invitation link generator for "${name}".`);
}
</script>
</body>
</html>
