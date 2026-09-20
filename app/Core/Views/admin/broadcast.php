<?php
$pageTitle = 'Email Broadcast';
include __DIR__ . '/header.php';
?>

<div class="content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1><ion-icon name="megaphone-outline" style="color: #FFA600; vertical-align: middle;"></ion-icon> Broadcast Message</h1>
        <p style="color: #cbd5e1; margin-top: 5px; font-size: 0.9rem;">Send email campaigns and announcements to your users.</p>
    </div>
</div>

<?php if(isset($_GET['success'])): ?>
    <div style="background: rgba(46, 204, 113, 0.15); border: 1px solid #2ecc71; color: #2ecc71; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: center;">
        <ion-icon name="checkmark-circle" style="font-size: 1.5rem; margin-right: 10px;"></ion-icon> 
        <div><strong>Success!</strong> Broadcast sent successfully to all selected users!</div>
    </div>
<?php endif; ?>

<div class="card" style="max-width: 900px; margin: 0 auto; border-radius: 16px; padding: 10px;">
    <div style="padding: 20px 25px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center;">
        <ion-icon name="paper-plane-outline" style="color: #FFA600; font-size: 1.5rem; margin-right: 10px;"></ion-icon>
        <h3 style="margin: 0; font-size: 1.3rem;">Compose New Broadcast</h3>
    </div>
    
    <div class="card-body" style="padding: 30px;">
        <form method="POST" action="/<?= ADMIN_PATH ?>/broadcast/send">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?>">

            <div style="margin-bottom: 25px;">
                <label style="display: block; color: #888; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; margin-bottom: 8px;">Recipients</label>
                <select name="group" class="form-control" required style="width: 100%; background: #0c0d14; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 14px 20px; border-radius: 10px; font-size: 1rem; box-sizing: border-box; cursor: pointer; appearance: none;">
                    <option value="all">All Users (Complete Database)</option>
                    <option value="admins">Admins & Staff Only</option>
                    <option value="active">Active Subscribers</option>
                </select>
                <small style="color: #64748b; margin-top: 8px; display: block;">Select the target audience for this email broadcast.</small>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; color: #888; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; margin-bottom: 8px;">Load Email Template <span style="text-transform: none; font-weight: normal; font-size: 0.8rem; color: #64748b;">(Optional)</span></label>
                <select id="templateSelector" class="form-control" style="width: 100%; background: #0c0d14; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 14px 20px; border-radius: 10px; font-size: 1rem; box-sizing: border-box; cursor: pointer; appearance: none;">
                    <option value="">-- Select a Saved Template --</option>
                    <?php if(!empty($templates)): ?>
                        <?php foreach($templates as $t): ?>
                            <option value="<?= htmlspecialchars($t['id']) ?>"><?= htmlspecialchars($t['name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <small style="color: #64748b; margin-top: 8px; display: block;">Loading a template will overwrite the subject and message below.</small>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; color: #888; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; margin-bottom: 8px;">Subject Line</label>
                <input type="text" name="subject" class="form-control" placeholder="e.g. Important Update: Maintenance Scheduled" required style="width: 100%; background: #0c0d14; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 14px 20px; border-radius: 10px; font-size: 1rem; box-sizing: border-box;">
            </div>
            
            <div style="margin-bottom: 25px;">
                <label style="display: block; color: #888; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; margin-bottom: 8px;">Message Body</label>
                
                <!-- Casjoe Visual Editor Component -->
                <div style="border: 1px solid rgba(255,255,255,0.15); border-radius: 10px; overflow: hidden; background: #0c0d14;">
                    <?php 
                        $editorName = 'message';
                        $editorValue = '';
                        require dirname(__DIR__, 3) . '/Views/partials/casjoe_editor.php'; 
                    ?>
                </div>

                <div style="background: rgba(9, 132, 227, 0.1); border-left: 3px solid #0984e3; padding: 15px; border-radius: 4px; margin-top: 20px;">
                    <div style="color: #cbd5e1; font-size: 0.95rem; font-weight: bold; margin-bottom: 5px;">
                        <ion-icon name="information-circle" style="color: #0984e3; vertical-align: middle; font-size: 1.2rem; margin-right: 5px;"></ion-icon> Personalization Tags
                    </div>
                    <small style="color: #94a3b8; display: block; margin-bottom: 12px;">Insert these tags into your message to automatically personalize the email for each recipient:</small>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <span style="background: #0984e3; color: white; padding: 5px 12px; border-radius: 6px; font-size: 0.8rem; font-family: monospace;">(name)</span>
                        <span style="background: #0984e3; color: white; padding: 5px 12px; border-radius: 6px; font-size: 0.8rem; font-family: monospace;">(first name)</span>
                        <span style="background: #0984e3; color: white; padding: 5px 12px; border-radius: 6px; font-size: 0.8rem; font-family: monospace;">(email)</span>
                    </div>
                </div>
            </div>

            <div style="text-align: right; margin-top: 30px;">
                <button type="submit" style="background: linear-gradient(135deg, #FFA600 0%, #ff8c00 100%); border: none; color: #000; padding: 14px 40px; border-radius: 10px; font-weight: 800; font-size: 1.05rem; cursor: pointer; display: inline-flex; align-items: center; box-shadow: 0 4px 15px rgba(255, 166, 0, 0.3); transition: all 0.3s ease;">
                    <ion-icon name="paper-plane" style="margin-right: 8px; font-size: 1.3rem;"></ion-icon> Send Broadcast
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const templates = <?= json_encode($templates ?? []) ?>;
    document.getElementById('templateSelector').addEventListener('change', function() {
        const templateId = this.value;
        if (!templateId) {
            document.getElementById('casjoe-workspace').innerHTML = '';
            return;
        }
        
        const template = templates.find(t => t.id == templateId);
        if (template) {
            document.querySelector('input[name="subject"]').value = template.subject || '';
            document.getElementById('casjoe-workspace').innerHTML = template.content || '';
        }
    });
</script>

<?php include __DIR__ . '/footer.php'; ?>
