<?php require __DIR__ . '/../../../../views/partials/header.php'; ?>

<h1>Compose Email</h1>

<div class="card">
    <form method="POST" action="/mail/send">
        <label>To (Email Address)</label>
        <input type="email" name="to" placeholder="recipient@example.com" required>

        <label>Subject</label>
        <input type="text" name="subject" placeholder="Subject Line" required>

        <label>Message (HTML Supported)</label>
        <textarea name="message" rows="10" placeholder="Type your message here..."></textarea>

        <p><small>Note: This email will be wrapped in your company branded template automatically.</small></p>

        <button type="submit">Send Email</button>
    </form>
</div>

<?php require __DIR__ . '/../../../../views/partials/footer.php'; ?>
