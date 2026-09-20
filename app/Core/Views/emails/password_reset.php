<h1>Reset Your Password 🔐</h1>

<p>Hi <strong><?= htmlspecialchars($name ?? 'there') ?></strong>,</p>

<p>We received a request to reset your password for your Casjoe account. Click the button below to create a new password:</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="<?= htmlspecialchars($resetUrl) ?>" class="btn">
        Reset Password →
    </a>
</div>

<div class="highlight-box">
    <p><strong>⚠️ Security Notice:</strong></p>
    <p>This link will expire in <strong>1 hour</strong> for security reasons. If you didn't request a password reset, you can safely ignore this email.</p>
</div>

<p>For your security, please:</p>
<ul style="line-height: 2; color: #555;">
    <li>Never share your password with anyone</li>
    <li>Use a strong, unique password</li>
    <li>Enable two-factor authentication if available</li>
</ul>

<p style="margin-top: 30px;">
    <strong>Stay secure,</strong><br>
    The Casjoe Security Team
</p>
