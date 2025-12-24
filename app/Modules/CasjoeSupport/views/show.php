<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?= $ticket['id'] ?> | Support</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        .message-bubble {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            max-width: 80%;
        }

        .message-user {
            background: rgba(255, 255, 255, 0.05);
            margin-left: auto;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .message-staff {
            background: rgba(0, 0, 102, 0.4);
            margin-right: auto;
            border: 1px solid #000066;
        }

        .message-meta {
            font-size: 0.8rem;
            opacity: 0.6;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand">
                <ion-icon name="help-buoy"></ion-icon>
                Casjoe<span>Support</span>
            </div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="/" class="nav-link"><ion-icon name="apps-outline"></ion-icon>Back to
                        Apps</a></li>
                <li class="nav-item"><a href="/support" class="nav-link active"><ion-icon
                            name="list-outline"></ion-icon>My Tickets</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <h1>Ticket #<?= $ticket['id'] ?></h1>
                <span class="status-badge status-<?= $ticket['status'] ?>"><?= ucfirst($ticket['status']) ?></span>
            </div>

            <div class="card" style="margin-bottom: 20px;">
                <h2><?= htmlspecialchars($ticket['subject']) ?></h2>
            </div>

            <div style="margin-bottom: 30px; display: flex; flex-direction: column;">
                <?php foreach ($messages as $msg): ?>
                    <div class="message-bubble <?= $msg['is_staff'] ? 'message-staff' : 'message-user' ?>">
                        <div class="message-meta">
                            <?= $msg['is_staff'] ? 'Support Agent' : 'You' ?> &bull;
                            <?= date('M d, H:i', strtotime($msg['created_at'])) ?>
                        </div>
                        <div><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($ticket['status'] != 'closed'): ?>
                <div class="card">
                    <h3>Reply</h3>
                    <form method="POST" action="/support/reply">
                        <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                        <textarea name="message" rows="4" required
                            style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 15px; font-family: inherit;"></textarea>
                        <button type="submit" class="btn">Send Reply</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="card" style="text-align: center; opacity: 0.7;">
                    This ticket is closed. Please open a new ticket if you need further assistance.
                </div>
            <?php endif; ?>

        </main>
    </div>
</body>

</html>