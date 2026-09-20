<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <title>Bank Transfer Instructions | Casjoe Apps</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        body { background: #f4f6f9; font-family: 'Inter', sans-serif; }
        .instruction-card {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body class="p-4">

<div class="instruction-card text-center">
    <div class="mb-4">
        <ion-icon name="business-outline" style="font-size: 4rem; color: #000066;"></ion-icon>
    </div>
    
    <h2 class="mb-3">Bank Transfer Instructions</h2>
    <p class="text-muted mb-4">Please make a transfer of the exact amount to the account below. Your service will be activated once the payment is confirmed.</p>
    
    <div class="alert alert-info border-0 bg-light-primary">
        <h1 class="display-4 fw-bold text-primary mb-0">
            <?= htmlspecialchars($currency) ?> <?= number_format((float)$amount, 2) ?>
        </h1>
        <small class="text-muted">Amount to Transfer</small>
    </div>

    <div class="list-group list-group-flush text-start mb-4">
        <div class="list-group-item py-3">
            <small class="text-muted d-block">Bank Name</small>
            <strong class="fs-5"><?= htmlspecialchars($settings['bank_name'] ?? 'Casjoe Bank') ?></strong>
        </div>
        <div class="list-group-item py-3">
            <small class="text-muted d-block">Account Name</small>
            <strong class="fs-5"><?= htmlspecialchars($settings['bank_account_name'] ?? 'Casjoe Inc.') ?></strong>
        </div>
        <div class="list-group-item py-3">
            <small class="text-muted d-block">Account Number</small>
            <strong class="fs-5 text-primary copy-text" role="button"><?= htmlspecialchars($settings['bank_account_number'] ?? '1234567890') ?></strong>
        </div>
        <div class="list-group-item py-3">
            <small class="text-muted d-block">Reference / Memo</small>
            <strong class="fs-5 text-danger"><?= htmlspecialchars($ref) ?></strong>
            <div class="small text-danger  mt-1"><ion-icon name="alert-circle-outline"></ion-icon> IMPORTANT: Include this reference in your transfer memo.</div>
        </div>
        <?php if (!empty($settings['bank_swift_iban'])): ?>
        <div class="list-group-item py-3">
            <small class="text-muted d-block">SWIFT / IBAN</small>
            <strong class="fs-5"><?= htmlspecialchars($settings['bank_swift_iban']) ?></strong>
        </div>
        <?php endif; ?>
    </div>

    <a href="/billing" class="btn btn-outline-primary w-100 mb-2">I have made the transfer</a>
    <a href="/billing" class="btn btn-link text-muted">Cancel</a>
</div>

</body>
</html>
