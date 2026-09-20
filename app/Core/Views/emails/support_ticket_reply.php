<?php
/**
 * Admin Reply to Support Ticket - User Notification
 * Variables: $ticketId, $subject, $adminReply, $userName
 */
?>
<h1>Reply to Your Support Ticket #<?= $ticketId ?></h1>

<p>Hello <?= htmlspecialchars($userName) ?>,</p>

<p>Our support team has replied to your ticket.</p>

<div class="highlight-box">
    <strong>Ticket #<?= $ticketId ?></strong><br>
    <strong>Subject:</strong> <?= htmlspecialchars($subject) ?>
</div>

<p><strong>Admin Response:</strong></p>
<div style="background: #f0fdf4; padding: 20px; border-radius: 8px; border-left: 3px solid #10b981;">
    <?= nl2br(htmlspecialchars($adminReply)) ?>
</div>

<p style="text-align: center;">
    <a href="<?= APP_URL ?>/support/view/<?= $ticketId ?>" class="btn">View Full Conversation</a>
</p>

<p>If you have any further questions, please reply to your ticket through the portal.</p>

<p>Best regards,<br>
<strong>Casjoe Support Team</strong></p>
