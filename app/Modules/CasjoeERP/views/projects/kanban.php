<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban Board | Casjoe BOS</title>
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
        .kanban-container {
            width: 100%;
            overflow-x: auto;
            padding: 5px;
            margin-top: 20px;
        }
        .kanban-board {
            display: flex;
            gap: 20px;
            min-width: max-content; /* Ensure enough space for all columns */
            padding-bottom: 20px;
            align-items: flex-start;
        }
        .kanban-column {
            width: 300px; /* Fixed width for consistency */
            flex-shrink: 0; /* Prevent columns from shrinking */
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .column-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e1e4e8;
            font-weight: 600;
            color: #444;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        .task-card {
            background: white;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-left: 4px solid #ccc;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .priority-high { border-left-color: #ef5350; }
        .priority-medium { border-left-color: #ffa726; }
        .priority-low { border-left-color: #66bb6a; }

        .task-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        .task-meta {
            font-size: 0.8rem;
            color: #888;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .task-assignee {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .avatar-xs {
            width: 20px;
            height: 20px;
            background: #e0e0e0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            color: #666;
        }
        .move-actions {
            display: flex;
            justify-content: flex-end;
            gap: 5px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #f0f0f0;
        }
        .btn-move {
            background: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 2px 6px;
            font-size: 0.75rem;
            color: #666;
            cursor: pointer;
        }
        .btn-move:hover {
            background: #f0f0f0;
            color: #333;
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../layout/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Kanban Board</h2>
            <div style="display: flex; gap: 10px;">
                <form method="GET" style="display: flex; gap: 10px;">
                    <select name="project_id" class="form-control" onchange="this.form.submit()" style="padding: 6px;">
                        <option value="">All Projects</option>
                        <?php foreach($projects as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($projectId == $p['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <a href="/erp/tasks/create" class="btn btn-primary"><ion-icon name="add-outline"></ion-icon> New Task</a>
            </div>
        </div>

        <div class="kanban-container">
            <div class="kanban-board">
                <?php 
                $columns = [
                    'pending' => ['label' => 'To Do', 'color' => '#666'],
                    'in_progress' => ['label' => 'In Progress', 'color' => '#42a5f5'],
                    'review' => ['label' => 'Review', 'color' => '#ffa726'],
                    'done' => ['label' => 'Done', 'color' => '#66bb6a']
                ];

                foreach ($columns as $statusKey => $col): 
                ?>
                <div class="kanban-column">
                    <div class="column-header" style="border-bottom-color: <?= $col['color'] ?>;">
                        <span><?= $col['label'] ?></span>
                        <span style="background: rgba(255,255,255,0.1); padding: 2px 8px; border-radius: 10px; font-size: 0.75rem;">
                            <?= count($board[$statusKey]) ?>
                        </span>
                    </div>

                    <?php foreach ($board[$statusKey] as $task): 
                        $priorityClass = 'priority-' . strtolower($task['priority'] ?? 'medium');
                    ?>
                    <div class="task-card <?= $priorityClass ?>">
                        <div class="task-title"><?= htmlspecialchars($task['title']) ?></div>
                        <div class="task-meta">
                            <span>Project: <?= htmlspecialchars($task['project_name'] ?? 'N/A') ?></span>
                        </div>
                        <?php if(!empty($task['due_date'])): ?>
                        <div class="task-meta" style="color: #ff5252;">
                            <ion-icon name="calendar-outline" style="vertical-align: middle;"></ion-icon> 
                            <?= date('M d', strtotime($task['due_date'])) ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="task-meta">
                             <div class="task-assignee">
                                <div class="avatar-xs">
                                    <?= $task['assignee_first'] ? substr($task['assignee_first'], 0, 1) : '?' ?>
                                </div>
                                <span><?= htmlspecialchars($task['assignee_first'] ?? 'Unassigned') ?></span>
                            </div>
                        </div>

                        <div class="move-actions">
                            <form action="/erp/task/update-status" method="POST" style="display:inline;">
                                <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                
                                <?php if ($statusKey !== 'pending'): ?>
                                    <button type="submit" name="status" value="<?= $statusKey === 'done' ? 'review' : ($statusKey === 'review' ? 'in_progress' : 'pending') ?>" class="btn-move" title="Move Back">
                                        <ion-icon name="arrow-back-outline"></ion-icon>
                                    </button>
                                <?php endif; ?>

                                <?php if ($statusKey !== 'done'): ?>
                                    <button type="submit" name="status" value="<?= $statusKey === 'pending' ? 'in_progress' : ($statusKey === 'in_progress' ? 'review' : 'done') ?>" class="btn-move" title="Move Forward">
                                        <ion-icon name="arrow-forward-outline"></ion-icon>
                                    </button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <?php if (empty($board[$statusKey])): ?>
                        <div style="text-align: center; padding: 20px; color: #888; font-size: 0.9rem; font-style: italic;">
                            No tasks
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>
