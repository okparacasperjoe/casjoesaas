<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage List | Casjoe Mail</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php $active = 'mail_audiences'; include dirname(__DIR__) . '/partials/sidebar_mail.php'; ?>

        <main class="main-content">
            <div class="top-bar">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <h1 id="listNameDisplay" style="margin: 0;"><?= htmlspecialchars($list['name']) ?></h1>
                    <button id="editNameBtn" onclick="document.getElementById('editNameForm').style.display='flex'; document.getElementById('listNameDisplay').style.display='none'; this.style.display='none';" class="btn-sm" style="background: none; border: none; color: #64748b; cursor: pointer; padding: 5px; font-size: 1.2rem;">
                        <ion-icon name="pencil-outline"></ion-icon>
                    </button>
                    
                    <form id="editNameForm" action="/mail/lists/update-name" method="POST" style="display: none; align-items: center; gap: 10px; margin: 0;">
                        <input type="hidden" name="list_id" value="<?= $list['id'] ?>">
                        <input type="text" name="name" value="<?= htmlspecialchars($list['name']) ?>" class="form-control" style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1.5rem; font-weight: bold; width: 300px; max-width: 100%;">
                        <button type="submit" class="btn-sm" style="background: #000066; color: white; border-radius: 6px; padding: 8px 15px; border: none; cursor: pointer;">Save</button>
                    </form>
                </div>
                <a href="/mail/lists" class="btn-sm" style="color: var(--text-color);"><ion-icon name="arrow-back-outline"></ion-icon> Back</a>
            </div>
            
            <style>
                .list-layout {
                    display: grid;
                    grid-template-columns: 1fr 2fr;
                    gap: 20px;
                }
                .table-responsive {
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                }
                @media (max-width: 900px) {
                    .list-layout {
                        grid-template-columns: 1fr;
                    }
                    .top-bar {
                        flex-direction: column;
                        align-items: flex-start;
                        gap: 15px;
                    }
                }
            </style>
            
            <div class="list-layout">
                <!-- Add Subscriber Form (Now First) -->
                <div class="card app-card-white" style="padding: 25px; align-items: stretch; text-align: left; height: fit-content; border-top: 4px solid #000066;">
                    <h3 style="margin-bottom: 25px; color: #000066; font-size: 1.2rem; font-weight: 700; border-bottom: 2px solid #f0f4ff; padding-bottom: 10px;">
                        <ion-icon name="person-add-outline" style="vertical-align: middle; margin-right: 8px;"></ion-icon>Add Subscriber
                    </h3>
                    <form action="/mail/lists/add-subscriber" method="POST">
                        <input type="hidden" name="list_id" value="<?= $list['id'] ?>">
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.9rem;">Email Address <span style="color: red;">*</span></label>
                            <input type="email" name="email" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #1e293b; box-sizing: border-box;">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.9rem;">First Name</label>
                            <input type="text" name="first_name" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #1e293b; box-sizing: border-box;">
                        </div>
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.9rem;">Last Name</label>
                            <input type="text" name="last_name" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #1e293b; box-sizing: border-box;">
                        </div>
                        <button type="submit" class="btn" style="width: 100%; padding: 12px; font-size: 1rem; justify-content: center; background: #000066; color: white; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 600;">
                            <ion-icon name="add-circle"></ion-icon> Add Subscriber
                        </button>
                    </form>
                    
                    <div style="margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 25px;">
                        <h4 style="margin-bottom: 15px; color: #000066; font-size: 1.1rem; font-weight: 700;">
                            <ion-icon name="cloud-upload-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon> Bulk Import
                        </h4>
                        <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 15px;">Import many subscribers at once from a CSV file or URL.</p>
                        <a href="/mail/lists/import?list_id=<?= $list['id'] ?>" class="btn" style="width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px; background: #f1f5f9; color: #000066; border: 1px solid #cbd5e1; padding: 12px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
                            <ion-icon name="document-text-outline" style="font-size: 1.2rem;"></ion-icon> Import Options
                        </a>
                    </div>
                </div>

                <!-- Subscribers List (Now Second) -->
                <div class="card app-card-white" style="align-items: stretch; text-align: left;">
                    <h3 style="margin-bottom: 20px;">Subscribers</h3>
                    <?php if (empty($subscribers)): ?>
                        <p style="color: var(--text-muted);">No subscribers yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table style="width: 100%; border-collapse: collapse; min-width: 500px;">
                                <thead>
                                    <tr style="border-bottom: 1px solid var(--glass-border);">
                                        <th style="padding: 10px; text-align: left;">Email</th>
                                        <th style="padding: 10px; text-align: left;">Name</th>
                                        <th style="padding: 10px; text-align: left;">Status</th>
                                        <th style="padding: 10px; text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subscribers as $sub): ?>
                                        <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                            <td style="padding: 10px;"><?= htmlspecialchars($sub['email']) ?></td>
                                            <td style="padding: 10px;"><?= htmlspecialchars($sub['first_name'] . ' ' . $sub['last_name']) ?></td>
                                            <td style="padding: 10px;"><span class="status-badge status-active">Active</span></td>
                                            <td style="padding: 10px; text-align: right;">
                                                <button style="color: red; background: none; border: none; cursor: pointer;">&times;</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
