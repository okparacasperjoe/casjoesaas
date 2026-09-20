<?php
// app/Modules/CasjoeERP/Views/crm/workflows/builder.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workflow Builder - CasjoeSaaS</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .builder-container { background: var(--card-bg); padding: 20px; border-radius: 8px; border: 1px solid var(--border-color); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-control { width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 4px; background: var(--input-bg); color: var(--text-color); }
        .step-card { border: 1px dashed var(--border-color); padding: 15px; margin-bottom: 15px; border-radius: 4px; position: relative; }
        .remove-btn { position: absolute; right: 10px; top: 10px; background: none; border: none; color: #dc3545; cursor: pointer; }
        .dynamic-config { margin-top: 10px; padding: 10px; background: var(--bg-color); border-radius: 4px; display: none; }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/../../../../Core/Views/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h1>Workflow Builder</h1>
                <a href="/erp/crm/workflows" class="btn btn-secondary">Cancel</a>
            </header>

            <div class="builder-container">
                <form id="workflowForm" action="/erp/crm/workflows/save" method="POST">
                    <div class="form-group">
                        <label for="wf_name">Workflow Name</label>
                        <input type="text" id="wf_name" name="name" class="form-control" required placeholder="e.g. Lead Nurture">
                    </div>
                    <div class="form-group">
                        <label for="wf_desc">Description</label>
                        <textarea id="wf_desc" name="description" class="form-control" rows="2" placeholder="What does this workflow do?"></textarea>
                    </div>

                    <h3 style="margin-top: 30px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Step 1: Choose Trigger</h3>
                    <div class="step-card">
                        <div class="form-group">
                            <label for="trigger_type">When this happens...</label>
                            <select id="trigger_type" name="trigger_type" class="form-control" onchange="updateTriggerConfig()">
                                <option value="">Select Trigger...</option>
                                <option value="lead_created">Lead Created</option>
                                <option value="score_threshold">Score Threshold Reached</option>
                                <option value="smartform_submitted">SmartForm Submitted</option>
                                <option value="stage_changed">Pipeline Stage Changed</option>
                                <option value="inbound_message">Inbound Message Received</option>
                            </select>
                        </div>
                        
                        <div id="trigger_config_score" class="dynamic-config form-group">
                            <label>Minimum Score</label>
                            <input type="number" name="trigger_config[min_score]" class="form-control" placeholder="e.g. 60">
                        </div>
                        <div id="trigger_config_stage" class="dynamic-config form-group">
                            <label>Pipeline Stage</label>
                            <select name="trigger_config[stage]" class="form-control">
                                <option value="new">New</option>
                                <option value="contacted">Contacted</option>
                                <option value="qualified">Qualified</option>
                                <option value="proposal">Proposal Sent</option>
                            </select>
                        </div>
                    </div>

                    <h3 style="margin-top: 30px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Step 2: Actions Sequence</h3>
                    <div id="actionsContainer">
                        <!-- Actions injected by JS -->
                    </div>
                    
                    <button type="button" class="btn btn-secondary" onclick="addAction()" style="margin-bottom: 20px;">+ Add Action</button>
                    
                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn btn-primary">Save Workflow</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <template id="actionTemplate">
        <div class="step-card action-item">
            <button type="button" class="remove-btn" onclick="this.closest('.action-item').remove()">✖</button>
            <div class="form-group">
                <label>Then do this...</label>
                <select name="actions[][type]" class="form-control action-type-select" onchange="updateActionConfig(this)">
                    <option value="">Select Action...</option>
                    <option value="send_email">Send Email</option>
                    <option value="send_whatsapp">Send WhatsApp</option>
                    <option value="move_stage">Move Pipeline Stage</option>
                    <option value="award_score">Award Lead Score</option>
                    <option value="notify_staff">Notify Staff</option>
                </select>
            </div>
            <div class="action-config-container"></div>
        </div>
    </template>

    <script src="/js/casjoe_theme.js"></script>
    <script>
        function updateTriggerConfig() {
            document.querySelectorAll('.dynamic-config').forEach(el => el.style.display = 'none');
            const type = document.getElementById('trigger_type').value;
            if(type === 'score_threshold') document.getElementById('trigger_config_score').style.display = 'block';
            if(type === 'stage_changed') document.getElementById('trigger_config_stage').style.display = 'block';
        }

        function addAction() {
            const template = document.getElementById('actionTemplate');
            const clone = template.content.cloneNode(true);
            document.getElementById('actionsContainer').appendChild(clone);
        }

        function updateActionConfig(selectEl) {
            const container = selectEl.closest('.action-item').querySelector('.action-config-container');
            const type = selectEl.value;
            let html = '';
            
            if(type === 'send_email') {
                html = `
                    <div class="form-group dynamic-config" style="display:block">
                        <label>Subject</label>
                        <input type="text" name="actions_config[][subject]" class="form-control" placeholder="e.g. Welcome {{name}}!">
                        <label style="margin-top:10px;">Body Template</label>
                        <textarea name="actions_config[][body]" class="form-control" rows="3" placeholder="Hi {{name}}, your score is {{score}}..."></textarea>
                    </div>`;
            } else if(type === 'send_whatsapp') {
                html = `
                    <div class="form-group dynamic-config" style="display:block">
                        <label>Message Body</label>
                        <textarea name="actions_config[][message]" class="form-control" rows="2" placeholder="Hello {{name}}..."></textarea>
                    </div>`;
            } else if(type === 'move_stage') {
                html = `
                    <div class="form-group dynamic-config" style="display:block">
                        <label>Target Stage</label>
                        <select name="actions_config[][stage]" class="form-control">
                            <option value="qualified">Qualified</option>
                            <option value="won">Closed Won</option>
                            <option value="lost">Closed Lost</option>
                        </select>
                    </div>`;
            } else if(type === 'award_score') {
                html = `
                    <div class="form-group dynamic-config" style="display:block">
                        <label>Points to Add</label>
                        <input type="number" name="actions_config[][points]" class="form-control" placeholder="e.g. 10">
                    </div>`;
            } else if(type === 'notify_staff') {
                html = `
                    <div class="form-group dynamic-config" style="display:block">
                        <label>Notification Message</label>
                        <input type="text" name="actions_config[][message]" class="form-control" placeholder="e.g. Please review lead {{name}}">
                    </div>`;
            }
            container.innerHTML = html;
        }

        // Add first action by default
        document.addEventListener('DOMContentLoaded', () => { addAction(); });
    </script>
</body>
</html>
