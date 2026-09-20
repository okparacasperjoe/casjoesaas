<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <title>Casjoe Blog - Business Insights, Updates & Resources</title>
    <meta name="description" content="Stay updated with the latest business insights, resources, and platform updates from Casjoe. Learn how to grow and scale your business in Africa.">
    <meta name="keywords" content="Casjoe Blog, Business Growth Africa, Fintech Insights, ERP Updates, Entrepreneurship Nigeria">
    
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
      }]
    }
    </script>
    
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://app.casjoe.com/blog">
    <meta property="og:title" content="Casjoe Blog - Business Insights & Resources">
    <meta property="og:description" content="Insights, updates, and resources for growing your business with Casjoe.">
    <meta property="og:image" content="https://app.casjoe.com/assets/casjoe_og_image.png">

    <!-- Canonical -->
    <link rel="canonical" href="https://app.casjoe.com/blog" />
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

        /* Blog Grid */
        .content-area { padding: 80px 0; min-height: 60vh; }
        .blog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 40px; }
        .blog-card {
            background: #fff; border-radius: 16px; overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02); transition: 0.3s; border: 1px solid var(--border);
            display: flex; flex-direction: column;
        }
        .blog-card:hover { transform: translateY(-8px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: var(--brand-amber); }
        .blog-img { height: 220px; background: #eee; background-size: cover; background-position: center; position: relative; }
        .blog-badge {
            position: absolute; top: 16px; left: 16px; background: var(--brand-amber); color: #000033; 
            padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;
        }
        .blog-content { padding: 32px; flex: 1; display: flex; flex-direction: column; }
        .blog-date { font-size: 0.85rem; color: #94A3B8; margin-bottom: 12px; display: flex; align-items: center; gap: 6px; }
        .blog-content h3 { margin: 0 0 12px; font-size: 1.4rem; color: var(--brand-blue); line-height: 1.3; font-weight: 700; }
        .blog-content p { color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; margin-bottom: 24px; flex: 1; }
        .read-more {
            color: var(--brand-blue); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s;
        }
        .read-more:hover { color: var(--brand-amber); gap: 12px; }

        /* Footer */
        footer { background: #000022; color: #fff; padding: 80px 0 40px; border-top: 1px solid rgba(255,255,255,0.05); }
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; margin-bottom: 60px; }
        .footer-col h4 { color: #fff; margin-bottom: 24px; font-size: 1.1rem; }
        .footer-col a { display: block; color: var(--text-secondary); text-decoration: none; margin-bottom: 12px; transition: 0.2s; }
        .footer-col a:hover { color: var(--brand-amber); }
        .copyright { text-align: center; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 32px; color: var(--text-secondary); font-size: 0.9rem; }

        @media(max-width: 768px) {
            .nav-links { display: none; }
            .page-header h1 { font-size: 2rem; }
            .blog-grid { grid-template-columns: 1fr; }
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
            <h1>Casjoe Blog</h1>
            <p>Insights, updates, and resources for growing your business.</p>
        </div>
    </header>

    <div class="content-area">
        <div class="container">
            <div class="blog-grid">
                <?php foreach($posts as $post): ?>
                <div class="blog-card">
                    <div class="blog-img" style="background-image: url('<?= htmlspecialchars($post['image_url'] ?: '/assets/blog_placeholder.jpg') ?>');">
                        <?php if($post['is_published']): ?><div class="blog-badge">New</div><?php endif; ?>
                    </div>
                    <div class="blog-content">
                        <div class="blog-date">
                            <ion-icon name="calendar-outline"></ion-icon> <?= date('F j, Y', strtotime($post['created_at'])) ?>
                        </div>
                        <h3><?= htmlspecialchars($post['title']) ?></h3>
                        <p><?= htmlspecialchars($post['excerpt']) ?></p>
                        <a href="/blog/<?= htmlspecialchars($post['slug']) ?>" class="read-more">Read Article <ion-icon name="arrow-forward-outline"></ion-icon></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if(empty($posts)): ?>
            <div style="text-align: center; padding: 40px; background: #fff; border-radius: 12px; border: 1px solid var(--border);">
                <h3>No posts found</h3>
                <p style="color: var(--text-secondary);">Check back later for updates.</p>
            </div>
            <?php endif; ?>
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
