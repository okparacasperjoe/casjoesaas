<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($doc['title']) ?> - Printable Certificate</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 40px;
            color: #1e293b;
            background: #fff;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .doc-body {
            white-space: pre-wrap;
            font-size: 14px;
            margin-bottom: 40px;
        }
        .cert-box {
            border: 2px solid #0284c7;
            border-radius: 8px;
            padding: 20px;
            margin-top: 40px;
            page-break-inside: avoid;
            background: #f0fdf4;
        }
        .no-print {
            margin-bottom: 20px;
            text-align: right;
        }
        .btn-print {
            background: #0284c7;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="no-print">
        <button onclick="window.print()" class="btn-print">Print / Save as PDF</button>
    </div>

    <div class="header">
        <div>
            <h1 style="margin: 0; font-size: 24px; color: #0f172a;"><?= htmlspecialchars($doc['title']) ?></h1>
            <div style="font-size: 13px; color: #64748b; margin-top: 4px;">
                Category: <?= htmlspecialchars(ucwords(str_replace('_', ' ', $doc['category']))) ?> &bull; Prepared on: <?= date('M d, Y', strtotime($doc['created_at'])) ?>
            </div>
        </div>
        <div style="text-align: right; font-size: 13px; color: #64748b;">
            Recipient: <strong><?= htmlspecialchars($doc['recipient_name']) ?></strong><br>
            <?= htmlspecialchars($doc['recipient_email']) ?>
        </div>
    </div>

    <div class="doc-body">
<?= htmlspecialchars($doc['content']) ?>
    </div>

    <?php if ($doc['status'] === 'signed' && !empty($doc['signature'])): ?>
        <div class="cert-box">
            <div style="font-weight: bold; font-size: 16px; color: #16a34a; margin-bottom: 12px; display: flex; justify-content: space-between;">
                <span>DIGITAL SIGNATURE & AUDIT CERTIFICATE</span>
                <span>STATUS: VERIFIED SIGNED</span>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <tr>
                    <td style="width: 60%; vertical-align: top;">
                        <p style="margin: 4px 0;"><strong>Legally Signed By:</strong> <?= htmlspecialchars($doc['signed_name']) ?></p>
                        <p style="margin: 4px 0;"><strong>Email Address:</strong> <?= htmlspecialchars($doc['recipient_email']) ?></p>
                        <p style="margin: 4px 0;"><strong>Date & Time:</strong> <?= date('F d, Y \a\t h:i:s A T', strtotime($doc['signed_at'])) ?></p>
                        <p style="margin: 4px 0;"><strong>IP Address:</strong> <?= htmlspecialchars($doc['signed_ip'] ?? 'N/A') ?></p>
                        <p style="margin: 4px 0; color: #64748b; font-size: 11px;"><strong>Verification UUID:</strong> <?= htmlspecialchars($doc['uuid']) ?></p>
                    </td>
                    <td style="width: 40%; text-align: center; vertical-align: middle;">
                        <div style="border: 1px solid #ccc; background: #fff; padding: 10px; border-radius: 4px; display: inline-block;">
                            <img src="<?= $doc['signature'] ?>" alt="Signature" style="max-width: 220px; max-height: 80px; object-fit: contain;">
                            <div style="font-size: 10px; color: #666; margin-top: 4px;">Electronic Signature</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
