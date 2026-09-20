<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?= htmlspecialchars($title ?? 'Casjoe Pay') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --ck-bg: #f8fafc;
            --ck-card: #ffffff;
            --ck-border: #e2e8f0;
            --ck-text: #0f172a;
            --ck-muted: #64748b;
            --ck-input-bg: #f8fafc;
            --ck-input-border: #cbd5e1;
            --ck-accent: #000066;
            --ck-gold: #ffa600;
        }
        html.dark-theme {
            --ck-bg: #0b0f19;
            --ck-card: #111827;
            --ck-border: #1f2937;
            --ck-text: #f8fafc;
            --ck-muted: #94a3b8;
            --ck-input-bg: #0f172a;
            --ck-input-border: #334155;
            --ck-accent: #ffa600;
            --ck-gold: #ffa600;
        }
        body {
            background: var(--ck-bg);
            color: var(--ck-text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .checkout-wrapper {
            width: 100%;
            max-width: 480px;
        }
        .checkout-card {
            background: var(--ck-card);
            border: 1px solid var(--ck-border);
            padding: 36px 32px;
            border-radius: 20px;
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.15);
        }
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(255, 166, 0, 0.12);
            color: #ffa600;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .amount-display {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--ck-text);
            margin: 12px 0 20px;
            letter-spacing: -0.5px;
        }
        .order-summary-box {
            background: var(--ck-input-bg);
            border: 1px dashed var(--ck-border);
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 24px;
            text-align: left;
        }
        .order-summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
        }
        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--ck-muted);
            margin-bottom: 6px;
        }
        .form-control-custom {
            background: var(--ck-input-bg) !important;
            border: 1px solid var(--ck-input-border) !important;
            color: var(--ck-text) !important;
            border-radius: 10px !important;
            padding: 12px 16px !important;
            font-size: 0.95rem !important;
            transition: all 0.2s ease;
        }
        .form-control-custom:focus {
            border-color: var(--ck-gold) !important;
            box-shadow: 0 0 0 3px rgba(255, 166, 0, 0.18) !important;
        }
        .btn-pay {
            background: linear-gradient(135deg, #ffa600 0%, #ff8800 100%);
            color: #0b0f19;
            font-weight: 800;
            font-size: 1.05rem;
            border: none;
            border-radius: 12px;
            padding: 14px 20px;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px rgba(255, 166, 0, 0.35);
        }
        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 166, 0, 0.45);
            color: #000;
        }
        .btn-cancel {
            display: inline-block;
            color: var(--ck-muted);
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;
            margin-top: 16px;
            transition: color 0.2s;
        }
        .btn-cancel:hover {
            color: var(--ck-text);
        }
        .trust-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--ck-border);
            font-size: 0.78rem;
            color: var(--ck-muted);
        }
        .trust-badge ion-icon {
            font-size: 1rem;
            color: #10b981;
        }
    </style>
</head>
<body>
    <div class="checkout-wrapper">
        <div class="checkout-card text-center">
            <div class="brand-badge">
                <ion-icon name="shield-checkmark"></ion-icon> Casjoe Pay Checkout
            </div>
            
            <h4 class="fw-bold mb-1" style="color: var(--ck-text);"><?= htmlspecialchars($title) ?></h4>
            <p class="text-muted small mb-3">Review your order and complete secure payment</p>
            
            <div class="amount-display">
                <span style="font-size: 1.3rem; vertical-align: middle; color: var(--ck-muted);"><?= htmlspecialchars($currency) === 'NGN' ? '₦' : htmlspecialchars($currency) . ' ' ?></span><?= number_format($amount, 2) ?>
            </div>

            <div class="order-summary-box">
                <div class="order-summary-row mb-1">
                    <span class="text-muted">Item / Description</span>
                    <strong style="color: var(--ck-text); max-width: 60%; text-align: right; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($title) ?></strong>
                </div>
                <div class="order-summary-row mb-1">
                    <span class="text-muted">Reference</span>
                    <code style="color: var(--ck-gold);"><?= htmlspecialchars($ref) ?></code>
                </div>
                <div class="order-summary-row">
                    <span class="text-muted">Total Due</span>
                    <strong style="color: #10b981;"><?= htmlspecialchars($currency) ?> <?= number_format($amount, 2) ?></strong>
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger text-start py-2 px-3 small mb-3" style="border-radius: 8px;">
                    <ion-icon name="alert-circle" style="vertical-align: -2px;"></ion-icon> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="/pay/checkout/process" method="POST" id="checkoutForm">
                <input type="hidden" name="amount" value="<?= htmlspecialchars($amount) ?>">
                <input type="hidden" name="currency" value="<?= htmlspecialchars($currency) ?>">
                <input type="hidden" name="ref" value="<?= htmlspecialchars($ref) ?>">
                <input type="hidden" name="title" value="<?= htmlspecialchars($title) ?>">
                <input type="hidden" name="description" value="<?= htmlspecialchars($title) ?>">
                <input type="hidden" name="callback" value="<?= htmlspecialchars($callback ?? '') ?>">
                <input type="hidden" name="cancel" value="<?= htmlspecialchars($cancel ?? '') ?>">
                <?php if (!empty($subId)): ?>
                    <input type="hidden" name="sub_id" value="<?= htmlspecialchars($subId) ?>">
                <?php endif; ?>
                <input type="hidden" name="owner_id" value="<?= htmlspecialchars($ownerId ?? 0) ?>">

                <div class="mb-3 text-start">
                    <label class="form-label">Customer Email Address</label>
                    <input type="email" name="email" class="form-control form-control-custom" placeholder="you@example.com" value="<?= htmlspecialchars($email ?? '') ?>" required>
                </div>
                
                <div class="mb-4 text-start">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control form-control-custom" placeholder="John Doe" value="<?= htmlspecialchars($name ?? '') ?>" required>
                </div>

                <button type="submit" class="btn-pay" id="payBtn">
                    Pay <?= htmlspecialchars($currency) === 'NGN' ? '₦' : htmlspecialchars($currency) . ' ' ?><?= number_format($amount, 2) ?>
                </button>
            </form>
            
            <?php if (!empty($cancel)): ?>
                <div>
                    <a href="<?= htmlspecialchars($cancel) ?>" class="btn-cancel">
                        <ion-icon name="arrow-back-outline" style="vertical-align: -2px;"></ion-icon> Cancel and Return
                    </a>
                </div>
            <?php endif; ?>

            <div class="trust-badge">
                <span><ion-icon name="lock-closed"></ion-icon> 256-Bit SSL Encrypted</span>
                <span>•</span>
                <span>PCI-DSS Compliant</span>
            </div>
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script>
        document.getElementById('checkoutForm')?.addEventListener('submit', function() {
            var btn = document.getElementById('payBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
            }
        });
    </script>
</body>
</html>
