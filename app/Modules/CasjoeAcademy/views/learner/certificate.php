<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Certificate of Completion - <?= htmlspecialchars($data['certificate_code']) ?></title>
    <style>
        .certificate-container {
            width: 800px;
            height: 600px;
            padding: 20px;
            margin: 50px auto;
            border: 10px solid #787878;
            background: #fdfdfd;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            position: relative;
            font-family: 'Georgia', serif;
            text-align: center;
        }
        .cert-border {
            border: 2px solid #333;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        h1 {
            font-size: 3rem;
            color: #000066; /* Casjoe Primary */
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        h2 {
            font-size: 1.5rem;
            font-family: 'Arial', sans-serif;
            color: #555;
            font-weight: normal;
            margin-top: 5px;
        }
        .recipient {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 30px 0 10px;
            border-bottom: 1px solid #ccc;
            display: inline-block;
            padding: 0 40px 10px;
            color: #333;
        }
        .course-name {
            font-size: 2rem;
            font-weight: bold;
            color: #FFA600; /* Casjoe Secondary */
            margin: 20px 0;
        }
        .meta {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            width: 80%;
            font-family: 'Arial', sans-serif;
            font-size: 0.9rem;
            color: #777;
        }
        .meta-group {
            text-align: center;
        }
        .signature {
            font-family: 'Cursive', serif;
            font-size: 1.5rem;
            color: #333;
            border-top: 1px solid #333;
            padding-top: 10px;
            margin-top: 10px;
            width: 200px;
            display: inline-block;
        }
        .verification {
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 0.7rem;
            color: #999;
            font-family: monospace;
        }
        @media print {
            body { margin: 0; padding: 0; background: none; }
            .certificate-container { box-shadow: none; margin: 0; border: 5px solid #333; width: 100%; height: 100vh; }
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="cert-border">
            <div style="font-size: 4rem; color: #FFA600; margin-bottom: 20px;">★</div>
            <h1>Certificate of Completion</h1>
            <h2>is hereby awarded to</h2>
            
            <div class="recipient"><?= htmlspecialchars($data['first_name'] . ' ' . $data['last_name']) ?></div>
            
            <p>For successfully completing the course</p>
            
            <div class="course-name"><?= htmlspecialchars($data['course_title']) ?></div>
            
            <p>at <strong><?= htmlspecialchars($data['company_name'] ?? 'Casjoe Business School') ?></strong></p>
            
            <div class="meta">
                <div class="meta-group">
                    <div><?= date('F d, Y', strtotime($data['issued_at'])) ?></div>
                    <div style="border-top: 1px solid #ccc; margin-top: 5px; padding-top: 5px;">Date Issued</div>
                </div>
                <!-- <div class="meta-group">
                    <div class="signature">Casjoe Instructor</div>
                    <div>Instructor</div>
                </div> -->
                <div class="meta-group">
                    <div class="signature">System Verified</div>
                    <div style="border-top: 1px solid #ccc; margin-top: 5px; padding-top: 5px;">Verification</div>
                </div>
            </div>

            <div class="verification">ID: <?= htmlspecialchars($data['certificate_code']) ?></div>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 20px;" class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #000066; color: white; border: none; border-radius: 5px;">Print Certificate</button>
    </div>
</body>
</html>

