<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <title><?= htmlspecialchars($page['title'] ?? 'Page') ?> - Casjoe</title>
    <meta name="description" content="<?= htmlspecialchars(mb_substr(strip_tags($page['content'] ?? ''), 0, 160)) ?>...">
    
    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [{
        "@type": "ListItem",
        "position": 1,
        "name": "Home",
        "item": "https://app.casjoe.com/"
      },{
        "@type": "ListItem",
        "position": 2,
        "name": "<?= htmlspecialchars($page['title'] ?? 'Page') ?>",
        "item": "https://app.casjoe.com/<?= htmlspecialchars($page['slug'] ?? '') ?>"
      }]
    }
    </script>
    
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars($page['title'] ?? 'Page') ?> - Casjoe">
    <meta property="og:description" content="<?= htmlspecialchars(mb_substr(strip_tags($page['content'] ?? ''), 0, 160)) ?>...">
    
    <!-- Canonical -->
    <?php if(isset($page['slug'])): ?>
    <link rel="canonical" href="https://app.casjoe.com/<?= htmlspecialchars($page['slug']) ?>" />
    <?php endif; ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        :root {
            --bg-color: #F8FAFC;
            --bg-hero: #00004D;
            --brand-blue: #000066;
            --brand-amber: #FFA600;
            --text-primary: #0F172A;
            --text-on-hero: #FFFFFF;
            --text-secondary: #64748B;
            --card-bg: #FFFFFF;
            --border: #E2E8F0;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; outline: none; }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

        /* Navbar */
        nav {
            position: fixed; top: 0; width: 100%; z-index: 1000;
            background: rgba(2, 2, 26, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding: 16px 0;
        }
        .nav-inner { display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 1.5rem; font-weight: 800; display: flex; align-items: center; gap: 12px; color: #fff; text-decoration: none; letter-spacing: -0.5px; }
        
        .nav-links { display: flex; gap: 32px; align-items: center; }
        .nav-link { color: rgba(255,255,255,0.7); text-decoration: none; font-weight: 500; font-size: 0.95rem; transition: 0.3s; }
        .nav-link:hover { color: #fff; }
        
        .btn-nav {
            background: var(--brand-amber); color: #000033; padding: 10px 24px; border-radius: 50px; 
            font-weight: 600; text-decoration: none; font-size: 0.95rem; transition: 0.3s;
        }
        .btn-nav:hover { background: #FFB733; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(255, 166, 0, 0.3); }

        /* Page Hero */
        .page-header {
            background-color: #00004D;
            background-image: linear-gradient(135deg, #00004D 0%, #000066 50%, #0D0D73 100%);
            padding: 140px 0 60px;
            color: #fff;
            text-align: center;
            position: relative;
        }
        .page-header::before {
            content: ''; position: absolute; top:0; left:0; width:100%; height:100%;
            background-image: radial-gradient(circle at center, rgba(255, 166, 0, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        .page-header h1 { font-size: 3rem; font-weight: 800; margin-bottom: 10px; color: #fff; }
        .page-header p { font-size: 1.1rem; color: rgba(255,255,255,0.7); max-width: 600px; margin: 0 auto; }

        /* Content */
        .content-area { background: #fff; padding: 60px 0; min-height: 50vh; }
        .content-body { max-width: 800px; margin: 0 auto; color: #334155; font-size: 1.05rem; line-height: 1.8; }
        .content-body h2 { color: var(--brand-blue); margin-top: 40px; margin-bottom: 20px; font-size: 1.8rem; }
        .content-body h3 { color: var(--text-primary); margin-top: 30px; margin-bottom: 15px; font-size: 1.4rem; }
        .content-body p { margin-bottom: 20px; }
        .content-body ul { margin-bottom: 24px; padding-left: 20px; }
        .content-body li { margin-bottom: 8px; }

        /* Footer */
        footer { background: #000022; color: #fff; padding: 80px 0 40px; border-top: 1px solid rgba(255,255,255,0.05); }
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; margin-bottom: 60px; }
        .footer-col h4 { color: #fff; margin-bottom: 24px; font-size: 1.1rem; }
        .footer-col a { display: block; color: var(--text-secondary); text-decoration: none; margin-bottom: 12px; transition: 0.2s; }
        .footer-col a:hover { color: var(--brand-amber); }
        .copyright { text-align: center; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 32px; color: var(--text-secondary); font-size: 0.9rem; }

        @media(max-width: 768px) {
            .nav-links { display: none; } /* Hide on mobile for now or implement toggle */
            .page-header h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>

    <nav>
        <div class="container nav-inner">
            <a href="/" class="logo">
                <img src="/assets/casjoe_logo.webp" alt="Casjoe" style="height: 32px;">
            </a>
            <div class="nav-links">
                <a href="/about-us" class="nav-link">About</a>
                <a href="/blog" class="nav-link">Blog</a>
                <a href="/login" class="nav-link">Login</a>
                <a href="/register" class="btn-nav">Get Started</a>
            </div>
        </div>
    </nav>

    <header class="page-header">
        <div class="container">
            <h1><?= htmlspecialchars($page['title']) ?></h1>
            <p>Everything you need to know about our <?= strtolower($page['title']) ?>.</p>
        </div>
    </header>

    <div class="content-area">
        <div class="container">
            <div class="content-body">
                <?= $page['content'] ?>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col" style="grid-column: span 2;">
                    <div style="margin-bottom: 24px;">
                        <img src="/assets/casjoe_logo.webp" alt="Casjoe" style="height: 32px; width: auto; opacity: 0.8;">
                    </div>
                    <p style="color: var(--text-secondary); margin-bottom: 24px; line-height: 1.6; max-width: 300px;">
                        Casjoe is the complete business ecosystem for African businesses. Operate, sell, get paid, and scale from one connected platform.
                    </p>
                </div>
                <div class="footer-col">
                    <h4>Company</h4>
                    <a href="/about-us">About Us</a>
                    <a href="/blog">Blog</a>
                    <a href="/contact">Contact</a>
                </div>
                <div class="footer-col">
                    <h4>Legal</h4>
                    <a href="/privacy-policy">Privacy Policy</a>
                    <a href="/terms-of-service">Terms of Service</a>
                    <a href="/accessibility">Accessibility</a>
                </div>
            </div>
            <div class="copyright">
                &copy; <?= date('Y') ?> Casjoe LLC. All rights reserved.
            </div>
        </div>
    </footer>

    <?php include __DIR__ . '/partials/google_prompt.php'; ?>
</body>
</html>
