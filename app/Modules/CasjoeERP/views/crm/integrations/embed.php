<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Embed Form | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .embed-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .step-card {
            background: #fff; border-radius: 12px; padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #eee;
            margin-bottom: 25px;
        }
        .step-card h3 { margin: 0 0 15px 0; color: #333; display: flex; align-items: center; gap: 10px; }
        .step-number {
            background: #4e73df; color: #fff; width: 28px; height: 28px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem; font-weight: bold;
        }
        .code-block {
            background: #2d3436; color: #dfe6e9; border-radius: 8px;
            padding: 15px; position: relative; font-family: 'Courier New', Courier, monospace;
            font-size: 0.85rem; overflow-x: auto; margin-top: 10px;
        }
        .code-block pre { margin: 0; white-space: pre-wrap; word-break: break-all; }
        .copy-btn {
            position: absolute; top: 10px; right: 10px;
            background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
            color: #fff; padding: 5px 10px; border-radius: 4px; cursor: pointer;
            font-size: 0.75rem; transition: all 0.2s;
        }
        .copy-btn:hover { background: rgba(255,255,255,0.2); }
        .preview-box {
            border: 1px solid #ddd; border-radius: 8px; padding: 20px; background: #fbfbfb;
        }
        /* Sample Form Styles for Preview */
        .casjoe-form { max-width: 400px; margin: 0 auto; font-family: sans-serif; }
        .casjoe-group { margin-bottom: 15px; }
        .casjoe-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; }
        .casjoe-group input, .casjoe-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .casjoe-btn { background: #4e73df; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; width: 100%; font-weight: bold; }
    </style>
</head>
<body>
<?php require __DIR__ . '/../../layout/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Form Integration: <?= htmlspecialchars($integration['name']) ?></h2>
            <a href="/erp/crm/integrations" class="btn" style="background: #f8f9fa; color: #333; border: 1px solid #ddd;">
                <ion-icon name="arrow-back-outline" style="vertical-align: middle;"></ion-icon> Back
            </a>
        </div>

        <div class="embed-container">
            <div class="step-card">
                <h3><span class="step-number">1</span> Form Preview</h3>
                <p style="color: #666; margin-bottom: 20px;">This is how your form will look with the default styles provided below.</p>
                <div class="preview-box">
                    <form class="casjoe-form" onsubmit="return false;">
                        <div class="casjoe-group">
                            <label>Full Name</label>
                            <input type="text" placeholder="John Doe">
                        </div>
                        <div class="casjoe-group">
                            <label>Email Address</label>
                            <input type="email" placeholder="john@example.com">
                        </div>
                        <div class="casjoe-group">
                            <label>Phone Number</label>
                            <input type="text" placeholder="+1 234 567 890">
                        </div>
                        <div class="casjoe-group">
                            <label>Business Type</label>
                            <input type="text" placeholder="e.g. Real Estate, Education">
                        </div>
                        <div class="casjoe-group">
                            <label>How can we help?</label>
                            <textarea rows="3" placeholder="Tell us about your needs..."></textarea>
                        </div>
                        <button class="casjoe-btn">Submit Interest</button>
                    </form>
                </div>
            </div>

            <div class="step-card">
                <h3><span class="step-number">2</span> Copy HTML & CSS</h3>
                <p style="color: #666;">Paste this HTML anywhere on your website where you want the form to appear.</p>
                <div class="code-block">
                    <button class="copy-btn" onclick="copyCode('htmlCode')">Copy HTML</button>
                    <pre id="htmlCode"><?= htmlspecialchars('<!-- Casjoe CRM Form -->
<div id="casjoe-form-container">
    <form id="casjoe-lead-form" class="casjoe-form">
        <div class="casjoe-group">
            <label>Full Name</label>
            <input type="text" name="name" required>
        </div>
        <div class="casjoe-group">
            <label>Email Address</label>
            <input type="email" name="email" required>
        </div>
        <div class="casjoe-group">
            <label>Phone Number</label>
            <input type="text" name="phone">
        </div>
        <div class="casjoe-group">
            <label>Business Type</label>
            <input type="text" name="company">
        </div>
        <div class="casjoe-group">
            <label>How can we help?</label>
            <textarea name="notes" rows="3"></textarea>
        </div>
        <button type="submit" class="casjoe-btn" id="casjoe-submit-btn">Submit</button>
        <div id="casjoe-form-status" style="margin-top: 10px; font-size: 14px; text-align: center;"></div>
    </form>
</div>

<style>
    .casjoe-form { max-width: 400px; font-family: sans-serif; color: #333; }
    .casjoe-group { margin-bottom: 15px; }
    .casjoe-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; }
    .casjoe-group input, .casjoe-group textarea { 
        width: 100%; padding: 10px; border: 1px solid #ccc; 
        border-radius: 4px; box-sizing: border-box; font-size: 14px;
    }
    .casjoe-btn { 
        background: #4e73df; color: white; border: none; padding: 12px 20px; 
        border-radius: 4px; cursor: pointer; width: 100%; font-weight: bold; font-size: 16px;
    }
    .casjoe-btn:disabled { background: #ccc; cursor: not-allowed; }
</style>') ?></pre>
                </div>
            </div>

            <div class="step-card">
                <h3><span class="step-number">3</span> Copy JavaScript</h3>
                <p style="color: #666;">Paste this script right before the closing <code>&lt;/body&gt;</code> tag of your website.</p>
                <div class="code-block">
                    <button class="copy-btn" onclick="copyCode('jsCode')">Copy JS</button>
                    <pre id="jsCode"><?= htmlspecialchars('<script>
document.getElementById("casjoe-lead-form").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const form = e.target;
    const btn = document.getElementById("casjoe-submit-btn");
    const status = document.getElementById("casjoe-form-status");
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    btn.disabled = true;
    btn.innerText = "Sending...";
    status.innerText = "";

    fetch("' . 'https://' . $_SERVER['HTTP_HOST'] . '/api/erp/crm/webhook/' . $integration['webhook_secret'] . '", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            status.style.color = "green";
            status.innerText = "Thank you! Your information has been sent.";
            form.reset();
        } else {
            throw new Error(result.message || "Submission failed");
        }
    })
    .catch(error => {
        status.style.color = "red";
        status.innerText = "Error: " + error.message;
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerText = "Submit";
    });
});
</script>') ?></pre>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function copyCode(elementId) {
    const code = document.getElementById(elementId).innerText;
    const textArea = document.createElement("textarea");
    textArea.value = code;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand("copy");
    document.body.removeChild(textArea);
    
    const btn = document.querySelector(`button[onclick="copyCode('${elementId}')"]`);
    const originalText = btn.innerText;
    btn.innerText = "Copied!";
    setTimeout(() => { btn.innerText = originalText; }, 2000);
}
</script>
</body>
</html>
