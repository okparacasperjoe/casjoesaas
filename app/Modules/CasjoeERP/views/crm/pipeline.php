<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pipeline | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <style>
        body {
            background-color: var(--dark-bg);
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
        }

        .pipeline-wrapper {
            padding: 20px 30px;
            height: calc(100vh - 80px);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .pipeline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .pipeline-header h1 {
            font-size: 2rem;
            font-weight: 800;
            margin: 0;
            background: linear-gradient(135deg, #ffffff, #a8b2d1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .pipeline-actions {
            display: flex;
            gap: 15px;
        }

        .btn-glass {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-primary-glow {
            background: linear-gradient(135deg, var(--brand-blue), #00d2ff);
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 82, 204, 0.4);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 82, 204, 0.6);
        }

        .pipeline-board {
            display: flex;
            gap: 24px;
            overflow-x: auto;
            flex: 1;
            padding-bottom: 20px;
            /* Custom scrollbar for board */
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.2) transparent;
        }

        .pipeline-board::-webkit-scrollbar {
            height: 8px;
        }

        .pipeline-board::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
            border-radius: 4px;
        }

        .pipeline-board::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 4px;
        }

        .stage-column {
            min-width: 320px;
            flex: 0 0 320px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            max-height: 100%;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: var(--glass-shadow);
            position: relative;
            overflow: hidden;
        }

        .stage-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .stage-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stage-color-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            box-shadow: 0 0 10px currentColor;
        }

        .stage-count {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .stage-body {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            /* Custom scrollbar for columns */
            scrollbar-width: none; /* Firefox */
        }

        .stage-body::-webkit-scrollbar {
            display: none; /* Chrome/Safari */
        }

        .lead-card {
            background: rgba(20, 20, 35, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
            cursor: grab;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .lead-card:hover {
            transform: translateY(-3px);
            background: rgba(30, 30, 50, 0.8);
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        }

        .lead-card:active {
            cursor: grabbing;
        }

        .lead-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background-color: var(--card-accent, #3498db);
        }

        .lead-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .lead-name {
            font-weight: 600;
            font-size: 1.05rem;
            color: #fff;
            margin: 0;
        }

        .lead-company {
            font-size: 0.85rem;
            color: #a8b2d1;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .lead-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 15px;
        }

        .lead-value {
            font-weight: 700;
            font-size: 0.95rem;
            color: #4ade80;
            background: rgba(74, 222, 128, 0.1);
            padding: 4px 8px;
            border-radius: 6px;
        }

        .lead-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .add-lead-btn {
            background: rgba(255, 255, 255, 0.03);
            border: none;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding: 16px;
            width: 100%;
            color: #a8b2d1;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .add-lead-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        /* Sortable.js classes */
        .ghost-card {
            opacity: 0.4;
            background: rgba(0, 0, 0, 0.5) !important;
            border: 2px dashed rgba(255, 255, 255, 0.3) !important;
        }
        
        .drag-card {
            box-shadow: 0 15px 30px rgba(0,0,0,0.5) !important;
            transform: rotate(2deg) scale(1.02);
            cursor: grabbing !important;
        }

        /* Modal Styles */
        .glass-modal {
            display: none; 
            position: fixed; 
            top: 0; left: 0; width: 100vw; height: 100vh; 
            background: rgba(0,0,0,0.7); 
            backdrop-filter: blur(8px); 
            z-index: 10000; 
            align-items: center; 
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        .glass-modal-content {
            background: rgba(15, 15, 25, 0.85); 
            border: 1px solid rgba(255,255,255,0.1); 
            border-radius: 20px; 
            padding: 30px; 
            width: 90%; max-width: 500px; 
            color: #fff;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.3s ease;
        }

        .glass-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding-bottom: 15px;
        }

        .glass-modal-header h3 {
            margin: 0;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .close-modal-btn {
            background: transparent;
            border: none;
            color: #a8b2d1;
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .close-modal-btn:hover {
            color: #fff;
        }

        .glass-input {
            background: rgba(0,0,0,0.3); 
            border: 1px solid rgba(255,255,255,0.1); 
            color: #fff; 
            border-radius: 10px; 
            padding: 12px 15px; 
            width: 100%; 
            box-sizing: border-box;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .glass-input:focus {
            outline: none;
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 3px rgba(0, 82, 204, 0.2);
            background: rgba(0,0,0,0.5);
        }

        .glass-input::placeholder {
            color: rgba(255,255,255,0.3);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        /* Light Mode Overrides */
        html.light-theme .pipeline-header h1 {
            background: linear-gradient(135deg, #0f172a, #475569);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        html.light-theme .btn-glass {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e2e8f0;
            color: #475569;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        html.light-theme .btn-glass:hover {
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        html.light-theme .pipeline-board::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
        }
        html.light-theme .pipeline-board::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.2);
        }

        html.light-theme .stage-column {
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }

        html.light-theme .stage-header {
            border-bottom: 1px solid #e2e8f0;
        }

        html.light-theme .stage-title {
            color: #0f172a;
        }

        html.light-theme .stage-count {
            background: rgba(0, 0, 0, 0.05);
            color: #475569;
        }

        html.light-theme .lead-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        html.light-theme .lead-card:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        }

        html.light-theme .lead-name {
            color: #0f172a;
        }

        html.light-theme .lead-company {
            color: #64748b;
        }

        html.light-theme .lead-value {
            color: #16a34a;
            background: rgba(22, 163, 74, 0.1);
        }

        html.light-theme .add-lead-btn {
            background: rgba(0, 0, 0, 0.02);
            border-top: 1px solid #e2e8f0;
            color: #64748b;
        }

        html.light-theme .add-lead-btn:hover {
            background: rgba(0, 0, 0, 0.05);
            color: #0f172a;
        }
        
        /* Light Mode Ghost/Drag Cards */
        html.light-theme .ghost-card {
            background: rgba(255, 255, 255, 0.6) !important;
            border: 2px dashed #cbd5e1 !important;
        }
        html.light-theme .drag-card {
            box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
        }

        /* Light Mode Modal Overrides */
        html.light-theme .glass-modal {
            background: rgba(255, 255, 255, 0.4);
        }
        html.light-theme .glass-modal-content {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid #e2e8f0;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.1);
        }
        html.light-theme .glass-modal-header {
            border-bottom: 1px solid #e2e8f0;
        }
        html.light-theme .glass-modal-header h3 {
            color: #0f172a;
        }
        html.light-theme .close-modal-btn {
            color: #64748b;
        }
        html.light-theme .close-modal-btn:hover {
            color: #0f172a;
        }
        html.light-theme .form-group label {
            color: #475569 !important;
        }
        html.light-theme .glass-input {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #0f172a;
        }
        html.light-theme .glass-input:focus {
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        html.light-theme .glass-input::placeholder {
            color: #94a3b8;
        }
        html.light-theme .glass-input option {
            background: #ffffff !important;
            color: #0f172a !important;
        }

    
        /* Light Mode New Lead Button Override */
        html.light-theme .btn-primary-glow {
            background: #ffffff !important;
            color: #000000 !important;
            border: 1px solid #000000 !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08) !important;
        }
        html.light-theme .btn-primary-glow:hover {
            background: #000000 !important;
            color: #ffffff !important;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2) !important;
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    
    <main class="main-content" style="padding: 0;">
        <div class="pipeline-wrapper">
            <div class="pipeline-header">
                <h1>Sales Pipeline</h1>
                <div class="pipeline-actions">
                    <a href="/erp/crm" class="btn-glass">
                        <ion-icon name="list-outline"></ion-icon> Table View
                    </a>
                    <button class="btn-primary-glow" onclick="openAddLeadModal()">
                        <ion-icon name="add-outline"></ion-icon> New Lead
                    </button>
                </div>
            </div>

            <div class="pipeline-board">
                <?php foreach ($pipelineData as $stageId => $data): ?>
                <div class="stage-column" data-stage-id="<?= $stageId ?>">
                    <div class="stage-header">
                        <div class="stage-title">
                            <span class="stage-color-indicator" style="background-color: <?= $data['stage']['color'] ?>; color: <?= $data['stage']['color'] ?>"></span>
                            <?= htmlspecialchars($data['stage']['name']) ?>
                        </div>
                        <span class="badge stage-count"><?= count($data['leads']) ?></span>
                    </div>
                    
                    <div class="stage-body" id="stage-<?= $stageId ?>">
                        <?php foreach ($data['leads'] as $lead): ?>
                        <div class="lead-card" data-id="<?= $lead['id'] ?>" style="--card-accent: <?= $data['stage']['color'] ?>;">
                            <div class="lead-header">
                                <div>
                                    <h3 class="lead-name"><?= htmlspecialchars($lead['name'] ?? '') ?></h3>
                                    <div class="lead-company">
                                        <ion-icon name="business-outline"></ion-icon>
                                        <?= htmlspecialchars(($lead['company'] ?? $lead['company_name'] ?? '') ?: 'Individual') ?>
                                    </div>
                                </div>
                                <button style="background:transparent; border:none; color:rgba(255,255,255,0.4); cursor:pointer; padding:0;" title="Edit Lead">
                                    <ion-icon name="ellipsis-horizontal"></ion-icon>
                                </button>
                            </div>
                            
                            <?php
                                $score = (int)($lead['ai_score'] ?? 0);
                                if ($score <= 20) { $c = '#94a3b8'; $lbl = 'Cold'; }
                                elseif ($score <= 40) { $c = '#f59e0b'; $lbl = 'Warm'; }
                                elseif ($score <= 60) { $c = '#f97316'; $lbl = 'Hot'; }
                                elseif ($score <= 80) { $c = '#ef4444'; $lbl = 'Very Hot'; }
                                else { $c = '#8b5cf6'; $lbl = 'On Fire'; }
                            ?>
                            <div style="display:flex; align-items:center; gap:6px; margin:8px 0;">
                                <span style="background:<?= $c ?>; color:#fff; padding:2px 6px; border-radius:50px; font-size:0.7rem; font-weight:700; min-width:24px; text-align:center;"><?= $score ?></span>
                                <small style="color:<?= $c ?>; font-weight:600; font-size:0.75rem;"><?= $lbl ?></small>
                            </div>

                            <div class="lead-footer">
                                <div class="lead-value">NGN <?= number_format($lead['value'] ?? 0, 2) ?></div>
                                <div class="lead-avatar" title="<?= htmlspecialchars($lead['name'] ?? '') ?>">
                                    <?= strtoupper(substr($lead['name'] ?? 'U', 0, 1)) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <button class="add-lead-btn" onclick="openAddLeadModal(<?= $stageId ?>)">
                        <ion-icon name="add-circle-outline"></ion-icon> Quick Add
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <!-- Glassmorphic Add Lead Modal -->
    <div class="glass-modal" id="addLeadModal">
        <div class="glass-modal-content">
            <div class="glass-modal-header">
                <h3><ion-icon name="person-add-outline" style="color:var(--brand-blue);"></ion-icon> New Opportunity</h3>
                <button class="close-modal-btn" onclick="closeAddLeadModal()"><ion-icon name="close-outline"></ion-icon></button>
            </div>

            <form method="POST" action="/erp/crm/leads/store">
                <?= \App\Core\Services\CsrfService::getTokenField() ?>
                
                <div class="form-group" style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; font-weight:500; font-size:0.9rem; color:#a8b2d1;">Lead Name *</label>
                    <input type="text" name="name" class="glass-input" placeholder="e.g. John Doe" required>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                    <div class="form-group">
                        <label style="display:block; margin-bottom:6px; font-weight:500; font-size:0.9rem; color:#a8b2d1;">Email</label>
                        <input type="email" name="email" class="glass-input" placeholder="john@example.com">
                    </div>
                    <div class="form-group">
                        <label style="display:block; margin-bottom:6px; font-weight:500; font-size:0.9rem; color:#a8b2d1;">Phone</label>
                        <input type="text" name="phone" class="glass-input" placeholder="+234...">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                    <div class="form-group">
                        <label style="display:block; margin-bottom:6px; font-weight:500; font-size:0.9rem; color:#a8b2d1;">Company</label>
                        <input type="text" name="company" class="glass-input" placeholder="Acme Corp">
                    </div>
                    <div class="form-group">
                        <label style="display:block; margin-bottom:6px; font-weight:500; font-size:0.9rem; color:#a8b2d1;">Initial Stage</label>
                        <select name="stage_id" id="modal_stage_id" class="glass-input" style="appearance: none;">
                            <?php foreach ($pipelineData as $sId => $sData): ?>
                                <option value="<?= $sId ?>" style="background: #1a1a2e; color: #fff;"><?= htmlspecialchars($sData['stage']['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group" style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; font-weight:500; font-size:0.9rem; color:#a8b2d1;">Estimated Value (NGN)</label>
                    <input type="number" step="0.01" name="value" class="glass-input" placeholder="0.00">
                </div>

                <div class="form-group" style="margin-bottom:25px;">
                    <label style="display:block; margin-bottom:6px; font-weight:500; font-size:0.9rem; color:#a8b2d1;">Notes</label>
                    <textarea name="notes" class="glass-input" rows="3" placeholder="Enter context or requirements..."></textarea>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:12px;">
                    <button type="button" class="btn-glass" onclick="closeAddLeadModal()" style="padding:10px 20px;">Cancel</button>
                    <button type="submit" class="btn-primary-glow" style="padding:10px 24px;">Save Lead</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    // Initialize SortableJS for drag and drop
    document.addEventListener('DOMContentLoaded', function() {
        const stages = document.querySelectorAll('.stage-body');
        
        stages.forEach(stage => {
            new Sortable(stage, {
                group: 'pipeline',
                animation: 200,
                easing: "cubic-bezier(0.25, 0.1, 0.25, 1)",
                ghostClass: 'ghost-card',
                dragClass: 'drag-card',
                delay: 0, // No delay on desktop
                delayOnTouchOnly: true, // 150ms delay on mobile
                touchStartThreshold: 5,
                
                onEnd: function (evt) {
                    const itemEl = evt.item;
                    const newStageId = evt.to.closest('.stage-column').getAttribute('data-stage-id');
                    const oldStageId = evt.from.closest('.stage-column').getAttribute('data-stage-id');
                    const leadId = itemEl.getAttribute('data-id');
                    
                    if (newStageId !== oldStageId) {
                        // Update color accent immediately for visual feedback
                        const newColor = evt.to.closest('.stage-column').querySelector('.stage-color-indicator').style.backgroundColor;
                        itemEl.style.setProperty('--card-accent', newColor);
                        
                        // Update counts
                        updateStageCounts();
                        
                        // Save to backend
                        updateLeadStage(leadId, newStageId);
                    }
                }
            });
        });
    });

    function updateStageCounts() {
        document.querySelectorAll('.stage-column').forEach(column => {
            const count = column.querySelectorAll('.lead-card').length;
            column.querySelector('.stage-count').textContent = count;
        });
    }

    function updateLeadStage(leadId, stageId) {
        fetch('/erp/crm/pipeline/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                leadId: leadId,
                stageId: stageId
            })
        })
        .then(response => response.json())
        .then(data => {
            if(!data.success) {
                console.error('Update failed');
                // Could show a toast notification here
            }
        })
        .catch(error => {
            console.error('Network Error:', error);
        });
    }

    function openAddLeadModal(stageId = null) {
        const modal = document.getElementById('addLeadModal');
        if (stageId) {
            document.getElementById('modal_stage_id').value = stageId;
        }
        modal.style.display = 'flex';
    }

    function closeAddLeadModal() {
        document.getElementById('addLeadModal').style.display = 'none';
    }
    
    // Close modal on outside click
    document.getElementById('addLeadModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddLeadModal();
        }
    });
</script>
</body>
</html>
