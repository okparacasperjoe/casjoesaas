<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title><?= $reportTitle ?> | Casjoe ERP</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; color: #333; line-height: 1.6; }
        .report-header { border-bottom: 2px solid #000066; margin-bottom: 30px; padding-bottom: 10px; display: flex; justify-content: space-between; align-items: flex-end; }
        .report-header h1 { margin: 0; color: #000066; font-size: 1.8rem; }
        .report-header .meta { text-align: right; font-size: 0.9rem; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; background: #f4f4f4; padding: 12px; border: 1px solid #ddd; font-weight: 600; }
        td { padding: 12px; border: 1px solid #ddd; }
        tr:nth-child(even) { background: #fafafa; }
        
        .footer { margin-top: 50px; font-size: 0.8rem; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 20px; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #000066; color: white; border: none; border-radius: 4px;">Print / Save as PDF</button>
        <button onclick="window.history.back()" style="padding: 10px 20px; cursor: pointer; background: #eee; border: 1px solid #ccc; border-radius: 4px; margin-left: 10px;">Back</button>
    </div>

    <div class="report-header">
        <div>
            <h1><?= $reportTitle ?></h1>
            <p>Casjoe ERP Business Intelligence</p>
        </div>
        <div class="meta">
            <strong>Generated On:</strong> <?= date('F d, Y H:i') ?><br>
            <strong>Tenant ID:</strong> <?= $this->tenantId ?>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <?php foreach ($headers as $header): ?>
                    <th><?= htmlspecialchars($header) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data)): ?>
                <tr>
                    <td colspan="<?= count($headers) ?>" style="text-align: center; padding: 40px;">No data available for this report.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <?php foreach ($row as $value): ?>
                            <td><?= htmlspecialchars($value ?? '') ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        &copy; <?= date('Y') ?> Casjoe - Business, Connected. Confidential Business Report.
    </div>

    <script>
        // Auto-print if param is set
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('autoprint') === '1') {
            window.onload = () => window.print();
        }
    </script>
</body>
</html>
