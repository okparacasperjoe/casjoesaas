<?php require_once dirname(__DIR__, 4) . '/Core/Views/partials/mobile_nav.php'; ?>
<aside class="sidebar">
    <?php include __DIR__ . '/sidebar_links_css.php'; ?>
    <div class="links-brand">
        <a href="/dashboard"><img src="/assets/casjoe_logo.webp" alt="Casjoe Links" style="max-height: 40px; max-width: 100%;"></a>
    </div>
    <ul class="links-menu">
        <li class="links-item">
            <a href="/links" class="links-link <?= ($active ?? '') === 'links_dashboard' ? 'active' : '' ?>">
                <ion-icon name="grid-outline"></ion-icon> Dashboard
            </a>
        </li>
        <li class="links-item">
            <a href="/links/bio" class="links-link <?= ($active ?? '') === 'bio_pages' ? 'active' : '' ?>">
                <ion-icon name="person-outline"></ion-icon> Bio Pages
            </a>
        </li>
        <li class="links-item">
            <a href="/links/short" class="links-link <?= ($active ?? '') === 'short_urls' ? 'active' : '' ?>">
                <ion-icon name="link-outline"></ion-icon> Short URLs
            </a>
        </li>
        <li class="links-item">
            <a href="/links/qr" class="links-link <?= ($active ?? '') === 'qr_codes' ? 'active' : '' ?>">
                <ion-icon name="qr-code-outline"></ion-icon> QR Codes
            </a>
        </li>
        <li class="links-item">
            <a href="/links/funnels" class="links-link <?= ($active ?? '') === 'funnels' ? 'active' : '' ?>">
                <ion-icon name="funnel-outline"></ion-icon> Sales Funnels
            </a>
        </li>
        <li class="links-item">
            <a href="/links/social" class="links-link <?= ($active ?? '') === 'social_planner' ? 'active' : '' ?>">
                <ion-icon name="share-social-outline"></ion-icon> Social Planner
            </a>
        </li>
        <li class="links-item">
            <a href="/links/static" class="links-link <?= ($active ?? '') === 'static_sites' ? 'active' : '' ?>">
                <ion-icon name="cloud-upload-outline"></ion-icon> Static Hosting
            </a>
        </li>
    </ul>

    <div style="margin-top: auto; padding: 20px;">
        <a href="/dashboard" class="hero-btn" style="width: 100%; justify-content: center; background: rgba(255,255,255,0.1); color: #fff; display: flex; align-items: center; gap: 8px; text-decoration: none; padding: 12px; border-radius: 8px;">
            <ion-icon name="apps-outline"></ion-icon> All Apps
        </a>
    </div>
</aside>
