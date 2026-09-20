<?php
// Get current admin role for menu visibility
use App\Core\Services\PermissionService;

$currentAdminRole = \App\Core\Auth::user()['role'] ?? 'user';
$isSuperAdmin = \App\Core\Auth::isSuperAdmin();
$isModerator = ($currentAdminRole === 'moderator');
$uri = $_SERVER['REQUEST_URI'];
?>
<style>
/* ─── EXECUTIVE CASJOE SUPER ADMIN SIDEBAR SYSTEM ─── */
aside.sidebar {
    width: 270px !important;
    min-width: 270px !important;
    max-width: 270px !important;
    background: #060714 !important;
    background-image: linear-gradient(180deg, #090b1e 0%, #04050e 100%) !important;
    border-right: 1px solid rgba(255, 166, 0, 0.18) !important;
    box-shadow: 6px 0 25px rgba(0, 0, 0, 0.5) !important;
    position: fixed !important;
    top: 0 !important;
    bottom: 0 !important;
    left: 0 !important;
    height: 100vh !important;
    z-index: 9999 !important;
    padding: 0 !important;
    margin: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    box-sizing: border-box !important;
    overflow-y: auto !important;
    scrollbar-width: thin !important;
    scrollbar-color: rgba(255, 166, 0, 0.3) transparent !important;
}

aside.sidebar::-webkit-scrollbar {
    width: 4px;
}
aside.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 166, 0, 0.3);
    border-radius: 4px;
}

/* Brand Header */
aside.sidebar .brand {
    padding: 20px 22px !important;
    margin: 0 !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    background: rgba(0, 0, 0, 0.25) !important;
    position: sticky !important;
    top: 0 !important;
    z-index: 10 !important;
    backdrop-filter: blur(10px) !important;
}

aside.sidebar .brand img {
    height: 34px !important;
    width: auto !important;
    object-fit: contain !important;
}

aside.sidebar .admin-badge {
    background: linear-gradient(135deg, rgba(255, 166, 0, 0.25) 0%, rgba(255, 166, 0, 0.1) 100%) !important;
    border: 1px solid rgba(255, 166, 0, 0.4) !important;
    color: #FFA600 !important;
    font-size: 0.68rem !important;
    font-weight: 800 !important;
    padding: 4px 10px !important;
    border-radius: 20px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.8px !important;
    box-shadow: 0 0 10px rgba(255, 166, 0, 0.15) !important;
}

/* Menu List */
aside.sidebar .nav-menu {
    list-style: none !important;
    padding: 16px 14px 40px 14px !important;
    margin: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 4px !important;
}

/* Section Header Labels */
aside.sidebar .nav-section-label {
    font-size: 0.66rem !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: 1.2px !important;
    color: #64748b !important;
    padding: 16px 12px 6px 12px !important;
    margin: 0 !important;
    list-style: none !important;
}

/* Navigation Items & Links */
aside.sidebar .nav-item {
    list-style: none !important;
    margin: 0 !important;
    padding: 0 !important;
}

aside.sidebar .nav-link {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    padding: 10px 14px !important;
    border-radius: 10px !important;
    color: #94a3b8 !important;
    text-decoration: none !important;
    font-size: 0.88rem !important;
    font-weight: 500 !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    border: 1px solid transparent !important;
    background: transparent !important;
    line-height: 1.2 !important;
}

aside.sidebar .nav-link ion-icon {
    font-size: 1.25rem !important;
    min-width: 22px !important;
    color: #64748b !important;
    transition: all 0.2s ease !important;
}

/* Hover State */
aside.sidebar .nav-link:hover {
    color: #ffffff !important;
    background: rgba(255, 166, 0, 0.08) !important;
    border-color: rgba(255, 166, 0, 0.2) !important;
    transform: translateX(4px) !important;
    text-decoration: none !important;
}

aside.sidebar .nav-link:hover ion-icon {
    color: #FFA600 !important;
    transform: scale(1.15) !important;
}

/* Active State */
aside.sidebar .nav-link.active {
    color: #ffffff !important;
    background: linear-gradient(90deg, rgba(255, 166, 0, 0.25) 0%, rgba(255, 166, 0, 0.08) 100%) !important;
    border: 1px solid rgba(255, 166, 0, 0.4) !important;
    border-left: 4px solid #FFA600 !important;
    font-weight: 700 !important;
    box-shadow: 0 4px 15px rgba(255, 166, 0, 0.15) !important;
}

aside.sidebar .nav-link.active ion-icon {
    color: #FFA600 !important;
    transform: scale(1.1) !important;
}
</style>

