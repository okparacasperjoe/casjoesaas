<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sequence Enrollments | Casjoe Mail</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php $active = 'sequences'; include __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <h1><?= htmlspecialchars($sequence['name']) ?> - Enrollments</h1>
                <p style="color: #666; margin-top: 5px;">Manage subscribers enrolled in this sequence</p>
            </div>
            <a href="/mail/sequences/edit?id=<?= $sequence['id'] ?>" class="btn" style="background: transparent; border: 1px solid #ccc; color: #333;">
                <ion-icon name="arrow-back-outline"></ion-icon> Back to Sequence
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <?php if (empty($enrollments)): ?>
                <div style="text-align: center; padding: 60px 40px; color: #666;">
                    <div style="background: #f0f4ff; width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <ion-icon name="people-outline" style="font-size: 48px; color: #000066;"></ion-icon>
                    </div>
                    <h3 style="color: #333; margin-bottom: 10px;">No Enrollments Yet</h3>
                    <p>Go to Subscribers and enroll contacts in this sequence.</p>
                </div>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #eee; text-align: left;">
                            <th style="padding: 12px;">Subscriber</th>
                            <th style="padding: 12px;">Status</th>
                            <th style="padding: 12px;">Current Step</th>
                            <th style="padding: 12px;">Enrolled</th>
                            <th style="padding: 12px;">Next Send</th>
                            <th style="padding: 12px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrollments as $enrollment): ?>
                            <?php
                            $statusColors = [
                                'active' => ['bg' => '#d4edda', 'text' => '#155724'],
                                'paused' => ['bg' => '#fff3cd', 'text' => '#856404'],
                                'completed' => ['bg' => '#d1ecf1', 'text' => '#0c5460'],
                                'stopped' => ['bg' => '#f8d7da', 'text' => '#721c24']
                            ];
                            $color = $statusColors[$enrollment['status']] ?? $statusColors['active'];
                            ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 15px;">
                                    <div style="font-weight: 600; color: #1a1a1a;">
                                        <?= htmlspecialchars($enrollment['first_name'] . ' ' . $enrollment['last_name']) ?>
                                    </div>
                                    <div style="color: #666; font-size: 0.9rem;">
                                        <?= htmlspecialchars($enrollment['email']) ?>
                                    </div>
                                </td>
                                <td style="padding: 15px;">
                                    <span style="background: <?= $color['bg'] ?>; color: <?= $color['text'] ?>; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                                        <?= ucfirst($enrollment['status']) ?>
                                    </span>
                                    <?php if ($enrollment['stop_reason']): ?>
                                        <div style="font-size: 0.75rem; color: #666; margin-top: 4px;">
                                            <?= ucfirst(str_replace('_', ' ', $enrollment['stop_reason'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 15px; color: #1a1a1a;">
                                    Step <?= $enrollment['current_step'] + 1 ?>
                                    <?php if ($enrollment['current_step_subject']): ?>
                                        <div style="font-size: 0.85rem; color: #666;">
                                            <?= htmlspecialchars($enrollment['current_step_subject']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 15px; color: #1a1a1a;">
                                    <?= date('M d, Y', strtotime($enrollment['enrolled_at'])) ?>
                                </td>
                                <td style="padding: 15px; color: #1a1a1a;">
                                    <?= $enrollment['next_send_at'] ? date('M d, Y H:i', strtotime($enrollment['next_send_at'])) : 'N/A' ?>
                                </td>
                                <td style="padding: 15px; text-align: right;">
                                    <div style="display: flex; justify-content: flex-end; gap: 5px;">
                                        <?php if ($enrollment['status'] == 'active'): ?>
                                            <form action="/mail/sequences/enrollments/pause" method="POST" style="display: inline;">
                                                <input type="hidden" name="enrollment_id" value="<?= $enrollment['id'] ?>">
                                                <button type="submit" class="btn-sm" style="background: #ffc107; color: #333; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">
                                                    <ion-icon name="pause-outline"></ion-icon>
                                                </button>
                                            </form>
                                        <?php elseif ($enrollment['status'] == 'paused'): ?>
                                            <form action="/mail/sequences/enrollments/resume" method="POST" style="display: inline;">
                                                <input type="hidden" name="enrollment_id" value="<?= $enrollment['id'] ?>">
                                                <button type="submit" class="btn-sm" style="background: #28a745; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">
                                                    <ion-icon name="play-outline"></ion-icon>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form action="/mail/sequences/unenroll" method="POST" style="display: inline;" onsubmit="return confirm('Unenroll this subscriber?');">
                                            <input type="hidden" name="enrollment_id" value="<?= $enrollment['id'] ?>">
                                            <button type="submit" class="btn-sm" style="background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">
                                                <ion-icon name="close-outline"></ion-icon>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</div>

<style>
    table th, table td { color: #1a1a1a !important; }
    
    .sidebar .nav-link { color: rgba(255, 255, 255, 0.8) !important; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { 
        color: #ffffff !important; 
        background: rgba(255, 255, 255, 0.1); 
    }
    .sidebar .nav-link ion-icon { color: inherit !important; }
</style>
</body>
</html>
