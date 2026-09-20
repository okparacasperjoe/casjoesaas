<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($cancelled) ? 'Booking Cancelled' : 'Booking Confirmed!' ?></title>
    <link rel="icon" type="image/png" href="/assets/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-page: #0a0a1a;
            --text-main: #ffffff;
            --text-muted: rgba(255,255,255,0.6);
            --card-bg: rgba(255,255,255,0.04);
            --card-border: rgba(255,255,255,0.08);
            --box-bg: rgba(255,255,255,0.06);
            --box-border: rgba(255,255,255,0.1);
        }
        html.light-theme {
            --bg-page: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --box-bg: #f1f5f9;
            --box-border: #cbd5e1;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-page); color: var(--text-main); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; transition: background 0.3s ease, color 0.3s ease; }
        .confirm-card { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 20px; padding: 40px; max-width: 520px; width: 100%; text-align: center; animation: fadeIn 0.5s ease; box-shadow: 0 4px 25px rgba(0,0,0,0.08); }
        .icon { font-size: 4rem; margin-bottom: 20px; }
        .confirm-card h1 { font-size: 1.8rem; font-weight: 800; margin-bottom: 10px; }
        .confirm-card h1.success { background: linear-gradient(135deg, #00c853, #69f0ae); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .confirm-card h1.cancel { background: linear-gradient(135deg, #e74a3b, #ff6b6b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .confirm-card p { color: var(--text-muted); line-height: 1.6; }
        .details-box { background: var(--box-bg); border: 1px solid var(--box-border); border-radius: 12px; padding: 20px; margin: 25px 0; text-align: left; }
        .details-box .row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
        html.light-theme .details-box .row { border-bottom: 1px solid rgba(0,0,0,0.05); }
        .details-box .row:last-child { border-bottom: none; }
        .details-box .label { color: var(--text-muted); font-size: 0.9rem; }
        .details-box .value { color: var(--text-main); font-weight: 600; font-size: 0.9rem; }
        .meet-link-box { background: rgba(66,133,244,0.08); border: 1px solid rgba(66,133,244,0.2); border-radius: 12px; padding: 15px 20px; margin: 20px 0; }
        .meet-link-box a { color: #4285f4; font-weight: 600; text-decoration: none; word-break: break-all; }
        .action-btns { display: flex; gap: 12px; margin-top: 25px; justify-content: center; flex-wrap: wrap; }
        .action-btn { display: inline-block; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 0.95rem; transition: all 0.2s; }
        .btn-gcal { background: #4285f4; color: #fff; }
        .btn-gcal:hover { background: #3367d6; transform: translateY(-2px); }
        .btn-done { background: rgba(255,255,255,0.1); color: var(--text-main); border: 1px solid var(--card-border); }
        .btn-done:hover { background: rgba(255,255,255,0.15); }
        .check-email { color: var(--text-muted); font-size: 0.85rem; margin-top: 20px; }
        .powered-by { text-align: center; margin-top: 30px; color: var(--text-muted); font-size: 0.8rem; opacity: 0.7; }
        .powered-by a { color: #FFA600; text-decoration: none; font-weight: 600; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div>
        <?php if (isset($cancelled)): ?>
            <div class="confirm-card">
                <div class="icon">❌</div>
                <h1 class="cancel">Booking Cancelled</h1>
                <p>This booking has been cancelled. If this was a mistake, please contact the host to rebook.</p>
            </div>
        <?php else: ?>
            <div class="confirm-card">
                <div class="icon">🎉</div>
                <h1 class="success">You're Booked!</h1>
                <p>A confirmation email has been sent to <strong><?= htmlspecialchars($booking['guest_name'] ?? '') ?></strong>.</p>

                <div class="details-box">
                    <div class="row">
                        <span class="label">Meeting</span>
                        <span class="value"><?= htmlspecialchars($booking['title'] ?? '') ?></span>
                    </div>
                    <div class="row">
                        <span class="label">Host</span>
                        <span class="value"><?= htmlspecialchars($booking['host_name'] ?? '') ?></span>
                    </div>
                    <div class="row">
                        <span class="label">Date</span>
                        <span class="value"><?= date('l, F j, Y', strtotime($booking['booking_date'])) ?></span>
                    </div>
                    <div class="row">
                        <span class="label">Time</span>
                        <span class="value"><?= date('g:i A', strtotime($booking['start_time'])) ?> — <?= date('g:i A', strtotime($booking['end_time'])) ?></span>
                    </div>
                    <div class="row">
                        <span class="label">Duration</span>
                        <span class="value"><?= $booking['duration'] ?? 30 ?> minutes</span>
                    </div>
                </div>

                <?php if (!empty($booking['meet_link'])): ?>
                <div class="meet-link-box">
                    <p style="font-size: 0.85rem; color: rgba(255,255,255,0.5); margin-bottom: 8px;">📹 Google Meet Link</p>
                    <a href="<?= htmlspecialchars($booking['meet_link']) ?>" target="_blank"><?= htmlspecialchars($booking['meet_link']) ?></a>
                </div>
                <?php endif; ?>

                <div class="action-btns">
                    <?php if (!empty($booking['gcal_link'])): ?>
                    <a href="<?= htmlspecialchars($booking['gcal_link']) ?>" target="_blank" class="action-btn btn-gcal">
                        📅 Add to Google Calendar
                    </a>
                    <?php endif; ?>
                    <a href="/" class="action-btn btn-done">Done</a>
                </div>

                <p class="check-email">📧 Check your email for a confirmation with all the details.</p>
            </div>
        <?php endif; ?>
        <div class="powered-by">Powered by <a href="/">Casjoe LLC</a></div>
    </div>
</body>
</html>