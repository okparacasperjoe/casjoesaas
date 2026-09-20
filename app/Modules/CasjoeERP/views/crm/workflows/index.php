<?php
// app/Modules/CasjoeERP/Views/crm/workflows/index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workflow Automations - CasjoeSaaS</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .template-card { border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-bottom: 20px; flex: 1; min-width: 250px; background-color: var(--card-bg); transition: transform 0.2s;}
        .template-card:hover { transform: translateY(-5px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .templates-container { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 30px; }
        .trigger-pill { background-color: var(--primary-light); color: var(--primary-color); padding: 4px 8px; border-radius: 12px; font-size: 12px; }
        .btn-header { display: flex; gap: 10px; }
        .switch { position: relative; display: inline-block; width: 40px; height: 20px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 2px; bottom: 2px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: var(--success-color, #28a745); }
        input:checked + .slider:before { transform: translateX(20px); }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/../../../../Core/Views/sidebar.php'; // Adjust path as needed ?>
        
        <main class="main-content">
            <header class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h1>Workflow Automations</h1>
                <div class="btn-header">
                    <a href="/erp/crm/workflows/logs" class="btn btn-secondary">Execution Logs</a>
                    <a href="/erp/crm/workflows/create" class="btn btn-primary">New Workflow</a>
                </div>
            </header>

            <section class="templates-section">
                <h3>Quick Start Templates</h3>
                <div class="templates-container">
                    <div class="template-card">
                        <h4>🚀 Hot Lead WhatsApp Alert</h4>
                        <p>When Score >= 60 -> Send WhatsApp alert & Notify Team.</p>
                        <button class="btn btn-sm btn-outline-primary">Use Template</button>
                    </div>
                    <div class="template-card">
                        <h4>⚡ Auto-Nurture New Lead</h4>
                        <p>When Lead Created -> Send Intro Email -> Add 10 Points.</p>
                        <button class="btn btn-sm btn-outline-primary">Use Template</button>
                    </div>
                    <div class="template-card">
                        <h4>🎯 Fast-Track Qualified Deals</h4>
                        <p>When Score >= 80 -> Move to Stage 'Qualified' -> Send Welcome Email.</p>
                        <button class="btn btn-sm btn-outline-primary">Use Template</button>
                    </div>
                </div>
            </section>

            <section class="workflows-list">
                <h3>Active Workflows</h3>
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="text-align: left; padding: 10px; border-bottom: 2px solid var(--border-color);">Name</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 2px solid var(--border-color);">Description</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 2px solid var(--border-color);">Trigger</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 2px solid var(--border-color);">Steps</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 2px solid var(--border-color);">Runs</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 2px solid var(--border-color);">Last Run</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 2px solid var(--border-color);">Status</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 2px solid var(--border-color);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Example row -->
                        <tr>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">Lead Nurture Sequence</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">Sends series of emails to new leads</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><span class="trigger-pill">Lead Created</span></td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">3</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">145</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">2 mins ago</td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">
                                <label class="switch">
                                    <input type="checkbox" checked>
                                    <span class="slider"></span>
                                </label>
                            </td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">
                                <a href="/erp/crm/workflows/edit/1" class="btn btn-sm btn-secondary">Edit</a>
                            </td>
                        </tr>
                        <?php if (isset($workflows) && is_array($workflows)): ?>
                            <?php foreach ($workflows as $wf): ?>
                            <tr>
                                <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><?= htmlspecialchars($wf['name']) ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><?= htmlspecialchars($wf['description']) ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><span class="trigger-pill"><?= htmlspecialchars($wf['trigger_type']) ?></span></td>
                                <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><?= htmlspecialchars($wf['steps_count']) ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><?= htmlspecialchars($wf['runs_count']) ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><?= htmlspecialchars($wf['last_run']) ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">
                                    <label class="switch">
                                        <input type="checkbox" <?= $wf['is_active'] ? 'checked' : '' ?>>
                                        <span class="slider"></span>
                                    </label>
                                </td>
                                <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">
                                    <a href="/erp/crm/workflows/edit/<?= $wf['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
    <script src="/js/casjoe_theme.js"></script>
</body>
</html>
