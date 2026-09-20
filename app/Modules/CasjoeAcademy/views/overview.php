<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academy Overview | Casjoe Business School</title>
    <meta name="description" content="Casjoe Academy overview and central hub.">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        :root {
            --erp-bg: #ffffff;
            --erp-bg-text: #ffffff;
            --erp-surface: #0a0f25;
            --erp-primary: #000066;
            --erp-gold: #FFA600;
            --erp-border: rgba(255,255,255,0.08);
            --erp-radius: 20px;
            --erp-shadow: 0 4px 20px rgba(0,0,0,0.3);
            --erp-shadow-hover: 0 10px 30px rgba(0,0,0,0.5);
            --erp-text-muted: #94a3b8;
        }

        body { background: var(--erp-bg); color: var(--erp-bg-text); font-family: 'Inter', sans-serif; }

        .app-container { min-height: 100vh; display: flex; }
        a { text-decoration: none; }
        .sidebar { background-color: #030014 !important; border-right: 2px solid var(--erp-gold) !important; z-index: 100;}
        .sidebar .nav-link { color: rgba(255,255,255,0.8) !important; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #ffffff !important; background: rgba(255,255,255,0.1); }
        .sidebar .nav-link ion-icon { color: var(--erp-gold) !important; }

        .erp-main { flex: 1; margin-left: 260px; padding: 0; overflow-y: auto; background: var(--erp-bg); }

        /* ── Hero Banner ── */
        .erp-hero {
            background: linear-gradient(135deg, #000044 0%, #000077 50%, #1a1aaa 100%);
            color: white;
            padding: 25px 30px 20px;
            position: relative;
            overflow: hidden;
            border-bottom: 2px solid var(--erp-gold);
        }
        .erp-hero::before {
            content: '';
            position: absolute;
            top: -50%; right: -10%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(255,166,0,0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        .erp-hero-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            z-index: 1;
        }
        .erp-hero h1 {
            font-size: 1.8rem;
            font-weight: 800;
            margin: 0 0 5px 0;
            letter-spacing: -0.5px;
        }
        .erp-hero .subtitle {
            font-size: 0.95rem;
            opacity: 0.8;
            margin: 0;
        }

        /* ── Main Content Grid ── */
        .erp-content {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-header {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--erp-gold);
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 40px;
        }

        .module-card {
            background: var(--erp-surface);
            border: 1px solid var(--erp-border);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: var(--erp-shadow);
            display: flex;
            flex-direction: column;
        }
        .module-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 3px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: translateX(-100%);
            transition: transform 0.5s ease;
        }
        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--erp-shadow-hover);
            border-color: rgba(255,166,0,0.3);
        }
        .module-card:hover::after {
            transform: translateX(100%);
        }

        .module-icon-wrap {
            width: 45px; height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 15px;
        }

        /* Module Colors */
        .mod-learner .module-icon-wrap { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .mod-catalog .module-icon-wrap { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .mod-business .module-icon-wrap { background: rgba(245, 166, 35, 0.1); color: #f5a623; }
        .mod-library .module-icon-wrap { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
        .mod-builder .module-icon-wrap { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

        .module-card h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: #fff;
        }
        .module-card p {
            color: var(--erp-text-muted);
            font-size: 0.85rem;
            line-height: 1.4;
            margin: 0 0 20px 0;
            flex: 1;
        }

        .module-btn {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            background: var(--erp-gold);
            padding: 8px 15px;
            border-radius: 8px;
            color: #030014;
            font-weight: 700;
            font-size: 0.85rem;
            transition: all 0.2s;
            border: none;
        }
        .module-card:hover .module-btn {
            background: #ffb833;
            color: #030014;
        }

        @media (max-width: 992px) {
            .erp-main { margin-left: 0; }
            .sidebar { left: -270px !important; transition: left 0.3s ease; position: fixed; height: 100vh; }
            .sidebar.active { left: 0 !important; }
            .erp-hero { padding: 80px 20px 30px; } /* Room for mobile nav */
            .erp-content { padding: 20px; }
            .mobile-toggle {
                display: block !important;
                position: static !important;
                background: rgba(255,255,255,0.1);
                color: white;
                border: none;
                border-radius: 8px;
                padding: 8px 12px;
                font-size: 1.5rem;
                cursor: pointer;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Mobile Sidebar Toggle -->
        <?php include dirname(__DIR__, 3) . '/Core/Views/partials/mobile_nav.php'; ?>
        
        <!-- Sidebar -->
        <?php $active = 'academy_overview'; include __DIR__ . '/partials/sidebar_academy.php'; ?>

        <!-- Main Content -->
        <main class="erp-main">
            
            <!-- Hero Banner -->
            <section class="erp-hero">
                <div class="erp-hero-top" style="display: flex; align-items: center; gap: 15px;">
                    <button class="mobile-toggle d-lg-none" onclick="toggleMobileMenu()" style="display: none;">
                        <ion-icon name="menu-outline"></ion-icon>
                    </button>
                    <div>
                        <div class="label" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; color: var(--erp-gold); margin-bottom: 8px; font-weight: bold;">
                            <ion-icon name="school" style="vertical-align: middle; margin-right: 5px;"></ion-icon> Casjoe Business School
                        </div>
                        <h1>Academy Overview</h1>
                        <p class="subtitle">Your central hub for learning, administration, and course creation.</p>
                    </div>
                </div>
            </section>

            <!-- Dashboard Content -->
            <div class="erp-content">
                
                <div class="section-header">
                    <ion-icon name="apps"></ion-icon> Academy Modules
                </div>

                <div class="modules-grid">
                    
                    <!-- My Learning -->
                    <a href="/academy/learn" class="module-card mod-learner">
                        <div class="module-icon-wrap">
                            <ion-icon name="book-outline"></ion-icon>
                        </div>
                        <h3>My Learning</h3>
                        <p>Access your enrolled courses, pick up where you left off, and track your gamification progress and certificates.</p>
                        <div class="module-btn">
                            Enter Dashboard <ion-icon name="arrow-forward"></ion-icon>
                        </div>
                    </a>

                    <!-- Course Catalog -->
                    <a href="/academy/catalog" class="module-card mod-catalog">
                        <div class="module-icon-wrap">
                            <ion-icon name="compass-outline"></ion-icon>
                        </div>
                        <h3>Course Catalog</h3>
                        <p>Browse the public course catalog, explore new training materials, and enroll in upcoming sessions.</p>
                        <div class="module-btn">
                            Browse Catalog <ion-icon name="arrow-forward"></ion-icon>
                        </div>
                    </a>

                    <!-- Business Admin -->
                    <a href="/academy/business" class="module-card mod-business">
                        <div class="module-icon-wrap">
                            <ion-icon name="business-outline"></ion-icon>
                        </div>
                        <h3>Business Administration</h3>
                        <p>Manage your team's training, assign licenses to staff members, and view the organizational leaderboard.</p>
                        <div class="module-btn">
                            Manage Team <ion-icon name="arrow-forward"></ion-icon>
                        </div>
                    </a>

                    <!-- Course Builder -->
                    <a href="/academy/instructor" class="module-card mod-builder">
                        <div class="module-icon-wrap">
                            <ion-icon name="construct-outline"></ion-icon>
                        </div>
                        <h3>Course Builder</h3>
                        <p>Create new courses, manage curriculum with drag-and-drop, and utilize AI to generate lesson outlines instantly.</p>
                        <div class="module-btn">
                            Build Courses <ion-icon name="arrow-forward"></ion-icon>
                        </div>
                    </a>

                    <!-- CEO Book Reader -->
                    <a href="/academy/library" class="module-card mod-library">
                        <div class="module-icon-wrap">
                            <ion-icon name="library-outline"></ion-icon>
                        </div>
                        <h3>CEO Book Reader</h3>
                        <p>Access the exclusive digital E-Library, read premium books in an immersive UI, and track reading progress.</p>
                        <div class="module-btn">
                            Open Library <ion-icon name="arrow-forward"></ion-icon>
                        </div>
                    </a>

                </div>
            </div>
        </main>
    </div>
</body>
</html>
