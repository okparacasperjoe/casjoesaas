<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($funnel['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #FFA600 0%, #000066 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .funnel-container {
            max-width: 700px;
            width: 100%;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .funnel-header {
            background: linear-gradient(135deg, #000066, #FFA600);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .funnel-header h1 { font-size: 28px; margin-bottom: 10px; }
        .funnel-header .progress-bar {
            background: rgba(255,255,255,0.3);
            height: 6px;
            border-radius: 3px;
            margin-top: 20px;
            overflow: hidden;
        }
        .funnel-header .progress-fill {
            background: white;
            height: 100%;
            transition: width 0.3s;
        }
        .funnel-content { padding: 40px; }
        .step-content { animation: slideIn 0.4s ease-out; }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-group { margin-bottom: 25px; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            outline: none;
            border-color: #FFA600;
        }
        .btn-primary {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #FFA600, #FF8C00);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn-primary:hover { transform: scale(1.02); }
        .landing-content { text-align: center; }
        .landing-content h2 { color: #000066; margin-bottom: 15px; font-size: 32px; }
        .landing-content p { color: #666; line-height: 1.8; margin-bottom: 30px; }
        .payment-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .payment-summary .amount {
            font-size: 36px;
            font-weight: 700;
            color: #000066;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="funnel-container">
        <div class="funnel-header">
            <h1><?= htmlspecialchars($funnel['name']) ?></h1>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= (($currentStepIndex + 1) / count($steps)) * 100 ?>%"></div>
            </div>
            <p style="margin-top: 10px; font-size: 14px;">
                Step <?= $currentStepIndex + 1 ?> of <?= count($steps) ?>
            </p>
        </div>

        <div class="funnel-content">
            <form method="POST" action="/f/<?= $funnelId ?>/process" class="step-content">
                <input type="hidden" name="step_index" value="<?= $currentStepIndex ?>">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?>">

                <?php
                $config = json_decode((is_array($currentStep) ? ($currentStep['config'] ?? '{}') : '{}'), true);
                
                $stepType = is_array($currentStep) ? ($currentStep['step_type'] ?? 'landing') : 'landing';
                switch ($stepType) {
                    case 'sales': // Support sales step type identical to landing
                    case 'landing':
                        ?>
                        <div class="landing-content">
                            <h2><?= htmlspecialchars($config['headline'] ?? 'Welcome!') ?></h2>
                            <div class="content-body">
                                <?= $config['content'] ?? '' ?>
                            </div>
                            <button type="submit" class="btn-primary">
                                <?= htmlspecialchars($config['cta_text'] ?? 'Continue') ?>
                            </button>
                        </div>
                        <?php
                        break;

                    case 'form':
                        ?>
                        <h2 style="margin-bottom: 25px; color: #000066;">
                            <?= htmlspecialchars($config['title'] ?? 'Enter Your Details') ?>
                        </h2>
                        <!-- Form fields will be loaded from Smart Forms in Phase 1E -->
                        <div class="form-group">
                            <label>Name *</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Phone *</label>
                            <input type="tel" name="phone" required>
                        </div>
                        <button type="submit" class="btn-primary">Continue</button>
                        <?php
                        break;

                    case 'payment':
                        $amount = $config['amount'] ?? 0;
                        ?>
                        <h2 style="margin-bottom: 25px; color: #000066;">Payment</h2>
                        <div class="payment-summary">
                            <div class="amount">₦<span id="display_amount"><?= number_format($amount, 2) ?></span></div>
                            <p style="text-align: center; margin-top: 10px; color: #666;">
                                <?= htmlspecialchars($config['description'] ?? '') ?>
                            </p>
                        </div>
                        
                        <?php if ($config['has_order_bump'] ?? false): ?>
                            <div style="border: 2px dashed #FFA600; padding: 15px; margin-bottom: 25px; border-radius: 8px; background: #fff8e6;">
                                <label style="display: flex; align-items: start; gap: 10px; cursor: pointer;">
                                    <input type="checkbox" name="order_bump" id="order_bump" value="1" onchange="updateTotalWithBump(this, <?= $amount ?>, <?= $config['bump_amount'] ?? 0 ?>)" style="margin-top: 4px; width: auto;">
                                    <div>
                                        <div style="font-weight: bold; color: #d32f2f;">⚡ ONE-TIME OFFER: <?= htmlspecialchars($config['bump_title'] ?? '') ?> (+₦<?= number_format($config['bump_amount'] ?? 0) ?>)</div>
                                        <div style="font-size: 14px; margin-top: 5px;"><?= htmlspecialchars($config['bump_description'] ?? '') ?></div>
                                    </div>
                                </label>
                            </div>
                            <script>
                                function updateTotalWithBump(checkbox, baseAmount, bumpAmount) {
                                    const total = checkbox.checked ? (baseAmount + bumpAmount) : baseAmount;
                                    document.getElementById('display_amount').innerText = new Intl.NumberFormat('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}).format(total);
                                }
                            </script>
                        <?php endif; ?>

                        <button type="submit" class="btn-primary">Proceed to Payment</button>
                        <?php
                        break;

                    case 'upsell':
                    case 'downsell':
                        ?>
                        <div style="text-align: center;">
                            <div style="background: #d32f2f; color: white; padding: 10px; font-weight: bold; margin-bottom: 20px; border-radius: 4px;">
                                ONE TIME OFFER - DO NOT CLOSE THIS PAGE
                            </div>
                            <h2 style="color: #000066; font-size: 28px; margin-bottom: 10px;">
                                <?= htmlspecialchars($config['headline'] ?? 'Wait! Special Offer') ?>
                            </h2>
                            <p style="color: #666; font-size: 18px; margin-bottom: 25px;">
                                <?= htmlspecialchars($config['subheadline'] ?? '') ?>
                            </p>
                            
                            <div style="border: 1px solid #eee; padding: 20px; border-radius: 8px; margin-bottom: 25px; background: #fafafa;">
                                <div style="font-size: 24px; font-weight: bold; color: #000066; margin-bottom: 15px;">
                                    <?= htmlspecialchars($config['product_name'] ?? 'Special Product') ?>
                                </div>
                                <div style="font-size: 32px; color: #2E7D32; font-weight: bold; margin-bottom: 20px;">
                                    ₦<?= number_format($config['price'] ?? 0) ?>
                                </div>
                                <div style="text-align: left; margin-bottom: 20px;">
                                    <?= $config['content'] ?? '' ?>
                                </div>
                            </div>
                            
                            <input type="hidden" name="offer_action" id="offer_action" value="accept">
                            
                            <button type="submit" class="btn-primary" style="background: #2E7D32; font-size: 22px; padding: 20px; margin-bottom: 15px;" onclick="document.getElementById('offer_action').value='accept';">
                                YES! Add To My Order
                            </button>
                            
                            <br>
                            <button type="submit" style="background: none; border: none; color: #666; text-decoration: underline; cursor: pointer; font-size: 16px;" onclick="document.getElementById('offer_action').value='decline';">
                                No thanks, I will pass on this offer
                            </button>
                        </div>
                        <?php
                        break;

                    case 'thankyou':
                        ?>
                        <div style="text-align: center;">
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #4CAF50, #2E7D32); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 48px; color: white;">✓</span>
                            </div>
                            <h2 style="color: #000066; margin-bottom: 15px;">
                                <?= htmlspecialchars($config['headline'] ?? 'Thank You!') ?>
                            </h2>
                            <p style="color: #666;">
                                <?= htmlspecialchars($config['message'] ?? 'We have received your submission.') ?>
                            </p>
                        </div>
                        <?php
                        break;
                }
                ?>
            </form>
        </div>
    </div>
</body>
</html>
