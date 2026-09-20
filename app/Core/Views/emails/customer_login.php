<h2>Your Client Portal Access</h2>

<p>Hello <?= htmlspecialchars($name) ?>,</p>

<p>An account has been created for you to access our secure Client Portal. You can view invoices, projects, and manage your account details.</p>

<div class="info-box">
    <p><strong>Login URL:</strong> <a href="<?= $login_url ?>"><?= $login_url ?></a></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
    <p><strong>Password:</strong> <?= htmlspecialchars($password) ?></p>
</div>

<p>Please keep these credentials safe. We recommend changing your password after your first login.</p>

<p>
    <a href="<?= $login_url ?>" class="button">Login Now</a>
</p>

<p>Best Regards,<br>
The Team</p>
