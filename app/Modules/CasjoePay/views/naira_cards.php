<?php
$title = $title ?? 'Naira Cards | Casjoe Pay';
$pageTitle = $pageTitle ?? 'Naira Cards';
$activeMenu = 'naira-cards';
require_once __DIR__ . '/partials/pay_header.php';

// Nigerian states for the registration form
$nigerianStates = [
    'ab' => 'Abia', 'ad' => 'Adamawa', 'ak' => 'Akwa Ibom', 'an' => 'Anambra',
    'ba' => 'Bauchi', 'by' => 'Bayelsa', 'be' => 'Benue', 'bo' => 'Borno',
    'cr' => 'Cross River', 'de' => 'Delta', 'eb' => 'Ebonyi', 'ed' => 'Edo',
    'ek' => 'Ekiti', 'en' => 'Enugu', 'fc' => 'FCT Abuja', 'go' => 'Gombe',
    'im' => 'Imo', 'ji' => 'Jigawa', 'kd' => 'Kaduna', 'kn' => 'Kano',
    'kt' => 'Katsina', 'ke' => 'Kebbi', 'ko' => 'Kogi', 'kw' => 'Kwara',
    'lg' => 'Lagos', 'na' => 'Nasarawa', 'ni' => 'Niger', 'og' => 'Ogun',
    'on' => 'Ondo', 'os' => 'Osun', 'oy' => 'Oyo', 'pl' => 'Plateau',
    'ri' => 'Rivers', 'so' => 'Sokoto', 'ta' => 'Taraba', 'yo' => 'Yobe', 'za' => 'Zamfara'
];
?>

<style>
    /* ── Naira Card Specific Styles ── */
    .nc-step-indicator {
        display: flex;
        gap: 0;
        margin-bottom: 30px;
        background: var(--pay-surface);
        border: 1px solid var(--pay-border);
        border-radius: 16px;
        overflow: hidden;
    }
    .nc-step {
        flex: 1;
        padding: 16px 20px;
        text-align: center;
        position: relative;
        transition: all 0.3s;
    }
    .nc-step-num {
        display: inline-flex;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        margin-right: 8px;
        border: 2px solid var(--pay-border);
        color: var(--pay-text-muted);
        background: transparent;
        transition: all 0.3s;
    }
    .nc-step-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--pay-text-muted);
        transition: all 0.3s;
    }
    .nc-step.completed {
        background: rgba(0, 214, 143, 0.06);
    }
    .nc-step.completed .nc-step-num {
        background: var(--pay-green);
        border-color: var(--pay-green);
        color: #000;
    }
    .nc-step.completed .nc-step-label {
        color: var(--pay-green);
    }
    .nc-step.active {
        background: var(--pay-gold-dim);
    }
    .nc-step.active .nc-step-num {
        background: var(--pay-gold);
        border-color: var(--pay-gold);
        color: #000;
    }
    .nc-step.active .nc-step-label {
        color: var(--pay-gold);
    }
    .nc-step + .nc-step {
        border-left: 1px solid var(--pay-border);
    }

    /* ── Card Component ── */
    .nc-card-visual {
        background: linear-gradient(135deg, #0d0d2b 0%, #1a1a40 50%, #0d0d2b 100%);
        border: 1px solid rgba(255, 166, 0, 0.2);
        border-radius: 18px;
        padding: 28px;
        width: 340px;
        min-height: 200px;
        position: relative;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5), 0 0 30px rgba(255, 166, 0, 0.05);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        overflow: hidden;
    }
    .nc-card-visual::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -30%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255, 166, 0, 0.08), transparent 70%);
        border-radius: 50%;
    }
    .nc-card-visual.physical {
        background: linear-gradient(135deg, #0a1628 0%, #132244 50%, #0a1628 100%);
        border-color: rgba(67, 97, 238, 0.3);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5), 0 0 30px rgba(67, 97, 238, 0.08);
    }
    .nc-card-visual.physical::before {
        background: radial-gradient(circle, rgba(67, 97, 238, 0.1), transparent 70%);
    }
    .nc-card-brand-badge {
        position: absolute;
        top: 20px;
        right: 25px;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        padding: 5px 10px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.06);
        color: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .nc-card-chip {
        width: 44px;
        height: 34px;
        background: linear-gradient(135deg, #e8c547 0%, #c6952c 100%);
        border-radius: 7px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.25);
        position: relative;
    }
    .nc-card-chip::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60%;
        height: 60%;
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 3px;
    }

    /* ── Status Badge ── */
    .nc-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .nc-status-badge.active {
        background: rgba(0, 214, 143, 0.12);
        color: var(--pay-green);
    }
    .nc-status-badge.inactive {
        background: rgba(255, 77, 106, 0.12);
        color: var(--pay-red);
    }

    /* ── Info Banner ── */
    .nc-info-banner {
        background: linear-gradient(135deg, rgba(255, 166, 0, 0.06) 0%, rgba(67, 97, 238, 0.04) 100%);
        border: 1px solid rgba(255, 166, 0, 0.15);
        border-radius: 14px;
        padding: 18px 22px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .nc-info-banner ion-icon {
        font-size: 1.6rem;
        color: var(--pay-gold);
        flex-shrink: 0;
    }
    .nc-info-banner p {
        margin: 0;
        font-size: 0.85rem;
        color: var(--pay-text-muted);
        line-height: 1.5;
    }

    /* ── Two-column form grid ── */
    .nc-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .nc-form-grid .nc-full-width {
        grid-column: 1 / -1;
    }
    @media (max-width: 600px) {
        .nc-form-grid {
            grid-template-columns: 1fr;
        }
        .nc-card-visual {
            width: 100%;
        }
    }

    /* ── Action buttons row ── */
    .nc-card-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        flex-wrap: wrap;
    }
    .nc-btn-sm {
        padding: 8px 16px;
        font-size: 0.78rem;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.25s;
    }
    .nc-btn-sm.green {
        background: rgba(0, 214, 143, 0.15);
        color: var(--pay-green);
    }
    .nc-btn-sm.green:hover {
        background: rgba(0, 214, 143, 0.25);
    }
    .nc-btn-sm.red {
        background: rgba(255, 77, 106, 0.15);
        color: var(--pay-red);
    }
    .nc-btn-sm.red:hover {
        background: rgba(255, 77, 106, 0.25);
    }
    .nc-btn-sm.blue {
        background: rgba(67, 97, 238, 0.15);
        color: var(--pay-blue);
    }
    .nc-btn-sm.blue:hover {
        background: rgba(67, 97, 238, 0.25);
    }
