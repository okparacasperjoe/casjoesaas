<h2>Welcome to <?= htmlspecialchars($company) ?>!</h2>

<p>Dear <?= htmlspecialchars($name) ?>,</p>

<p>We are absolutely delighted to welcome you as a valued customer. At <?= htmlspecialchars($company) ?>, we are committed to providing you with the best possible service and support.</p>

<p>We look forward to a successful partnership.</p>

<p>
    <a href="<?= $login_url ?>" class="button">Visit Client Portal</a>
</p>

<p>Best Regards,<br>
The <?= htmlspecialchars($company) ?> Team</p>
