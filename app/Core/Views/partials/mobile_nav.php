<div class="mobile-header">
    <div class="mobile-toggle" onclick="toggleSidebar()">
        <ion-icon name="menu-outline"></ion-icon>
    </div>
    <div class="mobile-brand">
        <a href="/dashboard">
            <img src="/assets/casjoe_logo.webp" alt="Casjoe" style="height: 40px; width: auto;">
        </a>
    </div>
    <div class="mobile-user" style="display: flex; align-items: center; gap: 12px;">
        <a href="/logout" title="Logout" style="display: flex; align-items: center;">
            <ion-icon name="log-out-outline" style="font-size: 1.6rem; color: #fca5a5;"></ion-icon>
        </a>
        <a href="/profile">
            <?php 
            $currentUser = \App\Core\Auth::user();
            if ($currentUser && !empty($currentUser['avatar'])): 
            ?>
                <img src="/uploads/avatars/<?= htmlspecialchars($currentUser['avatar']) ?>" alt="User Avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover; border: 1.5px solid #FFA600;">
            <?php else: ?>
                <ion-icon name="person-circle-outline" style="font-size: 1.8rem; color: white;"></ion-icon>
            <?php endif; ?>
        </a>
    </div>
</div>

<!-- Sidebar Overlay (click to close) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        if (sidebar) {
            sidebar.classList.toggle('active');
            
            if (overlay) {
                overlay.classList.toggle('active');
            }
            
            // Prevent body scroll when sidebar is active
            if (sidebar.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Automatically close sidebar when a link is clicked on mobile
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    toggleSidebar();
                }
            });
        });
    });
</script>

<?php
$currentUri = $_SERVER['REQUEST_URI'] ?? '/';
$isOverview = ($currentUri === '/dashboard' || $currentUri === '/');
$isProfile  = (strpos($currentUri, '/profile') !== false);
$isBilling  = (strpos($currentUri, '/billing') !== false || strpos($currentUri, '/pay') !== false);
$isAlerts   = (strpos($currentUri, '/notifications') !== false);
$isSupport  = (strpos($currentUri, '/support') !== false);
?>

<!-- Mobile Bottom Navigation (Glassmorphic & Safe-Area Aware) -->
<nav class="mobile-bottom-nav">
    <a href="/dashboard" class="nav-item <?= $isOverview ? 'active' : '' ?>">
        <ion-icon name="<?= $isOverview ? 'speedometer' : 'speedometer-outline' ?>"></ion-icon>
        <span>Overview</span>
    </a>
    <a href="/pay" class="nav-item <?= $isBilling ? 'active' : '' ?>">
        <ion-icon name="<?= $isBilling ? 'wallet' : 'wallet-outline' ?>"></ion-icon>
        <span>Wallet</span>
    </a>
    <a href="/profile" class="nav-item <?= $isProfile ? 'active' : '' ?>">
        <ion-icon name="<?= $isProfile ? 'person-circle' : 'person-circle-outline' ?>"></ion-icon>
        <span>Profile</span>
    </a>
    <a href="/notifications" class="nav-item <?= $isAlerts ? 'active' : '' ?>">
        <ion-icon name="<?= $isAlerts ? 'notifications' : 'notifications-outline' ?>"></ion-icon>
        <span>Alerts</span>
    </a>
    <a href="/support" class="nav-item <?= $isSupport ? 'active' : '' ?>">
        <ion-icon name="<?= $isSupport ? 'headset' : 'headset-outline' ?>"></ion-icon>
        <span>Support</span>
    </a>
</nav>

<style>
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.65);
        z-index: 999;
        display: none;
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        transition: opacity 0.25s ease;
    }
    .sidebar-overlay.active {
        display: block;
    }
    
    .mobile-header {
        display: none;
    }
    .mobile-bottom-nav {
        display: none;
    }
    @media (max-width: 991px) {
        .mobile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: calc(10px + env(safe-area-inset-top, 0px));
            padding-bottom: 10px;
            padding-left: 18px;
            padding-right: 18px;
            background: #000066;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            min-height: calc(56px + env(safe-area-inset-top, 0px));
            box-sizing: border-box;
            z-index: 1000;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.25);
        }
        .mobile-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
            min-height: 44px;
            cursor: pointer;
            font-size: 1.75rem;
            color: #ffffff;
            border-radius: 8px;
            transition: background 0.2s;
        }
        .mobile-toggle:active {
            background: rgba(255, 255, 255, 0.12);
        }
        .mobile-bottom-nav {
            display: flex;
            justify-content: space-around;
            align-items: center;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(3, 4, 25, 0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.45);
            z-index: 9999;
            padding-top: 8px;
            padding-bottom: calc(10px + env(safe-area-inset-bottom, 0px));
            padding-left: 8px;
            padding-right: 8px;
            min-height: calc(62px + env(safe-area-inset-bottom, 0px));
            box-sizing: border-box;
        }
        .mobile-bottom-nav .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 52px;
            min-height: 44px;
            color: rgba(255, 255, 255, 0.65);
            text-decoration: none;
            font-size: 0.72rem;
            font-weight: 600;
            transition: all 0.2s ease;
            position: relative;
        }
        .mobile-bottom-nav .nav-item ion-icon {
            font-size: 1.55rem;
            margin-bottom: 2px;
            transition: transform 0.2s ease, color 0.2s ease;
        }
        .mobile-bottom-nav .nav-item:active {
            transform: scale(0.92);
        }
        .mobile-bottom-nav .nav-item.active {
            color: #FFA600 !important;
        }
        .mobile-bottom-nav .nav-item.active ion-icon {
            color: #FFA600 !important;
            transform: translateY(-2px);
            filter: drop-shadow(0 2px 6px rgba(255, 166, 0, 0.4));
        }

        /* Mobile Drawer Scroll Fix */
        .sidebar {
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
            max-height: 100vh !important;
            padding-bottom: calc(80px + env(safe-area-inset-bottom, 0px)) !important;
        }
    }
</style>
