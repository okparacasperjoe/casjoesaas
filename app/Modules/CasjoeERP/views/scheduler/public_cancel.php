<?php
/**
 * @var array $booking
 * @var string $slug
 * @var string $token
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Booking | Casjoe Scheduler</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #0a0a1a; color: #fff; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .cancel-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 40px; max-width: 520px; width: 100%; animation: fadeIn 0.5s ease; }
        h1 { font-size: 1.8rem; font-weight: 800; margin-bottom: 15px; text-align: center; color: #ff6b6b; }
        p { color: rgba(255,255,255,0.6); line-height: 1.6; margin-bottom: 25px; text-align: center; }
        .booking-summary { background: rgba(255,255,255,0.06); border-radius: 12px; padding: 20px; margin-bottom: 25px; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; }
        .summary-item:last-child { margin-bottom: 0; }
        .label { color: rgba(255,255,255,0.4); }
        .value { font-weight: 600; }
        .form-group { margin-bottom: 25px; }
        label { display: block; margin-bottom: 10px; font-weight: 600; color: #fff; font-size: 0.95rem; }
        textarea { width: 100%; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 10px; padding: 15px; color: #fff; font-family: inherit; font-size: 1rem; resize: none; transition: all 0.2s; }
        textarea:focus { outline: none; border-color: #ff6b6b; background: rgba(255,255,255,0.12); }
        .btn-cancel { width: 100%; background: #e74a3b; color: #fff; border: none; padding: 14px; border-radius: 10px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: all 0.2s; }
        .btn-cancel:hover { background: #d63c2e; transform: translateY(-2px); }
        .back-link { display: block; text-align: center; margin-top: 20px; color: rgba(255,255,255,0.4); text-decoration: none; font-size: 0.9rem; }
        .back-link:hover { color: #fff; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="cancel-card">
        <h1>Cancel Meeting?</h1>
        <p>Are you sure you want to cancel this meeting? Please let the host know why.</p>

        <div class="booking-summary">
            <div class="summary-item">
                <span class="label">Meeting:</span>
                <span class="value"><?= htmlspecialchars($booking['title'] ?? '') ?></span>
            </div>
            <div class="summary-item">
                <span class="label">Host:</span>
                <span class="value"><?= htmlspecialchars($booking['host_name'] ?? '') ?></span>
            </div>
            <div class="summary-item">
                <span class="label">Date:</span>
                <span class="value"><?= date('l, M j, Y', strtotime($booking['booking_date'] ?? 'now')) ?></span>
            </div>
            <div class="summary-item">
                <span class="label">Time:</span>
                <span class="value"><?= date('g:i A', strtotime($booking['start_time'] ?? 'now')) ?></span>
            </div>
        </div>

        <form method="POST" action="/book/<?= htmlspecialchars($slug ?? '') ?>/cancel?token=<?= htmlspecialchars($token ?? '') ?>">
            <div class="form-group">
                <label for="cancel_reason">Reason for cancellation (optional)</label>
                <textarea id="cancel_reason" name="cancel_reason" rows="4" placeholder="E.g., I have a scheduling conflict..."></textarea>
            </div>
            <button type="submit" class="btn-cancel">Confirm Cancellation</button>
        </form>

        <a href="javascript:history.back()" class="back-link">Nevermind, keep my booking</a>
    </div>
</body>
</html>
