<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title><?= htmlspecialchars($doc['title']) ?> | E-Sign Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .cert-card {
            background: #f8fafc;
            border: 2px solid #cbd5e1;
            border-radius: 10px;
            padding: 20px;
            margin-top: 24px;
        }
        html.dark-theme .cert-card {
            background: #0f172a;
            border-color: #334155;
        }
        .share-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
        }
        html.dark-theme .share-box {
            background: #172554;
            border-color: #1e3a8a;
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <h2><?= htmlspecialchars($doc['title']) ?></h2>
                <p style="color: #64748b; font-size: 13px; margin: 2px 0 0 0;">
                    Created: <?= date('M d, Y h:i A', strtotime($doc['created_at'])) ?> &bull; Category: <strong style="text-transform: capitalize;"><?= htmlspecialchars(str_replace('_', ' ', $doc['category'])) ?></strong>
                </p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <a href="/erp/documents" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 4px;">
                    <ion-icon name="arrow-back-outline"></ion-icon> Back
                </a>
                <a href="/erp/documents/print?uuid=<?= $doc['uuid'] ?>" target="_blank" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 6px;">
                    <ion-icon name="print-outline"></ion-icon> Print / PDF
                </a>
                <?php if ($doc['status'] === 'draft'): ?>
                    <a href="/erp/documents/edit?id=<?= $doc['id'] ?>" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 6px;">
                        <ion-icon name="create-outline"></ion-icon> Edit Draft
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($errorMsg)): ?>
            <div style="background: #fef2f2; border: 1px solid #f87171; color: #991b1b; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <ion-icon name="alert-circle-outline" style="font-size: 22px; flex-shrink: 0;"></ion-icon>
                <div><?= htmlspecialchars($errorMsg) ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($successMsg)): ?>
            <div style="background: #ecfdf5; border: 1px solid #34d399; color: #065f46; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <ion-icon name="checkmark-circle-outline" style="font-size: 22px; flex-shrink: 0;"></ion-icon>
                <div><?= htmlspecialchars($successMsg) ?></div>
            </div>
        <?php endif; ?>

        <div style="max-width: 900px; margin: 0 auto;">
            <!-- Public Signing Share Link Box -->
            <div class="share-box">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 10px;">
                    <div style="font-weight: 700; font-size: 14px; color: #1e40af; display: flex; align-items: center; gap: 6px;">
                        <ion-icon name="link-outline" style="font-size: 18px;"></ion-icon> Shareable Signing Link for Recipient
                    </div>
                    <div>
                        <span style="font-size: 12px; font-weight: 600; text-transform: uppercase; padding: 2px 8px; border-radius: 4px; <?= $doc['status'] === 'signed' ? 'background: #dcfce7; color: #15803d;' : 'background: #fef3c7; color: #b45309;' ?>">
                            Status: <?= ucfirst($doc['status']) ?>
                        </span>
                    </div>
                </div>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="text" id="signLinkInput" value="<?= htmlspecialchars($signUrl) ?>" readonly class="form-control" style="background: #ffffff; font-family: monospace; font-size: 13px;">
                    <button type="button" onclick="copySigningLink()" class="btn btn-primary" style="white-space: nowrap; display: inline-flex; align-items: center; gap: 4px; padding: 8px 14px;">
                        <ion-icon name="copy-outline"></ion-icon> <span id="copyBtnText">Copy Link</span>
                    </button>
                    <a href="<?= htmlspecialchars($signUrl) ?>" target="_blank" class="btn btn-outline" style="white-space: nowrap; display: inline-flex; align-items: center; gap: 4px; padding: 8px 14px;">
                        <ion-icon name="open-outline"></ion-icon> Open
                    </a>
                </div>
                <div style="font-size: 12px; color: #64748b; margin-top: 6px;">
                    Send this link via Email or WhatsApp to <strong><?= htmlspecialchars($doc['recipient_name']) ?></strong> (<?= htmlspecialchars($doc['recipient_email']) ?>). They can sign instantly without creating an account.
                </div>
            </div>

            <!-- Main Document Presentation Card -->
            <div class="card" style="padding: 30px; box-shadow: 0 4px 16px rgba(0,0,0,0.05);">
                <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 14px;">
                    <div>
                        <h1 style="font-size: 22px; margin: 0 0 6px 0;"><?= htmlspecialchars($doc['title']) ?></h1>
                        <div style="font-size: 13px; color: #64748b;">
                            Prepared for: <strong><?= htmlspecialchars($doc['recipient_name']) ?></strong> (<?= htmlspecialchars($doc['recipient_email']) ?>)
                        </div>
                    </div>
                    <div>
                        <?php if (!empty($doc['signing_deadline'])): ?>
                            <div style="font-size: 12px; color: #dc2626; font-weight: 600;">
                                <ion-icon name="calendar-outline"></ion-icon> Deadline: <?= date('M d, Y', strtotime($doc['signing_deadline'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Document Body -->
                <div style="font-size: 14px; line-height: 1.8; color: inherit; white-space: pre-wrap; font-family: inherit; margin-bottom: 30px;">
<?= htmlspecialchars($doc['content']) ?>
                </div>

                <!-- Signature Verification Certificate (If Signed) -->
                <?php if ($doc['status'] === 'signed' && !empty($doc['signature'])): ?>
                    <div class="cert-card">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; border-bottom: 1px solid #cbd5e1; padding-bottom: 10px;">
                            <div style="display: flex; align-items: center; gap: 8px; color: #16a34a; font-weight: 700; font-size: 15px;">
                                <ion-icon name="shield-checkmark" style="font-size: 22px;"></ion-icon>
                                Digital Signature Certificate & Audit Trail
                            </div>
                            <span style="background: #dcfce7; color: #15803d; font-size: 12px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">
                                VERIFIED SIGNATURE
                            </span>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: center;">
                            <div>
                                <div style="margin-bottom: 8px; font-size: 13px;">
                                    <span style="color: #64748b;">Legally Signed By:</span><br>
                                    <strong style="font-size: 16px;"><?= htmlspecialchars($doc['signed_name']) ?></strong>
                                </div>
                                <div style="margin-bottom: 8px; font-size: 13px;">
                                    <span style="color: #64748b;">Recipient Email:</span><br>
                                    <strong><?= htmlspecialchars($doc['recipient_email']) ?></strong>
                                </div>
                                <div style="margin-bottom: 8px; font-size: 13px;">
                                    <span style="color: #64748b;">Timestamp:</span><br>
                                    <strong><?= date('M d, Y - h:i:s A T', strtotime($doc['signed_at'])) ?></strong>
                                </div>
                                <div style="font-size: 12px; color: #64748b;">
                                    <span>IP Address:</span> <code><?= htmlspecialchars($doc['signed_ip'] ?? 'N/A') ?></code>
                                </div>
                            </div>
                            <div style="text-align: center; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px;">
                                <div style="font-size: 12px; color: #64748b; margin-bottom: 6px; font-weight: 600;">Captured E-Signature:</div>
                                <img src="<?= $doc['signature'] ?>" alt="Signature" style="max-width: 280px; max-height: 100px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                <?php elseif ($doc['status'] === 'declined'): ?>
                    <div style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 16px; border-radius: 8px; margin-top: 20px;">
                        <strong style="display: flex; align-items: center; gap: 6px;">
                            <ion-icon name="close-circle-outline"></ion-icon> Signing Declined by Recipient
                        </strong>
                        <div style="margin-top: 6px; font-size: 13px;">
                            Reason: <?= htmlspecialchars($doc['declined_reason'] ?? 'None provided.') ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="border-top: 1px dashed #cbd5e1; padding-top: 20px; margin-top: 30px; display: flex; justify-content: space-between; align-items: center; color: #64748b; font-size: 13px;">
                        <div>Status: <strong>Awaiting recipient signature</strong></div>
                        <a href="<?= htmlspecialchars($signUrl) ?>" target="_blank" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px;">
                            <ion-icon name="pencil-outline"></ion-icon> Sign Document Now
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script>
function copySigningLink() {
    const input = document.getElementById('signLinkInput');
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value).then(() => {
        const btnText = document.getElementById('copyBtnText');
        btnText.textContent = "Copied!";
        setTimeout(() => { btnText.textContent = "Copy Link"; }, 2000);
    });
}
</script>
</body>
</html>
