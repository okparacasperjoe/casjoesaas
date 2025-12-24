<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket #<?= $id ?> | Casjoe Apps</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .chat-box {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 30px;
        }
        .message {
            max-width: 80%;
            padding: 15px 20px;
            border-radius: 15px;
            line-height: 1.5;
            position: relative;
        }
        .message.user {
            align-self: flex-end;
            background: var(--primary);
            color: white;
            border-bottom-right-radius: 2px;
        }
        .message.staff {
            align-self: flex-start;
            background: #f1f5f9;
            color: #333;
            border-bottom-left-radius: 2px;
        }
        .meta {
            font-size: 0.75rem;
            margin-top: 5px;
            opacity: 0.8;
            text-align: right;
        }
        .staff .meta { text-align: left; }
    </style>
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="brand">
             <img src="/assets/casjoe_logo.png" alt="Casjoe Apps" style="height: 40px;">
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="/support" class="nav-link"><ion-icon name="arrow-back-outline"></ion-icon> Back to List</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2><?= htmlspecialchars($ticket['subject']) ?> <span style="font-size: 1rem; color: #666;">#<?= $ticket['id'] ?></span></h2>
            <div style="font-size: 0.9rem; padding: 5px 15px; background: #eee; border-radius: 20px;">
                Status: <strong><?= ucfirst($ticket['status']) ?></strong>
            </div>
        </div>

        <div class="card" style="margin-bottom: 100px;"> <!-- Padding for bottom input -->
            <div class="chat-box">
                <?php foreach ($messages as $msg): ?>
                    <div class="message <?= $msg['is_staff'] ? 'staff' : 'user' ?>">
                        <div class="content"><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                        <div class="meta">
                            <?= $msg['is_staff'] ? 'Support Staff' : 'You' ?> • <?= date('M j, g:i a', strtotime($msg['created_at'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

            <form method="POST">
                <div class="form-group">
                    <label>Reply</label>
                    <textarea name="message" class="form-control" rows="4" required placeholder="Type your reply here..."></textarea>
                </div>
                <button type="submit" class="btn">Send Reply</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
