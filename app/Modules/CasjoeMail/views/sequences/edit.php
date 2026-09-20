<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sequence | Casjoe Mail</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        /* Force Quill Content Black */
        .ql-editor { color: black !important; min-height: 300px; font-size: 16px; }
        .ql-snow .ql-stroke { stroke: #333 !important; }
        .ql-snow .ql-fill { fill: #333 !important; }
        .ql-snow .ql-picker { color: #333 !important; }
    </style>
    <style>
        .layout-grid {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 30px;
            align-items: start;
        }
        .settings-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            position: sticky;
            top: 20px;
        }
        .step-timeline {
            position: relative;
            padding-left: 20px;
        }
        .step-timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e0e0e0;
            border-radius: 2px;
        }
        .step-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid #eee;
            position: relative;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .step-card:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }
        .step-marker {
            position: absolute;
            left: -29px;
            top: 25px;
            width: 20px;
            height: 20px;
            background: #000066;
            border: 4px solid #f4f6f9;
            border-radius: 50%;
            z-index: 2;
        }
        .delay-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #f0f4ff;
            color: #000066;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .condition-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 6px;
            margin-right: 8px;
        }
        .condition-reply { background: #e3f2fd; color: #1565c0; }
        .condition-click { background: #fff3e0; color: #ef6c00; }
        
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.2s;
        }
        .modal-overlay.open {
            display: flex;
            opacity: 1;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 16px;
            width: 100%;
            max-width: 650px;
            max-height: 90vh;
            overflow-y: auto;
            transform: translateY(20px);
            transition: transform 0.2s;
        }
        .modal-overlay.open .modal-content {
            transform: translateY(0);
        }
        
        @media (max-width: 900px) {
            .layout-grid { grid-template-columns: 1fr; }
            .settings-card { position: static; order: -1; }
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php $active = 'sequences'; include __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <a href="/mail/sequences" style="text-decoration: none; color: #ccc; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 5px;">
                    <ion-icon name="arrow-back-outline"></ion-icon> Back to Sequences
                </a>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <h1 style="font-size: 24px; margin: 0; color: #ffffff;"><?= htmlspecialchars($sequence['name']) ?></h1>
                    <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; background: <?= $sequence['is_active'] ? '#e6f4ea' : '#f1f3f4' ?>; color: <?= $sequence['is_active'] ? '#1e7e34' : '#5f6368' ?>;">
                        <?= $sequence['is_active'] ? 'Active' : 'Paused' ?>
                    </span>
                </div>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="/mail/sequences/enrollments?id=<?= $sequence['id'] ?>" class="btn" style="background: white; color: #333; border: 1px solid #ddd;">
                    <ion-icon name="people-outline" style="margin-right: 8px;"></ion-icon> Enrollments
                </a>
                <button onclick="showAddStepModal()" class="btn" style="background: #000066; color: #ffffff;">
                    <ion-icon name="add-outline" style="margin-right: 8px;"></ion-icon> Add Step
                </button>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert" style="background: #e6f4ea; color: #1e7e34; padding: 12px 20px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #cce8d4; display: flex; align-items: center; gap: 10px;">
                <ion-icon name="checkmark-circle"></ion-icon>
                <?= htmlspecialchars(str_replace('_', ' ', ucfirst($_GET['success']))) ?>
            </div>
        <?php endif; ?>

        <div class="layout-grid">
            <!-- Sidebar Settings -->
            <div class="settings-card">
                <h3 style="margin: 0 0 20px 0; font-size: 18px; color: #1a1a1a;">Sequence Settings</h3>
                <form action="/mail/sequences/update" method="POST">
                    <input type="hidden" name="id" value="<?= $sequence['id'] ?>">
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 600; color: #555;">Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($sequence['name']) ?>" required class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 600; color: #555;">Description</label>
                        <textarea name="description" rows="3" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; resize: vertical;"><?= htmlspecialchars($sequence['description'] ?? '') ?></textarea>
                    </div>

                    <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="is_active" <?= $sequence['is_active'] ? 'checked' : '' ?> style="width: 16px; height: 16px; accent-color: #28a745;">
                            <div>
                                <div style="font-weight: 600; font-size: 14px;">Active Status</div>
                                <div style="font-size: 12px; color: #666;">Sequence is running</div>
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="btn" style="width: 100%; justify-content: center; background: #000066; color: #ffffff;">Save Changes</button>
                    
                    <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
                    
                    <div style="font-size: 13px; color: #666;">
                        <strong>Trigger:</strong> <?= ucfirst($sequence['trigger_event']) ?>
                    </div>
                </form>
            </div>

            <!-- Main Timeline -->
            <div class="step-timeline">
                <?php if (empty($steps)): ?>
                    <div style="text-align: center; padding: 60px 40px; background: white; border-radius: 12px; border: 2px dashed #eee;">
                        <div style="background: #f0f4ff; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                            <ion-icon name="mail-unread-outline" style="font-size: 24px; color: #000066;"></ion-icon>
                        </div>
                        <h3 style="margin: 0 0 8px 0; color: #333;">No emails yet</h3>
                        <p style="color: #666; margin-bottom: 20px; font-size: 14px;">Add your first email to start this sequence.</p>
                        <button onclick="showAddStepModal()" class="btn" style="background: #000066; color: #ffffff;">
                            <ion-icon name="add-outline" style="margin-right: 8px;"></ion-icon> Add First Step
                        </button>
                    </div>
                <?php else: ?>
                    <?php foreach ($steps as $index => $step): ?>
                        <div class="step-card">
                            <div class="step-marker"></div>
                            
                            <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                                <div>
                                    <?php if ($index > 0): ?>
                                        <div class="delay-badge">
                                            <ion-icon name="time-outline"></ion-icon>
                                            Wait <?= $step['delay_days'] ?>d <?= $step['delay_hours'] ?>h
                                        </div>
                                    <?php else: ?>
                                        <div class="delay-badge" style="background: #e8f5e9; color: #2e7d32;">
                                            <ion-icon name="flash-outline"></ion-icon>
                                            Immediate Send
                                        </div>
                                    <?php endif; ?>
                                    <h3 style="margin: 0; font-size: 18px; color: #1a1a1a;">
                                        <?= htmlspecialchars($step['subject']) ?>
                                    </h3>
                                </div>
                                <div style="display: flex; gap: 8px;">
                                    <button onclick="editStep(<?= htmlspecialchars(json_encode($step)) ?>)" class="btn-icon" style="background: #f8f9fa; border: 1px solid #eee; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px; cursor: pointer; color: #555;">
                                        <ion-icon name="create-outline"></ion-icon>
                                    </button>
                                    <form action="/mail/sequences/steps/delete" method="POST" onsubmit="return confirm('Delete this step?');">
                                        <input type="hidden" name="step_id" value="<?= $step['id'] ?>">
                                        <button type="submit" class="btn-icon" style="background: #fff5f5; border: 1px solid #ffe3e3; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px; cursor: pointer; color: #dc3545;">
                                            <ion-icon name="trash-outline"></ion-icon>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div style="color: #555; font-size: 14px; line-height: 1.6; margin-bottom: 20px; padding: 15px; background: #fcfcfc; border-radius: 8px; border: 1px solid #f0f0f0;">
                                <?= nl2br(htmlspecialchars(substr($step['content'], 0, 200))) ?><?= strlen($step['content']) > 200 ? '...' : '' ?>
                            </div>

                            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                <?php if ($step['stop_on_reply']): ?>
                                    <div class="condition-tag condition-reply">
                                        <ion-icon name="arrow-undo"></ion-icon> Stops on reply
                                    </div>
                                <?php endif; ?>
                                <?php if ($step['stop_on_click']): ?>
                                    <div class="condition-tag condition-click">
                                        <ion-icon name="finger-print"></ion-icon> Stops on click
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div style="text-align: center; margin-top: 30px;">
                        <button onclick="showAddStepModal()" class="btn" style="background: #f8f9fa; color: #000066; border: 1px solid #ddd; padding: 12px 24px;">
                            <ion-icon name="add-circle-outline" style="margin-right: 8px; font-size: 18px;"></ion-icon> Add Another Step
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- Modal -->
<div id="stepModal" class="modal-overlay">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 id="modalTitle" style="margin: 0; font-size: 22px;">Add Email Step</h2>
            <button onclick="closeStepModal()" style="background: none; border: none; font-size: 24px; color: #999; cursor: pointer; padding: 5px;">&times;</button>
        </div>
        
        <form id="stepForm" action="/mail/sequences/steps/add" method="POST">
            <input type="hidden" name="sequence_id" value="<?= $sequence['id'] ?>">
            <input type="hidden" id="step_id" name="step_id" value="">
            
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px; text-transform: uppercase; color: #666;">Wait Delay</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <input type="number" name="delay_days" id="delay_days" value="0" min="0" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                        <div style="font-size: 12px; color: #666; margin-top: 4px;">Days</div>
                    </div>
                    <div>
                        <input type="number" name="delay_hours" id="delay_hours" value="0" min="0" max="23" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                        <div style="font-size: 12px; color: #666; margin-top: 4px;">Hours</div>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Subject Line</label>
                <input type="text" name="subject" id="subject" required class="form-control" placeholder="e.g. Just checking in..." style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Email Content</label>
                <div id="quill-editor" style="background: white; border: 1px solid #ddd; border-radius: 6px;"></div>
                <input type="hidden" name="content" id="hiddenContent">
                <div style="font-size: 12px; color: #666; margin-top: 5px;">Available variables: {first_name}, {last_name}, {email}</div>
            </div>

            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 10px; font-weight: 600; font-size: 13px; text-transform: uppercase; color: #666;">Stop Conditions</label>
                <div style="display: flex; gap: 20px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="stop_on_reply" id="stop_on_reply" checked style="accent-color: #000066;">
                        <span style="font-size: 14px;">Stop on reply</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="stop_on_click" id="stop_on_click" style="accent-color: #000066;">
                        <span style="font-size: 14px;">Stop on click</span>
                    </label>
                </div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeStepModal()" class="btn" style="background: transparent; color: #666; border: 1px solid #ddd;">Cancel</button>
                <button type="submit" class="btn" style="background: #000066;">Save Step</button>
            </div>
        </form>
    </div>
</div>

<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
const modal = document.getElementById('stepModal');

var quill = new Quill('#quill-editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link', 'image', 'video'],
            ['clean']
        ]
    }
});

