<?php
$currentMonthStr = $month ?? date('Y-m');
$time = strtotime($currentMonthStr . '-01');
if (!$time) {
    $time = time();
    $currentMonthStr = date('Y-m');
}

$monthName = date('F Y', $time);
$prevMonth = date('Y-m', strtotime('-1 month', $time));
$nextMonth = date('Y-m', strtotime('+1 month', $time));

$daysInMonth = (int)date('t', $time);
$firstDayOfWeek = (int)date('w', $time); // 0 (Sun) to 6 (Sat)

// Group calendar posts by day (1 to daysInMonth)
$postsByDay = [];
if (!empty($calendarPosts)) {
    foreach ($calendarPosts as $cp) {
        $dateStr = !empty($cp['scheduled_at']) ? $cp['scheduled_at'] : $cp['created_at'];
        $dayNum = (int)date('j', strtotime($dateStr));
        $postsByDay[$dayNum][] = $cp;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Content Calendar | Casjoe Links</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/casjoe_links.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 16px;
        }
        .month-nav {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--cl-surface);
            padding: 8px 18px;
            border-radius: var(--cl-radius-sm);
            border: 1px solid var(--cl-border);
            backdrop-filter: blur(12px);
        }
        .month-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--cl-text);
            min-width: 170px;
            text-align: center;
        }
        .nav-arrow {
            color: var(--cl-text-muted);
            font-size: 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 4px;
            border-radius: 6px;
            transition: all 0.15s;
        }
        .nav-arrow:hover {
            color: var(--cl-accent);
            background: var(--cl-surface-hover);
        }
        .calendar-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 12px;
        }
        .calendar-grid {
            min-width: 720px;
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            background: var(--cl-surface);
            padding: 12px;
            border-radius: var(--cl-radius);
            border: 1px solid var(--cl-border);
            backdrop-filter: blur(16px);
        }
        .day-header {
            text-align: center;
            font-weight: 700;
            font-size: 12.5px;
            color: var(--cl-text-muted);
            padding: 10px 0;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        html.light-theme .day-header {
            background: #f1f5f9;
        }
        .day-cell {
            background: rgba(255, 255, 255, 0.03);
            min-height: 120px;
            border-radius: var(--cl-radius-xs);
            padding: 10px;
            display: flex;
            flex-direction: column;
            border: 1px solid var(--cl-border);
            transition: all 0.2s;
        }
        html.light-theme .day-cell {
            background: #ffffff;
        }
        .day-cell:hover {
            border-color: var(--cl-accent-border);
            transform: translateY(-2px);
        }
        .day-cell.empty {
            background: transparent;
            border-color: transparent;
            opacity: 0.3;
        }
        .day-cell.today {
            border: 2px solid var(--cl-accent) !important;
            background: rgba(255, 166, 0, 0.05);
            box-shadow: 0 0 16px var(--cl-accent-glow);
        }
        .day-number {
            font-weight: 700;
            font-size: 13.5px;
            color: var(--cl-text);
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .day-cell.today .day-number span:first-child {
            color: var(--cl-accent);
            font-weight: 800;
        }
        .today-pill {
            font-size: 10px;
            background: var(--cl-accent);
            color: #0f172a;
            padding: 2px 7px;
            border-radius: 10px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .post-chip {
            background: rgba(255, 166, 0, 0.12);
            border: 1px solid rgba(255, 166, 0, 0.25);
            border-radius: 6px;
            padding: 5px 8px;
            margin-bottom: 5px;
            font-size: 11.5px;
            color: var(--cl-accent);
            cursor: pointer;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 600;
        }
        .post-chip.published {
            background: rgba(34, 197, 94, 0.12);
            border-color: rgba(34, 197, 94, 0.25);
            color: #4ade80;
        }
        html.light-theme .post-chip {
            background: #fef3c7;
            border-color: #fde68a;
            color: #b45309;
        }
        html.light-theme .post-chip.published {
            background: #d1fae5;
            border-color: #a7f3d0;
            color: #065f46;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php $active = 'social_planner'; include dirname(__DIR__) . '/partials/sidebar_links.php'; ?>
        
        <main class="main-content">
            <div id="casjoe-links-app">
                <div class="calendar-header">
                    <div>
                        <a href="/links/social" class="cl-link" style="font-size: 0.9rem; margin-bottom: 8px;">
                            <ion-icon name="arrow-back-outline"></ion-icon> Back to Social Planner
                        </a>
                        <h1 style="margin: 4px 0 0 0; font-size: 28px; font-weight: 800; color: var(--cl-text);">Content Calendar</h1>
                    </div>

                    <div class="month-nav">
                        <a href="/links/social/calendar?month=<?= $prevMonth ?>" class="nav-arrow" title="Previous Month">
                            <ion-icon name="chevron-back-outline"></ion-icon>
                        </a>
                        <div class="month-title"><?= $monthName ?></div>
                        <a href="/links/social/calendar?month=<?= $nextMonth ?>" class="nav-arrow" title="Next Month">
                            <ion-icon name="chevron-forward-outline"></ion-icon>
                        </a>
                    </div>

                    <div>
                        <a href="/links/social/composer" class="cl-btn">
                            <ion-icon name="add-circle-outline"></ion-icon> Schedule Post
                        </a>
                    </div>
                </div>

                <!-- Responsive Calendar Wrapper -->
                <div class="calendar-wrapper">
                    <div class="calendar-grid">
                        <div class="day-header">Sun</div>
                        <div class="day-header">Mon</div>
                        <div class="day-header">Tue</div>
                        <div class="day-header">Wed</div>
                        <div class="day-header">Thu</div>
                        <div class="day-header">Fri</div>
                        <div class="day-header">Sat</div>

                        <!-- Empty days before 1st of month -->
                        <?php for ($i = 0; $i < $firstDayOfWeek; $i++): ?>
                            <div class="day-cell empty"></div>
                        <?php endfor; ?>

                        <!-- Month Days -->
                        <?php 
                        $todayYearMonth = date('Y-m');
                        $todayDay = (int)date('j');
                        $isCurrentMonth = ($currentMonthStr === $todayYearMonth);

                        for ($day = 1; $day <= $daysInMonth; $day++): 
                            $isToday = ($isCurrentMonth && $day === $todayDay);
                            $dayPosts = $postsByDay[$day] ?? [];
                        ?>
                            <div class="day-cell <?= $isToday ? 'today' : '' ?>">
                                <div class="day-number">
                                    <span><?= $day ?></span>
                                    <?php if ($isToday): ?>
                                        <span class="today-pill">TODAY</span>
                                    <?php endif; ?>
                                </div>

                                <div style="flex: 1; overflow-y: auto; max-height: 95px;">
                                    <?php foreach ($dayPosts as $dp): ?>
                                        <div class="post-chip <?= $dp['status'] === 'published' ? 'published' : '' ?>" title="<?= htmlspecialchars($dp['content']) ?>">
                                            <ion-icon name="<?= $dp['status'] === 'published' ? 'checkmark-circle' : 'time-outline' ?>"></ion-icon>
                                            <span><?= htmlspecialchars(mb_strimwidth($dp['content'], 0, 22, '...')) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endfor; ?>

                        <!-- Trailing empty days to complete 7-day row -->
                        <?php 
                        $totalCells = $firstDayOfWeek + $daysInMonth;
                        $trailingCells = (7 - ($totalCells % 7)) % 7;
                        for ($i = 0; $i < $trailingCells; $i++): ?>
                            <div class="day-cell empty"></div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
