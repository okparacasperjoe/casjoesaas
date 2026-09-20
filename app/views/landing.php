<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <title><?= $settings['seo_title'] ?? 'Casjoe - Complete Business Ecosystem for Africa | ERP & Payments' ?></title>
    <meta name="description" content="<?= $settings['seo_description'] ?? 'Casjoe provides a unified ecosystem for African businesses to operate, sell, get paid, and scale. Integrated ERP, Payments, CRM, and more in one account.' ?>">
    <meta name="keywords" content="Casjoe, Business Ecosystem Africa, ERP Nigeria, Payments Africa, Ecommerce Platform, CRM Africa, Business Management Software">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://app.casjoe.com/">
    <meta property="og:title" content="<?= $settings['seo_title'] ?? 'Casjoe - Complete Business Ecosystem for Africa' ?>">
    <meta property="og:description" content="<?= $settings['seo_description'] ?? 'One Account. Every Tool Your Business Needs. Built for African businesses.' ?>">
    <meta property="og:image" content="https://app.casjoe.com/assets/casjoe_og_image.png">
    <meta property="og:site_name" content="Casjoe">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:site" content="@casjoetech">
    <meta property="twitter:url" content="https://app.casjoe.com/">
    <meta property="twitter:title" content="<?= $settings['seo_title'] ?? 'Casjoe - Complete Business Ecosystem' ?>">
    <meta property="twitter:description" content="One Account. Every Tool Your Business Needs. Operate, sell, get paid, grow, and scale.">
    <meta property="twitter:image" content="https://app.casjoe.com/assets/casjoe_og_image.png">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://app.casjoe.com/" />

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "Casjoe",
      "alternateName": "Casjoe Technologies",
      "url": "https://app.casjoe.com",
      "logo": "https://app.casjoe.com/assets/casjoe_logo.webp",
      "contactPoint": [
        {
          "@type": "ContactPoint",
          "telephone": "+234-705-040-9050",
          "contactType": "customer service",
          "areaServed": "NG",
          "availableLanguage": ["en"]
        }
      ],
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Lagos State",
        "addressLocality": "Lagos",
        "addressCountry": "NG"
      },
      "sameAs": [
        "https://twitter.com/casjoetech",
        "https://instagram.com/casjoetech",
        "https://linkedin.com/company/casjoetech",
        "https://facebook.com/casjoetech"
      ]
    }
    </script>

    <!-- FAQ Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "What is Casjoe?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Casjoe is a complete business ecosystem for African entrepreneurs. It provides integrated tools for payments (Casjoe Pay), e-commerce (Casjoe Mart), operations (Casjoe BOS), and marketing (Casjoe Mail) under one unified account."
          }
        },
        {
          "@type": "Question",
          "name": "How does Casjoe Pay work?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Casjoe Pay allows you to accept global payments, manage multiple currencies, and settle into your local Nigerian bank account or MoMo wallet instantly."
          }
        },
        {
          "@type": "Question",
          "name": "Is Casjoe available in Nigeria?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Yes, Casjoe is specifically designed for the African market with dedicated support and offices in Aba, Abuja, Lagos, and Port Harcourt, Nigeria."
          }
        },
        {
          "@type": "Question",
          "name": "Can I use Casjoe for free?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Yes, it is free to start your Casjoe account. You only pay for specific modules or transaction fees as your business grows."
          }
        }
      ]
    }
    </script>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "SoftwareApplication",
      "name": "Casjoe Platform",
      "applicationCategory": "BusinessApplication",
      "operatingSystem": "Web",
      "url": "https://app.casjoe.com",
      "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "NGN"
      },
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.9",
        "reviewCount": "120"
      }
    }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script>
        // Prevent flash of wrong theme
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();

        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            const icon = document.querySelector('.menu-icon ion-icon');
            navLinks.classList.toggle('active');
            icon.setAttribute('name', navLinks.classList.contains('active') ? 'close-outline' : 'menu-outline');
        }

        function toggleTheme() {
            const html = document.documentElement;
            const current = html.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
        }

        // Deter source viewing
        document.addEventListener('contextmenu', event => event.preventDefault());
        document.onkeydown = function(e) {
            if(e.keyCode == 123) { return false; }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) { return false; }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) { return false; }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) { return false; }
            if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) { return false; }
        }
    </script>
    <style>
        /* ═══════════════════════════════════════════════
           DESIGN SYSTEM — CSS Custom Properties
           ═══════════════════════════════════════════════ */
        
        /* ── DARK MODE (Default) ── */
        [data-theme='dark'] {
            --bg-primary: #030014;
            --bg-secondary: #0A0A2E;
            --bg-card: rgba(255,255,255,0.04);
            --bg-card-hover: rgba(255,255,255,0.08);
            --bg-card-solid: #0D0D35;
            --bg-nav: rgba(3,0,20,0.85);
            --bg-hero-from: #030014;
            --bg-hero-to: #0A0A2E;
            --bg-footer: #020010;

            --text-primary: #F8FAFC;
            --text-secondary: #94A3B8;
            --text-muted: #64748B;
            --text-heading: #FFFFFF;

            --border-primary: rgba(255,255,255,0.08);
            --border-hover: rgba(255,166,0,0.4);
            --border-card: rgba(255,255,255,0.06);

            --glass-bg: rgba(255,255,255,0.05);
            --glass-border: rgba(255,255,255,0.1);
            --glass-shadow: 0 8px 32px rgba(0,0,0,0.4);

            --brand-navy: #000066;
            --brand-amber: #FFA600;
            --brand-amber-glow: rgba(255,166,0,0.15);

            --badge-bg: rgba(255,166,0,0.1);
            --badge-border: rgba(255,166,0,0.3);
            --badge-text: #FFA600;

            --stat-card-bg: rgba(255,255,255,0.06);
            --solution-check: #FFA600;

            --flow-line: rgba(255,166,0,0.3);
            --flow-dot-active: #FFA600;
            --flow-dot-inactive: rgba(255,255,255,0.15);

            --segment-card-bg: rgba(255,255,255,0.04);
            --segment-card-border: rgba(255,255,255,0.08);
            --segment-icon-bg: rgba(255,166,0,0.12);

            --cta-bg: linear-gradient(135deg, #0A0A2E 0%, #000066 100%);
            --cta-text: #FFFFFF;

            --theme-icon-sun: none;
            --theme-icon-moon: block;
        }

        /* ── LIGHT MODE ── */
        [data-theme='light'] {
            --bg-primary: #FFFFFF;
            --bg-secondary: #F8FAFC;
            --bg-card: rgba(0,0,102,0.03);
            --bg-card-hover: rgba(0,0,102,0.06);
            --bg-card-solid: #FFFFFF;
            --bg-nav: rgba(255,255,255,0.9);
            --bg-hero-from: #FFFFFF;
            --bg-hero-to: #F1F5F9;
            --bg-footer: #F8FAFC;

            --text-primary: #0F172A;
            --text-secondary: #475569;
            --text-muted: #94A3B8;
            --text-heading: #000066;

            --border-primary: rgba(0,0,102,0.08);
            --border-hover: rgba(255,166,0,0.5);
            --border-card: rgba(0,0,102,0.06);

            --glass-bg: rgba(255,255,255,0.7);
            --glass-border: rgba(0,0,102,0.1);
            --glass-shadow: 0 8px 32px rgba(0,0,102,0.08);

            --brand-navy: #000066;
            --brand-amber: #FFA600;
            --brand-amber-glow: rgba(255,166,0,0.08);

            --badge-bg: rgba(0,0,102,0.06);
            --badge-border: rgba(0,0,102,0.15);
            --badge-text: #000066;

            --stat-card-bg: rgba(0,0,102,0.04);
            --solution-check: #000066;

            --flow-line: rgba(0,0,102,0.15);
            --flow-dot-active: #FFA600;
            --flow-dot-inactive: rgba(0,0,102,0.1);

            --segment-card-bg: #FFFFFF;
            --segment-card-border: rgba(0,0,102,0.08);
            --segment-icon-bg: rgba(255,166,0,0.1);

            --cta-bg: linear-gradient(135deg, #000066 0%, #000044 100%);
            --cta-text: #FFFFFF;

            --theme-icon-sun: block;
            --theme-icon-moon: none;
        }

        /* ═══════════════════════════════════════════════
           RESET & BASE
           ═══════════════════════════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Utilities */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; width: 100%; }
        .text-center { text-align: center; }
        .section { padding: 100px 0; position: relative; }
        @media(max-width: 768px) { .section { padding: 64px 0; } }

        /* ═══════════════════════════════════════════════
           SCROLL REVEAL
           ═══════════════════════════════════════════════ */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }
        .reveal-delay-5 { transition-delay: 0.5s; }
        .reveal-delay-6 { transition-delay: 0.6s; }

        /* ═══════════════════════════════════════════════
           BADGE
           ═══════════════════════════════════════════════ */
        .badge {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 6px 18px; border-radius: 50px;
            font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;
            background: var(--badge-bg);
            border: 1px solid var(--badge-border);
            color: var(--badge-text);
            margin-bottom: 20px;
        }
        .badge::before {
            content: ''; width: 6px; height: 6px;
            background: var(--brand-amber); border-radius: 50%;
            display: inline-block;
            animation: badgePulse 2s ease-in-out infinite;
        }
        @keyframes badgePulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.4); }
        }

        /* Section Header */
        .section-header { text-align: center; max-width: 720px; margin: 0 auto 64px; }
        .section-header h2 {
            font-size: 2.5rem; font-weight: 800; letter-spacing: -0.03em;
            color: var(--text-heading); margin-bottom: 16px; line-height: 1.15;
        }
        .section-header p { color: var(--text-secondary); font-size: 1.1rem; line-height: 1.7; }
        @media(max-width: 768px) {
            .section-header h2 { font-size: 1.8rem; }
            .section-header p { font-size: 1rem; }
        }

        /* ═══════════════════════════════════════════════
           BUTTONS
           ═══════════════════════════════════════════════ */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 12px 28px; border-radius: 50px; font-weight: 600; font-size: 0.95rem;
            text-decoration: none; cursor: pointer; border: none;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            white-space: nowrap;
        }
        .btn-primary {
            background: var(--brand-amber); color: #000033;
            box-shadow: 0 4px 20px rgba(255,166,0,0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(255,166,0,0.5);
            background: #FFB833;
        }
        .btn-secondary {
            background: transparent;
            color: var(--text-primary);
            border: 2px solid var(--border-primary);
        }
        .btn-secondary:hover {
            border-color: var(--brand-amber);
            color: var(--brand-amber);
            background: var(--brand-amber-glow);
        }
        .btn-ghost {
            background: var(--glass-bg); color: var(--text-primary);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(10px);
        }
        .btn-ghost:hover { border-color: var(--brand-amber); }

        /* ═══════════════════════════════════════════════
           NAVBAR
           ═══════════════════════════════════════════════ */
        nav {
            position: fixed; top: 0; width: 100%; z-index: 1000;
            background: var(--bg-nav);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-primary);
            padding: 14px 0;
            transition: all 0.3s;
        }
        nav.scrolled { box-shadow: 0 4px 30px rgba(0,0,0,0.1); }
        .nav-inner { display: flex; justify-content: space-between; align-items: center; }
        .logo {
            text-decoration: none; display: flex; align-items: center;
        }
        .logo img {
            height: 32px; width: auto; max-width: 180px; object-fit: contain;
        }
        @media(min-width: 768px) {
            .logo img { height: 40px; max-width: 240px; }
        }
        /* Show yellow logo on dark mode, navy logo on light mode */
        .logo-dark { display: block; }
        .logo-light { display: none; }
        [data-theme='light'] .logo-dark { display: none; }
        [data-theme='light'] .logo-light { display: block; }

        .nav-links {
            display: none;
            position: absolute; top: 100%; left: 0; width: 100%;
            background: var(--bg-nav);
            backdrop-filter: blur(20px);
            flex-direction: column; padding: 24px; gap: 16px;
            border-bottom: 1px solid var(--border-primary);
        }
        .nav-links.active { display: flex; }
        .nav-link {
            color: var(--text-secondary); text-decoration: none;
            font-size: 0.9rem; font-weight: 500; transition: color 0.2s;
        }
        .nav-link:hover { color: var(--brand-amber); }

        .menu-icon { display: block; font-size: 1.8rem; color: var(--text-primary); cursor: pointer; }

        .nav-right-container { display: flex; align-items: center; gap: 12px; }
        .nav-actions { display: flex; align-items: center; gap: 8px; }
        .hide-on-mobile { display: none; }
        .nav-actions .btn { padding: 6px 12px; font-size: 0.8rem; }

        .theme-toggle {
            width: 40px; height: 40px; border-radius: 12px; border: 1px solid var(--border-primary);
            background: var(--glass-bg); cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-primary); font-size: 1.2rem;
            transition: all 0.3s;
        }
        .theme-toggle:hover { border-color: var(--brand-amber); color: var(--brand-amber); }
        [data-theme='dark'] .theme-sun { display: none; }
        [data-theme='dark'] .theme-moon { display: block; }
        [data-theme='light'] .theme-sun { display: block; }
        [data-theme='light'] .theme-moon { display: none; }

        /* Mobile Nav Actions */
        .mobile-actions { display: flex; gap: 12px; width: 100%; margin-top: 8px; }
        .mobile-actions .btn { flex: 1; padding: 10px; font-size: 0.85rem; }

        @media(min-width: 768px) {
            .nav-links {
                display: flex; position: static; flex-direction: row;
                width: auto; background: none; border: none; padding: 0; gap: 32px;
                backdrop-filter: none;
            }
            .menu-icon { display: none; }
            .hide-on-mobile { display: inline-flex; }
            .nav-actions { gap: 12px; }
            .nav-actions .btn { padding: 10px 24px; font-size: 0.95rem; }
            .mobile-actions { display: none; }
        }

        /* ═══════════════════════════════════════════════
           HERO
           ═══════════════════════════════════════════════ */
        .hero {
            padding: 140px 0 80px;
            position: relative; overflow: hidden;
            background: linear-gradient(180deg, var(--bg-hero-from) 0%, var(--bg-hero-to) 100%);
        }
        /* Ambient glow circles */
        .hero::before {
            content: '';
            position: absolute; top: -200px; right: -200px;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(255,166,0,0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero::after {
            content: '';
            position: absolute; bottom: -200px; left: -200px;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(0,0,102,0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero .container { position: relative; z-index: 2; }

        .hero-content {
            display: grid; grid-template-columns: 1fr; gap: 48px;
            align-items: center;
        }
        @media(min-width: 1024px) {
            .hero-content { grid-template-columns: 1fr 1fr; gap: 64px; }
        }

        .hero-text-block h1 {
            font-size: 2.8rem; font-weight: 900; line-height: 1.1;
            letter-spacing: -0.03em; color: var(--text-heading);
            margin-bottom: 20px;
        }
        .hero-text-block h1 .text-amber { color: var(--brand-amber); }
        @media(min-width: 768px) { .hero-text-block h1 { font-size: 3.5rem; } }
        @media(min-width: 1200px) { .hero-text-block h1 { font-size: 3.8rem; } }

        .hero-desc {
            font-size: 1.1rem; color: var(--text-secondary); line-height: 1.7;
            margin-bottom: 12px; max-width: 540px;
        }
        .hero-sub {
            font-size: 1rem; font-weight: 700; color: var(--text-heading);
            margin-bottom: 24px;
        }
        .hero-bullets {
            display: flex; flex-wrap: wrap; gap: 16px; margin-bottom: 32px;
            list-style: none;
        }
        .hero-bullets li {
            display: flex; align-items: center; gap: 6px;
            font-size: 0.85rem; color: var(--text-secondary); font-weight: 500;
        }
        .hero-bullets li ion-icon { color: var(--brand-amber); font-size: 1rem; }
        .hero-cta { display: flex; gap: 16px; flex-wrap: wrap; }

        @media(max-width: 768px) {
            .hero { padding: 100px 0 48px; }
            .hero-text-block h1 { font-size: 2.2rem; }
            .hero-text-block { text-align: center; }
            .hero-desc, .hero-sub { text-align: center; margin-left: auto; margin-right: auto; }
            .hero-bullets { justify-content: center; }
            .hero-cta { justify-content: center; flex-direction: column; }
            .hero-cta .btn { width: 100%; }
        }

        /* ═══════════════════════════════════════════════
           HERO IMAGE & ORBIT (Hero Right Side)
           ═══════════════════════════════════════════════ */
        .hero-image-wrapper {
            position: relative;
            width: 100%;
            height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-yellow-orbit {
            position: absolute;
            width: 600px;
            height: 600px;
            background: var(--brand-amber);
            border-radius: 50%;
            opacity: 0.15;
            filter: blur(80px);
            animation: pulseOrbit 8s ease-in-out infinite;
            z-index: 0;
        }
        .hero-single-img-container {
            position: relative;
            width: 280px;
            height: 580px;
            border-radius: 40px;
            border: 12px solid #0f172a;
            background: #000;
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.6), inset 0 0 0 1px #334155;
            overflow: hidden;
            z-index: 1;
            animation: heroFloat 6s ease-in-out infinite;
        }
        /* Dynamic Island / Notch */
        .hero-single-img-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 110px;
            height: 25px;
            background: #0f172a;
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
            z-index: 2;
        }
        .hero-single-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top;
            display: block;
        }
        @keyframes pulseOrbit {
            0%, 100% { transform: scale(1); opacity: 0.15; }
            50% { transform: scale(1.1); opacity: 0.25; }
        }
        @keyframes heroFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* ═══════════════════════════════════════════════
           LOGO CLOUD / REPLACE TOOLS
           ═══════════════════════════════════════════════ */
        .logo-cloud { background: var(--bg-secondary); border-top: 1px solid var(--border-primary); border-bottom: 1px solid var(--border-primary); }
        .logo-cloud .section-header { margin-bottom: 40px; }
        .tool-logos {
            display: flex; flex-wrap: wrap; justify-content: center;
            gap: 32px; align-items: center; opacity: 0.5;
        }
        .tool-logo {
            font-size: 0.95rem; font-weight: 700; color: var(--text-muted);
            display: flex; align-items: center; gap: 8px;
            padding: 8px 16px; border-radius: 8px;
            border: 1px solid var(--border-primary);
            text-decoration: line-through;
            transition: 0.3s;
        }

        /* ═══════════════════════════════════════════════
           PROBLEM SECTION
           ═══════════════════════════════════════════════ */
        .problem-grid {
            display: grid; grid-template-columns: 1fr; gap: 20px; margin-top: 40px;
        }
        @media(min-width: 768px) { .problem-grid { grid-template-columns: repeat(3, 1fr); } }
        .problem-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 20px; padding: 28px;
            transition: all 0.3s;
        }
        .problem-card:hover {
            border-color: #EF4444;
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(239,68,68,0.1);
        }
        .problem-icon {
            width: 48px; height: 48px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(239,68,68,0.1); color: #EF4444; font-size: 1.4rem;
            margin-bottom: 16px;
        }
        .problem-card h4 { font-size: 1.1rem; font-weight: 700; color: var(--text-heading); margin-bottom: 8px; }
        .problem-card p { font-size: 0.9rem; color: var(--text-secondary); line-height: 1.6; }

        /* ═══════════════════════════════════════════════
           SOLUTION SECTION
           ═══════════════════════════════════════════════ */
        .solution-grid {
            display: grid; grid-template-columns: 1fr; gap: 16px;
            max-width: 700px; margin: 0 auto;
        }
        @media(min-width: 640px) { .solution-grid { grid-template-columns: 1fr 1fr; } }
        .solution-item {
            display: flex; align-items: center; gap: 14px;
            padding: 16px 20px; border-radius: 14px;
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            transition: all 0.3s;
        }
        .solution-item:hover { border-color: var(--brand-amber); transform: translateX(4px); }
        .solution-item ion-icon { font-size: 1.3rem; color: var(--solution-check); flex-shrink: 0; }
        .solution-item span { font-size: 0.95rem; font-weight: 600; color: var(--text-heading); }

        /* ═══════════════════════════════════════════════
           HOW IT WORKS (3 Steps)
           ═══════════════════════════════════════════════ */
        .steps-grid {
            display: grid; grid-template-columns: 1fr; gap: 24px;
        }
        @media(min-width: 768px) { .steps-grid { grid-template-columns: repeat(3, 1fr); } }
        .step-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 24px; padding: 32px;
            position: relative; overflow: hidden;
            transition: all 0.3s;
        }
        .step-card:hover { border-color: var(--brand-amber); transform: translateY(-6px); box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
        .step-number {
            font-size: 4rem; font-weight: 900;
            color: var(--brand-amber); opacity: 0.15;
            position: absolute; top: 16px; right: 20px;
            line-height: 1;
        }
        .step-icon {
            width: 56px; height: 56px; border-radius: 16px;
            background: var(--brand-amber-glow); border: 1px solid var(--badge-border);
            display: flex; align-items: center; justify-content: center;
            color: var(--brand-amber); font-size: 1.6rem;
            margin-bottom: 20px;
        }
        .step-card h3 { font-size: 1.2rem; font-weight: 700; color: var(--text-heading); margin-bottom: 10px; }
        .step-card p { font-size: 0.9rem; color: var(--text-secondary); line-height: 1.6; }

        /* ═══════════════════════════════════════════════
           ECOSYSTEM / MODULES (Bento Grid)
           ═══════════════════════════════════════════════ */
        .bento-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }
        @media(min-width: 640px) {
            .bento-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media(min-width: 1024px) {
            .bento-grid {
                grid-template-columns: repeat(4, 1fr);
                grid-auto-rows: auto;
            }
            .bento-grid .bento-card:nth-child(1) { grid-column: span 2; }
            .bento-grid .bento-card:nth-child(4) { grid-column: span 2; }
            .bento-grid .bento-card:nth-child(5) { grid-column: span 2; }
            .bento-grid .bento-card:nth-child(8) { grid-column: span 2; }
        }

        .bento-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 24px; padding: 28px;
            position: relative; overflow: hidden;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .bento-card:hover {
            border-color: var(--border-hover);
            transform: translateY(-6px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .bento-card::after {
            content: '';
            position: absolute; top: 0; right: 0;
            width: 120px; height: 120px;
            background: radial-gradient(circle, var(--brand-amber-glow) 0%, transparent 70%);
            pointer-events: none; opacity: 0;
            transition: opacity 0.4s;
        }
        .bento-card:hover::after { opacity: 1; }

        .bento-icon {
            width: 52px; height: 52px; border-radius: 14px;
            background: var(--brand-amber-glow);
            border: 1px solid var(--badge-border);
            display: flex; align-items: center; justify-content: center;
            color: var(--brand-amber); font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .bento-card h3 { font-size: 1.15rem; font-weight: 700; color: var(--text-heading); margin-bottom: 8px; }
        .bento-card .bento-sub { font-size: 0.95rem; font-weight: 600; color: var(--brand-amber); margin-bottom: 8px; }
        .bento-card p { font-size: 0.88rem; color: var(--text-secondary); line-height: 1.6; }

        /* ═══════════════════════════════════════════════
           FLOW SECTION (Animated Timeline)
           ═══════════════════════════════════════════════ */
        .flow-timeline {
            position: relative; max-width: 700px; margin: 0 auto;
            padding-left: 48px;
        }
        .flow-timeline::before {
            content: '';
            position: absolute; left: 20px; top: 0; bottom: 0;
            width: 2px;
            background: var(--flow-line);
        }
        .flow-step {
            position: relative; padding: 20px 0 20px 24px;
            opacity: 0.4;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .flow-step.active {
            opacity: 1;
        }
        .flow-dot {
            position: absolute; left: -36px; top: 24px;
            width: 16px; height: 16px; border-radius: 50%;
            background: var(--flow-dot-inactive);
            border: 3px solid var(--bg-primary);
            transition: all 0.5s;
            z-index: 2;
        }
        .flow-step.active .flow-dot {
            background: var(--flow-dot-active);
            box-shadow: 0 0 0 6px var(--brand-amber-glow);
        }
        .flow-step-num {
            font-size: 0.7rem; font-weight: 700; color: var(--brand-amber);
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;
        }
        .flow-step h4 { font-size: 1.05rem; font-weight: 700; color: var(--text-heading); margin-bottom: 4px; }
        .flow-step p { font-size: 0.85rem; color: var(--text-secondary); }

        /* Simulator Specific Styles */
        .flow-timeline.simulating::before {
            background: linear-gradient(to bottom, var(--brand-amber) var(--sim-progress, 0%), var(--flow-line) var(--sim-progress, 0%));
            transition: background 0.3s ease-out;
        }
        .flow-sim-toast {
            margin-top: 12px; padding: 12px 16px; 
            background: var(--bg-card); 
            border-left: 4px solid var(--brand-amber); 
            border-radius: 8px; font-size: 0.82rem; 
            color: var(--text-primary); line-height: 1.5; 
            display: none; 
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-card);
            border-left-width: 4px;
            box-shadow: var(--glass-shadow);
            animation: simSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes simSlideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .flow-timeline.simulating .flow-step {
            opacity: 0.4;
            transform: scale(1);
        }
        .flow-timeline.simulating .flow-step.sim-active {
            opacity: 1;
            transform: scale(1.02);
        }
        .flow-timeline.simulating .flow-step.sim-active .flow-dot {
            background: var(--flow-dot-active);
            box-shadow: 0 0 0 8px var(--brand-amber-glow);
        }

        /* ═══════════════════════════════════════════════
           AI SECTION
           ═══════════════════════════════════════════════ */
        .ai-grid {
            display: grid; grid-template-columns: 1fr; gap: 32px;
        }
        @media(min-width: 768px) { .ai-grid { grid-template-columns: 1fr 1fr; } }

        .ai-brief-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 24px; padding: 32px;
            position: relative; overflow: hidden;
        }
        .ai-brief-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, var(--brand-amber), var(--brand-navy));
        }
        .ai-brief-header {
            display: flex; align-items: center; gap: 12px; margin-bottom: 20px;
        }
        .ai-avatar {
            width: 40px; height: 40px; border-radius: 12px;
            background: linear-gradient(135deg, var(--brand-amber), #FF8C00);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.2rem;
        }
        .ai-brief-title { font-weight: 700; color: var(--text-heading); font-size: 1rem; }
        .ai-brief-sub { font-size: 0.75rem; color: var(--text-muted); }

        .ai-insight-item {
            padding: 14px 0;
            border-bottom: 1px solid var(--border-primary);
        }
        .ai-insight-item:last-child { border-bottom: none; }
        .ai-insight-label { font-size: 0.75rem; color: var(--brand-amber); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .ai-insight-text { font-size: 0.9rem; color: var(--text-primary); margin-top: 4px; line-height: 1.5; }

        .ai-prompts { display: flex; flex-direction: column; gap: 12px; justify-content: center; }
        .ai-prompt {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 14px; padding: 16px 20px;
            display: flex; align-items: center; gap: 12px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .ai-prompt:hover { border-color: var(--brand-amber); transform: translateX(4px); }
        .ai-prompt ion-icon { color: var(--brand-amber); font-size: 1.2rem; flex-shrink: 0; }
        .ai-prompt span { font-size: 0.88rem; color: var(--text-secondary); }
        .ai-prompt code {
            font-size: 0.82rem; color: var(--text-heading); font-weight: 600;
            background: var(--brand-amber-glow);
            padding: 2px 8px; border-radius: 6px;
            font-family: 'Inter', monospace;
        }
        .ai-pipeline-card:hover {
            border-color: var(--brand-amber) !important;
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18);
        }

        /* ═══════════════════════════════════════════════
           WHO IT'S FOR (6 cards)
           ═══════════════════════════════════════════════ */
        .who-grid {
            display: grid; grid-template-columns: 1fr; gap: 20px;
        }
        @media(min-width: 640px) { .who-grid { grid-template-columns: repeat(2, 1fr); } }
        @media(min-width: 1024px) { .who-grid { grid-template-columns: repeat(3, 1fr); } }

        .who-card {
            background: var(--segment-card-bg);
            border: 1px solid var(--segment-card-border);
            border-radius: 20px; padding: 28px;
            transition: all 0.3s;
        }
        .who-card:hover { border-color: var(--brand-amber); transform: translateY(-4px); box-shadow: 0 16px 48px rgba(0,0,0,0.1); }
        .who-icon {
            width: 52px; height: 52px; border-radius: 14px;
            background: var(--segment-icon-bg);
            display: flex; align-items: center; justify-content: center;
            color: var(--brand-amber); font-size: 1.5rem;
            margin-bottom: 18px;
        }
        .who-card h3 { font-size: 1.1rem; font-weight: 700; color: var(--text-heading); margin-bottom: 8px; }
        .who-card p { font-size: 0.88rem; color: var(--text-secondary); line-height: 1.6; }

        /* ═══════════════════════════════════════════════
           CTA SECTION
           ═══════════════════════════════════════════════ */
        .cta-section {
            background: var(--cta-bg);
            border-radius: 32px;
            padding: 80px 40px;
            margin: 0 24px;
            text-align: center;
            position: relative; overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute; top: -100px; right: -100px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(255,166,0,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        .cta-section h2 { font-size: 2.2rem; font-weight: 800; color: #fff; margin-bottom: 16px; max-width: 600px; margin-left: auto; margin-right: auto; line-height: 1.15; }
        .cta-section p { color: rgba(255,255,255,0.7); font-size: 1.05rem; margin-bottom: 32px; }
        .cta-section .btn-primary { font-size: 1.05rem; padding: 14px 36px; }
        @media(max-width: 768px) {
            .cta-section { padding: 48px 24px; margin: 0 16px; border-radius: 24px; }
            .cta-section h2 { font-size: 1.6rem; }
        }

        /* ═══════════════════════════════════════════════
           FOOTER
           ═══════════════════════════════════════════════ */
        footer {
            padding: 60px 0 32px;
            background: var(--bg-footer);
            border-top: 1px solid var(--border-primary);
            margin-top: 100px;
        }
        .footer-bottom {
            display: flex; flex-wrap: wrap; justify-content: space-between;
            align-items: center; gap: 16px;
            padding-top: 32px;
            border-top: 1px solid var(--border-primary);
        }
        .footer-copy { font-size: 0.85rem; color: var(--text-muted); }
        .footer-legal { display: flex; gap: 24px; }
        .footer-legal a { font-size: 0.85rem; color: var(--text-muted); text-decoration: none; transition: color 0.2s; }
        .footer-legal a:hover { color: var(--brand-amber); }

        /* ═══════════════════════════════════════════════
           COOKIE CONSENT
           ═══════════════════════════════════════════════ */
        .cookie-consent-banner {
            position: fixed; bottom: -100%;
            left: 0; right: 0; width: 100%;
            background: var(--bg-nav);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid var(--glass-border);
            padding: 18px 32px;
            z-index: 9999;
            box-shadow: 0 -4px 30px rgba(0,0,0,0.15);
            display: flex; flex-direction: row; justify-content: space-between; align-items: center; gap: 24px;
            transition: bottom 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .cookie-consent-banner.show { bottom: 0; }
        .cookie-content { display: flex; align-items: center; gap: 14px; flex: 1; }
        .cookie-ndpc-box { display: none; }
        .cookie-icon {
            width: 38px; height: 38px; background: var(--brand-amber-glow);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: var(--brand-amber); font-size: 1.15rem; flex-shrink: 0;
        }
        .cookie-text h4 { display: none; }
        .cookie-text p { color: var(--text-secondary); font-size: 0.82rem; line-height: 1.5; text-align: left; }
        .cookie-actions { display: flex; flex-direction: row; align-items: center; gap: 12px; flex-shrink: 0; }
        .cookie-actions .btn-accept-all {
            background: linear-gradient(135deg, #ff3366, #ff2e55);
            color: #ffffff !important;
            border: none;
            border-radius: 8px;
            padding: 10px 22px;
            font-weight: 700;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(255, 46, 85, 0.2);
        }
        .cookie-actions .btn-accept-all:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(255, 46, 85, 0.35);
        }
        .cookie-actions .btn-accept-necessary {
            background: var(--bg-card);
            color: var(--text-primary) !important;
            border: 1px solid var(--border-card);
            border-radius: 8px;
            padding: 10px 22px;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            white-space: nowrap;
        }
        .cookie-actions .btn-accept-necessary:hover {
            background: var(--bg-card-hover);
        }

        /* Mobile responsiveness */
        @media (max-width: 991px) {
            .cookie-consent-banner {
                flex-direction: column;
                align-items: stretch;
                padding: 20px;
                gap: 16px;
            }
            .cookie-content {
                align-items: flex-start;
            }
            .cookie-actions {
                flex-direction: column;
                width: 100%;
            }
            .cookie-actions .btn-accept-all,
            .cookie-actions .btn-accept-necessary {
                width: 100%;
                padding: 12px;
            }
        }
    </style>
    <!-- Microsoft Clarity Tracking Code -->
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "xmg6md3utg");
    </script>
    
    <!-- Google Analytics (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-05W6PSDS11"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-05W6PSDS11');
    </script>
</head>
<body>

    <!-- ═══════════════════════════════════════
         1. NAVBAR
         ═══════════════════════════════════════ -->
    <nav id="navbar">
        <div class="container nav-inner">
            <a href="/" class="logo">
                <img src="/assets/casjoe_logo.webp?v=<?= time() ?>" alt="Casjoe" class="logo-dark">
                <img src="/assets/casjoe_logo.webp?v=<?= time() ?>" alt="Casjoe" class="logo-light">
            </a>
            <div class="nav-links">
                <a href="#ecosystem" class="nav-link">Platform</a>
                <a href="#how-it-works" class="nav-link">How it works</a>
                <a href="#who-its-for" class="nav-link">Who it's for</a>
                <a href="#pricing" class="nav-link">Pricing</a>
                <a href="/blog" class="nav-link">Blog</a>
                <div class="mobile-actions">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="/dashboard" class="btn btn-primary" style="text-align:center;">Go to Dashboard</a>
                    <?php else: ?>
                        <a href="/login" class="btn btn-primary" style="text-align:center;">Login</a>
                        <a href="/register" class="btn btn-secondary" style="text-align:center;">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="nav-right-container">
                <div class="nav-actions">
                    <button class="theme-toggle" onclick="toggleTheme()" style="width:32px;height:32px;font-size:1rem;">
                        <ion-icon name="sunny-outline" class="theme-sun"></ion-icon>
                        <ion-icon name="moon-outline" class="theme-moon"></ion-icon>
                    </button>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="/dashboard" class="btn btn-primary">Dashboard</a>
                    <?php else: ?>
                        <a href="/register" class="btn btn-ghost hide-on-mobile">Sign Up</a>
                        <a href="/login" class="btn btn-primary">Login</a>
                    <?php endif; ?>
                </div>
                <div class="menu-icon" onclick="toggleMenu()">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>
            </div>
        </div>
    </nav>


    <!-- ═══════════════════════════════════════
         2. HERO SECTION
         ═══════════════════════════════════════ -->
    <header class="hero">
        <div class="container">
            <div class="hero-content">
                <!-- Left: Text -->
                <div class="hero-text-block">
                    <div class="badge reveal">AI-Powered Business OS</div>
                    <h1 class="reveal reveal-delay-1">
                        The <span class="text-amber">AI-Powered</span> Business Operating System for African Businesses
                    </h1>
                    <p class="hero-desc reveal reveal-delay-2">
                        Operate your entire business from one intelligent platform. Payments, customers, sales, operations, marketing, files, and your team &bull; all seamlessly connected.
                    </p>
                    <p class="hero-sub reveal reveal-delay-2">One Account. One Dashboard. One Business.</p>
                    <ul class="hero-bullets reveal reveal-delay-3">
                        <li><ion-icon name="flash" style="color: var(--brand-amber);"></ion-icon> <strong>Powered by Autonomous AI Pipelines & Enterprise ATS</strong></li>
                        <li><ion-icon name="checkmark-circle"></ion-icon> No credit card required</li>
                        <li><ion-icon name="checkmark-circle"></ion-icon> Activate only tools you need</li>
                        <li><ion-icon name="checkmark-circle"></ion-icon> Scale without switching platforms</li>
                    </ul>
                    <div class="hero-cta reveal reveal-delay-4">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="/dashboard" class="btn btn-primary">Go to Dashboard <ion-icon name="arrow-forward"></ion-icon></a>
                        <?php else: ?>
                            <a href="/register" class="btn btn-primary">Start Free <ion-icon name="arrow-forward"></ion-icon></a>
                        <?php endif; ?>
                        <a href="#ecosystem" class="btn btn-secondary">See Platform</a>
                    </div>
                </div>

                <!-- Right: Hero Image -->
                <div class="hero-image-wrapper reveal reveal-delay-3">
                    <div class="hero-yellow-orbit"></div>
                    <div class="hero-single-img-container">
                        <img src="/assets/cori_hero.png" alt="Cori AI Interface" class="hero-single-img">
                    </div>
                </div>
            </div>
        </div>
    </header>


    <!-- ═══════════════════════════════════════
         3. REPLACE TOOLS (Logo Cloud)
         ═══════════════════════════════════════ -->
    <section class="section logo-cloud" style="padding: 60px 0;">
        <div class="container">
            <div class="section-header reveal" style="margin-bottom:36px;">
                <h2 style="font-size:1.6rem;">Replace All These Tools With One Platform</h2>
                <p>One connected business ecosystem</p>
            </div>
            <div class="tool-logos reveal reveal-delay-1">
                <!-- Payments & Finance -->
                <span class="tool-logo">Paystack</span>
                <span class="tool-logo">Stripe</span>
                <span class="tool-logo">QuickBooks</span>
                <span class="tool-logo">Flutterwave</span>
                
                <!-- Marketing & Sales -->
                <span class="tool-logo">Mailchimp</span>
                <span class="tool-logo">Salesforce</span>
                <span class="tool-logo">HubSpot</span>
                
                <!-- Operations & Projects -->
                <span class="tool-logo">Jira</span>
                <span class="tool-logo">Asana</span>
                <span class="tool-logo">Trello</span>
                
                <!-- Files & Workspace -->
                <span class="tool-logo">Google Drive</span>
                <span class="tool-logo">Dropbox</span>
                <span class="tool-logo">Slack</span>
                
                <!-- Commerce & Web -->
                <span class="tool-logo">Shopify</span>
                <span class="tool-logo">WordPress</span>
                <span class="tool-logo">Linktree</span>
                
                <!-- Forms & Learning -->
                <span class="tool-logo">Typeform</span>
                <span class="tool-logo">Udemy</span>
                <span class="tool-logo">Teachable</span>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════
         4. PROBLEM SECTION
         ═══════════════════════════════════════ -->
    <section class="section">
        <div class="container">
            <div class="section-header reveal">
                <div class="badge">The Problem</div>
                <h2>Your Business Isn't Disorganized. Your Software Is.</h2>
                <p>You're switching between 5–10 disconnected apps just to run daily operations. Each new tool creates another silo, another login, another bill. The result? Zero shared intelligence.</p>
            </div>
            <div class="problem-grid">
                <div class="problem-card reveal reveal-delay-1">
                    <div class="problem-icon"><ion-icon name="apps-outline"></ion-icon></div>
                    <h4>Too Many Apps</h4>
                    <p>Payments here. CRM there. Files somewhere else. Your business lives in 10 different tabs.</p>
                </div>
                <div class="problem-card reveal reveal-delay-2">
                    <div class="problem-icon"><ion-icon name="unlink-outline"></ion-icon></div>
                    <h4>Nothing Talks to Anything</h4>
                    <p>Your sales data doesn't know your inventory exists. Your marketing doesn't know who your customers are.</p>
                </div>
                <div class="problem-card reveal reveal-delay-3">
                    <div class="problem-icon"><ion-icon name="cash-outline"></ion-icon></div>
                    <h4>Death by Subscriptions</h4>
                    <p>Each tool has its own pricing, billing cycle, and support team. You're paying more and getting less.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════
         5. ONE PLATFORM (Solution)
         ═══════════════════════════════════════ -->
    <section class="section" style="padding-top:40px;">
        <div class="container">
            <div class="section-header reveal">
                <div class="badge">The Solution</div>
                <h2>One Platform. Everything Connected.</h2>
                <p>Stop duct-taping your business together. Casjoe unifies everything under one intelligent roof.</p>
            </div>
            <div class="solution-grid">
                <div class="solution-item reveal reveal-delay-1">
                    <ion-icon name="checkmark-circle"></ion-icon>
                    <span>One wallet for all payments</span>
                </div>
                <div class="solution-item reveal reveal-delay-2">
                    <ion-icon name="checkmark-circle"></ion-icon>
                    <span>One customer database</span>
                </div>
                <div class="solution-item reveal reveal-delay-3">
                    <ion-icon name="checkmark-circle"></ion-icon>
                    <span>One analytics engine</span>
                </div>
                <div class="solution-item reveal reveal-delay-4">
                    <ion-icon name="checkmark-circle"></ion-icon>
                    <span>One login for everything</span>
                </div>
                <div class="solution-item reveal reveal-delay-5">
                    <ion-icon name="checkmark-circle"></ion-icon>
                    <span>One AI assistant across modules</span>
                </div>
                <div class="solution-item reveal reveal-delay-6">
                    <ion-icon name="checkmark-circle"></ion-icon>
                    <span>One dashboard to rule them all</span>
                </div>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════
         6. HOW IT WORKS (3 Steps)
         ═══════════════════════════════════════ -->
    <section id="how-it-works" class="section">
        <div class="container">
            <div class="section-header reveal">
                <div class="badge">How It Works</div>
                <h2>Start Small. Scale Without Starting Over.</h2>
                <p>Three steps to transform how you run your business forever.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card reveal reveal-delay-1">
                    <div class="step-number">01</div>
                    <div class="step-icon"><ion-icon name="person-add-outline"></ion-icon></div>
                    <h3>Create One Account</h3>
                    <p>Sign up once and establish your business identity. One account gives you access to the entire Casjoe ecosystem instantly.</p>
                </div>
                <div class="step-card reveal reveal-delay-2">
                    <div class="step-number">02</div>
                    <div class="step-icon"><ion-icon name="toggle-outline"></ion-icon></div>
                    <h3>Activate What You Need</h3>
                    <p>Choose only the tools you need right now. Start with payments, add a shop later. Your ecosystem scales automatically with your ambition.</p>
                </div>
                <div class="step-card reveal reveal-delay-3">
                    <div class="step-number">03</div>
                    <div class="step-icon"><ion-icon name="rocket-outline"></ion-icon></div>
                    <h3>Grow Naturally</h3>
                    <p>Every module shares data, intelligence, and your wallet. You never outgrow the platform because it continuously adapts to your needs.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════
         7. ECOSYSTEM / MODULES (Bento Grid)
         ═══════════════════════════════════════ -->
    <section id="ecosystem" class="section">
        <div class="container">
            <div class="section-header reveal">
                <div class="badge">The Ecosystem</div>
                <h2>Everything Your Business Needs. One Login.</h2>
                <p>Eight powerful modules. One unified platform. Activate what you need, when you need it.</p>
            </div>
            <div class="bento-grid">
                <!-- 1. Casjoe Pay -->
                <div class="bento-card reveal reveal-delay-1">
                    <div class="bento-icon"><ion-icon name="wallet-outline"></ion-icon></div>
                    <h3>Casjoe Pay</h3>
                    <div class="bento-sub">Get Paid Anywhere</div>
                    <p>Accept global payments, manage multi-currency wallets, and settle instantly. The financial backbone of your entire business.</p>
                </div>
                <!-- 2. Casjoe BOS -->
                <div class="bento-card reveal reveal-delay-2">
                    <div class="bento-icon"><ion-icon name="layers-outline"></ion-icon></div>
                    <h3>Casjoe BOS</h3>
                    <div class="bento-sub">Run Your Daily Operations</div>
                    <p>Inventory, HR, projects, invoicing, and CRM &bull; your complete back-office in one centralized place.</p>
                </div>
                <!-- 3. Casjoe Mail -->
                <div class="bento-card reveal reveal-delay-3">
                    <div class="bento-icon"><ion-icon name="mail-outline"></ion-icon></div>
                    <h3>Casjoe Mail</h3>
                    <div class="bento-sub">Grow Your Customer Base</div>
                    <p>Professional email campaigns, automated sequences, and transactional messaging that drives revenue.</p>
                </div>
                <!-- 4. Casjoe Cloud -->
                <div class="bento-card reveal reveal-delay-4">
                    <div class="bento-icon"><ion-icon name="cloud-outline"></ion-icon></div>
                    <h3>Casjoe Cloud</h3>
                    <div class="bento-sub">Keep Every Business File Safe</div>
                    <p>Secure cloud storage for documents, media, and assets. Shared across your team with permission controls.</p>
                </div>
                <!-- 5. Casjoe Links -->
                <div class="bento-card reveal reveal-delay-1">
                    <div class="bento-icon"><ion-icon name="link-outline"></ion-icon></div>
                    <h3>Casjoe Links</h3>
                    <div class="bento-sub">Your Brand In One Smart Link</div>
                    <p>Link-in-bio pages, sales funnels, and landing pages to help you build your online presence in minutes.</p>
                </div>
                <!-- 6. Casjoe Academy -->
                <div class="bento-card reveal reveal-delay-2">
                    <div class="bento-icon"><ion-icon name="school-outline"></ion-icon></div>
                    <h3>Casjoe Academy</h3>
                    <div class="bento-sub">Train Your Team</div>
                    <p>Practical business courses and team training. Upskill your staff or monetize your knowledge.</p>
                </div>
                <!-- 7. Casjoe Forms -->
                <div class="bento-card reveal reveal-delay-3">
                    <div class="bento-icon"><ion-icon name="clipboard-outline"></ion-icon></div>
                    <h3>Casjoe Forms</h3>
                    <div class="bento-sub">Smart Forms That Do The Work</div>
                    <p>Collect data, registrations, and payments with intelligent forms that auto-connect to your CRM.</p>
                </div>
                <!-- 8. Casjoe Mart -->
                <div class="bento-card reveal reveal-delay-4">
                    <div class="bento-icon"><ion-icon name="storefront-outline"></ion-icon></div>
                    <h3>Casjoe Mart</h3>
                    <div class="bento-sub">Sell Online Like Jumia, Selar, Jiji & Temu</div>
                    <p>Your own marketplace for physical goods, digital products, and freelance services, complete with built-in payments.</p>
                </div>
            </div>
            <div class="text-center reveal" style="margin-top: 40px;">
                <p style="color: var(--text-muted); font-size: 0.9rem; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <ion-icon name="lock-closed" style="color:var(--brand-amber);"></ion-icon>
                    All modules share a unified data layer and wallet
                </p>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════
         8. FLOW SECTION (Animated Timeline)
         ═══════════════════════════════════════ -->
    <section class="section" id="flow-section">
        <div class="container">
            <div class="section-header reveal">
                <div class="badge">Connected Workflow</div>
                <h2>See How Everything Works Together</h2>
                <p>One customer order triggers an automated chain across every module. Here's the magic.</p>
                <div style="margin-top: 24px; text-align: center;">
                    <button class="btn btn-primary" id="btn-simulate-flow" style="background: linear-gradient(135deg, var(--brand-amber), #ff6b6b); color: #ffffff; border: none; padding: 12px 28px; border-radius: 30px; font-weight: 700; font-size: 0.95rem; cursor: pointer; box-shadow: 0 0 20px rgba(255, 166, 0, 0.4); transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px;">
                        <ion-icon name="flash-outline" style="font-size: 1.2rem;"></ion-icon>
                        ⚡ Simulate Live Order Sequence
                    </button>
                </div>
            </div>
            <div class="flow-timeline" id="simulation-timeline">
                <div class="flow-step" data-flow>
                    <div class="flow-dot"></div>
                    <div class="flow-step-num">Step 01</div>
                    <h4>Customer Places Order</h4>
                    <p>A customer discovers your product on Casjoe Mart and places an order.</p>
                    <div class="flow-sim-toast"></div>
                </div>
                <div class="flow-step" data-flow>
                    <div class="flow-dot"></div>
                    <div class="flow-step-num">Step 02</div>
                    <h4>Payment is Processed</h4>
                    <p>Casjoe Pay instantly processes and settles the payment into your wallet.</p>
                    <div class="flow-sim-toast"></div>
                </div>
                <div class="flow-step" data-flow>
                    <div class="flow-dot"></div>
                    <div class="flow-step-num">Step 03</div>
                    <h4>Invoice is Generated</h4>
                    <p>Casjoe BOS automatically creates and sends a branded invoice.</p>
                    <div class="flow-sim-toast"></div>
                </div>
                <div class="flow-step" data-flow>
                    <div class="flow-dot"></div>
                    <div class="flow-step-num">Step 04</div>
                    <h4>Inventory Updates</h4>
                    <p>Stock levels adjust in real time. Low-stock alerts trigger if needed.</p>
                    <div class="flow-sim-toast"></div>
                </div>
                <div class="flow-step" data-flow>
                    <div class="flow-dot"></div>
                    <div class="flow-step-num">Step 05</div>
                    <h4>CRM Profile Updates</h4>
                    <p>The customer's profile is enriched with purchase history and preferences.</p>
                    <div class="flow-sim-toast"></div>
                </div>
                <div class="flow-step" data-flow>
                    <div class="flow-dot"></div>
                    <div class="flow-step-num">Step 06</div>
                    <h4>Follow-up Email Sent</h4>
                    <p>Casjoe Mail sends an automated thank-you email with recommendations.</p>
                    <div class="flow-sim-toast"></div>
                </div>
                <div class="flow-step" data-flow>
                    <div class="flow-dot"></div>
                    <div class="flow-step-num">Step 07</div>
                    <h4>AI Analyzes the Sale</h4>
                    <p>Your AI assistant logs the trend and surfaces insights about the customer segment.</p>
                    <div class="flow-sim-toast"></div>
                </div>
                <div class="flow-step" data-flow>
                    <div class="flow-dot"></div>
                    <div class="flow-step-num">Step 08</div>
                    <h4>Dashboard Reflects Everything</h4>
                    <p>Revenue, customers, inventory, and AI alerts, all updated in real time on a single screen.</p>
                    <div class="flow-sim-toast"></div>
                </div>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════
         9. AI SECTION
         ═══════════════════════════════════════ -->
    <section class="section">
        <div class="container">
            <div class="section-header reveal">
                <div class="badge">Intelligence Core</div>
                <h2>Meet CORI AI, Your Business Manager</h2>
                <p>CORI AI is an always-on assistant that reads every signal across your entire business and tells you exactly what to do next.</p>
            </div>
            <div class="ai-grid">
                <!-- AI Weekly Brief -->
                <div class="ai-brief-card reveal reveal-delay-1">
                    <div class="ai-brief-header">
                        <div class="ai-avatar"><ion-icon name="sparkles"></ion-icon></div>
                        <div>
                            <div class="ai-brief-title">CORI AI Weekly Brief</div>
                            <div class="ai-brief-sub">Monday, 9:00 AM • Auto-generated</div>
                        </div>
                    </div>
                    <div class="ai-insight-item">
                        <div class="ai-insight-label">📈 Revenue</div>
                        <div class="ai-insight-text">Revenue up 12.4% this week. Top performer: Product SKU-0847. Consider increasing ad spend on this category.</div>
                    </div>
                    <div class="ai-insight-item">
                        <div class="ai-insight-label">⚠️ Inventory</div>
                        <div class="ai-insight-text">3 items below reorder threshold. Auto-purchase order drafted for your approval.</div>
                    </div>
                    <div class="ai-insight-item">
                        <div class="ai-insight-label">👥 Customers</div>
                        <div class="ai-insight-text">47 new customers acquired. Retention rate: 89%. Your email sequence is performing 2x above industry average.</div>
                    </div>
                </div>
                <!-- Agentic AI Prompts -->
                <div class="ai-prompts reveal reveal-delay-2">
                    <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:8px; font-weight:600;">Just tell CORI AI what to do:</p>
                    <div class="ai-prompt">
                        <ion-icon name="chatbubble-outline"></ion-icon>
                        <span><code>"Send invoice #4021 to customer Adaobi"</code></span>
                    </div>
                    <div class="ai-prompt">
                        <ion-icon name="chatbubble-outline"></ion-icon>
                        <span><code>"Remind sales team about Q3 targets"</code></span>
                    </div>
                    <div class="ai-prompt">
                        <ion-icon name="chatbubble-outline"></ion-icon>
                        <span><code>"Schedule a weekly audit every Monday"</code></span>
                    </div>
                    <div class="ai-prompt">
                        <ion-icon name="chatbubble-outline"></ion-icon>
                        <span><code>"Show me my top customers this month"</code></span>
                    </div>
                    <div class="ai-prompt">
                        <ion-icon name="chatbubble-outline"></ion-icon>
                        <span><code>"Draft a marketing email for new arrivals"</code></span>
                    </div>
                </div>
            </div>

            <!-- Autonomous AI Action Pipeline Hub Showcase -->
            <div class="ai-pipeline-hub reveal reveal-delay-3" style="margin-top: 36px; background: var(--bg-card); border: 1px solid var(--border-card); border-radius: 24px; padding: 32px; position: relative; overflow: hidden;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(0, 0, 102, 0.25); border: 1px solid rgba(255, 166, 0, 0.35); display: flex; align-items: center; justify-content: center; color: var(--brand-amber); font-size: 1.5rem; flex-shrink: 0;">
                            <ion-icon name="flash"></ion-icon>
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                <span style="font-weight: 800; color: var(--text-heading); font-size: 1.18rem;">Autonomous AI Action Pipelines</span>
                                <span style="font-size: 0.74rem; font-weight: 800; background: var(--badge-bg); color: var(--brand-amber); border: 1px solid var(--badge-border); padding: 3px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;">⚡ Executive Engine</span>
                            </div>
                            <div style="font-size: 0.88rem; color: var(--text-secondary); margin-top: 4px;">CORI AI doesn't just answer questions — it executes multi-step strategic workflows from start to finish.</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; font-weight: 700; color: #10b981; background: rgba(16, 185, 129, 0.12); padding: 7px 16px; border-radius: 20px; border: 1px solid rgba(16, 185, 129, 0.28);">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; display: inline-block; box-shadow: 0 0 8px #10b981;"></span>
                        Live Autonomous Processing
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px;">
                    <div class="ai-pipeline-card" style="background: var(--segment-card-bg); border: 1px solid var(--segment-card-border); border-radius: 18px; padding: 22px; transition: all 0.3s ease; cursor: default;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                            <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(255, 166, 0, 0.14); display: flex; align-items: center; justify-content: center; color: var(--brand-amber); font-size: 1.3rem;">
                                <ion-icon name="search-outline"></ion-icon>
                            </div>
                            <span style="font-size: 0.72rem; font-weight: 700; color: #3b82f6; background: rgba(59, 130, 246, 0.14); padding: 4px 10px; border-radius: 8px;">1-Click Trigger</span>
                        </div>
                        <div style="font-weight: 800; color: var(--text-heading); font-size: 0.98rem; margin-bottom: 6px;">Deep Market & Competitor Research</div>
                        <p style="font-size: 0.84rem; color: var(--text-secondary); margin: 0; line-height: 1.45;">Synthesizes real-time industry trends and competitor pricing data into actionable executive briefs.</p>
                    </div>

                    <div class="ai-pipeline-card" style="background: var(--segment-card-bg); border: 1px solid var(--segment-card-border); border-radius: 18px; padding: 22px; transition: all 0.3s ease; cursor: default;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                            <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(255, 166, 0, 0.14); display: flex; align-items: center; justify-content: center; color: var(--brand-amber); font-size: 1.3rem;">
                                <ion-icon name="document-text-outline"></ion-icon>
                            </div>
                            <span style="font-size: 0.72rem; font-weight: 700; color: #3b82f6; background: rgba(59, 130, 246, 0.14); padding: 4px 10px; border-radius: 8px;">1-Click Trigger</span>
                        </div>
                        <div style="font-weight: 800; color: var(--text-heading); font-size: 0.98rem; margin-bottom: 6px;">Executive Sales Proposal Generator</div>
                        <p style="font-size: 0.84rem; color: var(--text-secondary); margin: 0; line-height: 1.45;">Auto-generates customized client pitch decks, scope definitions, and commercial pricing quotes instantly.</p>
                    </div>

                    <div class="ai-pipeline-card" style="background: var(--segment-card-bg); border: 1px solid var(--segment-card-border); border-radius: 18px; padding: 22px; transition: all 0.3s ease; cursor: default;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                            <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(255, 166, 0, 0.14); display: flex; align-items: center; justify-content: center; color: var(--brand-amber); font-size: 1.3rem;">
                                <ion-icon name="mail-unread-outline"></ion-icon>
                            </div>
                            <span style="font-size: 0.72rem; font-weight: 700; color: #3b82f6; background: rgba(59, 130, 246, 0.14); padding: 4px 10px; border-radius: 8px;">1-Click Trigger</span>
                        </div>
                        <div style="font-weight: 800; color: var(--text-heading); font-size: 0.98rem; margin-bottom: 6px;">Automated Outreach Sequences</div>
                        <p style="font-size: 0.84rem; color: var(--text-secondary); margin: 0; line-height: 1.45;">Deploys personalized multi-channel follow-ups and high-converting customer engagement workflows.</p>
                    </div>

                    <div class="ai-pipeline-card" style="background: var(--segment-card-bg); border: 1px solid var(--segment-card-border); border-radius: 18px; padding: 22px; transition: all 0.3s ease; cursor: default;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                            <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(255, 166, 0, 0.14); display: flex; align-items: center; justify-content: center; color: var(--brand-amber); font-size: 1.3rem;">
                                <ion-icon name="trending-up-outline"></ion-icon>
                            </div>
                            <span style="font-size: 0.72rem; font-weight: 700; color: #3b82f6; background: rgba(59, 130, 246, 0.14); padding: 4px 10px; border-radius: 8px;">1-Click Trigger</span>
                        </div>
                        <div style="font-weight: 800; color: var(--text-heading); font-size: 0.98rem; margin-bottom: 6px;">Strategic Growth & Resource Planning</div>
                        <p style="font-size: 0.84rem; color: var(--text-secondary); margin: 0; line-height: 1.45;">Evaluates cash flow, inventory thresholds, and talent velocity to recommend optimal growth targets.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════
         10. WHO IT'S FOR (6 cards)
         ═══════════════════════════════════════ -->
    <section id="who-its-for" class="section">
        <div class="container">
            <div class="section-header reveal">
                <div class="badge">Built For You</div>
                <h2>Who Casjoe Is For</h2>
                <p>Whether you're just starting or scaling to thousands of customers, Casjoe grows with you.</p>
            </div>
            <div class="who-grid">
                <div class="who-card reveal reveal-delay-1">
                    <div class="who-icon"><ion-icon name="rocket-outline"></ion-icon></div>
                    <h3>Starting a Business</h3>
                    <p>Launch with payments and a shop. Add modules as you find your footing. Zero overhead, zero complexity.</p>
                </div>
                <div class="who-card reveal reveal-delay-2">
                    <div class="who-icon"><ion-icon name="trending-up-outline"></ion-icon></div>
                    <h3>Growing Businesses</h3>
                    <p>You've outgrown spreadsheets. Activate CRM, operations, and marketing to keep all your data synchronized.</p>
                </div>
                <div class="who-card reveal reveal-delay-3">
                    <div class="who-icon"><ion-icon name="briefcase-outline"></ion-icon></div>
                    <h3>Agencies & Service Firms</h3>
                    <p>Manage clients, projects, invoicing, and team performance from a single dashboard.</p>
                </div>
                <div class="who-card reveal reveal-delay-1">
                    <div class="who-icon"><ion-icon name="storefront-outline"></ion-icon></div>
                    <h3>Retail & Commerce</h3>
                    <p>Sell online and offline. Track inventory, process payments, and run promotions from a unified system.</p>
                </div>
                <div class="who-card reveal reveal-delay-2">
                    <div class="who-icon"><ion-icon name="business-outline"></ion-icon></div>
                    <h3>Enterprises</h3>
                    <p>Full ERP with HR, finance, inventory, and compliance. Enterprise-grade controls for serious operations.</p>
                </div>
                <div class="who-card reveal reveal-delay-3">
                    <div class="who-icon"><ion-icon name="people-outline"></ion-icon></div>
                    <h3>Teams of Any Size</h3>
                    <p>From solo founders to 500-person companies. Role-based access, collaboration tools, and shared intelligence.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════
         BLOG FEED (Preserved)
         ═══════════════════════════════════════ -->
    <section class="section" id="blog-feed">
        <div class="container">
            <div class="section-header reveal">
                <div class="badge">Recent Insights</div>
                <h2>Latest from our Blog</h2>
                <p>Stay updated with the latest business trends, ecosystem updates, and growth strategies.</p>
            </div>
            <div class="who-grid">
                <?php if (empty($latestPosts)): ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: var(--bg-card); border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                        <ion-icon name="newspaper-outline" style="font-size: 3rem; color: var(--brand-amber); margin-bottom: 15px;"></ion-icon>
                        <h3 style="color: var(--text-heading); font-size: 1.5rem; margin-bottom: 10px;">Our Blog is Coming Soon</h3>
                        <p style="color: var(--text-secondary);">We are working on some amazing content. Check back later!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($latestPosts as $post): ?>
                        <article class="who-card reveal" style="padding:0; overflow:hidden;">
                            <?php if (!empty($post['image_url'])): ?>
                                <div style="width:100%;height:200px;overflow:hidden;">
                                    <img src="<?= htmlspecialchars($post['image_url']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
                                </div>
                            <?php else: ?>
                                <div style="width:100%;height:200px;background:var(--bg-card);display:flex;align-items:center;justify-content:center;color:var(--brand-amber);">
                                    <ion-icon name="image-outline" style="font-size:3rem;"></ion-icon>
                                </div>
                            <?php endif; ?>
                            <div style="padding:24px;">
                                <h3 style="font-size:1.1rem;font-weight:700;color:var(--text-heading);margin-bottom:8px;"><?= htmlspecialchars($post['title']) ?></h3>
                                <p style="font-size:0.88rem;color:var(--text-secondary);margin-bottom:16px;line-height:1.6;"><?= htmlspecialchars($post['excerpt'] ?? substr(strip_tags($post['content']), 0, 150) . '...') ?></p>
                                <a href="/blog/<?= htmlspecialchars($post['slug']) ?>" style="color:var(--brand-amber);text-decoration:none;font-weight:700;font-size:0.9rem;display:inline-flex;align-items:center;gap:6px;">
                                    Read More <ion-icon name="arrow-forward-outline"></ion-icon>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="text-center" style="margin-top:40px;">
                <a href="/blog" class="btn btn-secondary">View All Posts</a>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════
         11. CTA SECTION
         ═══════════════════════════════════════ -->
    <section class="section" id="pricing" style="padding-bottom:0;">
        <div class="cta-section reveal">
            <h2>Run your entire business from one intelligent operating system</h2>
            <p>One Account. One Dashboard. Everything Connected.</p>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="/dashboard" class="btn btn-primary">Go to Dashboard <ion-icon name="arrow-forward"></ion-icon></a>
            <?php else: ?>
                <a href="/register" class="btn btn-primary">Start Free <ion-icon name="arrow-forward"></ion-icon></a>
            <?php endif; ?>
            <p style="margin-top:20px;font-size:0.85rem;color:rgba(255,255,255,0.5);">Free forever to start • No credit card required</p>
        </div>
    </section>


    <!-- ═══════════════════════════════════════
         12. FOOTER
         ═══════════════════════════════════════ -->
    <footer>
        <div class="container">
            <div class="footer-bottom">
                <span class="footer-copy">&copy; <?= date('Y') ?> Casjoe. Built for African businesses.</span>
                <div class="footer-legal">
                    <a href="/privacy-policy">Privacy</a>
                    <a href="/terms-of-service">Terms</a>
                    <a href="/contact">Contact</a>
                </div>
            </div>
        </div>
    </footer>


    <!-- Casjoe Chat Widget -->
    <script>
        window.casjoeTenantId = <?= \App\Core\TenantContext::getTenantId() ?? 13 ?>;
        <?php if(isset($_SESSION['user_id'])): ?>
        window.casjoeUser = {
            id: <?= $_SESSION['user_id'] ?>,
            tenant_id: window.casjoeTenantId
        };
        <?php endif; ?>
    </script>
    <script src="/js/chat-widget.js?v=cori_v2"></script>


    <!-- Cookie Consent Banner -->
    <div id="cookie-consent" class="cookie-consent-banner">
        <div class="cookie-content">
            <div class="cookie-icon">
                <ion-icon name="shield-checkmark-outline"></ion-icon>
            </div>
            <div class="cookie-text">
                <p>We use Cookies and similar tools (collectively referred to as "Cookies") to provide you with a smooth experience on the Casjoe platform and continuously optimize our services. Some of these strictly necessary Cookies are only used to ensure the proper functioning of the website and will not be used for advertising or user tracking. For more details, please refer to our <a href="/privacy-policy" style="color: var(--brand-amber); text-decoration: underline;">Cookie Policy</a> and <a href="/privacy-policy" style="color: var(--brand-amber); text-decoration: underline;">Privacy Policy</a>.</p>
            </div>
        </div>
        <div class="cookie-actions">
            <button onclick="handleCookieConsent('accepted_necessary')" class="btn-accept-necessary">Accept Only Strictly Necessary Cookies</button>
            <button onclick="handleCookieConsent('accepted_all')" class="btn-accept-all">Accept All</button>
        </div>
    </div>


    <!-- ═══════════════════════════════════════
         SCRIPTS
         ═══════════════════════════════════════ -->
    <script>
        // Cookie Consent
        function handleCookieConsent(status) {
            localStorage.setItem('cookie_consent', status);
            document.getElementById('cookie-consent').classList.remove('show');
            setTimeout(() => { document.getElementById('cookie-consent').style.display = 'none'; }, 600);
        }
        window.addEventListener('load', () => {
            const consent = localStorage.getItem('cookie_consent');
            if (!consent) {
                setTimeout(() => { document.getElementById('cookie-consent').classList.add('show'); }, 2500);
            } else {
                document.getElementById('cookie-consent').style.display = 'none';
            }
        });

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 60);
        });

        // Scroll Reveal (IntersectionObserver)
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

        // Flow Timeline (IntersectionObserver)
        const flowObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, { threshold: 0.3, rootMargin: '0px 0px -60px 0px' });

        document.querySelectorAll('[data-flow]').forEach(el => flowObserver.observe(el));

        // Smooth scroll for nav links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    // Close mobile menu
                    const navLinks = document.querySelector('.nav-links');
                    if (navLinks.classList.contains('active')) {
                        navLinks.classList.remove('active');
                        document.querySelector('.menu-icon ion-icon').setAttribute('name', 'menu-outline');
                    }
                }
            });
        });

        // Animate chart bars on load
        window.addEventListener('load', () => {
            const bars = document.querySelectorAll('.chart-bar');
            bars.forEach((bar, i) => {
                const h = bar.style.height;
                bar.style.height = '0%';
                setTimeout(() => { bar.style.height = h; }, 300 + i * 80);
            });
        });

        // Flow Simulator JS
        const btnSimulate = document.getElementById('btn-simulate-flow');
        const timeline = document.getElementById('simulation-timeline');
        const steps = timeline ? timeline.querySelectorAll('.flow-step') : [];

        const simMessages = [
            "🛒 <strong>Casjoe Mart:</strong> Customer <strong>Sarah K.</strong> purchased 'Ultra Wireless Earbuds' for ₦15,000 NGN.",
            "💳 <strong>Casjoe Pay:</strong> Settle payment! Credited ₦14,550 to Vendor's wallet (3% platform commission ₦450 deducted).",
            "📄 <strong>Casjoe BOS:</strong> Paid Invoice #INV-8891 generated automatically and marked as fully settled.",
            "📦 <strong>Inventory:</strong> SKU 'EAR-WRLS-01' stock decremented (12 → 11). System health status: Green.",
            "👥 <strong>CRM Sync:</strong> Sarah K. added as a high-value Customer with purchase history and tags.",
            "✉️ <strong>Casjoe Mail:</strong> Welcome campaign triggered! Receipt and personalized product suggestions dispatched.",
            "🤖 <strong>Cori AI:</strong> Trend analyzed: Segment 'Consumer Electronics' purchase volume increased by 8.4% today.",
            "📊 <strong>Dashboard:</strong> Executive ecosystem counters updated. Revenue, invoices, inventory, and AI alerts synced in real-time."
        ];

        let isSimulating = false;

        if (btnSimulate && timeline) {
            btnSimulate.addEventListener('click', () => {
                if (isSimulating) return;
                isSimulating = true;

                btnSimulate.disabled = true;
                btnSimulate.innerHTML = '<ion-icon name="sync-outline" class="spin" style="font-size: 1.2rem; display: inline-block; vertical-align: middle;"></ion-icon> Simulating workflow...';
                
                // Add spin class animation style inline if not present
                if (!document.getElementById('sim-spin-style')) {
                    const style = document.createElement('style');
                    style.id = 'sim-spin-style';
                    style.textContent = `
                        .spin { animation: simSpin 1s linear infinite; }
                        @keyframes simSpin { 100% { transform: rotate(360deg); } }
                    `;
                    document.head.appendChild(style);
                }

                // Reset timeline state
                timeline.classList.add('simulating');
                timeline.style.setProperty('--sim-progress', '0%');
                steps.forEach(step => {
                    step.classList.remove('sim-active');
                    const toast = step.querySelector('.flow-sim-toast');
                    if (toast) {
                        toast.style.display = 'none';
                        toast.innerHTML = '';
                    }
                });

                // Run steps sequentially
                let currentStepIndex = 0;

                function runNextStep() {
                    if (currentStepIndex >= steps.length) {
                        // End of simulation
                        isSimulating = false;
                        btnSimulate.disabled = false;
                        btnSimulate.innerHTML = '<ion-icon name="checkmark-circle-outline" style="font-size: 1.2rem; display: inline-block; vertical-align: middle;"></ion-icon> Simulation Finished! Run Again';
                        return;
                    }

                    const step = steps[currentStepIndex];
                    step.classList.add('sim-active');
                    
                    // Scroll to step nicely if needed
                    step.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                    // Show toast
                    const toast = step.querySelector('.flow-sim-toast');
                    if (toast) {
                        toast.innerHTML = simMessages[currentStepIndex];
                        toast.style.display = 'block';
                    }

                    // Update timeline line progress
                    const progress = ((currentStepIndex) / (steps.length - 1)) * 100;
                    timeline.style.setProperty('--sim-progress', `${progress}%`);

                    currentStepIndex++;
                    setTimeout(runNextStep, 2000); // 2 seconds per step
                }

                runNextStep();
            });
        }
    </script>

    <?php include __DIR__ . '/partials/google_prompt.php'; ?>
    
    <!-- AI Employee / Lead Capture Chat Widget -->
    <script>
        // Set Casjoe's tenant ID so leads go to Casjoe CRM
        window.casjoeTenantId = 13; 
    </script>
    <script src="/js/chat-widget.js"></script>
</body>
</html>