<aside class="sidebar">
    <div class="brand">
        <a href="/<?= ADMIN_PATH ?>" style="text-decoration:none; display:flex; align-items:center;">
            <img src="/casjoe_logo.png" alt="Casjoe Admin">
        </a>
        <span class="admin-badge"><?= $isSuperAdmin ? 'ADMIN' : 'MODERATOR' ?></span>
    </div>

    <ul class="nav-menu">
        <!-- ─── OVERVIEW ─── -->
        <li class="nav-section-label">Overview</li>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>" class="nav-link <?= $uri === '/' . ADMIN_PATH || $uri === '/' . ADMIN_PATH . '/' ? 'active' : '' ?>">
                <ion-icon name="speedometer-outline"></ion-icon> <span>Dashboard</span>
            </a>
        </li>

        <!-- ─── USERS & COMPLIANCE ─── -->
        <?php if ($isSuperAdmin || PermissionService::can('manage_users') || PermissionService::can('manage_kyc')): ?>
        <li class="nav-section-label">Users &amp; Compliance</li>
        <?php endif; ?>

        <?php if ($isSuperAdmin || PermissionService::can('manage_users')): ?>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/users" class="nav-link <?= strpos($uri, '/' . ADMIN_PATH . '/users') !== false ? 'active' : '' ?>">
                <ion-icon name="people-outline"></ion-icon> <span>Users</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if ($isSuperAdmin || PermissionService::can('manage_kyc')): ?>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/kyc" class="nav-link <?= strpos($uri, '/' . ADMIN_PATH . '/kyc') !== false ? 'active' : '' ?>">
                <ion-icon name="id-card-outline"></ion-icon> <span>KYC Requests</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- ─── FINANCE ─── -->
        <?php if ($isSuperAdmin || PermissionService::can('view_cards') || PermissionService::can('view_withdrawals') || PermissionService::can('view_deposits')): ?>
        <li class="nav-section-label">Finance</li>
        <?php endif; ?>

        <?php if ($isSuperAdmin): ?>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/invoices" class="nav-link <?= strpos($uri, '/' . ADMIN_PATH . '/invoices') !== false ? 'active' : '' ?>">
                <ion-icon name="document-text-outline"></ion-icon> <span>Invoices & Modules</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if ($isSuperAdmin || PermissionService::can('view_cards')): ?>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/cards" class="nav-link <?= strpos($uri, '/' . ADMIN_PATH . '/cards') !== false && strpos($uri, 'naira-cards') === false ? 'active' : '' ?>">
                <ion-icon name="card-outline"></ion-icon> <span>Virtual Cards</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/naira-cards-inventory" class="nav-link <?= strpos($uri, '/' . ADMIN_PATH . '/naira-cards-inventory') !== false ? 'active' : '' ?>">
                <ion-icon name="card-outline"></ion-icon> <span>Naira Cards</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if ($isSuperAdmin || PermissionService::can('view_withdrawals')): ?>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/withdrawals" class="nav-link <?= strpos($uri, '/' . ADMIN_PATH . '/withdrawals') !== false ? 'active' : '' ?>">
                <ion-icon name="cash-outline"></ion-icon> <span>Withdrawals</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if ($isSuperAdmin || PermissionService::can('view_deposits')): ?>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/deposits" class="nav-link <?= strpos($uri, '/' . ADMIN_PATH . '/deposits') !== false ? 'active' : '' ?>">
                <ion-icon name="wallet-outline"></ion-icon> <span>Deposits</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- ─── MARKETING & COMMUNICATIONS ─── -->
        <?php if ($isSuperAdmin || PermissionService::can('send_broadcast') || PermissionService::can('manage_settings')): ?>
        <li class="nav-section-label">Marketing</li>
        <?php endif; ?>

        <?php if ($isSuperAdmin || PermissionService::can('send_broadcast')): ?>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/broadcast" class="nav-link <?= strpos($uri, '/' . ADMIN_PATH . '/broadcast') !== false ? 'active' : '' ?>">
                <ion-icon name="megaphone-outline"></ion-icon> <span>Broadcasts</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if ($isSuperAdmin || PermissionService::can('manage_settings')): ?>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/email-templates" class="nav-link <?= strpos($uri, '/email-templates') !== false ? 'active' : '' ?>">
                <ion-icon name="color-wand-outline"></ion-icon> <span>Email Templates</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/email-campaigns" class="nav-link <?= strpos($uri, '/email-campaigns') !== false ? 'active' : '' ?>">
                <ion-icon name="paper-plane-outline"></ion-icon> <span>Email Campaigns</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- ─── CONTENT ─── -->
        <?php if ($isSuperAdmin || PermissionService::can('manage_cms')): ?>
        <li class="nav-section-label">Content</li>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/cms" class="nav-link <?= strpos($uri, '/' . ADMIN_PATH . '/cms') !== false ? 'active' : '' ?>">
                <ion-icon name="document-text-outline"></ion-icon> <span>Pages &amp; Blog</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if ($isSuperAdmin || PermissionService::can('train_bot')): ?>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/bot-training" class="nav-link <?= strpos($uri, '/' . ADMIN_PATH . '/bot-training') !== false ? 'active' : '' ?>">
                <ion-icon name="chatbubbles-outline"></ion-icon> <span>Bot Training</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- ─── COMMERCE ─── -->
        <?php if ($isSuperAdmin || PermissionService::can('manage_coupons') || PermissionService::can('manage_shop_ads') || PermissionService::can('manage_stores')): ?>
        <li class="nav-section-label">Commerce</li>
        <?php endif; ?>

        <?php if ($isSuperAdmin || PermissionService::can('manage_shop_ads')): ?>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/shop/ads" class="nav-link <?= strpos($uri, '/' . ADMIN_PATH . '/shop/ads') !== false ? 'active' : '' ?>">
                <ion-icon name="pricetag-outline"></ion-icon> <span>Shop Ads</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/shop/sliders" class="nav-link <?= strpos($uri, '/' . ADMIN_PATH . '/shop/sliders') !== false ? 'active' : '' ?>">
                <ion-icon name="images-outline"></ion-icon> <span>Shop Sliders</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if ($isSuperAdmin || PermissionService::can('manage_stores')): ?>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/stores" class="nav-link <?= strpos($uri, '/' . ADMIN_PATH . '/stores') !== false ? 'active' : '' ?>">
                <ion-icon name="storefront-outline"></ion-icon> <span>Stores</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- ─── PLATFORM ─── -->
        <?php if ($isSuperAdmin || PermissionService::can('manage_modules') || PermissionService::can('view_soc') || PermissionService::can('manage_support')): ?>
        <li class="nav-section-label">Platform</li>
        <?php endif; ?>

        <?php if ($isSuperAdmin || PermissionService::can('manage_modules')): ?>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/modules" class="nav-link <?= strpos($uri, '/' . ADMIN_PATH . '/modules') !== false ? 'active' : '' ?>">
                <ion-icon name="cube-outline"></ion-icon> <span>Modules</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if ($isSuperAdmin || PermissionService::can('manage_coupons')): ?>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/coupons" class="nav-link <?= strpos($uri, '/' . ADMIN_PATH . '/coupons') !== false ? 'active' : '' ?>">
                <ion-icon name="ticket-outline"></ion-icon> <span>Billing Coupons</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if ($isSuperAdmin || PermissionService::can('view_soc')): ?>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/soc" class="nav-link <?= strpos($uri, '/' . ADMIN_PATH . '/soc') !== false ? 'active' : '' ?>">
                <ion-icon name="shield-checkmark-outline"></ion-icon> <span>SOC Dashboard</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if ($isSuperAdmin || PermissionService::can('manage_support')): ?>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/support" class="nav-link <?= strpos($uri, '/' . ADMIN_PATH . '/support') !== false ? 'active' : '' ?>">
                <ion-icon name="help-buoy-outline"></ion-icon> <span>Support Tickets</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- ─── SETTINGS ─── -->
        <?php if ($isSuperAdmin || PermissionService::can('manage_settings')): ?>
        <li class="nav-section-label">Settings</li>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/settings" class="nav-link <?= strpos($uri, '/' . ADMIN_PATH . '/settings') !== false && strpos($uri, 'integrations') === false ? 'active' : '' ?>">
                <ion-icon name="settings-outline"></ion-icon> <span>General Settings</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/settings/integrations" class="nav-link <?= strpos($uri, '/integrations') !== false ? 'active' : '' ?>">
                <ion-icon name="extension-puzzle-outline"></ion-icon> <span>Integrations</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/email-settings" class="nav-link <?= strpos($uri, '/email-settings') !== false ? 'active' : '' ?>">
                <ion-icon name="mail-outline"></ion-icon> <span>Email Settings</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if ($isSuperAdmin): ?>
        <li class="nav-item">
            <a href="/<?= ADMIN_PATH ?>/moderator-permissions" class="nav-link <?= strpos($uri, '/moderator-permissions') !== false ? 'active' : '' ?>">
                <ion-icon name="key-outline"></ion-icon> <span>Moderator Permissions</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- ─── QUICK LINKS ─── -->
        <li class="nav-section-label">Quick Links</li>
        <li class="nav-item">
            <a href="/dashboard" class="nav-link">
                <ion-icon name="arrow-back-outline"></ion-icon> <span>Back to App</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="/logout" class="nav-link">
                <ion-icon name="log-out-outline"></ion-icon> <span>Logout</span>
            </a>
        </li>
        
        <!-- Push Notification Toggle -->
        <li class="nav-item" id="enable-push-btn" style="display:none; cursor: pointer; margin-top: 10px;">
            <a onclick="subscribeToPush()" class="nav-link" style="background: rgba(40, 167, 69, 0.2); color: #28a745; border: 1px solid #28a745;">
                <ion-icon name="notifications-outline"></ion-icon> <span>Enable Notifications</span>
            </a>
        </li>
    </ul>
</aside>
