<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($job['title']) ?> at <?= htmlspecialchars($job['company_name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        :root {
            --pj-primary: #000066;
            --pj-primary-hover: #000044;
            --pj-accent: #FFA600;
            --pj-bg: #f8fafc;
            --pj-surface: #ffffff;
            --pj-text: #0f172a;
            --pj-text-muted: #64748b;
            --pj-border: #e2e8f0;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--pj-bg);
            color: var(--pj-text);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .pj-header {
            background: var(--pj-surface);
            padding: 20px 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pj-company-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--pj-primary);
            text-decoration: none;
        }

        .pj-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .pj-hero {
            background: linear-gradient(135deg, #000066, #1e3a8a);
            color: #ffffff;
            padding: 40px;
            border-radius: 20px 20px 0 0;
            position: relative;
            overflow: hidden;
        }
        
        .pj-hero::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 6px;
            background: var(--pj-accent);
        }

        .pj-hero h1 {
            margin: 0 0 10px 0;
            font-size: 2.5rem;
            font-weight: 800;
        }

        .pj-hero .pj-meta {
            display: flex;
            gap: 15px;
            color: #93c5fd;
            font-weight: 500;
            font-size: 1.1rem;
        }

        .pj-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .pj-content {
            background: var(--pj-surface);
            padding: 40px;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 40px;
        }

        .pj-description h2, .pj-description h3 {
            color: var(--pj-primary);
            margin-top: 30px;
        }

        .pj-apply-box {
            background: #f1f5f9;
            border: 1px solid var(--pj-border);
            border-radius: 12px;
            padding: 30px;
            margin-top: 40px;
        }

        .pj-apply-box h3 {
            margin-top: 0;
            color: var(--pj-primary);
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--pj-text);
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--pj-border);
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--pj-primary);
            box-shadow: 0 0 0 3px rgba(0,0,102,0.1);
        }

        .btn-submit {
            background: var(--pj-primary);
            color: #ffffff;
            border: none;
            padding: 14px 28px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: var(--pj-primary-hover);
        }

        .success-banner {
            background: #dcfce7;
            color: #166534;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            border: 1px solid #bbf7d0;
        }

        .success-banner ion-icon {
            font-size: 1.8rem;
        }

    </style>
</head>
<body>

    <header class="pj-header">
        <div class="pj-company-name"><?= htmlspecialchars($job['company_name']) ?></div>
    </header>

    <div class="pj-container">
        
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="success-banner">
                <ion-icon name="checkmark-circle"></ion-icon>
                Your application has been successfully submitted! The team will review it shortly.
            </div>
        <?php endif; ?>

        <div class="pj-hero">
            <h1><?= htmlspecialchars($job['title']) ?></h1>
            <div class="pj-meta">
                <span><ion-icon name="business-outline"></ion-icon> <?= htmlspecialchars($job['department']) ?></span>
                <span><ion-icon name="time-outline"></ion-icon> <?= date('F j, Y', strtotime($job['created_at'])) ?></span>
            </div>
        </div>

        <div class="pj-content">
            <div class="pj-description">
                <!-- Using nl2br for simple newlines -->
                <?= nl2br(htmlspecialchars($job['description'])) ?>
            </div>

            <?php if (!isset($_GET['success'])): ?>
            <div class="pj-apply-box" id="apply">
                <h3>Apply for this position</h3>
                <form action="/careers/job/apply" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                    <input type="hidden" name="tenant_id" value="<?= $job['tenant_id'] ?>">
                    
                    <div class="form-group">
                        <label for="candidate_name">Full Name *</label>
                        <input type="text" id="candidate_name" name="candidate_name" class="form-control" required placeholder="John Doe">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" class="form-control" required placeholder="john@example.com">
                    </div>

                    <div class="form-group">
                        <label for="resume">Resume / CV (PDF or DOCX)</label>
                        <input type="file" id="resume" name="resume" class="form-control" accept=".pdf,.doc,.docx" style="padding: 9px 16px;">
                    </div>

                    <?php 
                    $customQs = !empty($job['custom_questions']) ? json_decode($job['custom_questions'], true) : [];
                    if (!empty($customQs) && is_array($customQs)): 
                        foreach($customQs as $index => $question):
                    ?>
                        <div class="form-group">
                            <label><?= htmlspecialchars($question) ?> *</label>
                            <input type="text" name="answers[<?= $index ?>]" class="form-control" required>
                        </div>
                    <?php 
                        endforeach;
                    endif; 
                    ?>

                    <button type="submit" class="btn-submit">Submit Application</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
