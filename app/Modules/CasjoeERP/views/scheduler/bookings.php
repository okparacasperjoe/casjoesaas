<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Bookings | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .filter-bar { display: flex; gap: 10px; margin-bottom: 25px; }
        .filter-btn { padding: 8px 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.15); background: transparent; color: rgba(255,255,255,0.7); cursor: pointer; text-decoration: none; font-size: 0.9rem; transition: all 0.2s; }
        .filter-btn:hover, .filter-btn.active { background: #FFA600; color: #000; border-color: #FFA600; font-weight: 600; }
        .status-confirmed { background: rgba(0,200,83,0.15); color: #00c853; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
        .status-cancelled { background: rgba(231,74,59,0.15); color: #e74a3b; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
        .status-completed { background: rgba(78,115,223,0.15); color: #4e73df; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
        table td, table th { color: rgba(255,255,255,0.8); }
        @media (max-width: 768px) {
            .app-container { flex-direction: column; }
            .sidebar { position: fixed; top: 0; left: -100%; height: 100%; z-index: 1000; transition: left 0.3s ease; width: 260px !important; }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 70px !important; }
            .card { overflow-x: auto; }
            table { min-width: 600px; }
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../layout/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2><ion-icon name="list-outline" style="vertical-align: middle;"></ion-icon> All Bookings</h2>
            <a href="/erp/scheduler" class="btn" style="background: rgba(255,255,255,0.1); color: #fff;">← Back</a>
        </div>

        <div class="filter-bar">
            <a href="/erp/scheduler/bookings" class="filter-btn <?= ($filter ?? 'all') === 'all' ? 'active' : '' ?>">All</a>
            <a href="/erp/scheduler/bookings?filter=upcoming" class="filter-btn <?= ($filter ?? '') === 'upcoming' ? 'active' : '' ?>">Upcoming</a>
            <a href="/erp/scheduler/bookings?filter=cancelled" class="filter-btn <?= ($filter ?? '') === 'cancelled' ? 'active' : '' ?>">Cancelled</a>
        </div>

        <div class="card">
            <?php if (empty($bookings)): ?>
                <div style="text-align: center; padding: 40px; color: rgba(255,255,255,0.4);">
                    <ion-icon name="calendar-outline" style="font-size: 3rem;"></ion-icon>
                    <p>No bookings found.</p>
                </div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Meet Link</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($b['guest_name']) ?></strong><br>
                            <small style="color: rgba(255,255,255,0.5);"><?= htmlspecialchars($b['guest_email']) ?></small>
                            <?php if ($b['guest_notes']): ?>
                                <div style="margin-top: 5px; font-size: 0.8rem; color: #FFA600; font-style: italic; max-width: 250px;">
                                    "<?= htmlspecialchars($b['guest_notes']) ?>"
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= date('M j, Y', strtotime($b['booking_date'])) ?><br>
                            <small><?= date('g:i A', strtotime($b['start_time'])) ?> — <?= date('g:i A', strtotime($b['end_time'])) ?></small>
                        </td>
                        <td><span class="status-<?= $b['status'] ?>"><?= ucfirst($b['status']) ?></span></td>
                        <td>
                            <?php if ($b['meet_link']): ?>
                                <a href="<?= htmlspecialchars($b['meet_link']) ?>" target="_blank" style="color: #4285f4; text-decoration: none;">
                                    <ion-icon name="videocam-outline" style="vertical-align: middle;"></ion-icon> Join
                                </a>
                            <?php else: ?>
                                <span style="color: rgba(255,255,255,0.3);">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($b['status'] === 'confirmed'): ?>
                            <form method="POST" action="/erp/scheduler/cancel" id="cancelForm_<?= $b['id'] ?>" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                <input type="hidden" name="cancel_reason" id="cancelReason_<?= $b['id'] ?>">
                                <button type="button" onclick="cancelWithReason(<?= $b['id'] ?>)" style="background: rgba(231,74,59,0.15); color: #e74a3b; border: none; padding: 5px 12px; border-radius: 6px; cursor: pointer; font-size: 0.85rem;">Cancel</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Cancellation Modal -->
<div id="cancelModal" class="modal-overlay">
    <div class="modal-content">
        <h3><ion-icon name="close-circle-outline"></ion-icon> Cancel Meeting</h3>
        <p>Please provide a reason for cancelling this meeting. This will be sent to the guest.</p>
        
        <form id="modalCancelForm" method="POST" action="/erp/scheduler/cancel">
            <input type="hidden" name="id" id="modalBookingId">
            <div class="form-group">
                <textarea name="cancel_reason" id="modalCancelReason" placeholder="E.g., I have an urgent conflict..." rows="4"></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeCancelModal()">Go Back</button>
                <button type="submit" class="btn-danger">Confirm Cancellation</button>
            </div>
        </form>
    </div>
</div>

<style>
.modal-overlay {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.85);
    backdrop-filter: blur(8px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    animation: fadeIn 0.3s ease;
}
.modal-content {
    background: linear-gradient(145deg, #1a1a2e, #16213e);
    border: 1px solid rgba(255,166,0,0.3);
    border-radius: 24px;
    padding: 35px;
    width: 100%;
    max-width: 450px;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5), 0 0 30px rgba(255,166,0,0.1);
    transform: scale(0.95);
    transition: transform 0.3s ease;
}
.modal-overlay.active { display: flex; }
.modal-overlay.active .modal-content { transform: scale(1); }

.modal-content h3 {
    margin-top: 0;
    font-size: 1.5rem;
    color: #FFA600;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}
.modal-content p {
    color: rgba(255,255,255,0.6);
    font-size: 0.95rem;
    margin-bottom: 25px;
    line-height: 1.5;
}
.modal-content textarea {
    width: 100%;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 15px;
    color: #fff;
    font-family: inherit;
    font-size: 1rem;
    resize: none;
    transition: all 0.2s;
}
.modal-content textarea:focus {
    outline: none;
    border-color: #FFA600;
    background: rgba(255,255,255,0.08);
}
.modal-actions {
    display: flex;
    gap: 15px;
    margin-top: 25px;
}
.modal-actions button {
    flex: 1;
    padding: 12px;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    font-size: 0.95rem;
    transition: all 0.2s;
}
.btn-secondary {
    background: rgba(255,255,255,0.1);
    color: #fff;
    border: none;
}
.btn-secondary:hover { background: rgba(255,255,255,0.15); }
.btn-danger {
    background: #e74a3b;
    color: #fff;
    border: none;
    box-shadow: 0 4px 15px rgba(231,74,59,0.3);
}
.btn-danger:hover { background: #d63c2e; transform: translateY(-2px); }

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>

<script>
function cancelWithReason(id) {
    document.getElementById('modalBookingId').value = id;
    document.getElementById('cancelModal').classList.add('active');
    document.getElementById('modalCancelReason').focus();
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.remove('active');
}

// Close on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeCancelModal();
});

// Close on outside click
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});
</script>
</body>
</html>
