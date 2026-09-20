<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Training | Casjoe Business School</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        /* Netflix-style Enterprise Dashboard Aesthetics */
        body, html {
            background-color: #0f172a; /* Deep dark blue/black */
            color: #f8fafc;
        }
        .main-content { 
            margin-left: 270px; 
            padding: 40px;
        }
        .sidebar {
            background: #0f172a;
            border-right: 1px solid #1e293b;
        }
        .top-bar h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #fff;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .top-bar {
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #1e293b;
        }

        .dark-card {
            background: #1e293b;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            border: 1px solid #334155;
            max-width: 600px;
        }

        .course-preview {
            display: flex;
            align-items: center;
            gap: 20px;
            background: #0f172a;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #334155;
        }
        .course-thumb {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            background-size: cover;
            background-position: center;
        }
        
        .seats-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 10px;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .dark-input {
            background: #0f172a;
            border: 1px solid #334155;
            color: white;
            padding: 12px 15px;
            border-radius: 6px;
            width: 100%;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        .dark-input:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .btn-modern {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            transition: background 0.2s;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
        }
        .btn-modern:hover { background: #2563eb; }
        
        .btn-secondary {
            background: #334155;
            color: #f8fafc;
            border: 1px solid #475569;
        }
        .btn-secondary:hover { background: #475569; }

        @media (max-width: 992px) {
            .main-content { margin-left: 0; padding: 20px; }
            .sidebar { left: -270px !important; transition: left 0.3s ease; }
            .sidebar.active { left: 0 !important; }
        }
    </style>
</head>
<body>
    <div class="app-container" style="background: transparent;">
        <!-- Mobile Sidebar Toggle -->
        <?php include dirname(__DIR__, 4) . '/Core/Views/partials/mobile_nav.php'; ?>
        
        <!-- Sidebar -->
        <?php $active = 'academy_business'; include __DIR__ . '/../partials/sidebar_academy.php'; ?>

        <main class="main-content">
            <div class="top-bar">
                <a href="/academy/business" style="color: #94a3b8; font-size: 1.8rem;"><ion-icon name="arrow-back-circle"></ion-icon></a>
                <h1>Assign Training License</h1>
            </div>

            <div class="dark-card">
                <div class="course-preview">
                    <?php if(!empty($license['thumbnail'])): ?>
                        <div class="course-thumb" style="background-image: url('<?= htmlspecialchars($license['thumbnail']) ?>');"></div>
                    <?php else: ?>
                        <div class="course-thumb" style="background: #334155; display: flex; align-items: center; justify-content: center;"><ion-icon name="videocam" style="font-size: 2rem; color: #64748b;"></ion-icon></div>
                    <?php endif; ?>
                    
                    <div>
                        <h3 style="margin: 0 0 5px 0; color: white; font-size: 1.2rem;"><?= htmlspecialchars($license['title']) ?></h3>
                        <?php $available = $license['seats_total'] - $license['seats_used']; ?>
                        <div class="seats-pill">
                            <ion-icon name="ticket"></ion-icon> <?= $available ?> Seats Available
                        </div>
                    </div>
                </div>

                <form action="/academy/business/assign-store" method="POST">
                    <input type="hidden" name="license_id" value="<?= $license['id'] ?>">
                    
                    <div style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: #cbd5e1; font-weight: 500;">Select Team Member to Enroll</label>
                        <select name="user_id" class="dark-input" required>
                            <option value="">-- Choose Staff Member --</option>
                            <?php foreach ($staff as $u): ?>
                                <?php 
                                    $displayName = $u['email'];
                                    if (!empty($u['first_name'])) {
                                        $displayName = $u['first_name'] . ' ' . ($u['last_name'] ?? '');
                                    } elseif (!empty($u['name'])) {
                                        $displayName = $u['name'];
                                    }
                                ?>
                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($displayName) ?> (<?= htmlspecialchars($u['email'] ?? 'No Email') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        
                        <?php if(empty($staff)): ?>
                            <div style="margin-top: 15px; padding: 15px; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; color: #fcd34d; font-size: 0.9rem;">
                                <strong><ion-icon name="information-circle"></ion-icon> Notice:</strong><br>
                                All eligible staff members are already enrolled in this course!
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; gap: 15px;">
                        <button type="submit" class="btn-modern" <?= empty($staff) || $available <= 0 ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '' ?>>
                            <ion-icon name="person-add"></ion-icon> Assign Seat
                        </button>
                        <a href="/academy/business" class="btn-modern btn-secondary" style="text-decoration: none;">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
