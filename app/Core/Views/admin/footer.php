    </main>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pageHeader = document.querySelector('.page-header, .top-bar');
    if (pageHeader) {
        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'mobile-toggle';
        toggleBtn.innerHTML = '<ion-icon name="menu-outline"></ion-icon>';
        
        // Wrap title in a div if not already to group with button
        const title = pageHeader.querySelector('h1, h2');
        if (title) {
            const wrapper = document.createElement('div');
            wrapper.className = 'top-bar-left';
            title.parentNode.insertBefore(wrapper, title);
            wrapper.appendChild(toggleBtn);
            wrapper.appendChild(title);
        } else {
            pageHeader.prepend(toggleBtn);
        }
        
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        function toggleSidebar() {
            sidebar.classList.toggle('open');
            if (sidebar.classList.contains('open')) {
                overlay.classList.add('active');
            } else {
                overlay.classList.remove('active');
            }
        }
        
        toggleBtn.addEventListener('click', toggleSidebar);
        if(overlay) overlay.addEventListener('click', toggleSidebar);
    }
});
</script>
</body>
</html>