</style>

<!-- Flash Messages -->
<?php if (!empty($_SESSION['success'])): ?>
    <div style="background: rgba(0,214,143,0.1); border: 1px solid rgba(0,214,143,0.3); color: var(--pay-green); padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600;">
        <ion-icon name="checkmark-circle" style="vertical-align: middle; margin-right: 6px;"></ion-icon>
        <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div style="background: rgba(255,77,106,0.1); border: 1px solid rgba(255,77,106,0.3); color: var(--pay-red); padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600;">
        <ion-icon name="alert-circle" style="vertical-align: middle; margin-right: 6px;"></ion-icon>
        <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- Step Progress Indicator -->
<div class="nc-step-indicator">
    <div class="nc-step <?= !empty($cardUser) ? 'completed' : 'active' ?>">
        <span class="nc-step-num"><?= !empty($cardUser) ? '✓' : '1' ?></span>
        <span class="nc-step-label">KYC Registration</span>
    </div>
    <div class="nc-step <?= ($hasPhysicalCard ?? false) ? 'completed' : (!empty($cardUser) ? 'active' : '') ?>">
        <span class="nc-step-num"><?= ($hasPhysicalCard ?? false) ? '✓' : '2' ?></span>
        <span class="nc-step-label">Physical ATM Card</span>
    </div>
</div>

