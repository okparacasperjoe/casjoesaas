<?php
$structure = json_decode($form['structure'], true) ?: [];
$settings = json_decode($form['settings'] ?? '{}', true);
$formMode = $settings['form_mode'] ?? 'classic';
$themeColor = $settings['theme_color'] ?? '#000066';
$showProgress = $settings['show_progress'] ?? true;
$submitText = !empty($settings['submit_text']) ? $settings['submit_text'] : 'Submit';
$paymentEnabled = !empty($settings['payment_enabled']);
$paymentGateway = $settings['payment_gateway'] ?? 'paystack';
$paymentAmount = $settings['payment_amount'] ?? 0;
$paymentCurrency = $settings['payment_currency'] ?? 'NGN';
$paymentAmountType = $settings['payment_amount_type'] ?? 'fixed';
$paymentDescription = $settings['payment_description'] ?? '';
$isSuccess = isset($_GET['status']) && $_GET['status'] === 'success';

$tyTitle = !empty($settings['thank_you_title']) ? $settings['thank_you_title'] : 'Thank you!';
$tyText = !empty($settings['thank_you_text']) ? $settings['thank_you_text'] : 'Your submission has been received.';

// Calculate logic rules for JS
$rulesJs = [];
foreach ($structure as $f) {
    if (!empty($f['condition_enabled']) && !empty($f['condition_field'])) {
        $rulesJs[$f['name']] = [
            'field' => $f['condition_field'],
            'op' => $f['condition_op'] ?? '==',
            'val' => $f['condition_val'] ?? ''
        ];
    }
}

// Filter out section_break for conversational fields list
$convFields = [];
foreach ($structure as $f) {
    if ($f['type'] !== 'section_break') $convFields[] = $f;
}

