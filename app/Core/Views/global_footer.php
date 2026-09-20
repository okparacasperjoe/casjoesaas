    </main>
</div>
<!-- Global Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    
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
</body>
</html>
