<?php require __DIR__ . '/../../partials/header.php'; ?>

<h1>Subscription & Billing</h1>

<div class="card">
    <h2>Current Plan: <?= $sub ? htmlspecialchars(ucfirst($sub['plan'])) : 'None' ?></h2>
    <p>Status: <span
            class="status <?= $sub['status'] ?? '' ?>"><?= $sub ? ucfirst($sub['status']) : 'No Subscription' ?></span>
    </p>

    <?php if ($sub && $sub['status'] === 'trial'): ?>
        <p>Trial Ends: <?= date('M d, Y', strtotime($sub['trial_ends_at'])) ?></p>
    <?php endif; ?>

    <?php if ($sub && $sub['next_billing_at']): ?>
        <p>Next Billing: <?= date('M d, Y', strtotime($sub['next_billing_at'])) ?></p>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Upgrade Payment Method</h3>
    <form method="POST" action="/billing/subscribe">
        <label>Card Number (Mock)</label>
        <input type="text" name="card_number" placeholder="4242 4242 4242 4242" required>
        <input type="hidden" name="card_token" value="tok_visa_mock">
        <button type="submit">Pay $50/Month</button>
    </form>
    <p><small>This is a demo. Entering any data simulates a successful card tokenization.</small></p>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
