<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <title>Setup Wizard | Casjoe</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        body {
            background: radial-gradient(circle at center, #000033 0%, #030014 100%);
            color: #fff;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .wizard-container h1, .wizard-container h3, .wizard-container h4, .wizard-container h5, .wizard-container label {
            color: #ffffff !important;
        }
        .wizard-container {
            width: 90%;
            max-width: 600px;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,166,0,0.3);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            position: relative;
            overflow: hidden;
        }
        .step {
            display: none;
            animation: fadeIn 0.4s ease forwards;
        }
        .step.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .wizard-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .wizard-header h2 {
            margin: 0 0 10px 0;
            color: #FFA600;
            font-size: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .wizard-header p {
            color: rgba(255,255,255,0.6);
            margin: 0;
            font-size: 1.05rem;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: rgba(255,255,255,0.8);
            font-size: 0.95rem;
        }
        .form-group input, .form-group select {
            width: 100%;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,166,0,0.3);
            padding: 15px 20px;
            border-radius: 12px;
            color: #fff;
            font-size: 1.05rem;
            outline: none;
            transition: 0.3s;
            box-sizing: border-box;
        }
        .form-group input:focus, .form-group select:focus {
            background: rgba(255,255,255,0.15);
            border-color: #FFA600;
            box-shadow: 0 0 15px rgba(255,166,0,0.2);
        }
        .form-group select option {
            background: #000033;
            color: #fff;
        }
        .btn-primary {
            background: linear-gradient(135deg, #FFA600, #ff8c00);
            color: #000;
            border: none;
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255,166,0,0.3);
        }
        .btn-secondary {
            background: transparent;
            color: rgba(255,255,255,0.6);
            border: 1px solid rgba(255,255,255,0.2);
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn-secondary:hover {
            background: rgba(255,255,255,0.05);
            color: #fff;
        }
        .stepper {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        .stepper-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            transition: 0.3s;
        }
        .stepper-dot.active {
            background: #FFA600;
            box-shadow: 0 0 10px #FFA600;
            width: 24px;
            border-radius: 6px;
        }
        
        .success-animation {
            text-align: center;
            padding: 40px 0;
        }
        .success-icon {
            font-size: 5rem;
            color: #4cd137;
            margin-bottom: 20px;
            animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            opacity: 0;
            transform: scale(0);
        }
        @keyframes popIn {
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>

<div class="wizard-container">
    <div class="stepper">
        <div class="stepper-dot active" id="dot-1"></div>
        <div class="stepper-dot" id="dot-2"></div>
        <div class="stepper-dot" id="dot-3"></div>
        <div class="stepper-dot" id="dot-4"></div>
    </div>

    <form id="wizardForm" onsubmit="return false;">
        <!-- STEP 1 -->
        <div class="step active" id="step-1">
            <div class="wizard-header">
                <h2><ion-icon name="rocket"></ion-icon> Welcome to Casjoe</h2>
                <p>Let's set up your business profile in just a few clicks.</p>
            </div>
            <div class="form-group">
                <label>Business Name</label>
                <input type="text" name="business_name" id="business_name" placeholder="E.g. Acme Corp" required>
            </div>
            <div class="form-group">
                <label>Industry</label>
                <select name="industry" id="industry" required>
                    <option value="">Select your industry...</option>
                    <option value="Retail">Retail & E-commerce</option>
                    <option value="Services">Professional Services</option>
                    <option value="Technology">Technology & Software</option>
                    <option value="Health">Healthcare & Wellness</option>
                    <option value="Food">Food & Beverage</option>
                    <option value="Education">Education & Coaching</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <button class="btn-primary" onclick="nextStep(2)">Next <ion-icon name="arrow-forward"></ion-icon></button>
        </div>

        <!-- STEP 2 -->
        <div class="step" id="step-2">
            <div class="wizard-header">
                <h2><ion-icon name="cash-outline"></ion-icon> Regional Settings</h2>
                <p>How will you primarily bill your clients?</p>
            </div>
            <div class="form-group">
                <label>Primary Currency</label>
                <select name="currency" id="currency" required>
                    <option value="USD">USD - US Dollar</option>
                    <option value="NGN">NGN - Nigerian Naira</option>
                    <option value="EUR">EUR - Euro</option>
                    <option value="GBP">GBP - British Pound</option>
                    <option value="ZAR">ZAR - South African Rand</option>
                    <option value="KES">KES - Kenyan Shilling</option>
                </select>
            </div>
            <button class="btn-primary" onclick="nextStep(3)">Next <ion-icon name="arrow-forward"></ion-icon></button>
            <button class="btn-secondary" onclick="prevStep(1)">Back</button>
        </div>

        <!-- STEP 3 -->
        <div class="step" id="step-3">
            <div class="wizard-header">
                <h2><ion-icon name="cube-outline"></ion-icon> First Product</h2>
                <p>Add your first product or service so your catalog isn't empty.</p>
            </div>
            <div class="form-group">
                <label>Product / Service Name</label>
                <input type="text" name="product_name" id="product_name" placeholder="E.g. Premium Consulting Hour">
            </div>
            <div class="form-group">
                <label>Price</label>
                <input type="number" name="product_price" id="product_price" placeholder="0.00" step="0.01">
            </div>
            <button class="btn-primary" onclick="submitWizard()">Complete Setup <ion-icon name="checkmark-done-circle"></ion-icon></button>
            <button class="btn-secondary" onclick="prevStep(2)">Back</button>
        </div>

        <!-- STEP 4 (Success) -->
        <div class="step" id="step-4">
            <div class="success-animation">
                <ion-icon name="checkmark-circle" class="success-icon"></ion-icon>
                <h2 style="color: #FFA600; margin-bottom: 10px;">You're all set!</h2>
                <p style="color: rgba(255,255,255,0.7); line-height: 1.6;">We've configured your business profile, set up your currency wallets, and created your first product.<br><br>Redirecting you to the dashboard...</p>
            </div>
        </div>
    </form>
</div>

<script>
    function nextStep(step) {
        // Basic validation before moving
        if (step === 2) {
            if (!document.getElementById('business_name').value) {
                document.getElementById('business_name').focus();
                return;
            }
            if (!document.getElementById('industry').value) {
                document.getElementById('industry').focus();
                return;
            }
        }
        
        showStep(step);
    }

    function prevStep(step) {
        showStep(step);
    }

    function showStep(step) {
        document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.stepper-dot').forEach((el, index) => {
            if (index < step) {
                el.classList.add('active');
            } else {
                el.classList.remove('active');
            }
        });
        document.getElementById('step-' + step).classList.add('active');
    }

    async function submitWizard() {
        const btn = document.querySelector('#step-3 .btn-primary');
        btn.innerHTML = 'Saving... <ion-icon name="sync" style="animation: spin 1s linear infinite;"></ion-icon>';
        btn.disabled = true;

        const formData = new FormData(document.getElementById('wizardForm'));
        
        try {
            const res = await fetch('/ai-builder/process', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            
            if (data.success) {
                showStep(4);
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 2500);
            }
        } catch (e) {
            console.error(e);
            alert("Something went wrong. Please try again.");
            btn.innerHTML = 'Complete Setup <ion-icon name="checkmark-done-circle"></ion-icon>';
            btn.disabled = false;
        }
    }
</script>

<style>
    @keyframes spin { 100% { transform: rotate(360deg); } }
</style>

</body>
</html>
