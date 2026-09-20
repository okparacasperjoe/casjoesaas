<style>
    .mobile-header { display: none; }
    @media (max-width: 768px) {
        .mobile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background: #000066;
            color: white;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
    }
</style>
<div class="mobile-header">
    <div class="mobile-toggle" onclick="toggleSidebar()" style="cursor: pointer;">
        <ion-icon name="menu-outline" style="font-size: 1.8rem;"></ion-icon>
    </div>
    <div class="mobile-brand" style="font-weight: 700; color: white;">
        Academy Studio
    </div>
    <div class="mobile-user">
        <ion-icon name="person-circle-outline" style="font-size: 1.8rem; color: white;"></ion-icon>
    </div>
</div>

<!-- Sidebar Overlay (click to close) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar) sidebar.classList.toggle('active');
        if (overlay) overlay.classList.toggle('active');
    }
</script>
