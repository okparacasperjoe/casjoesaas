<h1>Welcome to the Team! 👋</h1>

<p>Hi <strong><?= htmlspecialchars($employeeName ?? 'there') ?></strong>,</p>

<p>Great news! You've been added to <strong><?= htmlspecialchars($companyName ?? 'the team') ?></strong> on Casjoe ERP.</p>

<?php if (!empty($hasLogin)): ?>
<div class="highlight-box">
    <h2>🔑 Your Login Credentials</h2>
    <p><strong>Email:</strong> <?= htmlspecialchars($email ?? '') ?></p>
    <p><strong>Temporary Password:</strong> <?= htmlspecialchars($password ?? '') ?></p>
    <p style="color: #dc3545; margin-top: 10px;">⚠️ Please change your password after your first login.</p>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="<?= $loginUrl ?? 'https://app.casjoe.com/login' ?>" class="btn">
        Login to Your Account →
    </a>
</div>
<?php else: ?>
<p>Your profile has been created in the system. You can access your employee information through the ERP portal.</p>
<?php endif; ?>

<p><strong>Your Role:</strong> <?= htmlspecialchars($role ?? 'Employee') ?></p>
<p><strong>Department:</strong> <?= htmlspecialchars($department ?? 'N/A') ?></p>

<p style="margin-top: 30px;">
    If you have any questions or need assistance, please don't hesitate to reach out to your manager or our support team.
</p>

<p style="margin-top: 30px;">
    <strong>Welcome aboard!</strong><br>
    The Casjoe Team
</p>
