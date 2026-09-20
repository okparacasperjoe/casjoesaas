<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Certificate of Completion | Casjoe Business School</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Great+Vibes&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f0f0f0;
            font-family: 'Roboto', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .certificate-container {
            background: #fff;
            width: 900px;
            padding: 40px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            border: 20px solid #000066; /* Casjoe Blue */
        }
        .inner-border {
            border: 5px solid #FFA600; /* Casjoe Amber */
            padding: 40px;
            height: 100%;
            box-sizing: border-box;
        }
        .header {
            font-family: 'Cinzel', serif;
            font-size: 3rem;
            color: #000066;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .sub-header {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .recipient {
            font-family: 'Great Vibes', cursive;
            font-size: 4rem;
            color: #333;
            margin: 20px 0;
            border-bottom: 2px solid #eee;
            display: inline-block;
            padding: 0 40px 10px;
        }
        .course-title {
            font-weight: bold;
            font-size: 1.5rem;
            color: #000066;
            margin: 20px 0 10px;
        }
        .description {
            font-size: 1.1rem;
            color: #555;
            line-height: 1.6;
            max-width: 700px;
            margin: 0 auto 50px;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 40px;
        }
        .signature {
            text-align: center;
        }
        .signature-line {
            width: 200px;
            border-top: 1px solid #333;
            margin-top: 5px;
            padding-top: 10px;
            font-weight: bold;
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        .verify-code {
            font-size: 0.8rem;
            color: #999;
            margin-top: 40px;
        }
        @media print {
            body { background: none; }
            .certificate-container { box-shadow: none; width: 100%; border-width: 10px; }
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Watermark Logo -->
        <img src="/assets/casjoe_logo.webp" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.05; width: 500px; pointer-events: none;">

        <div class="inner-border">
            <!-- Top Logo -->
             <img src="/assets/casjoe_logo.webp" alt="Casjoe Business School" style="height: 60px; margin-bottom: 20px;">

            <div class="header">Certificate of Completion</div>
            <div class="sub-header">This is to certify that</div>

            <div class="recipient"><?= htmlspecialchars($cert['student_name'] ?? 'Student') ?></div>

            <div class="description">
                has successfully completed the requirements for the course
                <div class="course-title"><?= htmlspecialchars($cert['course_title'] ?? 'Course') ?></div>
                demonstrating dedication, proficiency, and commitment to professional excellence.
            </div>

            <div class="footer">
                <div class="signature">
                    <!-- Director Signature -->
                    <img src="/assets/signature_casper.png" alt="Signature" style="height: 60px; margin-bottom: 0px;">
                    <div class="signature-line"></div>
                    <div style="font-weight: bold; color: #000066; margin-top: 5px;">Casper Joe Okpara</div>
                    <div style="font-size: 0.8rem; color: #666;">Casjoe Business School Director</div>
                </div>
                
                <div style="text-align: center; position: relative;">
                    <!-- Seal Image -->
                    <img src="/assets/seal_badge.png" alt="Official Seal" style="width: 130px; height: 130px; margin: 0 auto;">
                    <div style="font-size: 0.8rem; color: #999; margin-top: 5px;"><?= date('F j, Y', strtotime($cert['issued_at'])) ?></div>
                </div>

                <div class="signature">
                     <!-- Real signature could go here -->
                    <div style="height: 60px;"></div> <!-- Placeholder -->
                    <div class="signature-line"></div>
                    <div style="font-weight: bold; color: #000066; margin-top: 5px;">Instructor Name</div>
                    <div style="font-size: 0.8rem; color: #666;">Course Instructor</div>
                </div>
            </div>

            <div class="verify-code">
                Certificate ID: <?= htmlspecialchars($cert['certificate_code']) ?><br>
                Verify at: <?= $_SERVER['HTTP_HOST'] ?>/academy/certificate/<?= htmlspecialchars($cert['certificate_code']) ?>
            </div>
        </div>
    </div>
    
    <div class="share-container">
        <h3>Share your Achievement</h3>
        <div class="share-buttons">
            <!-- Add to LinkedIn Profile -->
            <a href="https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME&name=<?= urlencode($cert['course_title'] ?? 'Course Completion') ?>&organizationName=Casjoe%20Academy&issueYear=<?= date('Y', strtotime($cert['issued_at'])) ?>&issueMonth=<?= date('m', strtotime($cert['issued_at'])) ?>&certUrl=<?= urlencode('https://' . $_SERVER['HTTP_HOST'] . '/academy/certificate/' . $cert['certificate_code']) ?>&certId=<?= htmlspecialchars($cert['certificate_code']) ?>" 
               target="_blank" class="btn-linkedin-add">
               <ion-icon name="logo-linkedin"></ion-icon> Add to LinkedIn Profile
            </a>

            <!-- Share Post -->
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode('https://' . $_SERVER['HTTP_HOST'] . '/academy/certificate/' . $cert['certificate_code']) ?>" 
               target="_blank" class="btn-linkedin-share">
               <ion-icon name="share-social"></ion-icon> Share Post
            </a>

            <!-- Download PDF -->
            <button id="downloadPdf" class="btn-download">
                <ion-icon name="download"></ion-icon> Download Certificate
            </button>

            <!-- Copy Link -->
            <button onclick="copyLink()" class="btn-copy">
                <ion-icon name="link"></ion-icon> Copy Link
            </button>
        </div>
    </div>

    <style>
        body {
            /* Updated layout for vertical stacking */
            flex-direction: column;
            padding: 50px 0;
            height: auto; 
        }
        .share-container {
            text-align: center;
            margin-top: 30px;
            padding-bottom: 50px;
        }
        .share-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 15px;
            flex-wrap: wrap; 
        }
        .btn-linkedin-add, .btn-linkedin-share, .btn-copy, .btn-download {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 5px;
            text-decoration: none;
            font-family: 'Roboto', sans-serif;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            font-size: 1rem;
        }
        .btn-linkedin-add {
            background: #0077b5;
            color: white;
        }
        .btn-linkedin-share {
            background: #fff;
            color: #0077b5;
            border: 1px solid #0077b5;
        }
        .btn-copy {
            background: #eee;
            color: #333;
        }
        .btn-download {
            background: #FFA600; /* Casjoe Amber */
            color: #000;
        }
        .btn-linkedin-add:hover, .btn-linkedin-share:hover, .btn-copy:hover, .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        @media print {
            .share-container { display: none; }
            body { padding: 0; }
        }
    </style>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- PDF Generation Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        function copyLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                alert('Link copied to clipboard!');
            });
        }

        document.getElementById('downloadPdf').addEventListener('click', function() {
            const element = document.querySelector('.certificate-container');
            const opt = {
                margin:       0,
                filename:     'Casjoe_Certificate.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'landscape' }
            };
            
            // Clone the element to remove the watermark opacity specifically for PDF if needed, 
            // but the current CSS should visually render fine.
            html2pdf().from(element).set(opt).save();
        });
    </script>
</body>
</html>