if ($formMode === 'conversational'):
// ============================================================
// CONVERSATIONAL MODE
// ============================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?= htmlspecialchars($form['title']) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<?php if ($paymentEnabled && $paymentGateway === 'paystack'): ?>
<script src="https://js.paystack.co/v1/inline.js"></script>
<?php elseif ($paymentEnabled && $paymentGateway === 'flutterwave'): ?>
<script src="https://checkout.flutterwave.com/v3.js"></script>
<?php endif; ?>
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --theme:<?= htmlspecialchars($themeColor) ?>;
  --accent:#FFA600;
  --bg-dark:<?= htmlspecialchars($themeColor) ?>;
}
html,body{height:100%;overflow:hidden;font-family:'Inter',system-ui,-apple-system,sans-serif}
body{background:linear-gradient(160deg, var(--theme) 0%, #000011 100%);color:#fff}

/* PROGRESS BAR */
.cf-progress{position:fixed;top:0;left:0;width:100%;height:4px;z-index:1000;background:rgba(255,255,255,0.1)}
.cf-progress-bar{height:100%;background:var(--accent);transition:width 0.5s cubic-bezier(0.4,0,0.2,1);border-radius:0 2px 2px 0}

/* TOP NAV */
.cf-nav{position:fixed;top:0;left:0;right:0;height:60px;display:flex;align-items:center;justify-content:space-between;padding:0 24px;z-index:100}
.cf-nav-logo img{max-height:36px;border-radius:6px;opacity:0.9}
.cf-nav-back{background:rgba(255,255,255,0.1);border:none;color:#fff;width:40px;height:40px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:20px;transition:all 0.2s;backdrop-filter:blur(10px)}
.cf-nav-back:hover{background:rgba(255,255,255,0.2);transform:scale(1.1)}
.cf-nav-back:disabled{opacity:0.3;cursor:default;transform:none}
.cf-nav-counter{font-size:13px;color:rgba(255,255,255,0.5);font-weight:500}

/* SLIDE CONTAINER */
.cf-slides{position:relative;width:100%;height:100vh}
.cf-slide{position:absolute;top:0;left:0;width:100%;height:100%;display:flex;align-items:center;justify-content:center;padding:80px 24px 40px;opacity:0;transform:translateY(40px);pointer-events:none;transition:all 0.45s cubic-bezier(0.4,0,0.2,1)}
.cf-slide.active{opacity:1;transform:translateY(0);pointer-events:auto}
.cf-slide.exit-up{opacity:0;transform:translateY(-40px);pointer-events:none}

.cf-slide-inner{width:100%;max-width:620px}

/* QUESTION */
.cf-qnum{font-size:14px;color:var(--accent);font-weight:700;margin-bottom:8px;text-transform:uppercase;letter-spacing:1px}
.cf-qlabel{font-size:28px;font-weight:700;line-height:1.3;margin-bottom:8px}
.cf-qdesc{font-size:15px;color:rgba(255,255,255,0.55);margin-bottom:28px;line-height:1.5}
.cf-required{color:#ff6b6b;font-size:14px;margin-left:6px}

/* TEXT INPUT (bottom border style) */
.cf-input{width:100%;background:transparent;border:none;border-bottom:2px solid rgba(255,255,255,0.3);padding:16px 0;font-size:22px;color:#fff;font-family:inherit;outline:none;transition:border-color 0.3s}
.cf-input::placeholder{color:rgba(255,255,255,0.3)}
.cf-input:focus{border-bottom-color:var(--accent)}
textarea.cf-input{resize:none;min-height:80px;line-height:1.5}
input[type="date"].cf-input{color-scheme:dark}

/* OPTION CARDS (for dropdown / radio) */
.cf-options{display:flex;flex-direction:column;gap:10px;margin-top:8px}
.cf-opt{display:flex;align-items:center;gap:14px;padding:16px 20px;border:2px solid rgba(255,255,255,0.15);border-radius:12px;cursor:pointer;transition:all 0.25s;background:rgba(255,255,255,0.03)}
.cf-opt:hover{border-color:rgba(255,255,255,0.4);background:rgba(255,255,255,0.08);transform:translateX(4px)}
.cf-opt.selected{border-color:var(--accent);background:rgba(255,166,0,0.12)}
.cf-opt-key{width:28px;height:28px;border-radius:6px;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:rgba(255,255,255,0.7);flex-shrink:0}
.cf-opt.selected .cf-opt-key{background:var(--accent);color:#000}
.cf-opt-text{font-size:17px;font-weight:500}

/* STAR RATING */
.cf-stars{display:flex;gap:8px;margin-top:8px}
.cf-star{font-size:42px;cursor:pointer;color:rgba(255,255,255,0.2);transition:all 0.2s;user-select:none}
.cf-star:hover,.cf-star.active{color:var(--accent);transform:scale(1.15)}

/* CHECKBOX / TOGGLE */
.cf-check-wrap{display:flex;align-items:center;gap:14px;margin-top:12px;cursor:pointer}
.cf-check-box{width:28px;height:28px;border:2px solid rgba(255,255,255,0.3);border-radius:6px;display:flex;align-items:center;justify-content:center;transition:all 0.2s;flex-shrink:0}
.cf-check-box.checked{background:var(--accent);border-color:var(--accent)}
.cf-check-box.checked::after{content:'✓';color:#000;font-weight:700;font-size:16px}
.cf-check-text{font-size:17px}

/* FILE UPLOAD */
.cf-file-zone{border:2px dashed rgba(255,255,255,0.2);border-radius:16px;padding:40px;text-align:center;cursor:pointer;transition:all 0.3s;background:rgba(255,255,255,0.02)}
.cf-file-zone:hover{border-color:var(--accent);background:rgba(255,166,0,0.05)}
.cf-file-zone ion-icon{font-size:40px;color:rgba(255,255,255,0.3);margin-bottom:12px}
.cf-file-zone p{color:rgba(255,255,255,0.5);font-size:14px}
.cf-file-zone .selected-file{color:var(--accent);font-weight:600;margin-top:8px}
.cf-file-zone input{display:none}

/* TERMS BOX */
.cf-terms-box{max-height:200px;overflow-y:auto;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:20px;font-size:14px;line-height:1.6;color:rgba(255,255,255,0.7);margin-bottom:16px}

/* SIGNATURE */
.cf-sig-wrap{border:2px solid rgba(255,255,255,0.15);border-radius:12px;background:rgba(255,255,255,0.95);position:relative;overflow:hidden}
.cf-sig-pad{width:100%;height:180px;display:block;border-radius:10px}
.cf-sig-clear{position:absolute;top:8px;right:8px;background:rgba(0,0,0,0.6);color:#fff;border:none;padding:6px 14px;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600}

/* BUTTONS */
.cf-btn{display:inline-flex;align-items:center;gap:8px;padding:14px 32px;border:none;border-radius:50px;font-size:16px;font-weight:600;cursor:pointer;transition:all 0.25s;font-family:inherit}
.cf-btn-primary{background:var(--accent);color:#000}
.cf-btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(255,166,0,0.3)}
.cf-btn-outline{background:transparent;border:2px solid rgba(255,255,255,0.2);color:#fff}
.cf-btn-outline:hover{border-color:#fff;background:rgba(255,255,255,0.05)}
.cf-hint{margin-top:12px;font-size:13px;color:rgba(255,255,255,0.3);font-weight:500}
.cf-hint kbd{background:rgba(255,255,255,0.1);padding:2px 8px;border-radius:4px;font-size:11px;font-family:inherit}
.cf-error{color:#ff6b6b;font-size:14px;margin-top:8px;display:none}

/* REVIEW SCREEN */
.cf-review{width:100%;max-width:620px}
.cf-review h2{font-size:28px;font-weight:700;margin-bottom:24px}
.cf-review-item{padding:16px 0;border-bottom:1px solid rgba(255,255,255,0.08);display:flex;justify-content:space-between;align-items:center;gap:15px}
.cf-review-item:last-child{border:none}
.cf-review-content{flex:1;min-width:0;word-wrap:break-word;overflow-wrap:break-word}
.cf-review-label{font-size:13px;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px}
.cf-review-value{font-size:17px;font-weight:500;word-break:break-word}
.cf-review-edit{color:var(--accent);font-size:14px;font-weight:600;cursor:pointer;flex-shrink:0;padding:6px 12px;border-radius:6px;background:rgba(255,166,0,0.1);transition:background 0.2s}
.cf-review-edit:hover{background:rgba(255,166,0,0.2)}

/* PAYMENT SCREEN */
.cf-pay-card{background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:32px;text-align:center;margin-bottom:24px}
.cf-pay-amount{font-size:42px;font-weight:800;margin:16px 0 8px;color:var(--accent)}
.cf-pay-desc{color:rgba(255,255,255,0.5);font-size:15px}

/* SUCCESS SCREEN */
.cf-success{text-align:center}
.cf-success-check{width:80px;height:80px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;animation:cf-pop 0.6s cubic-bezier(0.4,0,0.2,1)}
.cf-success-check ion-icon{font-size:40px;color:#000}
@keyframes cf-pop{0%{transform:scale(0);opacity:0}50%{transform:scale(1.2)}100%{transform:scale(1);opacity:1}}
.cf-success h2{font-size:32px;font-weight:700;margin-bottom:12px}
.cf-success p{color:rgba(255,255,255,0.6);font-size:16px}

/* MOBILE */
@media(max-width:600px){
  .cf-qlabel{font-size:22px}
  .cf-input{font-size:18px}
  .cf-opt-text{font-size:15px}
  .cf-star{font-size:34px}
  .cf-pay-amount{font-size:32px}
}
</style>
</head>
<body data-paystack-key="<?= htmlspecialchars($paystackPublicKey ?? '') ?>" data-flutterwave-key="<?= htmlspecialchars($flutterwavePublicKey ?? '') ?>">

<?php if ($showProgress): ?>
<div class="cf-progress"><div class="cf-progress-bar" id="progressBar" style="width:0%"></div></div>
<?php endif; ?>

<nav class="cf-nav">
  <div style="display:flex;align-items:center;gap:12px">
    <button class="cf-nav-back" id="btnBack" onclick="goBack()" disabled><ion-icon name="arrow-back"></ion-icon></button>
    <?php if (!empty($form['logo'])): ?>
    <div class="cf-nav-logo"><img src="<?= htmlspecialchars($form['logo']) ?>" alt="Logo" onerror="this.parentElement.style.display='none'"></div>
    <?php endif; ?>
  </div>
  <div class="cf-nav-counter" id="navCounter"></div>
</nav>

<div class="cf-slides" id="slidesContainer">
  <?php if ($isSuccess): ?>
  <!-- SUCCESS SLIDE (shown on redirect back) -->
  <div class="cf-slide active">
    <div class="cf-slide-inner cf-success">
      <div class="cf-success-check"><ion-icon name="checkmark-outline"></ion-icon></div>
      <h2><?= htmlspecialchars($tyTitle) ?></h2>
      <p><?= htmlspecialchars($tyText) ?></p>
      <?php if (!empty($settings['redirect_url'])): ?>
      <p style="margin-top:16px;font-size:13px;color:rgba(255,255,255,0.3)">Redirecting in 3 seconds...</p>
      <?php endif; ?>
    </div>
  </div>
  <?php else: ?>

  <!-- WELCOME SLIDE -->
  <div class="cf-slide active" data-slide="welcome">
    <div class="cf-slide-inner" style="text-align:center">
      <?php if (!empty($form['logo'])): ?>
      <img src="<?= htmlspecialchars($form['logo']) ?>" alt="" style="max-width:200px;max-height:100px;border-radius:10px;margin-bottom:24px" onerror="this.style.display='none'">
      <?php endif; ?>
      <h1 style="font-size:36px;font-weight:800;margin-bottom:12px;line-height:1.2"><?= htmlspecialchars($form['title']) ?></h1>
      <?php if (!empty($form['description'])): ?>
      <p style="font-size:17px;color:rgba(255,255,255,0.55);max-width:480px;margin:0 auto 32px;line-height:1.6"><?= htmlspecialchars($form['description']) ?></p>
      <?php else: ?>
      <div style="margin-bottom:32px"></div>
      <?php endif; ?>
      <button class="cf-btn cf-btn-primary" onclick="goNext()" style="font-size:18px;padding:16px 48px">
        Start <ion-icon name="arrow-forward" style="font-size:20px"></ion-icon>
      </button>
      <div class="cf-hint">Takes about <?= max(1, ceil(count($convFields) * 0.3)) ?> min</div>
    </div>
  </div>

  <!-- QUESTION SLIDES -->
  <?php foreach ($convFields as $qi => $field):
    $isRequired = !empty($field['required']);
    $fieldName = htmlspecialchars($field['name']);
    $fieldLabel = htmlspecialchars($field['label']);
    $fieldType = $field['type'];
    $fieldPlaceholder = htmlspecialchars($field['placeholder'] ?? '');
    $fieldOptions = $field['options'] ?? '';
  ?>
  <div class="cf-slide" data-slide="q<?= $qi ?>" data-field-name="<?= $fieldName ?>" data-field-type="<?= $fieldType ?>" data-required="<?= $isRequired ? '1' : '0' ?>">
    <div class="cf-slide-inner">
      <div class="cf-qnum"><?= ($qi + 1) ?> of <?= count($convFields) ?></div>
      <div class="cf-qlabel"><?= $fieldLabel ?><?php if ($isRequired): ?><span class="cf-required">*</span><?php endif; ?></div>

      <?php if ($fieldType === 'content'): ?>
        <div class="cf-qdesc"><?= $fieldOptions ?></div>
        <button class="cf-btn cf-btn-primary" onclick="goNext()">Continue <ion-icon name="arrow-forward"></ion-icon></button>

      <?php elseif (in_array($fieldType, ['text', 'email', 'phone', 'url', 'number'])): ?>
        <?php
          $inputType = $fieldType;
          if ($fieldType === 'phone') $inputType = 'tel';
          if ($fieldType === 'text' || $fieldType === 'url') $inputType = $fieldType;
        ?>
        <input class="cf-input" type="<?= $inputType ?>" name="<?= $fieldName ?>" placeholder="<?= $fieldPlaceholder ?: 'Type your answer here...' ?>" autocomplete="off" data-field>
        <div class="cf-error" id="err-<?= $fieldName ?>">This field is required</div>
        <div style="margin-top:24px">
          <button class="cf-btn cf-btn-primary" onclick="goNext()">OK <ion-icon name="checkmark"></ion-icon></button>
          <div class="cf-hint">press <kbd>Enter ↵</kbd></div>
        </div>

      <?php elseif ($fieldType === 'date'): ?>
        <input class="cf-input" type="date" name="<?= $fieldName ?>" data-field>
        <div class="cf-error" id="err-<?= $fieldName ?>">This field is required</div>
        <div style="margin-top:24px">
          <button class="cf-btn cf-btn-primary" onclick="goNext()">OK <ion-icon name="checkmark"></ion-icon></button>
          <div class="cf-hint">press <kbd>Enter ↵</kbd></div>
        </div>

      <?php elseif ($fieldType === 'textarea'): ?>
        <textarea class="cf-input" name="<?= $fieldName ?>" rows="3" placeholder="<?= $fieldPlaceholder ?: 'Type your answer here...' ?>" data-field></textarea>
        <div class="cf-error" id="err-<?= $fieldName ?>">This field is required</div>
        <div style="margin-top:24px">
          <button class="cf-btn cf-btn-primary" onclick="goNext()">OK <ion-icon name="checkmark"></ion-icon></button>
          <div class="cf-hint"><kbd>Shift + Enter ↵</kbd> for new line</div>
        </div>

      <?php elseif ($fieldType === 'dropdown' || $fieldType === 'radio'): ?>
        <?php $opts = array_map('trim', explode(',', $fieldOptions)); ?>
        <div class="cf-options" data-field data-name="<?= $fieldName ?>">
          <?php foreach ($opts as $oi => $opt): ?>
          <div class="cf-opt" data-value="<?= htmlspecialchars($opt) ?>" onclick="selectOption(this)">
            <div class="cf-opt-key"><?= chr(65 + $oi) ?></div>
            <div class="cf-opt-text"><?= htmlspecialchars($opt) ?></div>
          </div>
          <?php endforeach; ?>
          <?php if (!empty($field['allow_other'])): ?>
          <div class="cf-opt" data-value="__OTHER__" onclick="selectOption(this)">
            <div class="cf-opt-key">?</div>
            <div class="cf-opt-text">Other</div>
          </div>
          <input class="cf-input" type="text" name="<?= $fieldName ?>_other" placeholder="Please specify..." style="display:none;margin-top:8px" id="other-<?= $fieldName ?>">
          <?php endif; ?>
        </div>
        <input type="hidden" name="<?= $fieldName ?>" data-field-hidden>
        <div class="cf-error" id="err-<?= $fieldName ?>">Please select an option</div>

      <?php elseif ($fieldType === 'rating'): ?>
        <div class="cf-stars" data-field data-name="<?= $fieldName ?>">
          <?php for ($s = 1; $s <= 5; $s++): ?>
          <span class="cf-star" data-value="<?= $s ?>" onclick="selectRating(this, <?= $s ?>)">★</span>
          <?php endfor; ?>
        </div>
        <input type="hidden" name="<?= $fieldName ?>" data-field-hidden>
        <div class="cf-error" id="err-<?= $fieldName ?>">Please give a rating</div>

      <?php elseif ($fieldType === 'checkbox'): ?>
        <div class="cf-check-wrap" onclick="toggleCheck(this)" data-field data-name="<?= $fieldName ?>">
          <div class="cf-check-box"></div>
          <div class="cf-check-text">Yes</div>
        </div>
        <input type="hidden" name="<?= $fieldName ?>" value="" data-field-hidden>
        <div style="margin-top:24px">
          <button class="cf-btn cf-btn-primary" onclick="goNext()">OK <ion-icon name="checkmark"></ion-icon></button>
        </div>

      <?php elseif ($fieldType === 'file'): ?>
        <div class="cf-file-zone" onclick="this.querySelector('input').click()" data-field data-name="<?= $fieldName ?>">
          <ion-icon name="cloud-upload-outline"></ion-icon>
          <p>Click to upload or drag & drop</p>
          <div class="selected-file" id="fname-<?= $fieldName ?>"></div>
          <input type="file" name="<?= $fieldName ?>" onchange="handleFileSelect(this, '<?= $fieldName ?>')">
        </div>
        <div class="cf-error" id="err-<?= $fieldName ?>">Please upload a file</div>
        <div style="margin-top:24px">
          <button class="cf-btn cf-btn-primary" onclick="goNext()">OK <ion-icon name="checkmark"></ion-icon></button>
        </div>

      <?php elseif ($fieldType === 'signature'): ?>
        <div class="cf-sig-wrap">
          <canvas class="cf-sig-pad" id="sig-<?= $fieldName ?>"></canvas>
          <button class="cf-sig-clear" type="button" onclick="clearSig('<?= $fieldName ?>')">Clear</button>
        </div>
        <input type="hidden" name="<?= $fieldName ?>" id="sigdata-<?= $fieldName ?>" data-field-hidden>
        <div class="cf-error" id="err-<?= $fieldName ?>">Please provide your signature</div>
        <div style="margin-top:24px">
          <button class="cf-btn cf-btn-primary" onclick="saveSigAndNext('<?= $fieldName ?>')">OK <ion-icon name="checkmark"></ion-icon></button>
        </div>

      <?php elseif ($fieldType === 'terms'): ?>
        <div class="cf-terms-box"><?= $fieldOptions ?></div>
        <div class="cf-check-wrap" onclick="toggleCheck(this)" data-field data-name="<?= $fieldName ?>">
          <div class="cf-check-box"></div>
          <div class="cf-check-text">I agree to the terms & conditions</div>
        </div>
        <input type="hidden" name="<?= $fieldName ?>" value="" data-field-hidden>
        <div class="cf-error" id="err-<?= $fieldName ?>">You must accept the terms</div>
        <div style="margin-top:24px">
          <button class="cf-btn cf-btn-primary" onclick="goNext()">OK <ion-icon name="checkmark"></ion-icon></button>
        </div>

      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>

  <!-- REVIEW SLIDE -->
  <div class="cf-slide" data-slide="review">
    <div class="cf-slide-inner cf-review" style="max-height:calc(100vh - 120px);overflow-y:auto;padding-right:15px;">
      <h2>Review your answers</h2>
      <div id="reviewContent"></div>
      <div style="margin-top:32px;display:flex;gap:12px;flex-wrap:wrap">
        <button class="cf-btn cf-btn-primary" onclick="submitForm()" id="btnSubmit" style="font-size:18px;padding:16px 48px"><?= htmlspecialchars($submitText) ?></button>
      </div>
    </div>
  </div>

  <?php if ($paymentEnabled): ?>
  <!-- PAYMENT SLIDE -->
  <div class="cf-slide" data-slide="payment">
    <div class="cf-slide-inner" style="text-align:center">
      <div class="cf-pay-card">
        <ion-icon name="card-outline" style="font-size:48px;color:var(--accent);margin-bottom:8px"></ion-icon>
        <h3 style="font-size:20px;font-weight:600;margin-bottom:4px">Payment Required</h3>
        <?php if ($paymentAmountType === 'fixed'): ?>
        <div class="cf-pay-amount"><?= $paymentCurrency ?> <?= number_format((float)$paymentAmount, 2) ?></div>
        <?php else: ?>
        <div style="margin:16px 0">
          <input class="cf-input" type="number" id="userPayAmount" placeholder="Enter amount" style="text-align:center;font-size:28px;font-weight:700;max-width:300px;margin:0 auto" min="1" step="0.01">
        </div>
        <?php endif; ?>
        <?php if ($paymentDescription): ?>
        <div class="cf-pay-desc"><?= htmlspecialchars($paymentDescription) ?></div>
        <?php endif; ?>
      </div>
      <button class="cf-btn cf-btn-primary" onclick="initiatePayment()" id="btnPay" style="font-size:18px;padding:16px 48px">
        <ion-icon name="lock-closed" style="font-size:18px"></ion-icon> Pay Now
      </button>
      <div class="cf-hint">Secure payment via <?= ucfirst($paymentGateway) ?></div>
      <div id="payError" style="color:#ff6b6b;margin-top:12px;display:none"></div>
    </div>
  </div>
  <?php endif; ?>

  <!-- INLINE SUCCESS -->
  <div class="cf-slide" data-slide="success">
    <div class="cf-slide-inner cf-success">
      <div class="cf-success-check"><ion-icon name="checkmark-outline"></ion-icon></div>
      <h2><?= htmlspecialchars($tyTitle) ?></h2>
      <p><?= htmlspecialchars($tyText) ?></p>
      <?php if (!empty($settings['redirect_url'])): ?>
      <p style="margin-top:16px;font-size:13px;color:rgba(255,255,255,0.3)">Redirecting...</p>
      <?php endif; ?>
    </div>
  </div>

  <?php endif; /* !$isSuccess */ ?>
</div>

<script>
const formId = <?= (int)$form['id'] ?>;
const totalFields = <?= count($convFields) ?>;
const paymentEnabled = <?= $paymentEnabled ? 'true' : 'false' ?>;
const paymentGateway = '<?= $paymentGateway ?>';
const paymentAmountType = '<?= $paymentAmountType ?>';
const paymentAmount = <?= (float)$paymentAmount ?>;
const paymentCurrency = '<?= $paymentCurrency ?>';
const redirectUrl = '<?= addslashes($settings['redirect_url'] ?? '') ?>';
const slides = document.querySelectorAll('.cf-slide');
const formData = {};
let currentSlide = 0;
let hasStarted = false;

const fieldRules = <?= json_encode($rulesJs) ?>;

function checkCondition(fieldName) {
    if (!fieldRules[fieldName]) return true;
    const rule = fieldRules[fieldName];
    const actualVal = formData[rule.field] || '';
    const targetVal = rule.val || '';
    
    let isMatch = false;
    let a = actualVal.toString().toLowerCase();
    let t = targetVal.toString().toLowerCase();

    switch(rule.op) {
        case '==': isMatch = (a === t); break;
        case '!=': isMatch = (a !== t); break;
        case 'contains': isMatch = (a.includes(t)); break;
        case '>': isMatch = (parseFloat(a) > parseFloat(t)); break;
        case '<': isMatch = (parseFloat(a) < parseFloat(t)); break;
        default: isMatch = (a === t);
    }
    return isMatch;
}

// Analytics
function track(event, data = {}) {
    fetch('/sf/' + formId + '/track', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({event, metadata: data})
    }).catch(() => {});
}
track('view');

// Drop-off tracking
let submitted = false;
window.addEventListener('beforeunload', () => {
    if (!submitted && hasStarted) {
        const slide = slides[currentSlide];
        const fn = slide?.dataset?.fieldName || 'unknown';
        navigator.sendBeacon('/sf/' + formId + '/track', JSON.stringify({event:'drop_off', metadata:{field: fn}}));
    }
});

// Init signature pads when slides become visible
function initSigPad(fieldName) {
    const canvas = document.getElementById('sig-' + fieldName);
    if (!canvas || sigPads[fieldName]) return;
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext('2d').scale(ratio, ratio);
    sigPads[fieldName] = new SignaturePad(canvas, {backgroundColor:'rgba(255,255,255,0)', penColor:'rgb(0,0,0)'});
}

function clearSig(fn) {
    if (sigPads[fn]) {sigPads[fn].clear(); document.getElementById('sigdata-' + fn).value = '';}
}

function saveSigAndNext(fn) {
    if (sigPads[fn] && !sigPads[fn].isEmpty()) {
        document.getElementById('sigdata-' + fn).value = sigPads[fn].toDataURL('image/png');
    }
    goNext();
}

// Navigation
function updateUI() {
    slides.forEach((s, i) => {
        s.classList.remove('active', 'exit-up');
        if (i === currentSlide) s.classList.add('active');
        else if (i < currentSlide) s.classList.add('exit-up');
    });

    const btnBack = document.getElementById('btnBack');
    if (btnBack) btnBack.disabled = (currentSlide === 0);

    // Progress
    const pb = document.getElementById('progressBar');
    if (pb) {
        // welcome=0, fields 1..N, review=N+1, payment=N+2, success=N+3
        const prog = Math.min(100, Math.round(((currentSlide) / (totalFields + 1)) * 100));
        pb.style.width = prog + '%';
    }

    // Counter
    const nc = document.getElementById('navCounter');
    if (nc) {
        const s = slides[currentSlide];
        if (s && s.dataset.slide && s.dataset.slide.startsWith('q')) {
            const qn = parseInt(s.dataset.slide.replace('q','')) + 1;
            nc.textContent = qn + ' of ' + totalFields;
        } else {
            nc.textContent = '';
        }
    }

    // Auto-focus
    setTimeout(() => {
        const activeSlide = slides[currentSlide];
        if (activeSlide) {
            const inp = activeSlide.querySelector('input.cf-input, textarea.cf-input');
            if (inp) inp.focus();

            // Init sig pad if needed
            const sigCanvas = activeSlide.querySelector('.cf-sig-pad');
            if (sigCanvas) {
                const fn = sigCanvas.id.replace('sig-','');
                initSigPad(fn);
            }
        }
    }, 500);
}

function validateCurrentSlide() {
    const slide = slides[currentSlide];
    if (!slide || !slide.dataset.fieldName) return true;

    const fieldName = slide.dataset.fieldName;
    const fieldType = slide.dataset.fieldType;
    const required = slide.dataset.required === '1';
    const errEl = document.getElementById('err-' + fieldName);

    if (!required) return true;

    let value = '';
    if (['text','email','phone','url','number','date','textarea'].includes(fieldType)) {
        const inp = slide.querySelector('[name="' + fieldName + '"]');
        value = inp ? inp.value.trim() : '';
    } else if (['dropdown','radio'].includes(fieldType)) {
        const hidden = slide.querySelector('[name="' + fieldName + '"]');
        value = hidden ? hidden.value : '';
    } else if (fieldType === 'rating') {
        const hidden = slide.querySelector('[name="' + fieldName + '"]');
        value = hidden ? hidden.value : '';
    } else if (fieldType === 'checkbox' || fieldType === 'terms') {
        const hidden = slide.querySelector('[name="' + fieldName + '"]');
        value = hidden ? hidden.value : '';
    } else if (fieldType === 'file') {
        const inp = slide.querySelector('input[type="file"]');
        value = (inp && inp.files.length > 0) ? 'has-file' : '';
    } else if (fieldType === 'signature') {
        value = document.getElementById('sigdata-' + fieldName)?.value || '';
    }

    if (!value) {
        if (errEl) errEl.style.display = 'block';
        return false;
    }

    // Email validation
    if (fieldType === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
        if (errEl) { errEl.textContent = 'Please enter a valid email'; errEl.style.display = 'block'; }
        return false;
    }

    if (errEl) errEl.style.display = 'none';
    return true;
}

function collectFieldValue(slide) {
    if (!slide || !slide.dataset.fieldName) return;
    const fn = slide.dataset.fieldName;
    const ft = slide.dataset.fieldType;

    if (['text','email','phone','url','number','date','textarea'].includes(ft)) {
        const inp = slide.querySelector('[name="' + fn + '"]');
        if (inp) formData[fn] = inp.value;
    } else if (['dropdown','radio'].includes(ft)) {
        const hidden = slide.querySelector('input[type="hidden"][name="' + fn + '"]');
        if (hidden) formData[fn] = hidden.value;
        // Check "other" field
        const otherInput = slide.querySelector('[name="' + fn + '_other"]');
        if (otherInput && otherInput.style.display !== 'none') formData[fn + '_other'] = otherInput.value;
    } else if (ft === 'rating') {
        const hidden = slide.querySelector('input[type="hidden"][name="' + fn + '"]');
        if (hidden) formData[fn] = hidden.value;
    } else if (ft === 'checkbox' || ft === 'terms') {
        const hidden = slide.querySelector('input[type="hidden"][name="' + fn + '"]');
        if (hidden) formData[fn] = hidden.value;
    } else if (ft === 'signature') {
        formData[fn] = document.getElementById('sigdata-' + fn)?.value || '';
    }
    // Files handled separately during submission
}

function goNext() {
    if (!validateCurrentSlide()) return;
    collectFieldValue(slides[currentSlide]);

    if (!hasStarted && currentSlide === 0) { hasStarted = true; track('start'); }

    // Track field interaction
    const curSlide = slides[currentSlide];
    if (curSlide?.dataset?.fieldName) {
        track('interaction', {field: curSlide.dataset.fieldName});
    }

    // Find next visible slide
    if (currentSlide < slides.length - 1) {
        let found = false;
        let nextSlideIndex = currentSlide + 1;

        while(nextSlideIndex < slides.length) {
            const ns = slides[nextSlideIndex];
            if (ns?.dataset?.slide === 'payment' && !paymentEnabled) {
                nextSlideIndex++;
                continue;
            }
            if (ns?.dataset?.fieldName) {
                if (checkCondition(ns.dataset.fieldName)) {
                    found = true;
                    break;
                } else {
                    nextSlideIndex++; // skip hidden field
                }
            } else {
                found = true; // Not a field slide (welcome, review)
                break;
            }
        }

        if (found) {
            currentSlide = nextSlideIndex;
            const ns = slides[currentSlide];
            if (ns?.dataset?.slide === 'review') buildReview();
            updateUI();
        }
    }
}

function goBack() {
    if (currentSlide > 0) {
        let prevSlideIndex = currentSlide - 1;
        let found = false;

        while(prevSlideIndex >= 0) {
            const ps = slides[prevSlideIndex];
            if (ps?.dataset?.slide === 'payment' && !paymentEnabled) {
                prevSlideIndex--;
                continue;
            }
            if (ps?.dataset?.fieldName) {
                if (checkCondition(ps.dataset.fieldName)) {
                    found = true;
                    break;
                } else {
                    prevSlideIndex--; // skip hidden field
                }
            } else {
                found = true;
                break;
            }
        }

        if (found) {
            currentSlide = prevSlideIndex;
            updateUI();
        }
    }
}

// Keyboard
document.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) {
        const active = document.activeElement;
        if (active && active.tagName === 'TEXTAREA') return; // Allow newlines
        e.preventDefault();
        goNext();
    }
});

// Option cards
function selectOption(el) {
    const container = el.closest('.cf-options');
    container.querySelectorAll('.cf-opt').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');

    const val = el.dataset.value;
    const fn = container.dataset.name;
    const hidden = el.closest('.cf-slide').querySelector('input[type="hidden"][name="' + fn + '"]');
    if (hidden) hidden.value = val;

    // Handle "Other"
    const otherInput = document.getElementById('other-' + fn);
    if (otherInput) {
        if (val === '__OTHER__') {
            otherInput.style.display = 'block';
            otherInput.focus();
            return; // Don't auto-advance
        } else {
            otherInput.style.display = 'none';
        }
    }

    // Auto-advance after selection
    const errEl = document.getElementById('err-' + fn);
    if (errEl) errEl.style.display = 'none';
    setTimeout(() => goNext(), 400);
}

// Stars
function selectRating(el, val) {
    const container = el.closest('.cf-stars');
    const fn = container.dataset.name;
    container.querySelectorAll('.cf-star').forEach((s, i) => {
        s.classList.toggle('active', i < val);
    });
    const hidden = el.closest('.cf-slide').querySelector('input[type="hidden"][name="' + fn + '"]');
    if (hidden) hidden.value = val;

    const errEl = document.getElementById('err-' + fn);
    if (errEl) errEl.style.display = 'none';
    setTimeout(() => goNext(), 500);
}

// Checkbox
function toggleCheck(el) {
    const box = el.querySelector('.cf-check-box');
    box.classList.toggle('checked');
    const fn = el.dataset.name || el.closest('[data-name]')?.dataset.name;
    const hidden = el.closest('.cf-slide')?.querySelector('input[type="hidden"]');
    if (hidden) hidden.value = box.classList.contains('checked') ? 'Yes' : '';
}

// File
function handleFileSelect(input, fn) {
    const label = document.getElementById('fname-' + fn);
    if (input.files.length > 0 && label) label.textContent = input.files[0].name;
}

// Review
function buildReview() {
    const rc = document.getElementById('reviewContent');
    let html = '';
    document.querySelectorAll('.cf-slide[data-field-name]').forEach((slide, i) => {
        const fn = slide.dataset.fieldName;
        const ft = slide.dataset.fieldType;
        if (ft === 'content') return;
        if (!checkCondition(fn)) return; // Skip hidden logic fields
        
        const label = slide.querySelector('.cf-qlabel')?.textContent?.replace('*','').trim() || fn;
        let val = formData[fn] || '';
        if (ft === 'signature' && val) val = '✓ Signature provided';
        if (ft === 'file') {
            const finp = slide.querySelector('input[type="file"]');
            val = finp?.files?.[0]?.name || '';
        }
        html += '<div class="cf-review-item">';
        html += '<div class="cf-review-content">';
        html += '<div class="cf-review-label">' + label + '</div>';
        html += '<div class="cf-review-value">' + (val || '<span style="color:rgba(255,255,255,0.3)">—</span>') + '</div>';
        html += '</div>';
        html += '<div class="cf-review-edit" onclick="jumpToQuestion(' + i + ')">Edit</div>';
        html += '</div>';
    });
    rc.innerHTML = html;
}

function jumpToQuestion(fieldIndex) {
    // Find the actual slide index (+1 for welcome slide)
    const questionSlides = [];
    slides.forEach((s, i) => { if (s.dataset.fieldName) questionSlides.push(i); });
    if (questionSlides[fieldIndex] !== undefined) {
        currentSlide = questionSlides[fieldIndex];
        updateUI();
    }
}

// Submit
function submitForm() {
    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.innerHTML = '<ion-icon name="hourglass-outline"></ion-icon> Submitting...';

    if (paymentEnabled) {
        // Go to payment slide
        const paySlide = [...slides].findIndex(s => s.dataset.slide === 'payment');
        if (paySlide !== -1) {
            currentSlide = paySlide;
            updateUI();
            btn.disabled = false;
            btn.innerHTML = '<?= htmlspecialchars($submitText) ?>';
            return;
        }
    }

    doSubmit();
}

function doSubmit(paymentRef) {
    submitted = true;
    const fd = new FormData();

    // Add text data
    for (const [k, v] of Object.entries(formData)) {
        fd.append(k, v);
    }

    // Add files
    document.querySelectorAll('.cf-slide input[type="file"]').forEach(inp => {
        if (inp.files.length > 0) fd.append(inp.name, inp.files[0]);
    });

    if (paymentRef) fd.append('_payment_ref', paymentRef);

    fetch('/sf/' + formId + '/submit', {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const successSlide = [...slides].findIndex(s => s.dataset.slide === 'success');
            if (successSlide !== -1) {
                currentSlide = successSlide;
                const pb = document.getElementById('progressBar');
                if (pb) pb.style.width = '100%';
                updateUI();
            }
            if (redirectUrl) setTimeout(() => window.location.href = redirectUrl, 3000);
        }
    })
    .catch(() => {
        // Fallback: traditional form submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/sf/' + formId + '/submit';
        form.enctype = 'multipart/form-data';
        for (const [k, v] of Object.entries(formData)) {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = k; inp.value = v;
            form.appendChild(inp);
        }
        if (paymentRef) {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = '_payment_ref'; inp.value = paymentRef;
            form.appendChild(inp);
        }
        document.body.appendChild(form);
        form.submit();
    });
}

// Payment
function initiatePayment() {
    const btn = document.getElementById('btnPay');
    const errDiv = document.getElementById('payError');
    errDiv.style.display = 'none';

    let amount = paymentAmount;
    if (paymentAmountType === 'user_entered') {
        const uInput = document.getElementById('userPayAmount');
        amount = parseFloat(uInput?.value) || 0;
        if (amount <= 0) { errDiv.textContent = 'Please enter a valid amount'; errDiv.style.display = 'block'; return; }
    }

    // Find email from form data
    let email = '';
    for (const [k, v] of Object.entries(formData)) {
        if (k.toLowerCase().includes('email') && v.includes('@')) { email = v; break; }
    }
    if (!email) {
        for (const v of Object.values(formData)) {
            if (typeof v === 'string' && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) { email = v; break; }
        }
    }
    if (!email) email = 'customer@example.com';

    btn.disabled = true;
    btn.innerHTML = '<ion-icon name="hourglass-outline"></ion-icon> Processing...';

    if (paymentGateway === 'paystack') {
        const key = document.body.dataset.paystackKey;
        if (!key) { errDiv.textContent = 'Payment not configured'; errDiv.style.display = 'block'; btn.disabled = false; btn.innerHTML = '<ion-icon name="lock-closed"></ion-icon> Pay Now'; return; }

        const handler = PaystackPop.setup({
            key: key,
            email: email,
            amount: Math.round(amount * 100),
            currency: paymentCurrency,
            ref: 'SF-' + formId + '-' + Date.now(),
            callback: function(response) {
                doSubmit(response.reference);
            },
            onClose: function() {
                btn.disabled = false;
                btn.innerHTML = '<ion-icon name="lock-closed" style="font-size:18px"></ion-icon> Pay Now';
            }
        });
        handler.openIframe();
    } else if (paymentGateway === 'flutterwave') {
        const key = document.body.dataset.flutterwaveKey;
        if (!key) { errDiv.textContent = 'Payment not configured'; errDiv.style.display = 'block'; btn.disabled = false; btn.innerHTML = '<ion-icon name="lock-closed"></ion-icon> Pay Now'; return; }

        FlutterwaveCheckout({
            public_key: key,
            tx_ref: 'SF-' + formId + '-' + Date.now(),
            amount: amount,
            currency: paymentCurrency,
            customer: {email: email},
            callback: function(response) {
                doSubmit(String(response.transaction_id));
            },
            onclose: function() {
                btn.disabled = false;
                btn.innerHTML = '<ion-icon name="lock-closed" style="font-size:18px"></ion-icon> Pay Now';
            }
        });
    }
}

// Auto-redirect on success page load
<?php if ($isSuccess && !empty($settings['redirect_url'])): ?>
setTimeout(() => window.location.href = '<?= addslashes($settings['redirect_url']) ?>', 3000);
<?php endif; ?>

// Init
updateUI();
</script>
</body>
</html>

<?php else:
// ============================================================
// CLASSIC MODE (original behavior preserved)
// ============================================================
?>
<!DOCTYPE html>
<html>
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <title><?= htmlspecialchars($form['title']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #ffa600, <?= htmlspecialchars($themeColor) ?>); 
            min-height: 100vh; 
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        }
        .form-container { max-width: 640px; margin: 40px auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .form-header { margin-bottom: 30px; text-align: center; }
        .form-header h2 { font-weight: 700; color: #333; }
        .btn-submit { width: 100%; padding: 12px; font-weight: 600; font-size: 1.1rem; }
        .signature-wrapper { border: 2px dashed #ccc; border-radius: 8px; background: #fafafa; position: relative; }
        .signature-pad { width: 100%; height: 200px; display: block; border-radius: 8px; }
        .signature-clear { position: absolute; top: 10px; right: 10px; z-index: 10; }
        .cf-stars-classic{display:flex;gap:6px;margin-top:4px}
        .cf-star-c{font-size:28px;cursor:pointer;color:#ddd;transition:color 0.2s}
        .cf-star-c:hover,.cf-star-c.active{color:#d97706}
        
        /* Prevent labels from turning red on validation errors */
        .form-label,
        .form-check-label,
        .is-invalid ~ .form-label, 
        .is-invalid ~ .form-check-label, 
        .was-validated :invalid ~ .form-label, 
        .was-validated :invalid ~ .form-check-label {
            color: #333 !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <?php if ($paymentEnabled && $paymentGateway === 'paystack'): ?>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <?php elseif ($paymentEnabled && $paymentGateway === 'flutterwave'): ?>
    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <?php endif; ?>
</head>
<body data-paystack-key="<?= htmlspecialchars($paystackPublicKey ?? '') ?>" data-flutterwave-key="<?= htmlspecialchars($flutterwavePublicKey ?? '') ?>">

<div class="container">
    <div class="form-container">
        <div class="form-header">
            <?php if (!empty($form['logo'])): ?>
                <div class="text-center mb-3">
                    <img src="<?= htmlspecialchars($form['logo']) ?>" alt="Company Logo" style="max-width: 250px; max-height: 120px;" onerror="this.style.display='none'">
                </div>
            <?php endif; ?>
            <h2><?= htmlspecialchars($form['title']) ?></h2>
            <?php if (!empty($form['description'])): ?>
                <p class="text-muted"><?= htmlspecialchars($form['description']) ?></p>
            <?php endif; ?>
        </div>
        
        <?php if ($isSuccess): ?>
            <div class="alert alert-success text-center">
                <h4><?= htmlspecialchars($tyTitle) ?></h4>
                <p><?= htmlspecialchars($tyText) ?></p>
                <?php if (!empty($settings['redirect_url'])): ?>
                    <p class="small text-muted mt-2">Redirecting...</p>
                    <script>setTimeout(() => window.location.href = '<?= addslashes($settings['redirect_url']) ?>', 3000);</script>
                <?php else: ?>
                    <a href="/sf/<?= $form['id'] ?>" class="btn btn-outline-success mt-2">Submit another</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
        
            <form id="smartForm" action="/sf/<?= $form['id'] ?>/submit" method="POST" enctype="multipart/form-data">
                <?php 
                if ($structure && is_array($structure)):
                    $steps = [];
                    $currentStep = 0;
                    $steps[$currentStep] = ['fields' => [], 'title' => '', 'btn_text' => 'Next'];
                    
                    foreach ($structure as $field) {
                        if ($field['type'] === 'section_break') {
                            $currentStep++;
                            $steps[$currentStep] = [
                                'fields' => [], 
                                'title' => $field['label'] ?? '', 
                                'btn_text' => $field['options'] ?? 'Next'
                            ];
                        } else {
                            $steps[$currentStep]['fields'][] = $field;
                        }
                    }
                    
                    $totalSteps = count($steps);
                    
                    foreach ($steps as $index => $step):
                        $isLastStep = ($index === $totalSteps - 1);
                        $isActive = ($index === 0);
                ?>
                    <div class="form-step" id="step-<?= $index ?>" style="<?= $isActive ? '' : 'display:none;' ?>">
                        
                        <?php if (!empty($step['title'])): ?>
                            <h4 class="mb-4 text-primary"><?= htmlspecialchars($step['title']) ?></h4>
                        <?php endif; ?>

                        <?php foreach ($step['fields'] as $field): 
                            $required = isset($field['required']) && $field['required'] ? 'required' : '';
                            $fieldName = htmlspecialchars($field['name']);
                            $label = htmlspecialchars($field['label']);
                            $placeholder = htmlspecialchars($field['placeholder'] ?? '');
                            
                            if ($field['type'] == 'content') {
                                echo '<div class="mb-4">';
                                if (!empty($label) && $label !== 'Information') echo '<h5 class="fw-bold mb-2">'.$label.'</h5>';
                                echo '<div class="text-muted">'.($field['options'] ?? '').'</div>';
                                echo '</div>';
                                continue;
                            }
                        ?>
                            <div class="mb-4 input-group-container">
                                <label class="form-label fw-bold">
                                    <?= $label ?>
                                    <?php if ($required): ?><span class="text-danger">*</span><?php endif; ?>
                                </label>
                                
                                <?php if ($field['type'] == 'text'): ?>
                                    <input type="text" name="<?= $fieldName ?>" class="form-control" placeholder="<?= $placeholder ?>" <?= $required ?>>
                                    
                                <?php elseif ($field['type'] == 'phone'): ?>
                                    <input type="tel" name="<?= $fieldName ?>" class="form-control" placeholder="<?= $placeholder ?: '+1 (555) 000-0000' ?>" <?= $required ?>>
                                    
                                <?php elseif ($field['type'] == 'email'): ?>
                                    <input type="email" name="<?= $fieldName ?>" class="form-control" placeholder="<?= $placeholder ?>" <?= $required ?>>
        
                                <?php elseif ($field['type'] == 'url'): ?>
                                    <input type="url" name="<?= $fieldName ?>" class="form-control" placeholder="<?= $placeholder ?: 'https://' ?>" <?= $required ?>>
        
                                <?php elseif ($field['type'] == 'number'): ?>
                                    <input type="number" name="<?= $fieldName ?>" class="form-control" placeholder="<?= $placeholder ?>" <?= $required ?>>

                                <?php elseif ($field['type'] == 'date'): ?>
                                    <input type="date" name="<?= $fieldName ?>" class="form-control" <?= $required ?>>

                                <?php elseif ($field['type'] == 'file'): ?>
                                    <input type="file" name="<?= $fieldName ?>" class="form-control" <?= $required ?>>
                                    
                                <?php elseif ($field['type'] == 'textarea'): ?>
                                    <textarea name="<?= $fieldName ?>" class="form-control" rows="3" placeholder="<?= $placeholder ?>" <?= $required ?>></textarea>
         
                                <?php elseif ($field['type'] == 'dropdown'): ?>
                                    <select name="<?= $fieldName ?>" class="form-select" <?= $required ?> onchange="handleDropdownChange(this, '<?= $fieldName ?>')">
                                        <option value="">Select an option...</option>
                                        <?php 
                                        $options = explode(',', $field['options']);
                                        foreach($options as $opt): 
                                        ?>
                                            <option value="<?= trim(htmlspecialchars($opt)) ?>"><?= trim(htmlspecialchars($opt)) ?></option>
                                        <?php endforeach; ?>
                                        <?php if (isset($field['allow_other']) && $field['allow_other']): ?>
                                            <option value="__OTHER__">Other (please specify)</option>
                                        <?php endif; ?>
                                    </select>
                                    <?php if (isset($field['allow_other']) && $field['allow_other']): ?>
                                        <input type="text" name="<?= $fieldName ?>_other" id="<?= $fieldName ?>_other" class="form-control mt-2" placeholder="Please specify..." style="display:none;">
                                    <?php endif; ?>

                                <?php elseif ($field['type'] == 'radio'): ?>
                                    <?php $opts = array_map('trim', explode(',', $field['options'])); ?>
                                    <?php foreach ($opts as $opt): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="<?= $fieldName ?>" value="<?= htmlspecialchars($opt) ?>" id="r_<?= $fieldName ?>_<?= md5($opt) ?>" <?= $required ?>>
                                        <label class="form-check-label" for="r_<?= $fieldName ?>_<?= md5($opt) ?>"><?= htmlspecialchars($opt) ?></label>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php if (!empty($field['allow_other'])): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="<?= $fieldName ?>" value="__OTHER__" id="r_<?= $fieldName ?>_other" onchange="document.getElementById('<?= $fieldName ?>_other_r').style.display='block'">
                                        <label class="form-check-label" for="r_<?= $fieldName ?>_other">Other</label>
                                    </div>
                                    <input type="text" name="<?= $fieldName ?>_other" id="<?= $fieldName ?>_other_r" class="form-control mt-1" placeholder="Please specify..." style="display:none;">
                                    <?php endif; ?>

                                <?php elseif ($field['type'] == 'rating'): ?>
                                    <div class="cf-stars-classic" data-name="<?= $fieldName ?>">
                                        <?php for ($s = 1; $s <= 5; $s++): ?>
                                        <span class="cf-star-c" data-value="<?= $s ?>" onclick="classicRating(this,<?= $s ?>,'<?= $fieldName ?>')">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <input type="hidden" name="<?= $fieldName ?>" id="rating_<?= $fieldName ?>" <?= $required ?>>
         
                                <?php elseif ($field['type'] == 'terms'): ?>
                                    <div class="border rounded p-3 mb-3" style="background: #f8f9fa;">
                                        <div class="border rounded p-3 mb-3" style="max-height: 250px; overflow-y: auto; background: white; font-size: 0.9rem;">
                                            <?= $field['options'] ?? 'No terms specified.' ?>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="<?= $fieldName ?>_accepted" value="1" id="terms_<?= $fieldName ?>" required>
                                            <label class="form-check-label fw-bold" for="terms_<?= $fieldName ?>">
                                                <?= $label ?> <span class="text-danger">*</span>
                                            </label>
                                        </div>
                                        <input type="hidden" name="<?= $fieldName ?>_timestamp" value="<?= date('Y-m-d H:i:s') ?>">
                                    </div>
                                    
                                <?php elseif ($field['type'] == 'checkbox'): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="<?= $fieldName ?>" value="Yes" id="chk_<?= $fieldName ?>" <?= $required ?>>
                                        <label class="form-check-label" for="chk_<?= $fieldName ?>">
                                            <?= $label ?>
                                        </label>
                                    </div>
                                    
                                <?php elseif ($field['type'] == 'signature'): ?>
                                    <div class="signature-wrapper">
                                        <canvas class="signature-pad" id="sig_<?= $fieldName ?>"></canvas>
                                        <button type="button" class="btn btn-sm btn-outline-secondary signature-clear" onclick="clearSignature('<?= $fieldName ?>')">Clear</button>
                                        <input type="hidden" name="<?= $fieldName ?>" id="input_sig_<?= $fieldName ?>" <?= $required ?>>
                                    </div>
                                    <div class="form-text text-muted">Please draw your signature above.</div>
                                <?php endif; ?>
                                <div class="invalid-feedback">This field is required.</div>
                            </div>
                        <?php endforeach; ?>

                        <div class="d-flex justify-content-between mt-4">
                            <?php if ($index > 0): ?>
                                <button type="button" class="btn btn-outline-secondary px-4" onclick="changeStep(<?= $index - 1 ?>)">Back</button>
                            <?php else: ?>
                                <div></div>
                            <?php endif; ?>

                            <?php if (!$isLastStep): ?>
                                <button type="button" class="btn btn-primary px-4" onclick="validateAndNext(<?= $index ?>)">
                                    <?= htmlspecialchars($step['btn_text'] ?: 'Next') ?> &rarr;
                                </button>
                            <?php else: ?>
                                <button type="submit" class="btn btn-success px-5 btn-submit"><?= htmlspecialchars($submitText) ?></button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </form>
            
        <?php endif; ?>
        
        <div class="mt-4 text-center text-muted small">
            Powered by <a href="#" class="text-decoration-none text-muted fw-bold">Casjoe Smart Forms</a>
        </div>
    </div>
</div>

<script>
    let hasStarted = false;
    const formId = <?= $form['id'] ?>;

    const fieldRules = <?= json_encode($rulesJs) ?>;

    function evaluateCondition(rule, dataObj) {
        const actualVal = dataObj[rule.field] || '';
        const targetVal = rule.val || '';
        let isMatch = false;
        let a = actualVal.toString().toLowerCase();
        let t = targetVal.toString().toLowerCase();

        switch(rule.op) {
            case '==': isMatch = (a === t); break;
            case '!=': isMatch = (a !== t); break;
            case 'contains': isMatch = (a.includes(t)); break;
            case '>': isMatch = (parseFloat(a) > parseFloat(t)); break;
            case '<': isMatch = (parseFloat(a) < parseFloat(t)); break;
            default: isMatch = (a === t);
        }
        return isMatch;
    }

    function applyClassicLogic() {
        const classicForm = document.getElementById('classicForm');
        if (!classicForm) return;
        const fd = new FormData(classicForm);
        let currentData = {};
        for (let [k, v] of fd.entries()) {
            currentData[k] = v;
        }

        for (const [fieldName, rule] of Object.entries(fieldRules)) {
            const isMatch = evaluateCondition(rule, currentData);
            const wrap = document.getElementById('wrap_' + fieldName);
            if (wrap) {
                wrap.style.display = isMatch ? 'block' : 'none';
                
                // If hidden, disable required inputs so HTML5 validation doesn't block submission
                const inputs = wrap.querySelectorAll('input, select, textarea');
                inputs.forEach(inp => {
                    if (!isMatch && inp.hasAttribute('required')) {
                        inp.dataset.wasRequired = 'true';
                        inp.removeAttribute('required');
                    } else if (isMatch && inp.dataset.wasRequired === 'true') {
                        inp.setAttribute('required', 'required');
                    }
                });
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const classicForm = document.getElementById('classicForm');
        if (classicForm) {
            classicForm.addEventListener('change', applyClassicLogic);
            classicForm.addEventListener('input', applyClassicLogic);
            applyClassicLogic();
        }
    });

    function track(event, data = {}) {
        fetch('/sf/' + formId + '/track', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ event: event, metadata: data })
        }).catch(() => {});
    }

    document.querySelectorAll('input, select, textarea').forEach(el => {
        el.addEventListener('focus', () => {
            if (!hasStarted) { hasStarted = true; track('start'); }
            track('interaction', { field: el.name });
        });
    });

    // Signature pads
    const signaturePads = {};
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.signature-pad').forEach(canvas => {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            const fieldName = canvas.id.replace('sig_', '');
            signaturePads[fieldName] = new SignaturePad(canvas, {backgroundColor: 'rgba(255,255,255,0)', penColor: 'rgb(0,0,0)'});
            signaturePads[fieldName].addEventListener("endStroke", () => {
                document.getElementById('input_sig_' + fieldName).value = signaturePads[fieldName].toDataURL("image/png");
            });
        });
    });

    function clearSignature(fieldName) {
        if (signaturePads[fieldName]) { signaturePads[fieldName].clear(); document.getElementById('input_sig_' + fieldName).value = ''; }
    }

    // Classic rating
    function classicRating(el, val, fn) {
        const stars = el.closest('.cf-stars-classic').querySelectorAll('.cf-star-c');
        stars.forEach((s, i) => s.classList.toggle('active', i < val));
        document.getElementById('rating_' + fn).value = val;
    }

    function changeStep(stepIndex) {
        document.querySelectorAll('.form-step').forEach(el => el.style.display = 'none');
        document.getElementById('step-' + stepIndex).style.display = 'block';
        window.scrollTo(0, 0);
    }

    function validateAndNext(currentIndex) {
        const stepEl = document.getElementById('step-' + currentIndex);
        const inputs = stepEl.querySelectorAll('input, select, textarea');
        let isValid = true;
        inputs.forEach(input => {
            // Skip hidden fields
            const wrap = input.closest('.classic-field');
            if (wrap && wrap.style.display === 'none') return;

            if (input.hasAttribute('required') && !input.value.trim() && input.type !== 'checkbox') {
                isValid = false; input.classList.add('is-invalid');
                if (input.type === 'hidden' && input.id.startsWith('input_sig_')) document.getElementById(input.id.replace('input_sig_','sig_')).classList.add('border','border-danger');
            } else if (input.hasAttribute('required') && input.type === 'checkbox' && !input.checked) {
                isValid = false; input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
                if (input.type === 'hidden' && input.id.startsWith('input_sig_')) document.getElementById(input.id.replace('input_sig_','sig_')).classList.remove('border','border-danger');
            }
        });
        if (isValid) changeStep(currentIndex + 1);
        else { const fe = stepEl.querySelector('.is-invalid'); if(fe) fe.scrollIntoView({behavior:'smooth',block:'center'}); }
    }

    function handleDropdownChange(selectElement, fieldName) {
        const otherInput = document.getElementById(fieldName + '_other');
        if (otherInput) {
            if (selectElement.value === '__OTHER__') { otherInput.style.display = 'block'; otherInput.required = true; }
            else { otherInput.style.display = 'none'; otherInput.required = false; otherInput.value = ''; }
        }
    }

    const smartFormEl = document.getElementById('smartForm');
    if(smartFormEl) {
        smartFormEl.addEventListener('submit', function(e) {
            for (const [fieldName, pad] of Object.entries(signaturePads)) {
                if (!pad.isEmpty()) document.getElementById('input_sig_' + fieldName).value = pad.toDataURL("image/png");
            }
        });
    }
</script>
</body>
</html>
<?php endif; ?>
