<?php
// CASJOE MAIL - CAMPAIGN REVIEW & SEND
$active = 'campaigns'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Review Campaign | Casjoe</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .review-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 20px;
            margin-top: 20px;
        }
        .review-panel {
            background: white;
            border-radius: 12px;
            padding: 30px;
            border: 1px solid #e2e8f0;
        }
        .settings-panel {
            background: #f8fafc;
            border-radius: 12px;
            padding: 25px;
            border: 1px solid #e2e8f0;
        }
        .info-row {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-size: 0.85rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 5px;
            display: block;
        }
        .info-value {
            font-size: 1.1rem;
            color: #000066;
            font-weight: 600;
        }
        .preview-box {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            background: white;
            height: 400px;
            overflow-y: auto;
            margin-top: 10px;
        }
        .form-group label {
            color: #1e293b;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            background: white;
            color: #1e293b;
            font-size: 1rem;
        }
        .send-btn {
            width: 100%;
            padding: 15px;
            background: #000066;
            color: #FFA600;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .send-btn:hover {
            background: #000044;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require dirname(__DIR__) . '/partials/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Review &amp; Send Campaign</h2>
            <a href="/mail/campaigns/edit?id=<?= $campaign['id'] ?>" class="btn btn-secondary">Edit Content</a>
        </div>

        <div class="review-container">
            <div class="review-panel">
                <div class="info-row">
                    <span class="info-label">Campaign Name</span>
                    <span class="info-value"><?= htmlspecialchars($campaign['name']) ?></span>
                </div>
                
                <div class="info-row" style="border-bottom: none; padding-bottom: 0;">
                    <span class="info-label">Subject Line</span>
                    <span class="info-value"><?= htmlspecialchars($campaign['subject']) ?></span>
                </div>

                <div class="info-label" style="margin-top: 20px;">Email Preview</div>
                <div class="preview-box">
                    <?= $campaign['content'] ?>
                </div>
            </div>

            <div class="settings-panel">
                <h3 style="color: #000066; margin-top: 0; margin-bottom: 20px;">Send Options</h3>
                
                <form action="/mail/campaigns/send" method="POST" id="sendForm">
                    <input type="hidden" name="id" value="<?= $campaign['id'] ?>">
                    
                    <div class="form-group">
                        <label>Select Recipients List</label>
                        <select name="list_id" class="form-control" required id="listSelect">
                            <option value="">-- Choose a List --</option>
                            <?php foreach ($lists as $list): ?>
                                <option value="<?= $list['id'] ?>">
                                    <?= htmlspecialchars($list['name']) ?> (<?= $list['sub_count'] ?> subscribers)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="send-btn" id="sendBtn">
                        <ion-icon name="paper-plane"></ion-icon> Send Campaign Now
                    </button>
                </form>
            </div>
        </div>

    </main>
</div>

<script>
document.getElementById('sendForm').addEventListener('submit', function(e) {
    const listId = document.getElementById('listSelect').value;
    if (!listId) {
        e.preventDefault();
        alert('Please select a recipient list.');
        return;
    }
    
    if (!confirm('Are you sure you want to send this campaign now? This action cannot be undone.')) {
        e.preventDefault();
        return;
    }
    
    const btn = document.getElementById('sendBtn');
    btn.innerHTML = '<ion-icon name="sync" class="spin"></ion-icon> Sending...';
    btn.disabled = true;
    btn.style.opacity = '0.7';
});
</script>
</body>
</html>
