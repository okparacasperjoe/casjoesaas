<?php require __DIR__ . '/../../../../views/partials/header.php'; ?>

<h1>Mail Settings</h1>

<?php if (isset($_GET['status']) && $_GET['status'] == 'saved'): ?>
    <div style="background: #dff0d8; padding: 10px; border-radius: 4px; color: #3c763d;">Settings Saved!</div>
<?php endif; ?>

<div class="card">
    <form method="POST" action="/mail/settings/save">
        <h3>SMTP Configuration</h3>

        <label>Protocol</label>
        <select name="protocol">
            <option value="smtp" <?= ($settings['protocol'] ?? '') == 'smtp' ? 'selected' : '' ?>>Standard SMTP</option>
            <option value="aws_ses" <?= ($settings['protocol'] ?? '') == 'aws_ses' ? 'selected' : '' ?>>Amazon SES (via
                SMTP)</option>
        </select>

        <label>Host</label>
        <input type="text" name="host" value="<?= htmlspecialchars($settings['host'] ?? '') ?>"
            placeholder="smtp.gmail.com" required>

        <label>Port</label>
        <input type="number" name="port" value="<?= htmlspecialchars($settings['port'] ?? '587') ?>" required>

        <label>Encryption</label>
        <select name="encryption">
            <option value="tls" <?= ($settings['encryption'] ?? '') == 'tls' ? 'selected' : '' ?>>TLS</option>
            <option value="ssl" <?= ($settings['encryption'] ?? '') == 'ssl' ? 'selected' : '' ?>>SSL</option>
            <option value="none" <?= ($settings['encryption'] ?? '') == 'none' ? 'selected' : '' ?>>None</option>
        </select>

        <br><br>
        <h3>Authentication</h3>
        <label>Username (or AWS Access Key)</label>
        <input type="text" name="username" value="<?= htmlspecialchars($settings['username'] ?? '') ?>" required>

        <label>Password (or AWS Secret Key)</label>
        <input type="password" name="password" value="<?= htmlspecialchars($settings['password'] ?? '') ?>" required>

        <br><br>
        <h3>Sender Info</h3>
        <label>From Email</label>
        <input type="email" name="from_email" value="<?= htmlspecialchars($settings['from_email'] ?? '') ?>" required>

        <label>From Name</label>
        <input type="text" name="from_name" value="<?= htmlspecialchars($settings['from_name'] ?? '') ?>" required>

        <button type="submit">Save Configuration</button>
    </form>
</div>

<?php require __DIR__ . '/../../../../views/partials/footer.php'; ?>