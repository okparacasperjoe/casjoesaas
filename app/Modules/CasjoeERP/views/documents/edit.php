<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Edit Draft Document | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <h2>Edit Draft Document</h2>
                <p style="color: #64748b; font-size: 13px; margin: 2px 0 0 0;">Modify agreement details before sending for signature</p>
            </div>
            <a href="/erp/documents" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 6px;">
                <ion-icon name="arrow-back-outline"></ion-icon> Back to Documents
            </a>
        </div>

        <div class="card" style="max-width: 900px; margin: 20px auto;">
            <form action="/erp/documents/update" method="POST">
                <input type="hidden" name="id" value="<?= $doc['id'] ?>">

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 6px;">Document Title *</label>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($doc['title']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 6px;">Category</label>
                        <select name="category" class="form-control">
                            <option value="contract" <?= $doc['category'] === 'contract' ? 'selected' : '' ?>>Client Contract / Agreement</option>
                            <option value="nda" <?= $doc['category'] === 'nda' ? 'selected' : '' ?>>Non-Disclosure Agreement (NDA)</option>
                            <option value="proposal" <?= $doc['category'] === 'proposal' ? 'selected' : '' ?>>Sales Proposal</option>
                            <option value="offer_letter" <?= $doc['category'] === 'offer_letter' ? 'selected' : '' ?>>Employment Offer Letter</option>
                            <option value="agreement" <?= $doc['category'] === 'agreement' ? 'selected' : '' ?>>Vendor / Supplier Agreement</option>
                            <option value="other" <?= $doc['category'] === 'other' ? 'selected' : '' ?>>Other Document</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 6px;">Recipient Type</label>
                        <select name="recipient_type" class="form-control">
                            <option value="client" <?= $doc['recipient_type'] === 'client' ? 'selected' : '' ?>>Client / Customer</option>
                            <option value="vendor" <?= $doc['recipient_type'] === 'vendor' ? 'selected' : '' ?>>Vendor / Supplier</option>
                            <option value="employee" <?= $doc['recipient_type'] === 'employee' ? 'selected' : '' ?>>Employee / Staff</option>
                            <option value="other" <?= $doc['recipient_type'] === 'other' ? 'selected' : '' ?>>External Partner</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 6px;">Signer Full Name *</label>
                        <input type="text" name="recipient_name" class="form-control" value="<?= htmlspecialchars($doc['recipient_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 6px;">Signer Email Address *</label>
                        <input type="email" name="recipient_email" class="form-control" value="<?= htmlspecialchars($doc['recipient_email']) ?>" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Signing Deadline (Optional)</label>
                    <input type="date" name="signing_deadline" class="form-control" value="<?= htmlspecialchars($doc['signing_deadline'] ?? '') ?>">
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Document Content & Terms *</label>
                    <textarea name="content" class="form-control" style="width: 100%; min-height: 380px; font-family: monospace, sans-serif; font-size: 14px; line-height: 1.6; padding: 14px;" required><?= htmlspecialchars($doc['content']) ?></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; align-items: center;">
                    <a href="/erp/documents/show?id=<?= $doc['id'] ?>" class="btn btn-outline">Cancel</a>
                    <button type="submit" name="submit_action" value="save" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 6px;">
                        <ion-icon name="save-outline"></ion-icon> Save Changes
                    </button>
                    <button type="submit" name="submit_action" value="send" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px;">
                        <ion-icon name="send-outline"></ion-icon> Save & Activate for Signing
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
