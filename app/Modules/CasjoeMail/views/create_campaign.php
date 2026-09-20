<!DOCTYPE html>
<html lang="en">

<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Campaign | Mail</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php $active = 'mail_campaigns'; include __DIR__ . '/partials/sidebar_mail.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="top-bar">
                <h1>New Email Campaign</h1>
            </div>

            <div class="card" style="max-width: 800px; margin: 0 auto;">
                <div style="background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); padding: 15px; border-radius: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; color: #333;">
                    <div>
                        <strong style="font-size: 1.05rem;">✨ AI Email Campaign Draft</strong>
                        <p style="margin: 3px 0 0; font-size: 0.85rem; opacity: 0.9;">Need instant copy? Let AI generate high-converting subject lines & HTML body.</p>
                    </div>
                    <button type="button" onclick="generateAiMail()" class="btn" style="background: #fff; color: #333; font-weight: bold; border: none; cursor: pointer; padding: 8px 16px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        ✨ Generate Now
                    </button>
                </div>

                <form method="POST" action="/mail/campaigns/send">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px;">Campaign Subject</label>
                        <input type="text" name="subject" required
                            style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px;">Recipients</label>
                        <select disabled
                            style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; background: #eee;">
                            <option>All Users (Active)</option>
                        </select>
                        <p style="font-size: 0.8rem; color: #666; margin-top: 5px;">Currently broadcasting to all
                            workspace users.</p>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px;">Email Body (HTML supported)</label>
                        <textarea name="body" rows="10" required
                            style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; font-family: monospace;"></textarea>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn" style="flex: 1; padding: 12px;">Send Blast</button>
                        <a href="/mail" class="btn"
                            style="background: transparent; border: 1px solid #ccc; color: var(--text-color);">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
    function generateAiMail() {
        const topic = prompt("What is this email campaign about? (e.g. 'End of Month 30% Sale', 'New Platform Update', 'Monthly Newsletter')");
        if (!topic) return;

        const subject = "Exciting Update: " + topic.replace(/\b\w/g, l => l.toUpperCase());
        const html = `<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #333;">
  <h2 style="color: #4A90E2;">Hello {First Name},</h2>
  <p>We have an exciting announcement to share with you today regarding <strong>${topic}</strong>.</p>
  <div style="background: #F4F8FC; border-left: 4px solid #4A90E2; padding: 15px; margin: 20px 0;">
    <p style="margin: 0;">Take advantage of this update to improve your workflow and achieve even better results.</p>
  </div>
  <p>Click below to explore more details and get started:</p>
  <p style="text-align: center; margin: 30px 0;">
    <a href="https://app.casjoe.com" style="background: #4A90E2; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; display: inline-block;">Learn More →</a>
  </p>
  <hr style="border: 0; border-top: 1px solid #eee; margin-top: 40px;" />
  <p style="font-size: 12px; color: #888;">You received this message because you are a valued member of Casjoe.</p>
</div>`;

        document.querySelector('input[name="subject"]').value = subject;
        document.querySelector('textarea[name="body"]').value = html;
    }
    </script>
</body>

</html>
