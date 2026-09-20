<?php
$title = "Read Document: " . htmlspecialchars($assignment['title'] ?? '');
require __DIR__ . '/../../layout/header.php';
?>
<div class="mb-4">
    <h2><?= $title ?></h2>
    <a href="/erp/my-portal/sops" class="btn btn-secondary btn-sm">Back to My Documents</a>
</div>

<div class="card mb-4">
    <div class="card-header bg-light">
        <strong>Document Content</strong>
    </div>
    <div class="card-body" style="max-height: 500px; overflow-y: auto; background-color: #ffffff; color: #000000; padding: 30px; border: 1px solid #eee;">
        <!-- The SOP content could contain HTML if created by rich text editor -->
        <?= $assignment['content'] ?>
    </div>
</div>

<?php if ($assignment['status'] == 'assigned'): ?>
<div class="card border-primary">
    <div class="card-header bg-primary text-white">
        <strong>Signature Required</strong>
    </div>
    <div class="card-body">
        <form action="/erp/my-portal/sops/sign" method="POST" id="signForm">
            <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">
            <input type="hidden" name="signature" id="signatureData">
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="agreed" value="1" id="agreeCheck" required>
                    <label class="form-check-label fw-bold" for="agreeCheck">
                        I have read and understood the contents of this document, and I agree to abide by it.
                    </label>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Draw Signature</label>
                    <div style="border: 1px solid #ccc; background: #fff;">
                        <canvas id="signatureCanvas" width="400" height="150" style="touch-action: none; cursor: crosshair;"></canvas>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="clearCanvas()">Clear Signature</button>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label>Print Name</label>
                    <input type="text" name="signed_name" class="form-control" placeholder="Type your full name" style="background-color: #ffffff; color: #000000; border: 1px solid #ccc;" required>
                    
                    <label class="mt-3">Date</label>
                    <input type="text" class="form-control" value="<?= date('M d, Y') ?>" style="background-color: #f8f9fa; color: #000000; border: 1px solid #ccc;" readonly>
                </div>
            </div>
            
            <button type="submit" class="btn btn-success mt-3" onclick="return prepareSignature()">Submit Signature</button>
        </form>
    </div>
</div>

<script>
// Simple Signature Canvas Logic
const canvas = document.getElementById('signatureCanvas');
const ctx = canvas.getContext('2d');
let drawing = false;

// Setup styling
ctx.lineWidth = 2;
ctx.strokeStyle = '#000';
ctx.lineCap = 'round';

function getPos(e) {
    const rect = canvas.getBoundingClientRect();
    if(e.touches) {
        return {
            x: e.touches[0].clientX - rect.left,
            y: e.touches[0].clientY - rect.top
        };
    }
    return {
        x: e.clientX - rect.left,
        y: e.clientY - rect.top
    };
}

function startDraw(e) {
    e.preventDefault();
    drawing = true;
    const pos = getPos(e);
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);
}

function draw(e) {
    if (!drawing) return;
    e.preventDefault();
    const pos = getPos(e);
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
}

function stopDraw(e) {
    if(drawing) {
        e.preventDefault();
        drawing = false;
    }
}

// Mouse events
canvas.addEventListener('mousedown', startDraw);
canvas.addEventListener('mousemove', draw);
canvas.addEventListener('mouseup', stopDraw);
canvas.addEventListener('mouseleave', stopDraw);

// Touch events
canvas.addEventListener('touchstart', startDraw, {passive: false});
canvas.addEventListener('touchmove', draw, {passive: false});
canvas.addEventListener('touchend', stopDraw, {passive: false});

function clearCanvas() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

function isCanvasBlank(canvas) {
    const blank = document.createElement('canvas');
    blank.width = canvas.width;
    blank.height = canvas.height;
    return canvas.toDataURL() === blank.toDataURL();
}

function prepareSignature() {
    if (isCanvasBlank(canvas)) {
        alert("Please draw your signature.");
        return false;
    }
    if (!document.getElementById('agreeCheck').checked) {
        alert("You must check the agreement box.");
        return false;
    }
    document.getElementById('signatureData').value = canvas.toDataURL();
    return true;
}
</script>

<?php else: ?>
<div class="card border-success">
    <div class="card-header bg-success text-white">
        <strong>Signature Details</strong>
    </div>
    <div class="card-body">
        <p><strong>Status:</strong> Signed</p>
        <p><strong>Signed By:</strong> <?= htmlspecialchars($assignment['signed_name']) ?></p>
        <p><strong>Date:</strong> <?= date('M d, Y h:i A', strtotime($assignment['signed_at'])) ?></p>
        
        <?php if (!empty($assignment['signature'])): ?>
            <p><strong>Signature:</strong></p>
            <img src="<?= $assignment['signature'] ?>" alt="Signature" style="border: 1px solid #ccc; max-width: 400px; background: #fff;">
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../../layout/footer.php'; ?>
