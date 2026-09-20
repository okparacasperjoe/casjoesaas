<h2>Portal Access Granted</h2>

<p>Hello <?= htmlspecialchars($name) ?>,</p>

<p>You have been granted access to the Client Portal for <strong><?= htmlspecialchars($company) ?></strong>.</p>

<p>Since you already have an account with us, you can simply log in using your existing email and password.</p>

<p>
    <a href="<?= $login_url ?>" class="button">Login to Portal</a>
</p>

<p>If you have forgotten your password, you can reset it on the login page.</p>

<p>Best Regards,<br>
The Team</p>