<!-- ═══════════════════════════════════════════════ -->
<!-- STEP 1: KYC REGISTRATION                       -->
<!-- ═══════════════════════════════════════════════ -->
<?php if (empty($cardUser)): ?>
    <div class="nc-info-banner">
        <ion-icon name="shield-checkmark-outline"></ion-icon>
        <p>Complete your identity verification to create Naira debit cards. Your NIN and personal details are required for card issuance compliance.</p>
    </div>

    <div class="pay-form-card" style="max-width: 680px; margin: 0;">
        <h3 class="pay-form-header" style="text-align: left; margin-bottom: 5px;">Card Registration (KYC)</h3>
        <p style="font-size: 0.8rem; color: var(--pay-text-muted); margin-bottom: 25px;">All fields are required to issue your Naira card.</p>

        <form method="POST" action="/pay/naira-cards/register">
            <div class="nc-form-grid">
                <div class="pay-input-group">
                    <label class="pay-label">First Name</label>
                    <input type="text" name="firstname" class="pay-input" placeholder="John" required value="<?= htmlspecialchars(explode(' ', $user['name'] ?? '')[0] ?? '') ?>">
                </div>
                <div class="pay-input-group">
                    <label class="pay-label">Last Name</label>
                    <input type="text" name="lastname" class="pay-input" placeholder="Doe" required value="<?= htmlspecialchars(explode(' ', $user['name'] ?? '', 2)[1] ?? '') ?>">
                </div>
                <div class="pay-input-group">
                    <label class="pay-label">Email Address</label>
                    <input type="email" name="email" class="pay-input" placeholder="john@example.com" required value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                </div>
                <div class="pay-input-group">
                    <label class="pay-label">Phone Number</label>
                    <input type="text" name="phone" class="pay-input" placeholder="08012345678" required value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                </div>
                <div class="pay-input-group">
                    <label class="pay-label">NIN (National ID Number)</label>
                    <input type="text" name="nin" class="pay-input" placeholder="12345678901" minlength="11" maxlength="11" pattern="[0-9]{11}" required>
                </div>
                <div class="pay-input-group">
                    <label class="pay-label">Date of Birth</label>
                    <input type="date" name="dob" class="pay-input" required max="<?= date('Y-m-d', strtotime('-18 years')) ?>">
                </div>
                <div class="pay-input-group nc-full-width">
                    <label class="pay-label">Address</label>
                    <input type="text" name="line1" class="pay-input" placeholder="123 Main Street, Ikeja" required>
                </div>
                <div class="pay-input-group">
                    <label class="pay-label">City</label>
                    <input type="text" name="city" class="pay-input" placeholder="Ikeja" required>
                </div>
                <div class="pay-input-group">
                    <label class="pay-label">State</label>
                    <select name="state" class="pay-select" required>
                        <option value="">Select State</option>
                        <?php foreach ($nigerianStates as $code => $name): ?>
                            <option value="<?= $code ?>"><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="pay-input-group">
                    <label class="pay-label">Country</label>
                    <select name="country" class="pay-select" required>
                        <option value="NGA">Nigeria (NGA)</option>
                        <option value="GHA">Ghana (GHA)</option>
                        <option value="USA">United States (USA)</option>
                        <option value="GBR">United Kingdom (GBR)</option>
                        <option value="CAN">Canada (CAN)</option>
                    </select>
                </div>
                <div class="pay-input-group">
                    <label class="pay-label">Postal Code</label>
                    <input type="text" name="postal_code" class="pay-input" placeholder="100001" required>
                </div>
            </div>

            <button type="submit" class="pay-btn-primary" style="margin-top: 20px;">
                <ion-icon name="shield-checkmark-outline" style="font-size: 1.2rem;"></ion-icon> Complete Registration
            </button>
        </form>
    </div>

