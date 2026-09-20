<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Your Business - Casjoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #020314 0%, #000066 50%, #0a1128 100%); min-height: 100vh; font-family: 'Inter', sans-serif; padding: 20px 0; }
        .onboarding-container { max-width: 600px; margin: 40px auto; background: white; padding: 40px; border-radius: 16px; box-shadow: 0 15px 35px rgba(0,0,0,0.35); position: relative; z-index: 10; }
        .step-indicator { display: flex; justify-content: space-between; margin-bottom: 30px; position: relative; }
        .step-indicator::before { content: ''; position: absolute; top: 12px; left: 0; right: 0; height: 2px; background: #e9ecef; z-index: 1; }
        .step { position: relative; z-index: 2; width: 24px; height: 24px; background: #e9ecef; border-radius: 50%; color: transparent; font-size: 0; transition: 0.3s; }
        .step.active { background: #000066; box-shadow: 0 0 0 4px rgba(0,0,102,0.1); }
        .step.completed { background: #28a745; }
        .btn-primary { background-color: #000066; border-color: #000066; }
        .btn-primary:hover { background-color: #00004d; }
    </style>
</head>
<body>

<div class="onboarding-container">
    <div class="text-center mb-4">
        <img src="/assets/casjoe_logo.webp" alt="Casjoe" style="height: 40px;">
    </div>
    
    <!-- Step Progress -->
    <div class="step-indicator">
        <?php for($i=1; $i<=8; $i++): ?>
            <div class="step <?= $i <= $step ? 'completed' : '' ?> <?= $i == $step ? 'active' : '' ?>"></div>
        <?php endfor; ?>
    </div>

    <!-- View Content -->
    <?php require $viewPath; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
