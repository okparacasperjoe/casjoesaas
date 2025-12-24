<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pipeline | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <style>
        .pipeline-container {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding-bottom: 20px;
            height: calc(100vh - 150px);
        }
        .stage-column {
            min-width: 300px;
            flex: 0 0 300px; /* Fixed width */
            background: #f4f5f7;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            max-height: 100%;
        }
        .stage-header {
            padding: 15px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #ddd;
        }
        .stage-body {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
        }
        .lead-card {
            background: white;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            cursor: grab;
            border-left: 4px solid #ccc;
        }
        .lead-card:hover {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .lead-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        .lead-subtitle {
            font-size: 0.85em;
            color: #666;
        }
        .lead-value {
            margin-top: 10px;
            font-weight: bold;
            color: #27ae60;
        }
        .new-lead-btn {
            background: #e2e4e7;
            border: none;
            width: 100%;
            padding: 10px;
            text-align: left;
            color: #555;
            cursor: pointer;
            border-radius: 0 0 8px 8px;
        }
        .new-lead-btn:hover { background: #d0d2d6; }
        
        .ghost-card { background: #e8ebed; border: 2px dashed #ccc; opacity: 0.5; }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

<div class="content-header">
    <h1>Sales Pipeline</h1>
    <div>
       <a href="/erp/crm/leads" class="btn btn-secondary">List View</a>
       <button class="btn btn-primary" onclick="alert('TODO: Modal')">Add Lead</button>
    </div>
</div>

<div class="pipeline-container">
    <?php foreach ($pipelineData as $stageId => $data): ?>
    <div class="stage-column" data-stage-id="<?= $stageId ?>">
        <div class="stage-header" style="border-bottom-color: <?= $data['stage']['color'] ?>">
            <?= htmlspecialchars($data['stage']['name']) ?>
            <span class="badge badge-light"><?= count($data['leads']) ?></span>
        </div>
        <div class="stage-body" id="stage-<?= $stageId ?>">
            <?php foreach ($data['leads'] as $lead): ?>
            <div class="lead-card" data-id="<?= $lead['id'] ?>" style="border-left-color: <?= $data['stage']['color'] ?>">
                <div class="lead-title"><?= htmlspecialchars($lead['contact_name']) ?></div>
                <div class="lead-subtitle"><?= htmlspecialchars($lead['company_name'] ?? 'Individual') ?></div>
                <div class="lead-value">NGN <?= number_format($lead['value'], 2) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="new-lead-btn">+ Add Lead</button>
    </div>
    <?php endforeach; ?>
</div>

<script>
    // Init SortableJS for each stage
    const stages = document.querySelectorAll('.stage-body');
    stages.forEach(stage => {
        new Sortable(stage, {
            group: 'pipeline', // allow dragging between lists
            animation: 150,
            ghostClass: 'ghost-card',
            onEnd: function (evt) {
                const itemEl = evt.item; // dragged HTMLElement
                const newStageId = evt.to.closest('.stage-column').getAttribute('data-stage-id');
                const leadId = itemEl.getAttribute('data-id');
                
                // Only update if moved to a different list
                if (evt.to !== evt.from) {
                    updateLeadStage(leadId, newStageId);
                }
            }
        });
    });

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
                alert('Failed to update stage.');
                // In a real app, revert the DOM change here
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating stage.');
        });
    }
</script>

    </main>
</div>
</body>
</html>
