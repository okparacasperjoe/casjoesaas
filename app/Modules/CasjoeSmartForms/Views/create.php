<?php 
$title = isset($form) ? 'Edit Form' : 'Create Smart Form'; 
include __DIR__ . '/layout/header.php'; 
?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<style>
/* CSS Reset and Variables */
:root {
  --bg: #f8fafc; --bg-card: #ffffff;
  --text-main: #0f172a; --text-sub: #64748b;
  --border: #e2e8f0;
  --pri: #000066; --pri-hov: #000044;
  --acc: #FFA600; --acc-hov: #e69500;
  --rad: 12px;
  --sw: 320px; /* Sidebar width */
  --dw: 400px; /* Drawer width */
}
* { box-sizing: border-box; }
body { margin: 0; padding: 0; font-family: 'Inter', -apple-system, sans-serif; background: var(--bg); color: var(--text-main); overflow: hidden; height: 100vh; }

/* LAYOUT */
.fb-wrap { display: flex; height: calc(100vh - 60px); width: 100%; overflow: hidden; background: var(--bg); border-radius: var(--rad); box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid var(--border); position: relative; }
.fb-head { position: absolute; top: 0; left: 0; right: 0; height: 60px; background: var(--bg-card); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 20px; z-index: 10; border-radius: var(--rad) var(--rad) 0 0; }
.fb-head-left { display: flex; align-items: center; gap: 15px; }
.fb-head-title { font-weight: 600; font-size: 16px; margin: 0; padding: 0; }
.fb-head-title input { border: 1px solid transparent; background: transparent; font-weight: 600; font-size: 16px; padding: 4px 8px; border-radius: 4px; width: 250px; }
.fb-head-title input:hover { border-color: var(--border); background: #f1f5f9; }
.fb-head-title input:focus { outline: none; border-color: var(--pri); background: #fff; }

.fb-btn { border: none; border-radius: 8px; padding: 8px 16px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 14px; text-decoration: none; transition: all 0.2s; }
.btn-pri { background: var(--pri); color: #fff; }
.btn-pri:hover { background: var(--pri-hov); }
.btn-sec { background: #f1f5f9; color: var(--text-main); }
.btn-sec:hover { background: #e2e8f0; }

.fb-main { display: flex; flex: 1; margin-top: 60px; height: calc(100% - 60px); }

/* SIDEBAR */
.fb-side { width: var(--sw); background: var(--bg-card); border-right: 1px solid var(--border); display: flex; flex-direction: column; overflow-y: auto; }
.side-head { padding: 15px 20px; font-weight: 600; font-size: 13px; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border); }
.field-btn { display: flex; align-items: center; gap: 12px; padding: 12px 20px; border: none; background: transparent; width: 100%; text-align: left; cursor: pointer; border-bottom: 1px solid #f1f5f9; transition: background 0.2s; }
.field-btn:hover { background: #f8fafc; }
.field-ic { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; background: #e0e7ff; color: #4f46e5; }
.field-title { font-weight: 500; font-size: 14px; color: var(--text-main); }
.field-desc { font-size: 12px; color: var(--text-sub); margin-top: 2px; }

/* CANVAS */
.fb-canvas { flex: 1; background: var(--bg); overflow-y: auto; padding: 40px; display: flex; justify-content: flex-start; }
.canvas-inner { width: 100%; max-width: 800px; margin-left: 0; }
.form-preview-box { background: var(--bg-card); border-radius: var(--rad); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.05); padding: 40px; min-height: 500px; }
.form-header-img { text-align: center; margin-bottom: 20px; }
.form-header-img img { max-width: 250px; max-height: 120px; border-radius: 8px; }
.form-desc { color: var(--text-sub); margin-bottom: 30px; }

/* SORTABLE LIST */
#fieldList { min-height: 200px; }
.cv-empty { text-align: center; padding: 60px 20px; color: var(--text-sub); border: 2px dashed var(--border); border-radius: var(--rad); margin-bottom: 20px; }
.cv-empty ion-icon { font-size: 48px; color: #cbd5e1; margin-bottom: 10px; }

.field-card { border: 1px solid var(--border); border-radius: 8px; padding: 15px; margin-bottom: 15px; background: #fff; position: relative; cursor: pointer; transition: border-color 0.2s, box-shadow 0.2s; }
.field-card:hover { border-color: var(--pri); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.field-card.sortable-ghost { opacity: 0.4; background: #e2e8f0; border: 2px dashed var(--pri); }
.fc-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.fc-label { font-weight: 600; font-size: 15px; color: var(--text-main); }
.fc-req { color: #ef4444; margin-left: 4px; }
.fc-actions { display: flex; gap: 5px; }
.fc-btn { background: transparent; border: none; color: var(--text-sub); cursor: pointer; padding: 4px; border-radius: 4px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.fc-btn:hover { background: #f1f5f9; color: var(--text-main); }
.fc-btn.del:hover { background: #fee2e2; color: #ef4444; }

/* DRAWER */
.overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.4); backdrop-filter: blur(2px); z-index: 100000; opacity: 0; pointer-events: none; transition: opacity 0.3s; }
.overlay.on { opacity: 1; pointer-events: auto; }

.drawer { position: fixed; top: 0; right: calc(var(--dw) * -1.2); width: var(--dw); max-width: 100vw; height: 100vh; background: var(--bg-card); z-index: 100001; transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: -4px 0 15px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
.drawer.on { right: 0; }

.dr-head { padding: 15px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: #fff; }
.dr-head h2 { margin: 0; font-size: 18px; font-weight: 600; }
.dr-body { padding: 20px; overflow-y: auto; flex: 1; min-height: 0; }

/* DRAWER FORM FIELDS */
.fg { margin-bottom: 20px; }
.fg label { display: block; font-size: 13px; font-weight: 600; color: var(--text-main); margin-bottom: 6px; }
.fg input[type="text"], .fg input[type="number"], .fg select, .fg textarea { width: 100%; padding: 10px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 14px; font-family: inherit; }
.fg input:focus, .fg select:focus, .fg textarea:focus { outline: none; border-color: var(--pri); box-shadow: 0 0 0 3px rgba(0,0,102,0.1); }
.fg small { display: block; margin-top: 4px; font-size: 12px; color: var(--text-sub); }

.toggle-wrap { display: flex; align-items: center; justify-content: space-between; padding: 12px; border: 1px solid var(--border); border-radius: 6px; }
.toggle-label { font-size: 14px; font-weight: 500; }
.toggle-switch { position: relative; width: 44px; height: 24px; }
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .4s; border-radius: 24px; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
input:checked + .slider { background-color: var(--pri); }
input:checked + .slider:before { transform: translateX(20px); }

/* MOBILE RESPONSIVENESS */
@media (max-width: 768px) {
  .fb-side { display: none; }
  .fb-main { flex-direction: column; }
  .fb-canvas { padding: 20px 15px; }
  .form-preview-box { padding: 20px; }
  .mobile-fab { display: flex; justify-content: center; align-items: center; position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; background-color: var(--acc); color: var(--pri); border-radius: 50%; box-shadow: 0 4px 15px rgba(255, 166, 0, 0.4); z-index: 99; font-size: 28px; cursor: pointer; border: none; }
}
@media (min-width: 769px) { .mobile-fab { display: none; } }
}
</style><form id="formBuilder" method="POST" action="<?= !empty($form['id']) ? '/smart-forms/update/'.$form['id'] : '/smart-forms/store' ?>" enctype="multipart/form-data">
  <!-- Hidden data -->
  <input type="hidden" name="structure" id="structureInput">
  <input type="hidden" name="settings" id="settingsInput" value="<?= htmlspecialchars($form['settings'] ?? '{}') ?>">
  
  <div class="fb-wrap">
    <!-- HEADER -->
    <header class="fb-head">
      <div class="fb-head-left">
        <a href="/smart-forms" class="fb-btn btn-sec" style="padding:6px;border-radius:50%;"><ion-icon name="arrow-back-outline"></ion-icon></a>
        <div class="fb-head-title">
          <input type="text" name="title" id="f-title" value="<?= htmlspecialchars($form['title'] ?? 'Untitled Form') ?>" required placeholder="Form Title">
        </div>
      </div>
      <div style="display:flex;gap:10px;">
        <button type="button" class="fb-btn btn-sec" onclick="openShareModal()"><ion-icon name="share-social-outline"></ion-icon> Share</button>
        <button type="button" class="fb-btn btn-sec" onclick="openSettings()"><ion-icon name="settings-outline"></ion-icon> Settings</button>
        <button type="button" class="fb-btn btn-pri" onclick="saveForm()"><ion-icon name="save-outline"></ion-icon> Save</button>
      </div>
    </header>

    <!-- MAIN BODY -->
    <div class="fb-main">
      
      <!-- SIDEBAR -->
      <aside class="fb-side">
        <div class="side-head">Add Field</div>
        <div id="fieldMenu">
          <button type="button" class="field-btn" onclick="addField('text')">
            <div class="field-ic"><ion-icon name="text-outline"></ion-icon></div>
            <div><div class="field-title">Short Text</div><div class="field-desc">Single line input</div></div>
          </button>
          <button type="button" class="field-btn" onclick="addField('email')">
            <div class="field-ic"><ion-icon name="mail-outline"></ion-icon></div>
            <div><div class="field-title">Email</div><div class="field-desc">Validates email address</div></div>
          </button>
          <button type="button" class="field-btn" onclick="addField('phone')">
            <div class="field-ic"><ion-icon name="call-outline"></ion-icon></div>
            <div><div class="field-title">Phone</div><div class="field-desc">Phone number input</div></div>
          </button>
          <button type="button" class="field-btn" onclick="addField('textarea')">
            <div class="field-ic"><ion-icon name="document-text-outline"></ion-icon></div>
            <div><div class="field-title">Long Text</div><div class="field-desc">Multi-line textarea</div></div>
          </button>
          <button type="button" class="field-btn" onclick="addField('dropdown')">
            <div class="field-ic"><ion-icon name="chevron-down-circle-outline"></ion-icon></div>
            <div><div class="field-title">Dropdown</div><div class="field-desc">Select from options</div></div>
          </button>
          <button type="button" class="field-btn" onclick="addField('checkbox')">
            <div class="field-ic"><ion-icon name="checkbox-outline"></ion-icon></div>
            <div><div class="field-title">Checkbox</div><div class="field-desc">Single tick box</div></div>
          </button>
          <button type="button" class="field-btn" onclick="addField('number')">
            <div class="field-ic" style="background:#dbeafe;color:#1d4ed8;"><ion-icon name="calculator-outline"></ion-icon></div>
            <div><div class="field-title">Number</div><div class="field-desc">Numeric input</div></div>
          </button>
          <button type="button" class="field-btn" onclick="addField('date')">
            <div class="field-ic" style="background:#fce7f3;color:#be185d;"><ion-icon name="calendar-outline"></ion-icon></div>
            <div><div class="field-title">Date</div><div class="field-desc">Date picker</div></div>
          </button>
          <button type="button" class="field-btn" onclick="addField('rating')">
            <div class="field-ic" style="background:#fef3c7;color:#d97706;"><ion-icon name="star-outline"></ion-icon></div>
            <div><div class="field-title">Rating</div><div class="field-desc">Star rating (1-5)</div></div>
          </button>
          <button type="button" class="field-btn" onclick="addField('radio')">
            <div class="field-ic" style="background:#ede9fe;color:#7c3aed;"><ion-icon name="radio-button-on-outline"></ion-icon></div>
            <div><div class="field-title">Multiple Choice</div><div class="field-desc">Radio button group</div></div>
          </button>
          <button type="button" class="field-btn" onclick="addField('file')">
            <div class="field-ic" style="background:#d1fae5;color:#059669;"><ion-icon name="cloud-upload-outline"></ion-icon></div>
            <div><div class="field-title">File Upload</div><div class="field-desc">Attach files</div></div>
          </button>
          <button type="button" class="field-btn" onclick="addField('url')">
            <div class="field-ic" style="background:#e0e7ff;color:#4338ca;"><ion-icon name="link-outline"></ion-icon></div>
            <div><div class="field-title">URL / Website</div><div class="field-desc">Web address input</div></div>
          </button>
          <button type="button" class="field-btn" onclick="addField('terms')">
            <div class="field-ic" style="background:#fee2e2;color:#dc2626;"><ion-icon name="shield-checkmark-outline"></ion-icon></div>
            <div><div class="field-title">Terms & Conditions</div><div class="field-desc">Accept terms checkbox</div></div>
          </button>
          <button type="button" class="field-btn" onclick="addField('signature')">
            <div class="field-ic" style="background:#fef08a;color:#854d0e;"><ion-icon name="create-outline"></ion-icon></div>
            <div><div class="field-title">Signature Pad</div><div class="field-desc">E-signature capture</div></div>
          </button>
          <button type="button" class="field-btn" onclick="addField('content')">
            <div class="field-ic" style="background:#e2e8f0;color:#334155;"><ion-icon name="information-circle-outline"></ion-icon></div>
            <div><div class="field-title">Info Block</div><div class="field-desc">HTML text and links</div></div>
          </button>
          <button type="button" class="field-btn" onclick="addField('section_break')">
            <div class="field-ic" style="background:#f1f5f9;color:#0f172a;"><ion-icon name="cut-outline"></ion-icon></div>
            <div><div class="field-title">Section Break</div><div class="field-desc">Splits form into pages</div></div>
          </button>
        </div>
      </aside>

      <!-- CANVAS -->
      <main class="fb-canvas">
        <div class="canvas-inner">
          <div class="form-preview-box">
            
            <div class="form-header-img" id="logoPreviewBox" style="<?= empty($form['logo']) ? 'display:none;' : '' ?>">
              <img id="logoPreviewImg" src="<?= htmlspecialchars($form['logo'] ?? '') ?>" alt="Logo">
            </div>
            
            <h2 id="prevTitle" style="text-align:center;margin-top:0;"><?= htmlspecialchars($form['title'] ?? 'Untitled Form') ?></h2>
            <p id="prevDesc" class="form-desc text-center"><?= htmlspecialchars($form['description'] ?? '') ?></p>

            <div id="fieldList">
              <!-- Fields rendered here -->
            </div>

            <div style="margin-top:20px;">
              <button type="button" class="fb-btn btn-pri" id="prevSubmitBtn" style="width:100%;justify-content:center;padding:12px;" onclick="saveForm()">Save & Publish Form</button>
            </div>
          </div>
        </div>
      </main>

    </div>
  </div>

  <!-- MOBILE FAB -->
  <button type="button" class="mobile-fab" onclick="openMobileMenu()"><ion-icon name="add-outline"></ion-icon></button>

  <!-- OVERLAYS -->
  <div class="overlay" id="ov" onclick="closeDrawer()"></div>
  <div class="overlay" id="ovMob" style="z-index:100005" onclick="closeMobileMenu()"></div>

  <!-- FIELD DRAWER -->
  <div class="drawer" id="drField">
    <div class="dr-head">
      <h2 id="drFieldTitle">Edit Field</h2>
      <div style="display:flex;gap:10px;">
        <button type="button" class="fb-btn btn-sec" onclick="closeDrawer()">Cancel</button>
        <button type="button" class="fb-btn btn-pri" onclick="saveField()">Done</button>
      </div>
    </div>
    <div class="dr-body" id="drFieldBody">
      <!-- Dynamic form populated by JS -->
    </div>
  </div>

  <!-- SETTINGS DRAWER -->
  <div class="drawer" id="drSet">
    <div class="dr-head">
      <h2>Form Settings</h2>
      <div style="display:flex;gap:10px;">
        <button type="button" class="fb-btn btn-sec" onclick="closeDrawer()">Cancel</button>
        <button type="button" class="fb-btn btn-pri" onclick="saveForm()">Save Form</button>
      </div>
    </div>
    <div class="dr-body">
      <h3 style="font-size:14px; text-transform:uppercase; color:#64748b; margin-bottom:15px; letter-spacing:0.5px;">Form Mode & Appearance</h3>
      <div class="fg">
        <label>Form Mode</label>
        <select id="s-form-mode">
          <option value="classic">Classic (traditional form)</option>
          <option value="conversational">Conversational (Typeform-style)</option>
        </select>
        <small>Conversational mode shows one question per screen with smooth transitions.</small>
      </div>
      <div class="fg">
        <label>Theme Color</label>
        <div style="display:flex; gap:8px; align-items:center;">
          <input type="color" id="s-theme-color" value="#000066" style="width:50px; height:36px; border:1px solid #e2e8f0; border-radius:6px; cursor:pointer; padding:2px;">
          <span style="font-size:13px; color:#64748b;">Background gradient for conversational mode</span>
        </div>
      </div>
      <div class="fg">
        <label style="display:flex; align-items:center; gap:8px;">
          <input type="checkbox" id="s-show-progress" checked style="width:16px; height:16px; margin:0;">
          Show Progress Bar
        </label>
      </div>

      <hr style="margin:20px 0; border-color:#e2e8f0;">
      <h3 style="font-size:14px; text-transform:uppercase; color:#64748b; margin-bottom:15px; letter-spacing:0.5px;">General</h3>
      <div class="fg">
        <label>Form Description</label>
        <input type="text" name="description" id="f-desc" value="<?= htmlspecialchars($form['description'] ?? '') ?>" oninput="document.getElementById('prevDesc').textContent=this.value">
      </div>
      <div class="fg">
        <label>Logo / Banner Image</label>
        <input type="file" name="logo" id="f-logo" accept="image/*" style="padding:6px" onchange="previewLogo(this)">
        <small>Select an image to display at the top of the form.</small>
      </div>
      <div class="fg">
        <label>Submit Button Text</label>
        <input type="text" id="s-submit-text" placeholder="e.g. Submit, Register, or Send">
      </div>
      <div class="fg">
        <label>Redirect URL (Optional)</label>
        <input type="text" id="s-redirect" placeholder="https://...">
        <small>Where to send users after submission</small>
      </div>
      <div class="fg">
        <label>Custom "Thank You" Title</label>
        <input type="text" id="s-ty-title" placeholder="Thank you!">
      </div>
      <div class="fg">
        <label>Custom "Thank You" Message</label>
        <textarea id="s-ty-text" rows="2" placeholder="Your submission has been received."></textarea>
      </div>

      <hr style="margin:20px 0; border-color:#e2e8f0;">
      <h3 style="font-size:14px; text-transform:uppercase; color:#64748b; margin-bottom:15px; letter-spacing:0.5px;">Form Limits & Expiry</h3>
      <div class="fg">
        <label>Maximum Submissions</label>
        <input type="number" id="s-limit" placeholder="e.g. 100" min="1">
        <small>Close form after this many submissions.</small>
      </div>
      <div class="fg">
        <label>Expiry Date & Time</label>
        <input type="datetime-local" id="s-expiry">
        <small>Close form automatically after this date.</small>
      </div>
      <div class="fg">
        <label>Closed Message</label>
        <input type="text" id="s-closed-msg" placeholder="This form is no longer accepting responses.">
      </div>

      <hr style="margin:20px 0; border-color:#e2e8f0;">
      <h3 style="font-size:14px; text-transform:uppercase; color:#64748b; margin-bottom:15px; letter-spacing:0.5px;">CRM & Automations</h3>
      <div class="fg">
        <label style="display:flex; align-items:center; gap:8px;">
            <input type="checkbox" id="s-crm" style="width:16px; height:16px; margin:0;">
            Sync Submissions with CRM Leads
        </label>
        <small style="margin-top:4px;">Automatically creates a lead in your CRM.</small>
      </div>

      <div class="fg" style="margin-top:15px;">
        <label style="display:flex; align-items:center; gap:8px;">
            <input type="checkbox" id="s-notify-admin" style="width:16px; height:16px; margin:0;" onchange="document.getElementById('notify-opts').style.display = this.checked ? 'block' : 'none'">
            Notify me on new submissions
        </label>
      </div>
      <div id="notify-opts" style="display:none; background:#f8fafc; padding:15px; border-radius:6px; border:1px solid #e2e8f0; margin-bottom:15px;">
        <div class="fg" style="margin-bottom:0;">
            <label>Admin Email Address</label>
            <input type="email" id="s-notify-email" placeholder="Leave blank to use your account email">
        </div>
      </div>

      <div class="fg" style="margin-top:15px;">
        <label style="display:flex; align-items:center; gap:8px;">
            <input type="checkbox" id="s-webhook-enabled" style="width:16px; height:16px; margin:0;" onchange="document.getElementById('webhook-opts').style.display = this.checked ? 'block' : 'none'">
            Enable Webhook Integration (Google Sheets, WhatsApp, etc.)
        </label>
      </div>
      <div id="webhook-opts" style="display:none; background:#f8fafc; padding:15px; border-radius:6px; border:1px solid #e2e8f0; margin-bottom:15px;">
        <div class="fg" style="margin-bottom:0;">
            <label>Webhook URL (POST endpoint)</label>
            <input type="url" id="s-webhook-url" placeholder="https://hook.eu1.make.com/...">
        </div>
      </div>

      <div class="fg" style="margin-top:15px;">
        <label style="display:flex; align-items:center; gap:8px;">
            <input type="checkbox" id="s-email-auto" style="width:16px; height:16px; margin:0;" onchange="document.getElementById('email-opts').style.display = this.checked ? 'block' : 'none'">
            Send 'Thank You' Email to Submitter
        </label>
      </div>
      <div id="email-opts" style="display:none; background:#f8fafc; padding:15px; border-radius:6px; border:1px solid #e2e8f0; margin-bottom:15px;">
        <div class="fg" style="margin-bottom:10px;">
            <label>Email Subject</label>
            <input type="text" id="s-email-subj" placeholder="Thank you for your submission">
        </div>
        <div class="fg" style="margin-bottom:0;">
            <label>Email Body (HTML supported)</label>
            <textarea id="s-email-body" rows="3" placeholder="We have received your details..."></textarea>
        </div>
      </div>

      <hr style="margin:20px 0; border-color:#e2e8f0;">
      <h3 style="font-size:14px; text-transform:uppercase; color:#64748b; margin-bottom:15px; letter-spacing:0.5px;">Payments</h3>
      <div class="fg">
        <label style="display:flex; align-items:center; gap:8px;">
            <input type="checkbox" id="s-payment" style="width:16px; height:16px; margin:0;" onchange="document.getElementById('payment-opts').style.display = this.checked ? 'block' : 'none'">
            Collect Payment on Submit
        </label>
        <small style="margin-top:4px;">Inline checkout — user pays without leaving the form.</small>
      </div>
      <div id="payment-opts" style="display:none; background:#f8fafc; padding:15px; border-radius:6px; border:1px solid #e2e8f0; margin-top:10px;">
        <div class="fg" style="margin-bottom:12px;">
            <label>Payment Gateway</label>
            <select id="s-payment-gw">
                <option value="paystack">Paystack</option>
                <option value="flutterwave">Flutterwave</option>
            </select>
        </div>
        <div class="fg" style="margin-bottom:12px;">
            <label>Amount Type</label>
            <select id="s-payment-amt-type" onchange="document.getElementById('fixed-amt-box').style.display = this.value === 'fixed' ? 'block' : 'none'">
                <option value="fixed">Fixed Amount</option>
                <option value="user_entered">User Enters Amount (Donations)</option>
            </select>
        </div>
        <div id="fixed-amt-box">
          <div style="display:flex; gap:10px; margin-bottom:12px;">
            <div class="fg" style="flex:2; margin-bottom:0;">
                <label>Amount</label>
                <input type="number" id="s-payment-amt" placeholder="0.00" step="0.01">
            </div>
            <div class="fg" style="flex:1; margin-bottom:0;">
                <label>Currency</label>
                <select id="s-payment-curr">
                    <option value="NGN">NGN (₦)</option>
                    <option value="USD">USD ($)</option>
                    <option value="GBP">GBP (£)</option>
                    <option value="EUR">EUR (€)</option>
                    <option value="GHS">GHS (GH₵)</option>
                    <option value="KES">KES (KSh)</option>
                    <option value="ZAR">ZAR (R)</option>
                </select>
            </div>
          </div>
        </div>
        <div class="fg" style="margin-bottom:0;">
            <label>Payment Description</label>
            <input type="text" id="s-payment-desc" placeholder="e.g. Registration Fee, Donation">
        </div>
      </div>
    </div>
  </div>

  <!-- SHARE MODAL -->
  <div class="drawer" id="drShare" style="width: 450px;">
    <div class="dr-head">
      <h2>Share Form</h2>
      <button type="button" class="fb-btn btn-sec" onclick="closeDrawer()">Close</button>
    </div>
    <div class="dr-body">
      <div class="fg">
        <label>Direct Link</label>
        <div style="display:flex; gap:8px;">
            <input type="text" id="share-link" value="<?= !empty($form['id']) ? 'https://' . $_SERVER['HTTP_HOST'] . '/sf/' . $form['id'] : 'Save form to generate link' ?>" readonly style="background:#f1f5f9;">
            <button type="button" class="fb-btn btn-pri" onclick="copyToClipboard('share-link')">Copy</button>
        </div>
      </div>
      <div class="fg mt-4">
        <label>Website Embed Code</label>
        <textarea id="share-embed" rows="4" readonly style="background:#f1f5f9; font-family:monospace; font-size:12px;"><?= !empty($form['id']) ? htmlspecialchars('<iframe src="https://' . $_SERVER['HTTP_HOST'] . '/sf/' . $form['id'] . '" width="100%" height="700px" style="border:none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"></iframe>') : 'Save form to generate embed code' ?></textarea>
        <button type="button" class="fb-btn btn-pri" style="margin-top:8px; width:100%; justify-content:center;" onclick="copyToClipboard('share-embed')">Copy Embed Code</button>
      </div>
    </div>
  </div>

  <!-- MOBILE MENU DRAWER -->
  <div class="drawer" id="drMobMenu" style="z-index:100006">
    <div class="dr-head">
      <h2>Add Field</h2>
      <button type="button" class="fb-btn btn-sec" onclick="closeMobileMenu()">Close</button>
    </div>
    <div class="dr-body" id="mobMenuBody">
      <!-- Copied from sidebar by JS -->
    </div>
  </div>

</form>

<script>
let fields = <?= isset($form) && !empty($form['structure']) ? $form['structure'] : '[]' ?>;
let settings = <?= isset($form) && !empty($form['settings']) ? $form['settings'] : '{}' ?>;
let editingIndex = -1;

// Init
document.addEventListener('DOMContentLoaded', () => {
  // Auto-minimize the global Casjoe sidebar
  document.documentElement.classList.add('sidebar-minimized');
  document.body.classList.add('sidebar-minimized');

  document.getElementById('f-title').addEventListener('input', e => {
    document.getElementById('prevTitle').textContent = e.target.value || 'Untitled Form';
  });
  
  // Load saved settings into UI
  if(settings.form_mode) document.getElementById('s-form-mode').value = settings.form_mode;
  if(settings.theme_color) document.getElementById('s-theme-color').value = settings.theme_color;
  if(settings.show_progress === false) document.getElementById('s-show-progress').checked = false;

  if(settings.redirect_url) document.getElementById('s-redirect').value = settings.redirect_url;
  if(settings.submit_text) {
    document.getElementById('s-submit-text').value = settings.submit_text;
    document.getElementById('prevSubmitBtn').textContent = settings.submit_text;
  }
  
  if(settings.thank_you_title) document.getElementById('s-ty-title').value = settings.thank_you_title;
  if(settings.thank_you_text) document.getElementById('s-ty-text').value = settings.thank_you_text;
  
  if(settings.limit_submissions) document.getElementById('s-limit').value = settings.limit_submissions;
  if(settings.expiry_date) document.getElementById('s-expiry').value = settings.expiry_date;
  if(settings.closed_message) document.getElementById('s-closed-msg').value = settings.closed_message;
  
  if(settings.crm_sync_enabled) document.getElementById('s-crm').checked = true;
  
  if(settings.notify_admin) {
      document.getElementById('s-notify-admin').checked = true;
      document.getElementById('notify-opts').style.display = 'block';
  }
  if(settings.notify_email) document.getElementById('s-notify-email').value = settings.notify_email;

  if(settings.webhook_enabled) {
      document.getElementById('s-webhook-enabled').checked = true;
      document.getElementById('webhook-opts').style.display = 'block';
  }
  if(settings.webhook_url) document.getElementById('s-webhook-url').value = settings.webhook_url;

  if(settings.email_automation_enabled) {
      document.getElementById('s-email-auto').checked = true;
      document.getElementById('email-opts').style.display = 'block';
  }
  if(settings.email_subject) document.getElementById('s-email-subj').value = settings.email_subject;
  if(settings.email_body) document.getElementById('s-email-body').value = settings.email_body;

  if(settings.payment_enabled) {
      document.getElementById('s-payment').checked = true;
      document.getElementById('payment-opts').style.display = 'block';
  }
  if(settings.payment_gateway) document.getElementById('s-payment-gw').value = settings.payment_gateway;
  if(settings.payment_amount_type) {
      document.getElementById('s-payment-amt-type').value = settings.payment_amount_type;
      if(settings.payment_amount_type !== 'fixed') document.getElementById('fixed-amt-box').style.display = 'none';
  }
  if(settings.payment_amount) document.getElementById('s-payment-amt').value = settings.payment_amount;
  if(settings.payment_currency) document.getElementById('s-payment-curr').value = settings.payment_currency;
  if(settings.payment_description) document.getElementById('s-payment-desc').value = settings.payment_description;
  
  function updateSettingsJson() {
      settings.form_mode = document.getElementById('s-form-mode').value;
      settings.theme_color = document.getElementById('s-theme-color').value;
      settings.show_progress = document.getElementById('s-show-progress').checked;

      settings.redirect_url = document.getElementById('s-redirect').value;
      settings.submit_text = document.getElementById('s-submit-text').value;
      
      settings.thank_you_title = document.getElementById('s-ty-title').value;
      settings.thank_you_text = document.getElementById('s-ty-text').value;
      
      settings.limit_submissions = document.getElementById('s-limit').value;
      settings.expiry_date = document.getElementById('s-expiry').value;
      settings.closed_message = document.getElementById('s-closed-msg').value;
      
      settings.crm_sync_enabled = document.getElementById('s-crm').checked;

      settings.notify_admin = document.getElementById('s-notify-admin').checked;
      settings.notify_email = document.getElementById('s-notify-email').value;

      settings.webhook_enabled = document.getElementById('s-webhook-enabled').checked;
      settings.webhook_url = document.getElementById('s-webhook-url').value;

      settings.email_automation_enabled = document.getElementById('s-email-auto').checked;
      settings.email_subject = document.getElementById('s-email-subj').value;
      settings.email_body = document.getElementById('s-email-body').value;
      
      settings.payment_enabled = document.getElementById('s-payment').checked;
      settings.payment_gateway = document.getElementById('s-payment-gw').value;
      settings.payment_amount_type = document.getElementById('s-payment-amt-type').value;
      settings.payment_amount = document.getElementById('s-payment-amt').value;
      settings.payment_currency = document.getElementById('s-payment-curr').value;
      settings.payment_description = document.getElementById('s-payment-desc').value;

      document.getElementById('prevSubmitBtn').textContent = settings.submit_text || 'Submit';
      document.getElementById('settingsInput').value = JSON.stringify(settings);
  }

  // Attach listener to all setting inputs
  const setInputs = ['s-form-mode', 's-theme-color', 's-show-progress', 's-redirect', 's-submit-text', 's-ty-title', 's-ty-text', 's-limit', 's-expiry', 's-closed-msg', 's-crm', 's-notify-admin', 's-notify-email', 's-webhook-enabled', 's-webhook-url', 's-email-auto', 's-email-subj', 's-email-body', 's-payment', 's-payment-gw', 's-payment-amt-type', 's-payment-amt', 's-payment-curr', 's-payment-desc'];
  setInputs.forEach(id => {
      const el = document.getElementById(id);
      if(el) {
          el.addEventListener('input', updateSettingsJson);
          el.addEventListener('change', updateSettingsJson);
      }
  });

  // Mobile menu clone
  document.getElementById('mobMenuBody').innerHTML = document.getElementById('fieldMenu').innerHTML;

  renderFields();

  // Sortable
  Sortable.create(document.getElementById('fieldList'), {
    handle: '.field-card',
    animation: 150,
    ghostClass: 'sortable-ghost',
    onEnd: function(e) {
      const item = fields.splice(e.oldIndex, 1)[0];
      fields.splice(e.newIndex, 0, item);
      renderFields();
    }
  });
});

function previewLogo(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('logoPreviewImg').src = e.target.result;
      document.getElementById('logoPreviewBox').style.display = 'block';
    }
    reader.readAsDataURL(input.files[0]);
  }
}

function renderFields() {
  const c = document.getElementById('fieldList');
  if(fields.length === 0) {
    c.innerHTML = `
      <div class="cv-empty">
        <ion-icon name="list-outline"></ion-icon>
        <h3>No fields yet</h3>
        <p>Add a field from the left sidebar to get started.</p>
      </div>`;
    return;
  }

  let html = '';
  fields.forEach((f, i) => {
    let req = f.required ? '<span class="fc-req">*</span>' : '';
    let preview = getPreview(f);
    
    html += `
      <div class="field-card" onclick="editField(${i})">
        <div class="fc-head">
          <div class="fc-label">${f.label} ${req} <span style="font-size:12px;color:#94a3b8;font-weight:normal;margin-left:8px;">(${f.type})</span></div>
          <div class="fc-actions">
            <button type="button" class="fc-btn del" onclick="event.stopPropagation(); deleteField(${i})"><ion-icon name="trash-outline"></ion-icon></button>
          </div>
        </div>
        <div>${preview}</div>
      </div>
    `;
  });
  c.innerHTML = html;
  document.getElementById('structureInput').value = JSON.stringify(fields);
}

function getPreview(f) {
  const disabledInput = `<input type="text" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:4px" disabled>`;
  if(f.type === 'text') return disabledInput;
  if(f.type === 'email') return `<input type="email" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:4px" placeholder="email@example.com" disabled>`;
  if(f.type === 'phone') return `<input type="tel" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:4px" placeholder="+234 ..." disabled>`;
  if(f.type === 'number') return `<input type="number" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:4px" placeholder="0" disabled>`;
  if(f.type === 'date') return `<input type="date" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:4px" disabled>`;
  if(f.type === 'url') return `<input type="url" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:4px" placeholder="https://..." disabled>`;
  if(f.type === 'textarea') return `<textarea style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:4px" disabled></textarea>`;
  if(f.type === 'dropdown') {
    let opts = (f.options || 'Option 1').split(',').map(o=>`<option>${o.trim()}</option>`).join('');
    return `<select style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:4px" disabled>${opts}</select>`;
  }
  if(f.type === 'radio') {
    let opts = (f.options || 'Option 1, Option 2').split(',');
    return opts.map((o,i) => `<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px"><input type="radio" disabled name="prev_radio"> <span style="font-size:14px">${o.trim()}</span></div>`).join('');
  }
  if(f.type === 'checkbox') return `<div style="display:flex;align-items:center;gap:8px"><input type="checkbox" disabled> <span style="font-size:14px">Option</span></div>`;
  if(f.type === 'rating') return `<div style="font-size:24px;color:#d97706;">★★★★☆</div>`;
  if(f.type === 'file') return `<div style="width:100%;padding:20px;border:2px dashed #cbd5e1;border-radius:8px;text-align:center;color:#94a3b8;"><ion-icon name="cloud-upload-outline" style="font-size:24px;"></ion-icon><br>Click or drag to upload</div>`;
  if(f.type === 'terms') return `<div style="background:#f8fafc;padding:10px;border-radius:6px;border:1px solid #e2e8f0;font-size:12px;max-height:60px;overflow:hidden;color:#64748b;">${(f.options || 'Terms text here...').substring(0,120)}...</div><div style="margin-top:6px;display:flex;align-items:center;gap:6px"><input type="checkbox" disabled> <span style="font-size:13px;color:#dc2626;font-weight:600;">I agree *</span></div>`;
  if(f.type === 'signature') return `<div style="width:100%;height:80px;border:2px dashed #cbd5e1;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#94a3b8;"><ion-icon name="create-outline" style="font-size:24px;margin-right:8px;"></ion-icon> Signature Pad</div>`;
  if(f.type === 'content') return `<div style="background:#f8fafc;padding:12px;border-radius:6px;font-size:13px;color:#475569;">${f.options || '<i>No content</i>'}</div>`;
  if(f.type === 'section_break') return `<div style="text-align:center;border-top:2px dashed #e2e8f0;margin-top:10px;padding-top:10px;color:#94a3b8;font-size:12px;font-weight:600;text-transform:uppercase;">Page Break</div>`;
  return disabledInput;
}

function addField(type) {
  closeMobileMenu();
  const labels = {
    text:'Short Text', email:'Email', phone:'Phone', number:'Number', date:'Date',
    url:'Website URL', textarea:'Long Text', dropdown:'Dropdown', radio:'Multiple Choice',
    checkbox:'Checkbox', rating:'Rating', file:'File Upload', terms:'Terms & Conditions',
    signature:'Signature', content:'Information', section_break:'New Page'
  };
  const defaultOpts = {
    dropdown:'Option 1, Option 2', radio:'Option A, Option B, Option C',
    content:'Information text here', terms:'Enter your terms and conditions text here...'
  };

  fields.push({
    type: type,
    name: 'field_' + Math.floor(Math.random()*10000),
    label: labels[type] || 'New Field',
    options: defaultOpts[type] || '',
    required: false,
    placeholder: ''
  });
  renderFields();
  editField(fields.length - 1);
}

function deleteField(idx) {
  if(confirm("Delete this field?")) {
    fields.splice(idx, 1);
    renderFields();
  }
}

// Drawer Logic
function openSettings() {
  document.getElementById('ov').classList.add('on');
  document.getElementById('drSet').classList.add('on');
}
function editField(idx) {
  editingIndex = idx;
  const f = fields[idx];
  let html = `
    <div class="fg">
      <label>Field Label</label>
      <input type="text" id="edLabel" value="${f.label}">
    </div>
    <div class="fg" style="display:none;">
      <label>Variable Name (Internal)</label>
      <input type="text" id="edName" value="${f.name}">
      <small>Used for API/webhooks. No spaces.</small>
    </div>
  `;

  // Placeholder field (for text, email, phone, number, url, textarea, date)
  if(['text','email','phone','number','url','textarea','date'].includes(f.type)) {
    html += `
      <div class="fg">
        <label>Placeholder Text</label>
        <input type="text" id="edPlaceholder" value="${f.placeholder || ''}" placeholder="e.g. Enter your answer...">
      </div>
    `;
  }

  // Options for dropdown, radio, checkbox, content, section_break, terms
  if(['dropdown','radio','checkbox','content','section_break','terms'].includes(f.type)) {
    let optLabel = 'Options (comma separated)';
    if(f.type==='content') optLabel = 'Content / HTML';
    if(f.type==='section_break') optLabel = 'Next Button Text';
    if(f.type==='terms') optLabel = 'Terms & Conditions Text (HTML supported)';

    html += `
      <div class="fg">
        <label>${optLabel}</label>
        <textarea id="edOpts" rows="4">${f.options || ''}</textarea>
      </div>
    `;
  }

  // Allow Other for dropdown/radio
  if(['dropdown','radio'].includes(f.type)) {
    html += `
      <div class="toggle-wrap" style="margin-top:10px;">
        <div class="toggle-label">Allow "Other" option</div>
        <label class="toggle-switch">
          <input type="checkbox" id="edAllowOther" ${f.allow_other ? 'checked' : ''}>
          <span class="slider"></span>
        </label>
      </div>
    `;
  }

  // Required toggle (not for content, section_break)
  if(f.type !== 'content' && f.type !== 'section_break') {
    html += `
      <div class="toggle-wrap" style="margin-top:15px;">
        <div class="toggle-label">Required Field</div>
        <label class="toggle-switch">
          <input type="checkbox" id="edReq" ${f.required ? 'checked' : ''}>
          <span class="slider"></span>
        </label>
      </div>
    `;
  }

  // CONDITIONAL LOGIC
  let prevFieldsHtml = '<option value="">-- Select Target Field --</option>';
  for(let i=0; i<idx; i++) {
    if(fields[i].type !== 'content' && fields[i].type !== 'section_break') {
      let sel = (f.condition_field === fields[i].name) ? 'selected' : '';
      prevFieldsHtml += `<option value="${fields[i].name}" ${sel}>${fields[i].label || fields[i].name}</option>`;
    }
  }

  if (idx > 0) {
    html += `
      <div style="border-top:1px solid #e2e8f0; margin-top:20px; padding-top:15px;">
        <div class="toggle-wrap" style="margin-bottom:10px;">
          <div class="toggle-label" style="color:var(--pri);font-weight:700;">Enable Conditional Logic</div>
          <label class="toggle-switch">
            <input type="checkbox" id="edCondEn" onchange="document.getElementById('condWrap').style.display=this.checked?'block':'none'" ${f.condition_enabled ? 'checked' : ''}>
            <span class="slider"></span>
          </label>
        </div>
        
        <div id="condWrap" style="display:${f.condition_enabled ? 'block' : 'none'}; background:#f8fafc; padding:15px; border-radius:8px; border:1px solid #e2e8f0;">
          <div class="fg" style="margin-bottom:10px;">
            <label>Show this field IF:</label>
            <select id="edCondField">
              ${prevFieldsHtml}
            </select>
          </div>
          <div class="fg" style="margin-bottom:10px;">
            <select id="edCondOp">
              <option value="==" ${f.condition_op === '==' ? 'selected' : ''}>Equals</option>
              <option value="!=" ${f.condition_op === '!=' ? 'selected' : ''}>Not Equals</option>
              <option value="contains" ${f.condition_op === 'contains' ? 'selected' : ''}>Contains</option>
              <option value=">" ${f.condition_op === '>' ? 'selected' : ''}>Greater Than</option>
              <option value="<" ${f.condition_op === '<' ? 'selected' : ''}>Less Than</option>
            </select>
          </div>
          <div class="fg" style="margin-bottom:0;">
            <input type="text" id="edCondVal" placeholder="Target Value..." value="${f.condition_val || ''}">
          </div>
          <small style="margin-top:8px; display:block; color:var(--text-sub);">If target is a dropdown/radio, type the exact text of the choice.</small>
        </div>
      </div>
    `;
  }

  document.getElementById('drFieldBody').innerHTML = html;
  
  document.getElementById('ov').classList.add('on');
  document.getElementById('drField').classList.add('on');
}

function saveField() {
  if(editingIndex > -1) {
    const f = fields[editingIndex];
    f.label = document.getElementById('edLabel').value;
    f.name = document.getElementById('edName').value.replace(/[^a-zA-Z0-9_]/g, '');
    
    const optEl = document.getElementById('edOpts');
    if(optEl) f.options = optEl.value;
    
    const reqEl = document.getElementById('edReq');
    if(reqEl) f.required = reqEl.checked;

    const placeholderEl = document.getElementById('edPlaceholder');
    if(placeholderEl) f.placeholder = placeholderEl.value;

    const allowOtherEl = document.getElementById('edAllowOther');
    if(allowOtherEl) f.allow_other = allowOtherEl.checked;

    const condEnEl = document.getElementById('edCondEn');
    if (condEnEl) {
      f.condition_enabled = condEnEl.checked;
      f.condition_field = document.getElementById('edCondField').value;
      f.condition_op = document.getElementById('edCondOp').value;
      f.condition_val = document.getElementById('edCondVal').value;
    }
  }
  closeDrawer();
  renderFields();
}

function closeDrawer() {
  document.getElementById('ov').classList.remove('on');
  document.getElementById('drSet').classList.remove('on');
  document.getElementById('drField').classList.remove('on');
  document.getElementById('drShare').classList.remove('on');
  editingIndex = -1;
  document.getElementById('drFieldBody').innerHTML = '';
}

function openSettings() {
  closeDrawer();
  closeMobileMenu();
  document.getElementById('drSet').classList.add('on');
  document.getElementById('ov').classList.add('on');
}

function openShareModal() {
  closeDrawer();
  closeMobileMenu();
  document.getElementById('drShare').classList.add('on');
  document.getElementById('ov').classList.add('on');
}

function copyToClipboard(id) {
  const el = document.getElementById(id);
  el.select();
  document.execCommand('copy');
  
  // Show temporary "Copied!" text on the button
  const btn = el.nextElementSibling;
  if(btn) {
      const originalText = btn.textContent;
      btn.textContent = 'Copied!';
      setTimeout(() => btn.textContent = originalText, 2000);
  }
}

function openMobileMenu() {
  document.getElementById('ovMob').classList.add('on');
  document.getElementById('drMobMenu').classList.add('on');
}
function closeMobileMenu() {
  document.getElementById('ovMob').classList.remove('on');
  document.getElementById('drMobMenu').classList.remove('on');
}

function saveForm() {
  document.getElementById('structureInput').value = JSON.stringify(fields);
  document.getElementById('settingsInput').value = JSON.stringify(settings);
  document.getElementById('formBuilder').submit();
}
</script>
<?php include __DIR__ . '/layout/footer.php'; ?>
