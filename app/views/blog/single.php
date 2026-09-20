<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <?php
    $pageTitle = !empty($post['meta_title']) ? $post['meta_title'] : $post['title'];
    $pageDescription = !empty($post['meta_description']) ? $post['meta_description'] : mb_substr(strip_tags($post['excerpt'] ?: $post['content']), 0, 160) . '...';
    $pageKeywords = !empty($post['meta_keywords']) ? $post['meta_keywords'] : 'Casjoe Blog, Business Insights';
    ?>
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($pageKeywords) ?>">
    
    <!-- Open Graph -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://app.casjoe.com/blog/<?= htmlspecialchars($post['slug']) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <?php if(!empty($post['image_url'])): ?>
    <meta property="og:image" content="<?= htmlspecialchars($post['image_url']) ?>">
    <?php endif; ?>

    <!-- Article Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BlogPosting",
      "headline": "<?= htmlspecialchars($post['title']) ?>",
      "image": "<?= htmlspecialchars($post['image_url'] ?: 'https://app.casjoe.com/assets/casjoe_og_image.png') ?>",
      "datePublished": "<?= $post['created_at'] ?>",
      "author": {
        "@type": "Person",
        "name": "Casjoe Team"
      },
      "publisher": {
        "@type": "Organization",
        "name": "Casjoe",
        "logo": {
          "@type": "ImageObject",
          "url": "https://app.casjoe.com/assets/casjoe_logo.webp"
        }
      },
      "description": "<?= htmlspecialchars($pageDescription) ?>"
    }
    </script>

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
        "name": "Blog",
        "item": "https://app.casjoe.com/blog"
      },{
        "@type": "ListItem",
        "position": 3,
        "name": "<?= htmlspecialchars($post['title']) ?>",
        "item": "https://app.casjoe.com/blog/<?= htmlspecialchars($post['slug']) ?>"
      }]
    }
    </script>

    <!-- Canonical -->
    <link rel="canonical" href="https://app.casjoe.com/blog/<?= htmlspecialchars($post['slug']) ?>" />
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

        /* Post Header */
        .post-header-section {
            background-color: #00004D;
            background-image: linear-gradient(135deg, #00004D 0%, #000066 50%, #0D0D73 100%);
            padding: 180px 0 100px; /* Taller for post title */
            color: #fff;
            text-align: center;
            position: relative;
        }
        .post-header-section::before {
            content: ''; position: absolute; top:0; left:0; width:100%; height:100%;
            background-image: radial-gradient(circle at center, rgba(255, 166, 0, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        .post-title { font-size: 3rem; font-weight: 800; margin-bottom: 20px; color: #fff; line-height: 1.2; max-width: 900px; margin-left: auto; margin-right: auto; }
        .post-meta { font-size: 1rem; color: rgba(255,255,255,0.7); display: flex; align-items: center; justify-content: center; gap: 20px; }
        .meta-item { display: flex; align-items: center; gap: 6px; }

        /* Post Content */
        .content-area { padding: 0 0 80px; margin-top: -60px; /* Overlap header */ position: relative; z-index: 10; }
        .post-wrapper {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 800px; margin: 0 auto;
        }
        .post-image { width: 100%; height: 400px; object-fit: cover; }
        
        .post-body { padding: 60px; color: #334155; font-size: 1.1rem; line-height: 1.8; }
        .post-body h2 { color: var(--brand-blue); margin-top: 40px; margin-bottom: 20px; font-size: 1.8rem; }
        .post-body h3 { color: var(--text-primary); margin-top: 30px; margin-bottom: 15px; font-size: 1.4rem; }
        .post-body p { margin-bottom: 24px; }
        .post-body ul { margin-bottom: 24px; padding-left: 20px; }
        .post-body li { margin-bottom: 8px; }
        .post-body a { color: var(--brand-blue); text-decoration: underline; }
        .post-body a:hover { color: var(--brand-amber); }
        .post-body blockquote {
            border-left: 4px solid var(--brand-amber); padding-left: 20px; 
            margin: 30px 0; font-style: italic; color: var(--brand-blue); background: #FFF9E5; padding: 20px; border-radius: 0 8px 8px 0;
        }

        /* Footer */
        footer { background: #000022; color: #fff; padding: 80px 0 40px; border-top: 1px solid rgba(255,255,255,0.05); }
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; margin-bottom: 60px; }
        .footer-col h4 { color: #fff; margin-bottom: 24px; font-size: 1.1rem; }
        .footer-col a { display: block; color: var(--text-secondary); text-decoration: none; margin-bottom: 12px; transition: 0.2s; }
        .footer-col a:hover { color: var(--brand-amber); }
        .copyright { text-align: center; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 32px; color: var(--text-secondary); font-size: 0.9rem; }

        .back-link { display: inline-flex; align-items: center; gap: 6px; color: rgba(255,255,255,0.6); text-decoration: none; margin-bottom: 20px; transition: 0.2s; }
        .back-link:hover { color: var(--brand-amber); }

        @media(max-width: 768px) {
            .nav-links { display: none; }
            .post-title { font-size: 2rem; }
            .post-body { padding: 30px; }
            .post-image { height: 250px; }
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

    <header class="post-header-section">
        <div class="container">
            <a href="/blog" class="back-link"><ion-icon name="arrow-back-outline"></ion-icon> Back to Blog</a>
            <!-- Visual Breadcrumbs -->
            <div style="margin-bottom: 24px;">
                <a href="/" style="color: var(--text-secondary); text-decoration: none; font-size: 0.9rem;">Home</a>
                <span style="color: var(--text-secondary); margin: 0 8px; font-size: 0.8rem;">/</span>
                <a href="/blog" style="color: var(--text-secondary); text-decoration: none; font-size: 0.9rem;">Blog</a>
                <span style="color: var(--text-secondary); margin: 0 8px; font-size: 0.8rem;">/</span>
                <span style="color: var(--brand-blue); font-weight: 500; font-size: 0.9rem;">Article</span>
            </div>

            <h1 class="post-title"><?= htmlspecialchars($post['title']) ?></h1>
            <div class="post-meta">
                <div class="meta-item"><ion-icon name="calendar-outline"></ion-icon> <?= date('F j, Y', strtotime($post['created_at'])) ?></div>
                <div class="meta-item"><ion-icon name="person-circle-outline"></ion-icon> Casjoe Team</div>
            </div>
        </div>
    </header>

    <div class="content-area">
        <div class="container">
            <div class="post-wrapper">
                <?php if($post['image_url']): ?>
                <img src="<?= htmlspecialchars($post['image_url']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="post-image">
                <?php endif; ?>
                
                <div class="post-body">
                    <?= $post['content'] ?>
                </div>
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

    <?php include dirname(__DIR__) . '/partials/google_prompt.php'; ?>
</body>
</html>
