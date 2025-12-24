<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Calendar | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #eee;
            border: 1px solid #eee;
        }
        .calendar-day-header {
            background: #f8f9fa;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            color: #555;
        }
        .calendar-day {
            background: #fff;
            min-height: 120px;
            padding: 10px;
            position: relative;
        }
        .calendar-day.other-month {
            background: #fcfcfc;
            color: #ccc;
        }
        .day-number {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        .task-item {
            font-size: 0.8rem;
            padding: 2px 4px;
            background: #e3f2fd;
            border-left: 3px solid #007bff;
            margin-bottom: 3px;
            border-radius: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }
        .task-priority-high { border-left-color: #dc3545; background: #fde8e8; }
        .task-priority-medium { border-left-color: #ffc107; background: #fff8e1; }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Calendar</h2>
            <div>
                <a href="/erp/calendar?month=<?= $prevMonth ?>&year=<?= $prevYear ?>" class="btn btn-sm" style="background: #eee; color: #333;">&laquo; Prev</a>
                <span style="display: inline-block; width: 150px; text-align: center; font-weight: bold;"><?= $monthName ?> <?= $year ?></span>
                <a href="/erp/calendar?month=<?= $nextMonth ?>&year=<?= $nextYear ?>" class="btn btn-sm" style="background: #eee; color: #333;">Next &raquo;</a>
            </div>
            <a href="/erp/tasks/create" class="btn"><ion-icon name="add-outline"></ion-icon> Add Task</a>
        </div>

        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="calendar-grid">
                <div class="calendar-day-header">Sun</div>
                <div class="calendar-day-header">Mon</div>
                <div class="calendar-day-header">Tue</div>
                <div class="calendar-day-header">Wed</div>
                <div class="calendar-day-header">Thu</div>
                <div class="calendar-day-header">Fri</div>
                <div class="calendar-day-header">Sat</div>

                <?php
                // Blank days before start
                for ($i = 0; $i < $startDayOfWeek; $i++) {
                    echo '<div class="calendar-day other-month"></div>';
                }

                // Days of month
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    echo '<div class="calendar-day">';
                    echo '<span class="day-number">' . $day . '</span>';
                    
                    if (isset($tasksByDay[$day])) {
                        foreach ($tasksByDay[$day] as $task) {
                            $priorityClass = '';
                            if ($task['priority'] === 'high') $priorityClass = 'task-priority-high';
                            if ($task['priority'] === 'medium') $priorityClass = 'task-priority-medium';

                            echo '<div class="task-item ' . $priorityClass . '" title="' . htmlspecialchars($task['title']) . '">';
                            echo htmlspecialchars($task['title']);
                            echo '</div>';
                        }
                    }

                    echo '</div>';
                }

                // Remaining blank days (optional, for grid completeness)
                $totalCells = $startDayOfWeek + $daysInMonth;
                $remainingCells = 7 - ($totalCells % 7);
                if ($remainingCells < 7) {
                    for ($i = 0; $i < $remainingCells; $i++) {
                        echo '<div class="calendar-day other-month"></div>';
                    }
                }
                ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>
