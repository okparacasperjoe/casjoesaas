<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Automations | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        @media (max-width: 768px) {
            .app-container { flex-direction: column; }
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                height: 100%;
                z-index: 1000;
                transition: left 0.3s ease;
                width: 260px !important;
                background-color: #000066; 
            }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 70px !important; }
        }

        /* Automation Timeline Styles */
        .automation-board {
            max-width: 900px;
            margin: 0 auto;
        }
        .board-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .board-description {
            background: #f8fafc;
            border-left: 4px solid #000066;
            padding: 15px 20px;
            border-radius: 4px;
            color: #475569;
            margin-bottom: 30px;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .automation-timeline {
            position: relative;
            padding-left: 30px;
            margin-top: 20px;
        }
        .automation-timeline::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 9px;
            height: calc(100% - 40px);
            width: 3px;
            background: #e2e8f0;
            border-radius: 3px;
        }
        .trigger-step {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: -20px;
            margin-bottom: 30px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }
        .trigger-icon {
            background: #e2e8f0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            font-size: 1.2rem;
            z-index: 2;
        }
        
        .automation-step {
            position: relative;
            margin-bottom: 30px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .automation-step:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }
        .automation-step::before {
            content: '';
            position: absolute;
            top: 30px;
            left: -28px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #000066;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #e2e8f0;
            z-index: 2;
        }
        .step-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        .step-title-area {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .step-title {
            font-size: 1.15rem;
            font-weight: 600;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .step-condition {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 4px 10px;
            border-radius: 20px;
            background: #f1f5f9;
            color: #475569;
        }
        .condition-warning { background: #fffbeb; color: #b45309; }
        .condition-danger { background: #fef2f2; color: #b91c1c; }
        .condition-info { background: #eff6ff; color: #1d4ed8; }
        
        .step-action {
            background: #fafaf9;
            border: 1px dashed #d6d3d1;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        .action-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #78716c;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .action-subject {
            font-size: 0.95rem;
            color: #292524;
            font-weight: 500;
        }
        .step-controls {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .btn-icon {
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        .btn-icon:hover { background: #f8fafc; color: #0f172a; }
        .btn-icon.danger:hover { background: #fef2f2; color: #ef4444; border-color: #fecaca; }
        
        .status-toggle {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        .status-active .status-dot { background: #22c55e; box-shadow: 0 0 0 2px #dcfce7; }
        .status-inactive .status-dot { background: #94a3b8; box-shadow: 0 0 0 2px #f1f5f9; }
        .status-active { color: #166534; }
        .status-inactive { color: #475569; }
    </style>
</head>
<body>
<?php require __DIR__ . '/../layout/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

        <div class="automation-board">
            <div class="board-header">
                <h1>Invoice Automations</h1>
                <a href="/erp/finance/reminders/create" class="btn btn-primary" style="display: flex; align-items: center; gap: 8px;">
                    <ion-icon name="add-outline"></ion-icon> Add Workflow Step
                </a>
            </div>

            <div class="board-description">
                <ion-icon name="information-circle-outline" style="font-size: 1.2rem; vertical-align: middle; margin-right: 5px;"></ion-icon>
                <strong>How this works:</strong> Automations evaluate your active Unpaid invoices daily. When an invoice meets the timing conditions defined below, the system automatically dispatches the configured email. Re-arrange your sequence logic here using positive days (overdue) or negative days (before due).
            </div>
            
            <div class="trigger-step">
                <div class="trigger-icon"><ion-icon name="flash"></ion-icon></div>
                Invoice becomes Active (Unpaid)
            </div>

            <div class="automation-timeline">
                <?php if (empty($reminders)): ?>
                    <div style="text-align:center; padding: 40px; background: white; border-radius: 12px; border: 1px dashed #cbd5e1; color: #64748b;">
                        <ion-icon name="git-network-outline" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 10px;"></ion-icon>
                        <p>No automation logic defined yet. Click "Add Workflow Step" to begin.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($reminders as $rem): ?>
                        <div class="automation-step <?= !$rem['is_active'] ? 'inactive' : '' ?>" style="<?= !$rem['is_active'] ? 'opacity: 0.6;' : '' ?>">
                            <div class="step-header">
                                <div class="step-title-area">
                                    <div class="step-title">
                                        <?= htmlspecialchars($rem['name']) ?>
                                        
                                        <div class="status-toggle <?= $rem['is_active'] ? 'status-active' : 'status-inactive' ?>">
                                            <div class="status-dot"></div>
                                            <?= $rem['is_active'] ? 'Running' : 'Paused' ?>
                                        </div>
                                    </div>
                                    
                                    <div style="display: flex; gap: 10px; margin-top: 8px;">
                                        <?php 
                                            // Determine wait timing text and color
                                            if ($rem['days_offset'] == 0) {
                                                $timingClass = 'condition-warning';
                                                $timingText = 'Exactly on Due Date';
                                            } elseif ($rem['days_offset'] > 0) {
                                                $timingClass = 'condition-danger';
                                                $timingText = 'Wait ' . $rem['days_offset'] . ' Days after Due Date';
                                            } else {
                                                $timingClass = 'condition-info';
                                                $timingText = abs($rem['days_offset']) . ' Days strictly before Due Date';
                                            }

                                            // Determine frequency text
                                            $freqText = "Sends Once";
                                            if(isset($rem['frequency_days']) && $rem['frequency_days'] > 0) {
                                                if($rem['frequency_days'] == 1) $freqText = "Repeats Every Day";
                                                else if($rem['frequency_days'] == 7) $freqText = "Repeats Weekly";
                                                else if($rem['frequency_days'] == 30) $freqText = "Repeats Monthly";
                                                else $freqText = "Repeats Every " . $rem['frequency_days'] . " Days";
                                            }
                                        ?>
                                        <div class="step-condition <?= $timingClass ?>">
                                            <ion-icon name="time-outline"></ion-icon> <?= $timingText ?>
                                        </div>
                                        <div class="step-condition">
                                            <ion-icon name="repeat-outline"></ion-icon> <?= $freqText ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="step-controls">
                                    <a href="/erp/finance/reminders/edit?id=<?= $rem['id'] ?>" class="btn-icon" title="Configure Logic">
                                        <ion-icon name="settings-outline"></ion-icon>
                                    </a>
                                    <form action="/erp/finance/reminders/delete" method="POST" style="display:inline;" onsubmit="return confirm('Delete this automation step permanently?');">
                                        <input type="hidden" name="id" value="<?= $rem['id'] ?>">
                                        <button type="submit" class="btn-icon danger" title="Delete Step">
                                            <ion-icon name="trash-outline"></ion-icon>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="step-action">
                                <div class="action-label">
                                    <ion-icon name="mail-outline"></ion-icon> Auto-Dispatch Email
                                </div>
                                <div class="action-subject">
                                    <strong>Subject:</strong> <?= htmlspecialchars($rem['subject']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <div class="trigger-step" style="margin-top: 10px; margin-bottom: 0;">
                    <div class="trigger-icon" style="background: #d4edda; color: #155724;"><ion-icon name="checkmark-done-circle"></ion-icon></div>
                    Sequence stops when invoice is Paid
                </div>
            </div>

        </div>

    </main>
</div>
</body>
</html>
