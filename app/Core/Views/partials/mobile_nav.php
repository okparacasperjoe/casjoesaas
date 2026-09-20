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

<!-- Mobile Bottom Navigation (Glassmorphic) -->
<nav class="mobile-bottom-nav">
    <a href="/dashboard" class="nav-item">
        <ion-icon name="speedometer-outline"></ion-icon>
        <span>Overview</span>
    </a>
    <a href="/profile" class="nav-item">
        <ion-icon name="person-circle-outline"></ion-icon>
        <span>Profile</span>
    </a>
    <a href="/billing" class="nav-item">
        <ion-icon name="card-outline"></ion-icon>
        <span>Billing</span>
    </a>
    <a href="/notifications" class="nav-item">
        <ion-icon name="notifications-outline"></ion-icon>
        <span>Alerts</span>
    </a>
    <a href="/support" class="nav-item">
        <ion-icon name="headset-outline"></ion-icon>
        <span>Support</span>
    </a>
</nav>

<style>
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.6);
        z-index: 999;
        display: none;
        backdrop-filter: blur(3px);
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
            padding: 10px 20px;
            background: #000066;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            box-sizing: border-box;
            z-index: 1000;
        }
        .mobile-bottom-nav {
            display: flex;
            justify-content: space-between;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 102, 0.75);
            backdrop-filter: blur(15px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.3);
            z-index: 9999;
            padding: 10px 20px 20px;
        }
        .mobile-bottom-nav .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.85rem;
        }
        .mobile-bottom-nav .nav-item ion-icon {
            font-size: 1.8rem;
            margin-bottom: 4px;
        }
        .mobile-bottom-nav .nav-item:active,
        .mobile-bottom-nav .nav-item:hover {
            color: #FFA600;
        }
    }
</style>