function showAddStepModal() {
    document.getElementById('modalTitle').textContent = 'Add Email Step';
    document.getElementById('stepForm').action = '/mail/sequences/steps/add';
    document.getElementById('step_id').value = '';
    document.getElementById('delay_days').value = '2';
    document.getElementById('delay_hours').value = '0';
    document.getElementById('subject').value = '';
    quill.root.innerHTML = '';
    document.getElementById('stop_on_reply').checked = true;
    document.getElementById('stop_on_click').checked = false;
    modal.classList.add('open');
}

function editStep(step) {
    document.getElementById('modalTitle').textContent = 'Edit Email Step';
    document.getElementById('stepForm').action = '/mail/sequences/steps/update';
    document.getElementById('step_id').value = step.id;
    document.getElementById('delay_days').value = step.delay_days;
    document.getElementById('delay_hours').value = step.delay_hours;
    document.getElementById('subject').value = step.subject;
    quill.root.innerHTML = step.content;
    document.getElementById('stop_on_reply').checked = step.stop_on_reply == 1;
    document.getElementById('stop_on_click').checked = step.stop_on_click == 1;
    modal.classList.add('open');
}

function closeStepModal() {
    modal.classList.remove('open');
}

modal.addEventListener('click', function(e) {
    if (e.target === this) closeStepModal();
});

// Sync content to hidden input on form submit
document.getElementById('stepForm').onsubmit = function() {
    var content = document.querySelector('#hiddenContent');
    content.value = quill.root.innerHTML;
    if (quill.getText().trim().length === 0 && content.value.trim() === '<p><br></p>') {
         content.value = '';
    }
};
</script>
</body>
</html>