<?php else: ?>
    <!-- KYC completed - show registration summary -->
    <div style="background: var(--pay-surface); border: 1px solid var(--pay-border); border-radius: var(--pay-radius); padding: 20px 25px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(0,214,143,0.12); display: flex; align-items: center; justify-content: center;">
                <ion-icon name="person-circle" style="font-size: 1.4rem; color: var(--pay-green);"></ion-icon>
            </div>
            <div>
                <div style="font-weight: 700; color: #fff; font-size: 0.95rem;"><?= htmlspecialchars($cardUser['firstname'] . ' ' . $cardUser['lastname']) ?></div>
                <div style="font-size: 0.75rem; color: var(--pay-text-muted); font-family: 'JetBrains Mono', monospace;">Customer: <?= htmlspecialchars($cardUser['customer_id']) ?></div>
            </div>
        </div>
        <span class="nc-status-badge active">
            <ion-icon name="checkmark-circle"></ion-icon> KYC Verified
        </span>
    </div>

    <!-- ═══════════════════════════════════════════════ -->
    <!-- STEP 2: PHYSICAL ATM CARD                      -->
    <!-- ═══════════════════════════════════════════════ -->
        <div style="border-top: 1px solid var(--pay-border); padding-top: 30px; margin-top: 10px;">
            <h3 style="color: #fff; font-size: 1.15rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <ion-icon name="card-outline" style="color: var(--pay-blue);"></ion-icon> Physical ATM Card
            </h3>

            <?php if ($hasPhysicalCard ?? false): ?>
                <div style="display: flex; flex-wrap: wrap; gap: 25px; margin-bottom: 25px;">
                    <?php foreach ($physicalCards as $pcard): ?>
                        <div>
                            <div class="nc-card-visual physical">
                                <div class="nc-card-brand-badge"><?= htmlspecialchars($pcard['brand'] ?? 'AfriGo') ?> · ATM</div>
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div class="nc-card-chip"></div>
                                    <ion-icon name="wifi-outline" style="font-size: 1.4rem; color: rgba(255,255,255,0.4); transform: rotate(90deg);"></ion-icon>
                                </div>
                                <div style="font-family: 'JetBrains Mono', monospace; font-size: 1.2rem; letter-spacing: 2.5px; color: #fff; margin: 20px 0 12px;">
                                    <?= htmlspecialchars($pcard['masked_pan'] ?? '**** **** **** ****') ?>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                                    <div>
                                        <div style="font-size: 0.6rem; text-transform: uppercase; color: rgba(255,255,255,0.4); letter-spacing: 0.5px; margin-bottom: 3px;">EXPIRY</div>
                                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: rgba(255,255,255,0.85);">
                                            <?= htmlspecialchars($pcard['expiry_month'] ?? '--') ?>/<?= htmlspecialchars($pcard['expiry_year'] ?? '--') ?>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-size: 0.6rem; text-transform: uppercase; color: rgba(255,255,255,0.4); letter-spacing: 0.5px; margin-bottom: 3px;">TYPE</div>
                                        <div style="font-size: 0.8rem; color: rgba(255,255,255,0.85); font-weight: 600;">Physical ATM</div>
                                    </div>
                                </div>
                                <div style="position: absolute; bottom: 28px; right: 28px;">
                                    <span class="nc-status-badge <?= ($pcard['status'] ?? 'active') === 'active' ? 'active' : 'inactive' ?>">
                                        <?= htmlspecialchars($pcard['status'] ?? 'active') ?>
                                    </span>
                                </div>
                            </div>
                            <!-- Card Actions -->
                            <div class="nc-card-actions">
                                <?php if (($pcard['status'] ?? 'active') === 'active'): ?>
                                    <form method="POST" action="/pay/naira-cards/toggle-status" style="margin:0;">
                                        <input type="hidden" name="card_id" value="<?= htmlspecialchars($pcard['card_id']) ?>">
                                        <input type="hidden" name="status" value="inactive">
                                        <button type="submit" class="nc-btn-sm red" onclick="return confirm('Deactivate this ATM card?')">
                                            <ion-icon name="pause-circle-outline"></ion-icon> Deactivate
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="/pay/naira-cards/toggle-status" style="margin:0;">
                                        <input type="hidden" name="card_id" value="<?= htmlspecialchars($pcard['card_id']) ?>">
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="nc-btn-sm green">
                                            <ion-icon name="play-circle-outline"></ion-icon> Activate
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <a href="/pay/naira-cards/history/<?= urlencode($pcard['card_id']) ?>" class="nc-btn-sm blue" style="text-decoration: none;">
                                    <ion-icon name="receipt-outline"></ion-icon> History
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <?php if (($availablePhysicalCards ?? 0) > 0): ?>
                    <div class="nc-info-banner" style="border-color: rgba(67, 97, 238, 0.2);">
                        <ion-icon name="card-outline" style="color: var(--pay-blue);"></ion-icon>
                        <p>You're eligible for a physical ATM card! Request one below and it will be linked to your account for ATM withdrawals and POS payments.</p>
                    </div>
                    <div class="pay-form-card" style="max-width: 500px; margin: 0;">
                        <h3 class="pay-form-header" style="text-align: left; margin-bottom: 8px;">Request Physical ATM Card</h3>
                        <p style="font-size: 0.82rem; color: var(--pay-text-muted); margin-bottom: 25px; line-height: 1.5;">
                            Your Casjoe-branded <strong style="color: var(--pay-blue);">AfriGo</strong> physical ATM card will be linked to your account.
                            Use it at any ATM or POS terminal in Nigeria.
                        </p>
                        <form method="POST" action="/pay/naira-cards/create-physical" onsubmit="return confirm('Request a physical ATM card? This action cannot be undone.')">
                            <button type="submit" class="pay-btn-primary" style="background: var(--pay-blue); color: #fff;">
                                <ion-icon name="card-outline" style="font-size: 1.2rem;"></ion-icon> Request Physical ATM Card
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div style="background: var(--pay-surface); border: 1px dashed var(--pay-border); padding: 40px; border-radius: var(--pay-radius); text-align: center; color: var(--pay-text-muted);">
                        <ion-icon name="hourglass-outline" style="font-size: 3rem; opacity: 0.3; margin-bottom: 15px; color: var(--pay-blue);"></ion-icon>
                        <p style="margin: 0; font-size: 0.95rem;">Physical ATM cards are currently out of stock. Please check back later or contact support.</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

<?php endif; ?>

<?php require_once __DIR__ . '/partials/pay_footer.php'; ?>
