<?php
$title = "Open New Ticket | Casjoe Priority Support";
require __DIR__ . '/../../../Core/Views/global_header.php';
$presetSubject = $_GET['subject'] ?? '';
?>
<style>
    .support-form-gradient {
        background: linear-gradient(135deg, rgba(0, 0, 102, 0.8) 0%, rgba(15, 23, 42, 0.95) 100%);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    .support-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
    }
    .form-control, .form-select {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #fff;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 0.98rem;
    }
    .form-control:focus, .form-select:focus {
        background: rgba(255, 255, 255, 0.09);
        border-color: #FFA600;
        color: #fff;
        box-shadow: 0 0 0 0.25rem rgba(255, 166, 0, 0.2);
    }
    .form-select option {
        background: #0f172a;
        color: #fff;
    }
    .ai-preview-card {
        background: linear-gradient(145deg, rgba(255, 166, 0, 0.1), rgba(0, 0, 102, 0.5));
        border: 1px solid rgba(255, 166, 0, 0.35);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }
</style>

<div class="container-fluid py-4" style="max-width: 960px;">
    <!-- Top Bar -->
    <div class="support-form-gradient d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 px-3 py-1 rounded-pill fw-bold mb-2">NEW PRIORITY TICKET</span>
            <h2 class="text-white fw-bold mb-1">Open Support Request</h2>
            <p class="text-muted small mb-0">Our support engineers will investigate and reply within your SLA time frame.</p>
        </div>
        <a href="/support" class="btn btn-outline-light fw-bold px-4 py-2 d-flex align-items-center gap-2" style="border-radius: 10px;">
            <ion-icon name="arrow-back-outline"></ion-icon> Back to Tickets
        </a>
    </div>

    <!-- AI Instant Assist Bar -->
    <div class="ai-preview-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
            <div class="d-flex align-items-center gap-2 text-warning fw-bold">
                <ion-icon name="sparkles" style="font-size: 1.3rem;"></ion-icon>
                <span>Need an answer instantly right now?</span>
            </div>
            <span class="badge bg-dark border border-warning text-warning" style="font-size: 0.75rem;">Powered by Cori AI Tokens</span>
        </div>
        <p class="text-muted small mb-3">Before submitting your ticket, you can ask Cori AI to run an instant check using your tokens. If Cori solves it, you can close or skip the ticket!</p>
        <button type="button" class="btn btn-warning fw-bold px-4 py-2 d-flex align-items-center gap-2" onclick="testWithCori()" id="cori-test-btn" style="background: #FFA600; color: #000066; border: none; border-radius: 8px;">
            <ion-icon name="flash"></ion-icon> Diagnose My Subject & Message with Cori AI
        </button>
        <div id="cori-test-result" class="mt-3 p-3 rounded text-white" style="background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); display: none;"></div>
    </div>

    <!-- Ticket Form Card -->
    <div class="support-card">
        <form method="POST" action="/support/create">
            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <label class="form-label fw-bold text-white small d-flex align-items-center gap-2">
                        <ion-icon name="create-outline" class="text-warning"></ion-icon> Ticket Subject
                    </label>
                    <input type="text" id="ticket-subject" name="subject" class="form-control" required placeholder="Briefly summarize your request or issue..." value="<?= htmlspecialchars($presetSubject) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-white small d-flex align-items-center gap-2">
                        <ion-icon name="flag-outline" class="text-warning"></ion-icon> Priority Level
                    </label>
                    <select name="priority" class="form-select">
                        <option value="low">🟢 Low (General Inquiry)</option>
                        <option value="medium" selected>🟡 Medium (Standard Issue)</option>
                        <option value="high">🟠 High (Workflow Impacted)</option>
                        <option value="urgent">🔴 Urgent (System Critical)</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-white small d-flex align-items-center gap-2">
                    <ion-icon name="document-text-outline" class="text-warning"></ion-icon> Detailed Message
                </label>
                <textarea id="ticket-message" name="message" class="form-control" rows="8" required placeholder="Please provide steps to reproduce, relevant error messages, or details about what you need..."></textarea>
            </div>

            <div class="d-flex justify-content-end gap-3 pt-3 border-top border-secondary border-opacity-25">
                <a href="/support" class="btn btn-outline-secondary fw-bold px-4 py-2" style="border-radius: 10px;">Cancel</a>
                <button type="submit" class="btn btn-warning fw-bold px-5 py-2 d-flex align-items-center gap-2 shadow-lg" style="background: #FFA600; color: #000066; border: none; border-radius: 10px;">
                    <ion-icon name="checkmark-circle"></ion-icon> Submit Ticket
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function testWithCori() {
    const subject = document.getElementById('ticket-subject').value.trim();
    const message = document.getElementById('ticket-message').value.trim();
    const btn = document.getElementById('cori-test-btn');
    const resultBox = document.getElementById('cori-test-result');

    if (!subject && !message) {
        alert('Please fill out the Subject or Detailed Message first so Cori AI can analyze it.');
        document.getElementById('ticket-subject').focus();
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Diagnosing with Cori AI...';
    resultBox.style.display = 'block';
    resultBox.innerHTML = '<div class="text-warning d-flex align-items-center gap-2"><span class="spinner-border spinner-border-sm"></span> <strong>Cori AI is reviewing your issue details...</strong></div>';

    const fullQuestion = `Subject: ${subject}\n\nDetails: ${message}`;
    const formData = new FormData();
    formData.append('message', fullQuestion);

    fetch('/support/ai-chat', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<ion-icon name="flash"></ion-icon> Diagnose My Subject & Message with Cori AI';

        if (data.success) {
            resultBox.innerHTML = `
                <div class="d-flex align-items-center gap-2 text-warning mb-2">
                    <ion-icon name="sparkles" style="font-size: 1.2rem;"></ion-icon>
                    <strong>CORI AI DIAGNOSTIC SUGGESTION</strong>
                </div>
                <div style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;">${data.reply}</div>
                <div class="mt-3 pt-3 border-top border-secondary border-opacity-25 text-muted small d-flex justify-content-between">
                    <span><ion-icon name="flash"></ion-icon> Token cost deducted from your Cori AI balance</span>
                    <span class="text-warning">If this resolved your issue, no need to submit the ticket below!</span>
                </div>
            `;
        } else {
            resultBox.innerHTML = `
                <div class="text-danger fw-bold"><ion-icon name="alert-circle"></ion-icon> ${data.error || 'Unable to analyze issue.'}</div>
                ${data.exhausted ? '<div class="mt-2"><a href="/billing" class="btn btn-sm btn-warning fw-bold">Top Up AI Credits</a></div>' : ''}
            `;
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<ion-icon name="flash"></ion-icon> Diagnose My Subject & Message with Cori AI';
        resultBox.innerHTML = `<div class="text-danger fw-bold"><ion-icon name="alert-circle"></ion-icon> Error connecting to Cori AI service.</div>`;
    });
}
</script>

<?php require __DIR__ . '/../../../Core/Views/global_footer.php'; ?>
