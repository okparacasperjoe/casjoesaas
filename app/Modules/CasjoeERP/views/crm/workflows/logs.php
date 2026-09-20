<?php
// app/Modules/CasjoeERP/Views/crm/workflows/logs.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Execution Logs - Workflow Automations</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-failed { background: #f8d7da; color: #721c24; }
        .badge-skipped { background: #fff3cd; color: #856404; }
        .filter-bar { background: var(--card-bg); padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid var(--border-color); display: flex; gap: 15px; align-items: center; }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/../../../../Core/Views/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h1>Execution Logs</h1>
                <a href="/erp/crm/workflows" class="btn btn-secondary">Back to Workflows</a>
            </header>

            <div class="filter-bar">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="filter_workflow" style="margin-right: 10px; font-weight: bold;">Filter by Workflow:</label>
                    <select id="filter_workflow" class="form-control" style="width: 250px; display: inline-block;">
                        <option value="">All Workflows</option>
                        <option value="1">Lead Nurture Sequence</option>
                        <option value="2">Hot Lead WhatsApp Alert</option>
                    </select>
                </div>
                <button class="btn btn-primary">Filter</button>
            </div>

            <div style="background: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); padding: 15px; overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="text-align: left; padding: 10px; border-bottom: 2px solid var(--border-color);">Timestamp</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 2px solid var(--border-color);">Workflow Name</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 2px solid var(--border-color);">Trigger Event</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 2px solid var(--border-color);">Lead / Contact</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 2px solid var(--border-color);">Action Taken</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 2px solid var(--border-color);">Status</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 2px solid var(--border-color);">Details / Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">2026-09-08 16:45:00</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">Hot Lead WhatsApp Alert</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">Score Reached 65</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">John Doe</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">Send WhatsApp</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><span class="badge badge-success">Success</span></td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">Message sent successfully.</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">2026-09-08 16:40:12</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">Auto-Nurture New Lead</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">Lead Created</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">Jane Smith</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">Send Intro Email</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><span class="badge badge-failed">Failed</span></td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">Invalid email address format.</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">2026-09-08 16:35:22</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">Fast-Track Qualified Deals</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">Score Reached 85</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">Acme Corp</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">Move Stage</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><span class="badge badge-skipped">Skipped</span></td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">Already in 'Qualified' stage.</td>
                        </tr>
                        <?php if (isset($logs) && is_array($logs)): ?>
                            <?php foreach ($logs as $log): ?>
                            <tr>
                                <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><?= htmlspecialchars($log['created_at']) ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><?= htmlspecialchars($log['workflow_name']) ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><?= htmlspecialchars($log['trigger_event']) ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><?= htmlspecialchars($log['target_name']) ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><?= htmlspecialchars($log['action_taken']) ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">
                                    <?php
                                        $statusClass = 'badge-skipped';
                                        if (strtolower($log['status']) === 'success') $statusClass = 'badge-success';
                                        if (strtolower($log['status']) === 'failed') $statusClass = 'badge-failed';
                                    ?>
                                    <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($log['status']) ?></span>
                                </td>
                                <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><?= htmlspecialchars($log['details']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="/js/casjoe_theme.js"></script>
</body>
</html>
