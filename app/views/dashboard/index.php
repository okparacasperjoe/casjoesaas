<?php require __DIR__ . '/../../views/partials/header.php'; ?>

<h1>Company Overview</h1>

<div class="stats-grid">
    <div class="card">
        <h3>Academy</h3>
        <p><?= $courseCount ?> Courses</p>
        <a href="/academy" class="btn">Manage Courses</a>
    </div>

    <div class="card">
        <h3>Casjoe Pay</h3>
        <p>Balance: NGN <?= number_format($balance, 2) ?></p>
        <a href="/casjoe-pay" class="btn">Go to Wallet</a>
    </div>

    <div class="card">
        <h3>ERP System</h3>
        <p><?= $empCount ?> Employees</p>
        <a href="/erp" class="btn">Manage Business</a>
    </div>

    <div class="card">
        <h3>Billing</h3>
        <p>Manage Subscription</p>
        <a href="/billing" class="btn">View Billing</a>
    </div>

    <div class="card">
        <h3>Casjoe Mail</h3>
        <p>Email Marketing</p>
        <a href="/mail" class="btn">Send Emails</a>
    </div>
</div>

<?php require __DIR__ . '/../../views/partials/footer.php'; ?>