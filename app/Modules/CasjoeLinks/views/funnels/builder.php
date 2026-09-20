<?php
$stepIcons  = ['landing'=>'desktop-outline','sales'=>'cash-outline','form'=>'reader-outline','payment'=>'card-outline','thankyou'=>'checkmark-circle-outline','upsell'=>'trending-up-outline','downsell'=>'arrow-down-circle-outline'];
$stepLabels = ['landing'=>'Landing Page','sales'=>'Sales Page','form'=>'Opt-In Form','payment'=>'Checkout','thankyou'=>'Thank You','upsell'=>'1-Click Upsell','downsell'=>'Downsell Offer'];
$stepColors = ['landing'=>'#3b82f6','sales'=>'#8b5cf6','form'=>'#06b6d4','payment'=>'#f59e0b','thankyou'=>'#10b981','upsell'=>'#ef4444','downsell'=>'#ec4899'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Build: <?= htmlspecialchars($funnel['name']) ?> | Casjoe Links</title>
<link rel="stylesheet" href="/css/style.css">
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;}
body{font-family:'Inter',sans-serif;background:#f1f5f9;margin:0;}
/* TOP BAR */
.fb-bar{position:fixed;top:0;left:0;right:0;z-index:200;height:58px;background:#fff;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;padding:0 20px;gap:12px;}
.fb-bar-left{display:flex;align-items:center;gap:12px;flex:1;}
.fb-bar-right{display:flex;align-items:center;gap:10px;}
.fb-name{font-weight:700;font-size:1rem;color:#1e293b;}
.fb-badge{padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;text-transform:uppercase;background:linear-gradient(135deg,#FFA600,#000066);color:#fff;}
.fb-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:all .15s;font-family:'Inter',sans-serif;}
.btn-sec{background:#f1f5f9;color:#475569;}.btn-sec:hover{background:#e2e8f0;}
.btn-pri{background:#000066;color:#fff;}.btn-pri:hover{background:#0000aa;}
.btn-grn{background:#10b981;color:#fff;}.btn-grn:hover{background:#059669;}
.btn-pub{background:linear-gradient(135deg,#FFA600,#e08000);color:#fff;}.btn-pub:hover{opacity:.9;}
.dot{width:8px;height:8px;border-radius:50%;background:#fbbf24;}.dot.on{background:#10b981;}
.sav{font-size:12px;color:#94a3b8;display:none;align-items:center;gap:5px;}.sav.vis{display:inline-flex;}
/* LAYOUT */
.fb-layout{display:grid;grid-template-columns:220px 1fr;min-height:100vh;padding-top:58px;}
/* SIDEBAR */
.fb-side{background:#fff;border-right:1px solid #e2e8f0;padding:20px 14px;position:sticky;top:58px;height:calc(100vh - 58px);overflow-y:auto;}
.side-title{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:12px;}
.pal-item{display:flex;align-items:center;gap:10px;padding:11px 12px;border-radius:10px;margin-bottom:8px;cursor:pointer;transition:all .15s;border:1.5px solid #e2e8f0;background:#f8fafc;color:#334155;font-size:13px;font-weight:500;user-select:none;}
.pal-item:hover{border-color:#FFA600;background:#fffbf0;transform:translateX(3px);}
.pal-ic{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;color:#fff;flex-shrink:0;}
.side-sep{border:none;border-top:1px solid #e2e8f0;margin:16px 0;}
.side-note{background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:10px 12px;font-size:11.5px;color:#0369a1;line-height:1.5;}
/* CANVAS */
.fb-canvas{padding:30px 40px;display:flex;flex-direction:column;align-items:center;}
.canvas-inner{width:100%;max-width:620px;}
/* STEP CARD */
.step-card{background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;overflow:hidden;transition:box-shadow .2s,border-color .2s;cursor:grab;}
.step-card:active{cursor:grabbing;}
.step-card:hover{box-shadow:0 6px 20px rgba(0,0,0,.08);border-color:#cbd5e1;}
.step-card.sortable-chosen{box-shadow:0 12px 32px rgba(0,0,102,.15);border-color:#000066;}
.step-card.sortable-ghost{opacity:.35;}
.card-head{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid #f1f5f9;}
.card-left{display:flex;align-items:center;gap:12px;}
.step-num{width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;}
.step-ic{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;}
.card-title{font-weight:600;font-size:14px;color:#1e293b;}
.card-sub{font-size:11px;color:#94a3b8;margin-top:1px;}
.card-btns{display:flex;gap:6px;}
.c-btn{width:32px;height:32px;border-radius:8px;border:1.5px solid #e2e8f0;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:15px;transition:all .15s;}
.c-btn.ed{color:#3b82f6;}.c-btn.ed:hover{background:#eff6ff;border-color:#93c5fd;}
.c-btn.dl{color:#ef4444;}.c-btn.dl:hover{background:#fef2f2;border-color:#fca5a5;}
.card-body{padding:12px 18px 14px;font-size:12.5px;color:#64748b;line-height:1.6;}
.card-body b{font-weight:600;color:#475569;}
/* CONNECTORS */
.conn{display:flex;flex-direction:column;align-items:center;padding:3px 0;}
.conn-line{width:2px;height:20px;background:#cbd5e1;}
.conn-arr{width:0;height:0;border-left:7px solid transparent;border-right:7px solid transparent;border-top:9px solid #cbd5e1;}
/* EMPTY */
.cv-empty{border:2px dashed #e2e8f0;border-radius:16px;padding:80px 30px;text-align:center;color:#94a3b8;width:100%;}
.cv-empty ion-icon{font-size:48px;color:#cbd5e1;display:block;margin:0 auto 12px;}
.cv-empty h3{font-size:16px;font-weight:600;color:#64748b;margin:0 0 6px;}
.cv-empty p{font-size:13px;margin:0;}
/* DRAWER */
.overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:300;}
.overlay.on{display:block;}
.drawer{position:fixed;top:0;bottom:0;right:-100%;width:100%;max-width:560px;height:100%;background:#fff;z-index:400;display:flex;flex-direction:column;overflow:hidden;transition:right .28s cubic-bezier(.4,0,.2,1);box-shadow:-8px 0 40px rgba(0,0,0,.12);}
.drawer.on{right:0;}
.dr-head{display:flex;align-items:center;justify-content:space-between;padding:0 20px;height:58px;border-bottom:1px solid #e2e8f0;flex-shrink:0;}
.dr-head h2{margin:0;font-size:15px;font-weight:700;color:#1e293b;}
.dr-close{width:34px;height:34px;border-radius:8px;border:1.5px solid #e2e8f0;background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:18px;color:#64748b;transition:all .15s;}
.dr-close:hover{background:#f1f5f9;}
.dr-body{flex:1;min-height:0;overflow-y:auto;padding:20px;display:flex;flex-direction:column;gap:14px;}
.dr-foot{padding:14px 20px;border-top:1px solid #e2e8f0;display:flex;gap:10px;flex-shrink:0;}
.dr-foot .fb-btn{flex:1;justify-content:center;padding:10px;font-size:14px;}
/* FIELDS */
.fg{display:flex;flex-direction:column;gap:5px;}
.fg label{font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;}
.fg input,.fg select,.fg textarea{width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13.5px;font-family:'Inter',sans-serif;color:#1e293b;transition:border-color .15s;outline:none;background:#fff;}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:#000066;}
.fg small{font-size:11px;color:#64748b;}
.sec-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;padding-bottom:8px;border-bottom:1px solid #f1f5f9;margin-bottom:2px;}
.fs{display:none;flex-direction:column;gap:14px;}
/* EDITOR */
#editorBox{display:none;flex-direction:column;gap:8px;}
#editorBox.on{display:flex;}
.ed-wrap{border:1.5px solid #e2e8f0;border-radius:10px;overflow:hidden;max-height:320px;overflow-y:auto;}
.ed-wrap .casjoe-editor-workspace{min-height:160px;max-height:260px;}
/* TOAST */
.toast{position:fixed;bottom:24px;right:24px;z-index:999;background:#1e293b;color:#fff;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:500;box-shadow:0 8px 24px rgba(0,0,0,.2);transform:translateY(80px);opacity:0;transition:all .25s;pointer-events:none;}
.toast.on{transform:translateY(0);opacity:1;}
.toast.ok{background:#065f46;}.toast.err{background:#991b1b;}
@keyframes sp{to{transform:rotate(360deg);}}
.spin{animation:sp 1s linear infinite;display:inline-block;}
</style>
</head>
<body>

<!-- TOP BAR -->
<div class="fb-bar">
  <div class="fb-bar-left">
    <a href="/links/funnels" class="fb-btn btn-sec" style="padding:6px 12px">
      <ion-icon name="arrow-back-outline"></ion-icon> Back
    </a>
    <div class="dot <?= $funnel['status']==='active'?'on':'' ?>"></div>
    <span class="fb-name"><?= htmlspecialchars($funnel['name']) ?></span>
    <span class="fb-badge"><?= ucfirst($funnel['type']) ?> Funnel</span>
    <span class="sav" id="savInd"><ion-icon name="sync-outline" class="spin"></ion-icon> Saving&hellip;</span>
  </div>
  <div class="fb-bar-right">
    <?php if($funnel['status']==='active'): ?>
      <a href="/f/<?= (int)$funnel['id'] ?>" target="_blank" class="fb-btn btn-grn">
        <ion-icon name="open-outline"></ion-icon> View Live
      </a>
    <?php elseif(count($steps)>0): ?>
      <button class="fb-btn btn-pub" onclick="publishFunnel()">
        <ion-icon name="rocket-outline"></ion-icon> Publish Funnel
      </button>
    <?php endif; ?>
  </div>
</div>

<!-- LAYOUT -->
<div class="fb-layout">

  <!-- SIDEBAR -->
  <aside class="fb-side">
    <div class="side-title">Add Step</div>
    <div class="pal-item" onclick="addStep('landing')">
      <div class="pal-ic" style="background:#3b82f6"><ion-icon name="desktop-outline"></ion-icon></div>Landing Page
    </div>
    <div class="pal-item" onclick="addStep('sales')">
      <div class="pal-ic" style="background:#8b5cf6"><ion-icon name="cash-outline"></ion-icon></div>Sales Page
    </div>
    <div class="pal-item" onclick="addStep('form')">
      <div class="pal-ic" style="background:#06b6d4"><ion-icon name="reader-outline"></ion-icon></div>Opt-In Form
    </div>
    <div class="pal-item" onclick="addStep('payment')">
      <div class="pal-ic" style="background:#f59e0b"><ion-icon name="card-outline"></ion-icon></div>Checkout
    </div>
    <div class="pal-item" onclick="addStep('thankyou')">
      <div class="pal-ic" style="background:#10b981"><ion-icon name="checkmark-circle-outline"></ion-icon></div>Thank You
    </div>
    <div class="pal-item" onclick="addStep('upsell')">
      <div class="pal-ic" style="background:#ef4444"><ion-icon name="trending-up-outline"></ion-icon></div>1-Click Upsell
    </div>
    <div class="pal-item" onclick="addStep('downsell')">
      <div class="pal-ic" style="background:#ec4899"><ion-icon name="arrow-down-circle-outline"></ion-icon></div>Downsell Offer
    </div>
    <hr class="side-sep">
    <div class="side-note">
      <ion-icon name="information-circle-outline" style="vertical-align:middle"></ion-icon>
      <strong>Tip:</strong> Drag &amp; drop steps to reorder the funnel flow.
    </div>
  </aside>

  <!-- CANVAS -->
  <main class="fb-canvas">
    <div class="canvas-inner">
      <ul id="stepList" style="list-style:none;padding:0;margin:0">

        <?php if(empty($steps)): ?>
          <div class="cv-empty">
            <ion-icon name="git-branch-outline"></ion-icon>
            <h3>Your funnel is empty</h3>
            <p>Click a step type on the left to begin building.</p>
          </div>
        <?php else: ?>

          <?php foreach($steps as $idx=>$step):
            $t   = $step['step_type'];
            $cfg = json_decode($step['config']??'{}',true) ?: [];
            $ic  = $stepIcons[$t]  ?? 'ellipse-outline';
            $lb  = $stepLabels[$t] ?? ucfirst($t);
            $cl  = $stepColors[$t] ?? '#64748b';
            $bg  = $cl.'22';
            // Build preview line
            if($t==='landing'||$t==='sales') {
              $prev = 'Headline: <b>'.htmlspecialchars($cfg['headline']??'Not set').'</b>';
              if(!empty($cfg['cta_text'])) $prev .= ' &middot; CTA: <b>'.htmlspecialchars($cfg['cta_text']).'</b>';
            } elseif($t==='form') {
              $prev = 'Form: <b>'.htmlspecialchars($cfg['form_title']??'Not configured').'</b>';
            } elseif($t==='payment') {
              $prev = 'Product: <b>'.htmlspecialchars($cfg['product_name']??'Not selected').'</b>';
            } elseif($t==='thankyou') {
              $prev = 'Message: <b>'.htmlspecialchars(mb_substr($cfg['message']??'Thank you!',0,60)).'</b>';
            } elseif($t==='upsell'||$t==='downsell') {
              $prev = 'Offer: <b>'.htmlspecialchars($cfg['product_name']??$cfg['headline']??'Special Offer').'</b> &middot; Price: <b>'.($cfg['currency']??'NGN').' '.number_format($cfg['amount']??0,2).'</b>';
            } else { $prev = ''; }
          ?>

            <?php if($idx > 0): ?>
              <li style="list-style:none">
                <div class="conn"><div class="conn-line"></div><div class="conn-arr"></div></div>
              </li>
            <?php endif; ?>

            <li class="step-card" data-step-id="<?= (int)$step['id'] ?>" data-step-type="<?= htmlspecialchars($t) ?>">
              <div class="card-head">
                <div class="card-left">
                  <div class="step-num" style="background:<?= $cl ?>"><?= $idx+1 ?></div>
                  <div class="step-ic" style="background:<?= $bg ?>">
                    <ion-icon name="<?= $ic ?>" style="color:<?= $cl ?>;font-size:18px"></ion-icon>
                  </div>
                  <div>
                    <div class="card-title"><?= $lb ?></div>
                    <div class="card-sub">Step <?= $idx+1 ?></div>
                  </div>
                </div>
                <div class="card-btns">
                  <button class="c-btn ed" title="Configure"
                    onclick="openDrawer(<?= (int)$step['id'] ?>,'<?= $t ?>')">
                    <ion-icon name="settings-outline"></ion-icon>
                  </button>
                  <button class="c-btn dl" title="Delete"
                    onclick="deleteStep(<?= (int)$step['id'] ?>,<?= (int)$funnel['id'] ?>)">
                    <ion-icon name="trash-outline"></ion-icon>
                  </button>
                </div>
              </div>
              <?php if($prev): ?>
                <div class="card-body"><?= $prev ?></div>
              <?php endif; ?>
            </li>

          <?php endforeach; ?>
        <?php endif; ?>

      </ul>
    </div>
  </main>
</div>

<!-- OVERLAY -->
<div class="overlay" id="ov" onclick="closeDrawer()"></div>

<!-- DRAWER -->
<div class="drawer" id="dr">
  <div class="dr-head">
    <h2 id="drTitle">Configure Step</h2>
    <div style="display:flex;gap:10px;align-items:center;">
      <button class="fb-btn btn-sec" onclick="closeDrawer()" style="padding:6px 12px;font-size:13px;border-radius:6px;height:34px;">Cancel</button>
      <button class="fb-btn btn-pri" onclick="saveStep()" style="padding:6px 14px;font-size:13px;border-radius:6px;height:34px;">
        <ion-icon name="checkmark-outline"></ion-icon> Save
      </button>
    </div>
  </div>

  <div class="dr-body">

    <!-- LANDING / SALES FIELDS -->
    <div id="fs-landing" class="fs">
      <div class="fg">
        <label>Headline</label>
        <input type="text" id="f-headline" placeholder="e.g. Transform Your Business Today">
      </div>
      <div class="fg">
        <label>Sub-headline</label>
        <input type="text" id="f-subheadline" placeholder="e.g. Join 10,000+ happy customers">
      </div>
      <div class="fg">
        <label>CTA Button</label>
        <div style="display:flex;gap:8px;align-items:center;">
          <input type="text" id="f-cta_text" placeholder="e.g. Get Started Now" style="flex:1;margin:0;">
          <input type="color" id="f-cta_color" value="#FFA600"
            title="Button colour"
            style="width:42px;height:42px;padding:3px;border:1.5px solid #e2e8f0;border-radius:8px;cursor:pointer;flex-shrink:0;">
        </div>
      </div>
    </div>

    <!-- OPT-IN FORM FIELDS -->
    <div id="fs-form" class="fs">
      <div class="fg">
        <label>Step Title</label>
        <input type="text" id="f-form_title" placeholder="e.g. Enter your details">
      </div>
      <div class="fg">
        <label>Smart Form</label>
        <select id="f-smart_form_id"><option value="">-- Loading forms... --</option></select>
        <small>Don't see your form?
          <a href="/smart-forms" target="_blank" style="color:#000066">Create one in Smart Forms &rarr;</a>
        </small>
      </div>
      <div class="fg">
        <label>Submit Button Text</label>
        <input type="text" id="f-submit_text" placeholder="e.g. Send Me Access">
      </div>
    </div>

    <!-- PAYMENT FIELDS -->
    <div id="fs-payment" class="fs">
      <div class="fg">
        <label>Product / Service</label>
        <select id="f-product_id"><option value="">-- Loading products... --</option></select>
      </div>
      <div class="fg">
        <label>Page Headline</label>
        <input type="text" id="f-pay_headline" placeholder="e.g. Complete Your Purchase">
      </div>
      <div class="fg">
        <label>Order Summary</label>
        <textarea id="f-order_summary" rows="3" placeholder="What the customer is getting..."></textarea>
      </div>
      <div class="sec-lbl" style="margin-top:10px;">Order Bump</div>
      <div class="fg" style="flex-direction:row;align-items:center;gap:8px">
        <input type="checkbox" id="f-has_order_bump" style="width:auto;margin:0">
        <label for="f-has_order_bump" style="text-transform:none;letter-spacing:0;font-size:13px;color:#334155;cursor:pointer;font-weight:600">
          Enable Order Bump
        </label>
      </div>
      <div class="fg">
        <label>Bump Title</label>
        <input type="text" id="f-bump_title" placeholder="e.g. Yes, add 1-on-1 Coaching">
      </div>
      <div class="fg">
        <label>Bump Amount</label>
        <input type="number" step="0.01" id="f-bump_amount" placeholder="e.g. 5000">
      </div>
      <div class="fg">
        <label>Bump Description</label>
        <textarea id="f-bump_description" rows="2" placeholder="Brief description of the bump offer..."></textarea>
      </div>
    </div>

    <!-- THANK YOU FIELDS -->
    <div id="fs-thankyou" class="fs">
      <div class="fg">
        <label>Headline</label>
        <input type="text" id="f-ty_headline" placeholder="e.g. You're In!">
      </div>
      <div class="fg">
        <label>Message</label>
        <textarea id="f-message" rows="3" placeholder="Thank you for your purchase!"></textarea>
      </div>
      <div class="fg">
        <label>Redirect URL <span style="font-weight:400;text-transform:none">(optional)</span></label>
        <input type="url" id="f-redirect_url" placeholder="https://...">
      </div>
      <div class="fg" style="flex-direction:row;align-items:center;gap:8px">
        <input type="checkbox" id="f-auto_redirect" style="width:auto;margin:0">
        <label for="f-auto_redirect"
          style="text-transform:none;letter-spacing:0;font-size:13px;color:#334155;cursor:pointer;font-weight:400">
          Auto-redirect after 5 seconds
        </label>
      </div>
    </div>

    <!-- UPSELL / DOWNSELL FIELDS -->
    <div id="fs-offer" class="fs">
      <div class="fg">
        <label>Headline</label>
        <input type="text" id="f-offer_headline" placeholder="e.g. Wait! Special One-Time Offer">
      </div>
      <div class="fg">
        <label>Product Name</label>
        <input type="text" id="f-offer_product_name" placeholder="e.g. VIP Upgrade">
      </div>
      <div class="fg" style="flex-direction:row;gap:10px;">
        <div style="flex:1;">
          <label>Amount</label>
          <input type="number" step="0.01" id="f-offer_amount" placeholder="e.g. 99.00">
        </div>
        <div style="width:80px;">
          <label>Currency</label>
          <input type="text" id="f-offer_currency" placeholder="USD" value="USD">
        </div>
      </div>
      <div class="fg">
        <label>Accept Button Text</label>
        <input type="text" id="f-accept_text" placeholder="e.g. Yes, Upgrade My Order">
      </div>
      <div class="fg">
        <label>Decline Link Text</label>
        <input type="text" id="f-decline_text" placeholder="e.g. No thanks, I'll pass">
      </div>
    </div>

    <!-- CASJOE VISUAL EDITOR
         IMPORTANT: This is PHP-rendered directly in the page.
         It is NEVER inside a JS string or template literal.
         It is simply hidden/shown via CSS based on step type. -->
    <div id="editorBox">
      <div class="sec-lbl">Page Content (Visual Editor)</div>
      <div class="ed-wrap">
        <?php
          $editorName  = 'drawer_content';
          $editorValue = '';
          include dirname(__DIR__, 4) . '/Views/partials/casjoe_editor.php';
        ?>
      </div>
    </div>

  </div><!-- /.dr-body -->

</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script>
const FID   = <?= (int)$funnel['id'] ?>;
const CLRS  = <?= json_encode($stepColors) ?>;
const LBLS  = <?= json_encode($stepLabels) ?>;

let SID = null, STYPE = null;  // active step id / type

/* ── SORTABLE ── */
const listEl = document.getElementById('stepList');
if (listEl) {
  Sortable.create(listEl, {
    animation: 200,
    ghostClass: 'sortable-ghost',
    chosenClass: 'sortable-chosen',
    filter: '.conn',
    draggable: '.step-card',
    onEnd: () => { renum(); saveOrder(); }
  });
}
function renum() {
  listEl.querySelectorAll('.step-card').forEach((c, i) => {
    const n = c.querySelector('.step-num');
    const s = c.querySelector('.card-sub');
    if (n) n.textContent = i + 1;
    if (s) s.textContent = 'Step ' + (i + 1);
  });
}
async function saveOrder() {
  const ids = Array.from(listEl.querySelectorAll('.step-card')).map(c => c.dataset.stepId);
  sv(true);
  try {
    await fetch('/links/funnels/reorder', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ funnel_id: FID, step_ids: ids })
    });
  } finally { sv(false); }
}

/* ── ADD STEP ── */
async function addStep(type) {
  sv(true);
  try {
    const r = await fetch('/links/funnels/steps', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ funnel_id: FID, step_type: type })
    });
    r.ok ? location.reload() : toast('Failed to add step', 'err');
  } finally { sv(false); }
}

/* ── DELETE STEP ── */
async function deleteStep(stepId, funnelId) {
  if (!confirm('Delete this step? This cannot be undone.')) return;
  sv(true);
  try {
    const r = await fetch('/links/funnels/steps/' + stepId + '/delete', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ funnel_id: funnelId })
    });
    r.ok ? location.reload() : toast('Failed to delete', 'err');
  } finally { sv(false); }
}

/* ── PUBLISH ── */
async function publishFunnel() {
  if (!confirm('Publish this funnel? It will go live immediately.')) return;
  const r = await fetch('/links/funnels/toggle/' + FID, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ status: 'active' })
  });
  if (r.ok) { toast('Funnel published! Reloading...', 'ok'); setTimeout(() => location.reload(), 1400); }
  else toast('Failed to publish', 'err');
}

/* ── DRAWER OPEN ── */
async function openDrawer(stepId, stepType) {
  SID = stepId; STYPE = stepType;
  document.getElementById('drTitle').textContent = 'Configure: ' + (LBLS[stepType] || stepType);

  // Show correct fieldset
  document.querySelectorAll('.fs').forEach(e => e.style.display = 'none');
  const eb = document.getElementById('editorBox');

  if (stepType === 'landing' || stepType === 'sales') {
    document.getElementById('fs-landing').style.display = 'flex';
    eb.classList.add('on');
  } else if (stepType === 'form') {
    document.getElementById('fs-form').style.display = 'flex';
    eb.classList.remove('on');
    await loadForms();
  } else if (stepType === 'payment') {
    document.getElementById('fs-payment').style.display = 'flex';
    eb.classList.remove('on');
    await loadProducts();
  } else if (stepType === 'thankyou') {
    document.getElementById('fs-thankyou').style.display = 'flex';
    eb.classList.remove('on');
  } else if (stepType === 'upsell' || stepType === 'downsell') {
    document.getElementById('fs-offer').style.display = 'flex';
    eb.classList.add('on');
  }

  // Open UI
  document.getElementById('ov').classList.add('on');
  document.getElementById('dr').classList.add('on');

  // Load saved config
  try {
    const r    = await fetch('/links/funnels/steps/' + stepId);
    const data = await r.json();
    if (data.success) populate(stepType, data.config || {});
  } catch(e) { toast('Could not load config', 'err'); }
}

/* ── DRAWER CLOSE ── */
function closeDrawer() {
  document.getElementById('ov').classList.remove('on');
  document.getElementById('dr').classList.remove('on');
  const ws = document.getElementById('casjoe-workspace');
  if (ws) ws.innerHTML = '';
  SID = null; STYPE = null;
}

/* ── POPULATE FIELDS ── */
function populate(type, cfg) {
  const set = (id, v) => {
    const e = document.getElementById(id);
    if (!e || v == null) return;
    e.type === 'checkbox' ? e.checked = !!v : e.value = v;
  };
  if (type === 'landing' || type === 'sales') {
    set('f-headline', cfg.headline);
    set('f-subheadline', cfg.subheadline);
    set('f-cta_text', cfg.cta_text);
    set('f-cta_color', cfg.cta_color);
    const ws = document.getElementById('casjoe-workspace');
    if (ws && cfg.content) ws.innerHTML = cfg.content;
  } else if (type === 'form') {
    set('f-form_title', cfg.form_title);
    set('f-smart_form_id', cfg.smart_form_id);
    set('f-submit_text', cfg.submit_text);
  } else if (type === 'payment') {
    set('f-product_id', cfg.product_id);
    set('f-pay_headline', cfg.pay_headline);
    set('f-order_summary', cfg.order_summary);
    set('f-has_order_bump', cfg.has_order_bump);
    set('f-bump_title', cfg.bump_title);
    set('f-bump_amount', cfg.bump_amount);
    set('f-bump_description', cfg.bump_description);
  } else if (type === 'thankyou') {
    set('f-ty_headline', cfg.ty_headline);
    set('f-message', cfg.message);
    set('f-redirect_url', cfg.redirect_url);
    set('f-auto_redirect', cfg.auto_redirect);
  } else if (type === 'upsell' || type === 'downsell') {
    set('f-offer_headline', cfg.headline);
    set('f-offer_product_name', cfg.product_name);
    set('f-offer_amount', cfg.amount);
    set('f-offer_currency', cfg.currency);
    set('f-accept_text', cfg.accept_text);
    set('f-decline_text', cfg.decline_text);
    const ws = document.getElementById('casjoe-workspace');
    if (ws && cfg.content) ws.innerHTML = cfg.content;
  }
}

/* ── COLLECT FIELDS ── */
function collect(type) {
  const g = id => { const e = document.getElementById(id); if (!e) return ''; return e.type === 'checkbox' ? e.checked : e.value; };
  if (type === 'landing' || type === 'sales') {
    const ws = document.getElementById('casjoe-workspace');
    return { headline: g('f-headline'), subheadline: g('f-subheadline'), cta_text: g('f-cta_text'), cta_color: g('f-cta_color'), content: ws ? ws.innerHTML : '' };
  }
  if (type === 'form')    return { form_title: g('f-form_title'), smart_form_id: g('f-smart_form_id'), submit_text: g('f-submit_text') };
  if (type === 'payment') return { 
    product_id: g('f-product_id'), 
    pay_headline: g('f-pay_headline'), 
    order_summary: g('f-order_summary'),
    has_order_bump: g('f-has_order_bump'),
    bump_title: g('f-bump_title'),
    bump_amount: g('f-bump_amount'),
    bump_description: g('f-bump_description')
  };
  if (type === 'thankyou') return { ty_headline: g('f-ty_headline'), message: g('f-message'), redirect_url: g('f-redirect_url'), auto_redirect: g('f-auto_redirect') };
  if (type === 'upsell' || type === 'downsell') {
    const ws = document.getElementById('casjoe-workspace');
    return {
      headline: g('f-offer_headline'),
      product_name: g('f-offer_product_name'),
      amount: g('f-offer_amount'),
      currency: g('f-offer_currency'),
      accept_text: g('f-accept_text'),
      decline_text: g('f-decline_text'),
      content: ws ? ws.innerHTML : ''
    };
  }
  return {};
}

/* ── SAVE STEP ── */
async function saveStep() {
  if (!SID) return;
  sv(true);
  try {
    const r = await fetch('/links/funnels/steps/' + SID, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ config: collect(STYPE) })
    });
    if (r.ok) { toast('Saved!', 'ok'); closeDrawer(); setTimeout(() => location.reload(), 800); }
    else toast('Save failed', 'err');
  } finally { sv(false); }
}

/* ── DROPDOWNS ── */
async function loadForms() {
  const sel = document.getElementById('f-smart_form_id');
  sel.innerHTML = '<option>Loading...</option>';
  try {
    const d = await (await fetch('/links/funnels/forms')).json();
    sel.innerHTML = '<option value="">-- Select a Form --</option>';
    (d.forms || []).forEach(f => { const o = document.createElement('option'); o.value = f.id; o.textContent = f.title; sel.appendChild(o); });
  } catch { sel.innerHTML = '<option>Failed to load</option>'; }
}
async function loadProducts() {
  const sel = document.getElementById('f-product_id');
  sel.innerHTML = '<option>Loading...</option>';
  try {
    const d = await (await fetch('/links/funnels/products')).json();
    sel.innerHTML = '<option value="">-- Select a Product --</option>';
    (d.products || []).forEach(p => { const o = document.createElement('option'); o.value = p.id; o.textContent = p.name + (p.price ? ' — ' + p.price : ''); sel.appendChild(o); });
  } catch { sel.innerHTML = '<option>Failed to load</option>'; }
}

/* ── UTILS ── */
function sv(v) { document.getElementById('savInd').classList.toggle('vis', v); }
function toast(msg, cls = '') {
  const e = document.getElementById('toast');
  e.textContent = msg; e.className = 'toast ' + cls; e.classList.add('on');
  setTimeout(() => e.classList.remove('on'), 2800);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });
</script>
</body>
</html>
