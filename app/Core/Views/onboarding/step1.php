<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<div class="text-center py-4">
    <div class="mb-4 d-inline-block p-4 rounded-circle shadow-sm" style="background: linear-gradient(135deg, rgba(255, 166, 0, 0.15) 0%, rgba(46, 204, 113, 0.15) 100%); border: 2px solid #FFA600;">
        <span style="font-size: 4rem; line-height: 1;">🎉</span>
    </div>

    <h1 class="fw-bold mb-2" style="font-size: 2.3rem; background: linear-gradient(135deg, #000066 0%, #FFA600 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
        Welcome to Casjoe, <?= htmlspecialchars($_SESSION['user_name'] ?? explode(' ', \App\Core\Auth::user()['name'] ?? 'Boss')[0] ?? 'Partner') ?>!
    </h1>
    <h4 class="text-dark fw-semibold mb-3">Africa's #1 AI Business Operating System</h4>
    <p class="text-muted mb-5 lead mx-auto" style="max-width: 500px; font-size: 1.05rem;">
        We are thrilled to celebrate this milestone with you! Get ready to automate your daily operations, grow your revenue, and manage everything from one unified dashboard.
    </p>
    
    <div class="card border-0 shadow-sm mb-5 mx-auto p-4 rounded-4" style="background: linear-gradient(135deg, #fdfbf7 0%, #f4f9f5 100%); border-left: 5px solid #2ed573 !important; max-width: 520px;">
        <div class="d-flex align-items-center gap-3 text-start">
            <div class="fs-1 text-success"><i class="bi bi-shield-check"></i></div>
            <div>
                <h6 class="fw-bold mb-1 text-dark">Setup takes less than 2 minutes</h6>
                <p class="small text-muted mb-0">Customize your business profile, select the exact modules you need, and launch your tailored workspace immediately.</p>
            </div>
        </div>
    </div>

    <form action="/onboarding/step1" method="POST">
        <button type="submit" class="btn btn-primary btn-lg px-5 py-3 fw-bold rounded-pill shadow" style="font-size: 1.15rem; background: linear-gradient(135deg, #000066 0%, #1a1a80 100%); border: none;">
            Let's Celebrate & Start Setup <i class="bi bi-arrow-right ms-2"></i>
        </button>
        
        <div class="mt-4 text-muted small">
            <i class="bi bi-lock-fill text-success me-1"></i> 256-bit Encrypted Workspace &bull; Instant Cloud Deployment
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof confetti === 'function') {
        // Trigger celebratory confetti blast
        var duration = 3.5 * 1000;
        var animationEnd = Date.now() + duration;
        var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 9999 };

        function randomInRange(min, max) {
            return Math.random() * (max - min) + min;
        }

        var interval = setInterval(function() {
            var timeLeft = animationEnd - Date.now();
            if (timeLeft <= 0) {
                return clearInterval(interval);
            }
            var particleCount = 50 * (timeLeft / duration);
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
        }, 250);
    }
});
</script>
