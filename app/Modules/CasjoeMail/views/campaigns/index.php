<?php $active = 'mail_campaigns'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Campaigns | Casjoe Mail</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/mail_app.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar_mail.php'; ?>

        <main class="erp-main">
            <!-- Mobile Nav -->
            <?php include __DIR__ . '/../../../../Core/Views/partials/mobile_nav.php'; ?>

            <div class="erp-hero">
                <div class="erp-hero-top">
                    <div>
                        <h1>Email Campaigns</h1>
                        <p class="subtitle">Draft, send, and monitor your email marketing campaigns.</p>
                    </div>
                    <a href="/mail/campaigns/create" class="hero-btn">
                        <ion-icon name="add-outline"></ion-icon> Create Campaign
                    </a>
                </div>
            </div>

            <div class="erp-content">
                <div class="section-header">
                    <ion-icon name="paper-plane-outline"></ion-icon> All Campaigns
                </div>

                <div class="module-card" style="padding: 0; min-height: 200px;">
                    <?php if (empty($campaigns)): ?>
                        <div style="text-align: center; padding: 40px; color: var(--erp-text-muted);">
                            <ion-icon name="mail-open-outline" style="font-size: 3rem; margin-bottom: 10px;"></ion-icon>
                            <p style="margin-bottom:20px; color:white;">No campaigns found. Start by creating your first email campaign!</p>
                            <a href="/mail/campaigns/create" class="hero-btn">Create Campaign</a>
                        </div>
                    <?php else: ?>
                        <table class="app-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Sent To</th>
                                    <th>Date</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($campaigns as $campaign): ?>
                                    <tr>
                                        <td style="font-weight: 500; color: white;"><?= htmlspecialchars($campaign['name']) ?></td>
                                        <td style="color: var(--erp-text-muted);"><?= htmlspecialchars($campaign['subject']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $campaign['status'] == 'sent' ? 'active' : 'pending' ?>">
                                                <?= ucfirst($campaign['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= $campaign['sent_count'] ?? 0 ?></td>
                                        <td style="font-size: 0.9rem;"><?= date('M d, Y', strtotime($campaign['created_at'])) ?></td>
                                        <td style="text-align: right; display: flex; justify-content: flex-end; gap: 10px; align-items:center;">
                                            <?php if ($campaign['status'] == 'draft'): ?>
                                                <form action="/mail/campaigns/send" method="POST" style="display:inline;" onsubmit="return confirm('Send this campaign to all subscribers?');">
                                                    <input type="hidden" name="id" value="<?= $campaign['id'] ?>">
                                                    <button type="submit" class="btn-icon" style="color:var(--erp-gold);" title="Send Now"><ion-icon name="paper-plane-outline"></ion-icon></button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <a href="/mail/campaigns/edit?id=<?= $campaign['id'] ?>" class="btn-icon" title="Edit"><ion-icon name="create-outline"></ion-icon></a>
                                            
                                            <form action="/mail/campaigns/delete" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                                <input type="hidden" name="id" value="<?= $campaign['id'] ?>">
                                                <button type="submit" class="btn-icon danger" title="Delete"><ion-icon name="trash-outline"></ion-icon></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
