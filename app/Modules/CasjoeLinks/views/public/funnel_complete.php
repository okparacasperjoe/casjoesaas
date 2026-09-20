<?php $title="Funnel Complete"; include __DIR__ . "/../../../Core/Views/header.php"; ?>
<div class="row min-vh-100 justify-content-center align-items-center bg-light">
    <div class="col-md-6 col-lg-5">
        <div class="card border-0 shadow-lg text-center p-5">
            <div class="mb-4 text-success">
                <ion-icon name="checkmark-circle" style="font-size: 64px;"></ion-icon>
            </div>
            <h2 class="mb-3"><?= htmlspecialchars($config["headline"] ?? "Thank You!") ?></h2>
            <p class="text-muted mb-4"><?= nl2br(htmlspecialchars($config["message"] ?? "Your action has been completed successfully.")) ?></p>
            <a href="/" class="btn btn-primary">Return to Home</a>
        </div>
    </div>
</div>
<?php include __DIR__ . "/../../../Core/Views/footer.php"; ?>
