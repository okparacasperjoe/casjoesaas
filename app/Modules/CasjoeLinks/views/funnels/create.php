<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Sales Funnel | Casjoe Links</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/casjoe_links.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .funnel-types-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            padding: 20px 0;
            max-width: 1200px;
            margin: 0 auto;
        }
        .funnel-type-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            padding: 28px 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .funnel-type-card:hover {
            border-color: #FFA600;
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(255,166,0,0.18);
        }
        .funnel-type-card.selected {
            border-color: #FFA600;
            background: linear-gradient(135deg, #FFF9F0 0%, #FFFFFF 100%);
            box-shadow: 0 0 0 3px rgba(255, 166, 0, 0.25);
        }
        .funnel-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #FFA600, #000066);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            box-shadow: 0 6px 16px rgba(0,0,102,0.15);
        }
        .funnel-icon ion-icon {
            font-size: 34px;
            color: white;
        }
        .funnel-type-card h3 {
            margin: 0 0 8px 0;
            color: #000066;
            font-size: 1.25rem;
            font-weight: 700;
        }
        .funnel-type-card p {
            color: #64748b;
            font-size: 0.92rem;
            line-height: 1.55;
            margin-bottom: 16px;
        }
        .funnel-feature-pill {
            display: inline-block;
            background: #f1f5f9;
            color: #334155;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 14px;
        }
        .funnel-feature-pill.highlight {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .funnel-type-card .best-for {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid #f1f5f9;
            font-size: 0.82rem;
            color: #94a3b8;
        }
        .selected-indicator {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 28px;
            height: 28px;
            background: #FFA600;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }
        .selected .selected-indicator {
            display: flex;
        }
        .floating-action-bar {
            position: fixed;
            bottom: 24px;
            right: 28px;
            display: none;
            gap: 12px;
            z-index: 100;
            animation: slideUp 0.25s ease-out;
        }
        .floating-action-bar.visible {
            display: flex;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .btn-continue {
            padding: 12px 24px;
            background: #000066;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 16px rgba(0,0,102,0.25);
            transition: all 0.2s;
        }
        .btn-continue:hover {
            background: #000044;
            transform: translateY(-2px);
        }
        .btn-quick {
            padding: 12px 24px;
            background: #FFA600;
            color: #000066;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 16px rgba(255,166,0,0.35);
            transition: all 0.2s;
        }
        .btn-quick:hover {
            background: #e69500;
            transform: translateY(-2px);
        }
        .page-header {
            margin-bottom: 20px;
        }
        .page-header h1 {
            color: #000066;
            margin-bottom: 6px;
            font-size: 1.8rem;
        }
        .page-header p {
            color: #64748b;
            font-size: 1rem;
        }
        html.dark-theme .funnel-type-card {
            background: #1e293b;
            border-color: #334155;
        }
        html.dark-theme .funnel-type-card.selected {
            background: #0f172a;
            border-color: #FFA600;
        }
        html.dark-theme .funnel-type-card h3 {
            color: #f8fafc;
        }
        html.dark-theme .funnel-feature-pill {
            background: #334155;
            color: #e2e8f0;
        }
        html.dark-theme .page-header h1 {
            color: #f8fafc !important;
        }
        html.dark-theme .page-header p {
            color: #94a3b8 !important;
        }
        html.dark-theme .btn-continue {
            background: #3b82f6;
            color: #ffffff;
        }
        @media (max-width: 768px) {
            .floating-action-bar {
                bottom: 85px !important;
                right: 16px !important;
                left: 16px !important;
                justify-content: center !important;
            }
            .btn-continue, .btn-quick {
                padding: 10px 14px !important;
                font-size: 13.5px !important;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php $active = 'funnels'; include dirname(__DIR__) . '/partials/sidebar_links.php'; ?>
        
        <main class="main-content">
            <div id="casjoe-links-app">
            <div class="page-header">
                <a href="/links/funnels" style="color: #64748b; text-decoration: none; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 4px; margin-bottom: 12px;">
                    <ion-icon name="arrow-back-outline"></ion-icon> Back to Funnels
                </a>
                <h1>Create Sales Funnel</h1>
                <p>Choose the funnel architecture that best converts your audience</p>
            </div>

            <form id="funnelTypeForm" method="GET" action="/links/funnels/create">
                <input type="hidden" name="type" id="selectedType" value="">

                <div class="funnel-types-grid">
                    <!-- Product Sales Funnel with 1-Click Upsells -->
                    <div class="funnel-type-card" data-type="product" data-name="Product Sales Funnel">
                        <div class="selected-indicator">
                            <ion-icon name="checkmark"></ion-icon>
                        </div>
                        <div>
                            <div class="funnel-icon">
                                <ion-icon name="cart-outline"></ion-icon>
                            </div>
                            <h3>Product &amp; Upsell Funnel</h3>
                            <div class="funnel-feature-pill highlight">
                                ? 1-Click Upsell, Downsell &amp; Order Bump
                            </div>
                            <p>Sell physical or digital products with instant checkout, post-purchase 1-click upsells, downsells, and order bumps.</p>
                        </div>
                        <div class="best-for">
                            <strong>Flow:</strong> Sales Page &rarr; Order Bump Checkout &rarr; 1-Click Upsell &rarr; Downsell &rarr; Thank You
                        </div>
                    </div>

                    <!-- Lead Generation Funnel -->
                    <div class="funnel-type-card" data-type="lead" data-name="Lead Generation Funnel">
                        <div class="selected-indicator">
                            <ion-icon name="checkmark"></ion-icon>
                        </div>
                        <div>
                            <div class="funnel-icon">
                                <ion-icon name="people-outline"></ion-icon>
                            </div>
                            <h3>Lead Capture Funnel</h3>
                            <div class="funnel-feature-pill">
                                ?? AI Lead Scoring &amp; CRM Sync
                            </div>
                            <p>Capture high-intent leads with opt-in pages and qualification forms. Automatically scores leads into CRM.</p>
                        </div>
                        <div class="best-for">
                            <strong>Flow:</strong> Opt-In Page &rarr; Qualification Form &rarr; Thank You Page
                        </div>
                    </div>

                    <!-- Service Consultation Funnel -->
                    <div class="funnel-type-card" data-type="service" data-name="Service Booking Funnel">
                        <div class="selected-indicator">
                            <ion-icon name="checkmark"></ion-icon>
                        </div>
                        <div>
                            <div class="funnel-icon">
                                <ion-icon name="briefcase-outline"></ion-icon>
                            </div>
                            <h3>Service &amp; Booking Funnel</h3>
                            <div class="funnel-feature-pill">
                                ?? Calendar &amp; Consultation
                            </div>
                            <p>Showcase your client packages, collect consultation requests, and let prospects book calls directly.</p>
                        </div>
                        <div class="best-for">
                            <strong>Flow:</strong> Service Showcase &rarr; Intake Form &rarr; Confirmation
                        </div>
                    </div>

                    <!-- Event Registration Funnel -->
                    <div class="funnel-type-card" data-type="event" data-name="Event Registration Funnel">
                        <div class="selected-indicator">
                            <ion-icon name="checkmark"></ion-icon>
                        </div>
                        <div>
                            <div class="funnel-icon">
                                <ion-icon name="calendar-outline"></ion-icon>
                            </div>
                            <h3>Event &amp; Webinar Funnel</h3>
                            <div class="funnel-feature-pill">
                                ??? Registration &amp; Reminders
                            </div>
                            <p>Drive attendance for webinars, workshops, product launches, or conferences with attendee tracking.</p>
                        </div>
                        <div class="best-for">
                            <strong>Flow:</strong> Registration Page &rarr; Attendee Form &rarr; Event Access
                        </div>
                    </div>
                </div>

                <!-- Floating action buttons shown upon card selection -->
                <div class="floating-action-bar" id="actionBar">
                    <button type="submit" class="btn-continue" id="continueBtn" title="Customize ERP stages and owner">
                        Step 2: Customize Setup <ion-icon name="arrow-forward-outline"></ion-icon>
                    </button>
                    <button type="button" class="btn-quick" id="quickLaunchBtn" title="Create immediately with smart defaults">
                        <ion-icon name="flash"></ion-icon> 1-Click Launch
                    </button>
                </div>
            </form>

            <!-- Hidden quick post form for 1-click launch -->
            <form id="quickLaunchForm" method="POST" action="/links/funnels/store" style="display:none;">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?>">
                <input type="hidden" name="type" id="quickType" value="">
                <input type="hidden" name="name" id="quickName" value="">
            </form>
            </div>
        </main>
    </div>

    <script>
        const cards = document.querySelectorAll('.funnel-type-card');
        const actionBar = document.getElementById('actionBar');
        const selectedTypeInput = document.getElementById('selectedType');
        const quickTypeInput = document.getElementById('quickType');
        const quickNameInput = document.getElementById('quickName');
        const quickLaunchBtn = document.getElementById('quickLaunchBtn');
        const quickLaunchForm = document.getElementById('quickLaunchForm');

        cards.forEach(card => {
            card.addEventListener('click', function() {
                cards.forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                
                const type = this.dataset.type;
                const name = this.dataset.name;

                selectedTypeInput.value = type;
                quickTypeInput.value = type;
                quickNameInput.value = name;
                
                actionBar.classList.add('visible');
            });
        });

        quickLaunchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (quickTypeInput.value) {
                quickLaunchBtn.innerHTML = '<ion-icon name="sync-outline" class="spin"></ion-icon> Launching...';
                quickLaunchBtn.disabled = true;
                quickLaunchForm.submit();
            }
        });
    </script>
</body>
</html>
