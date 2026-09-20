<?php
/**
 * New Support Ticket Created - Admin Notification
 * Variables: $ticketId, $subject, $priority, $userName, $userEmail, $message
 */
?>
<h1>New Support Ticket #<?= $ticketId ?></h1>

<p>Hello Admin,</p>

<p>A new support ticket has been created and requires your attention.</p>

<div class="highlight-box">
    <strong>Ticket #<?= $ticketId ?></strong><br>
    <strong>Subject:</strong> <?= htmlspecialchars($subject) ?><br>
    <strong>Priority:</strong> <span style="color: <?= $priority === 'high' ? '#ef4444' : ($priority === 'medium' ? '#f59e0b' : '#6b7280') ?>; font-weight: 600; text-transform: uppercase;"><?= $priority ?></span><br>
    <strong>From:</strong> <?= htmlspecialchars($userName) ?> (<?= htmlspecialchars($userEmail) ?>)
</div>

<p><strong>Message:</strong></p>
<div style="background: #f9fafb; padding: 20px; border-radius: 8px; border-left: 3px solid #000066;">
    <?= nl2br(htmlspecialchars($message)) ?>
</div>

<p style="text-align: center;">
    <a href="<?= APP_URL ?>/casper-joe/support/<?= $ticketId ?>" class="btn">View & Reply to Ticket</a>
</p>

<p>Please respond to this ticket as soon as possible.</p>

<p>Best regards,<br>
<strong>Casjoe Support System</strong></p>
