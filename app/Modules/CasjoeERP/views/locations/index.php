<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Locations | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .location-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 20px 24px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .location-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); transform: translateY(-1px); }
        .location-icon {
            width: 48px; height: 48px;
            background: linear-gradient(135deg, #000066, #0000aa);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.4rem; flex-shrink: 0;
        }
        .location-info { flex: 1; }
        .location-name { font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0 0 3px; }
        .location-meta { font-size: 0.85rem; color: #64748b; margin: 0; }
        .status-badge {
            padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600;
        }
        .status-active { background: #dcfce7; color: #166534; }
        .status-inactive { background: #fee2e2; color: #991b1b; }
        .action-btns { display: flex; gap: 8px; align-items: center; }
        .btn-icon {
            width: 34px; height: 34px; border-radius: 8px; display: flex;
            align-items: center; justify-content: center; cursor: pointer;
            border: 1px solid #e2e8f0; background: #fff; color: #64748b;
            text-decoration: none; transition: all 0.2s;
        }
        .btn-icon:hover { background: #f1f5f9; color: #1e293b; }
        .btn-icon.danger:hover { background: #fee2e2; color: #ef4444; border-color: #fca5a5; }

        .staff-table { width: 100%; border-collapse: collapse; }
        .staff-table th { text-align: left; font-size: 0.8rem; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.05em; padding: 10px 16px; border-bottom: 1px solid #f1f5f9; }
        .staff-table td { padding: 12px 16px; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
        .staff-table tr:last-child td { border-bottom: none; }
        .staff-avatar { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #000066, #0066cc); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; flex-shrink: 0; }
        .assign-select { font-size: 0.85rem; padding: 5px 10px; border: 1px solid #e2e8f0; border-radius: 6px; background: #fff; cursor: pointer; }

        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
        .empty-state { text-align: center; padding: 60px 20px; color: #94a3b8; }
        .empty-state ion-icon { font-size: 3.5rem; margin-bottom: 12px; color: #cbd5e1; display: block; }
    </style>
</head>
<body>
<?php require __DIR__ . '/../layout/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2><ion-icon name="location-outline" style="vertical-align: middle; margin-right: 6px;"></ion-icon> Business Locations</h2>
            <div class="user-menu">
                <a href="/erp/locations/create" class="btn btn-primary">
                    <ion-icon name="add-outline"></ion-icon> Add Location
                </a>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
        <div class="alert-success">
            <ion-icon name="checkmark-circle-outline"></ion-icon>
            <?php
            $msg = ['created' => 'Location created successfully.', 'updated' => 'Location updated.', 'deleted' => 'Location deleted.', 'assigned' => 'Staff assignment updated.'];
            echo $msg[$_GET['success']] ?? 'Done.';
            ?>
        </div>
        <?php endif; ?>

        <!-- Locations List -->
        <div style="margin-bottom: 28px;">
            <h3 style="font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8; margin-bottom: 14px;">Your Branches</h3>

            <?php if (empty($locations)): ?>
            <div class="empty-state">
                <ion-icon name="business-outline"></ion-icon>
                <h4 style="color: #64748b; margin-bottom: 6px;">No locations yet</h4>
                <p style="margin-bottom: 20px; font-size: 0.9rem;">Add your first business location to start managing branch-level inventory.</p>
                <a href="/erp/locations/create" class="btn btn-primary">+ Add First Location</a>
            </div>
            <?php else: foreach ($locations as $loc): ?>
            <div class="location-card">
                <div class="location-icon"><ion-icon name="business-outline"></ion-icon></div>
                <div class="location-info">
                    <p class="location-name"><?= htmlspecialchars($loc['name']) ?></p>
                    <p class="location-meta">
                        <?php if ($loc['address']): ?>
                        <ion-icon name="map-outline" style="vertical-align: middle; font-size: 0.9rem;"></ion-icon>
                        <?= htmlspecialchars($loc['address']) ?> &nbsp;·&nbsp;
                        <?php endif; ?>
                        <ion-icon name="people-outline" style="vertical-align: middle; font-size: 0.9rem;"></ion-icon>
                        <?= (int)$loc['staff_count'] ?> staff
                        <?php if ($loc['phone']): ?>
                        &nbsp;·&nbsp; <ion-icon name="call-outline" style="vertical-align: middle; font-size: 0.9rem;"></ion-icon>
                        <?= htmlspecialchars($loc['phone']) ?>
                        <?php endif; ?>
                    </p>
                </div>
                <span class="status-badge status-<?= $loc['status'] ?>"><?= ucfirst($loc['status']) ?></span>
                <div class="action-btns">
                    <a href="/erp/inventory?location_id=<?= $loc['id'] ?>" class="btn-icon" title="View Inventory">
                        <ion-icon name="cube-outline"></ion-icon>
                    </a>
                    <a href="/erp/locations/edit?id=<?= $loc['id'] ?>" class="btn-icon" title="Edit">
                        <ion-icon name="create-outline"></ion-icon>
                    </a>
                    <form action="/erp/locations/delete" method="POST" style="margin:0;" onsubmit="return confirm('Delete this location? Staff and inventory will be unassigned.');">
                        <input type="hidden" name="id" value="<?= $loc['id'] ?>">
                        <button type="submit" class="btn-icon danger" title="Delete">
                            <ion-icon name="trash-outline"></ion-icon>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>

        <!-- Staff Assignment Table -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div style="padding: 20px 24px 14px; border-bottom: 1px solid #f1f5f9;">
                <h3 style="margin: 0; font-size: 1rem; font-weight: 700; color: #1e293b;">
                    <ion-icon name="people-outline" style="vertical-align: middle; margin-right: 6px;"></ion-icon>
                    Staff Location Assignment
                </h3>
                <p style="margin: 4px 0 0; font-size: 0.85rem; color: #94a3b8;">Each staff member can be assigned to exactly one location.</p>
            </div>

            <?php if (empty($staff)): ?>
            <div class="empty-state">
                <ion-icon name="person-outline"></ion-icon>
                <p>No staff members found. Add users to your team first.</p>
            </div>
            <?php else: ?>
            <table class="staff-table">
                <thead>
                    <tr>
                        <th>Staff Member</th>
                        <th>Role</th>
                        <th>Assigned Location</th>
                        <th>Change Assignment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staff as $member): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="staff-avatar"><?= strtoupper(substr($member['name'] ?? $member['email'], 0, 1)) ?></div>
                                <div>
                                    <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($member['name'] ?? '—') ?></div>
                                    <div style="font-size: 0.8rem; color: #94a3b8;"><?= htmlspecialchars($member['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span style="background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;"><?= htmlspecialchars($member['role']) ?></span></td>
                        <td>
                            <?php if ($member['location_name']): ?>
                            <span style="color: #1e293b; font-weight: 500;"><?= htmlspecialchars($member['location_name']) ?></span>
                            <?php else: ?>
                            <span style="color: #94a3b8; font-style: italic;">Not assigned</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="/erp/locations/assign-staff" method="POST" style="display: flex; gap: 8px; align-items: center;">
                                <input type="hidden" name="user_id" value="<?= $member['id'] ?>">
                                <select name="location_id" class="assign-select" id="assign-<?= $member['id'] ?>">
                                    <option value="">— No Location —</option>
                                    <?php foreach ($locations as $loc): ?>
                                    <option value="<?= $loc['id'] ?>" <?= ($member['location_id'] == $loc['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($loc['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary" style="padding: 6px 14px; font-size: 0.82rem;">Save</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
