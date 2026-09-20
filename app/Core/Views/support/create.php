<?php require __DIR__ . '/../global_header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3" style="color: #000066; font-weight: 700;">New Support Ticket</h2>
        <a href="/support" class="btn btn-secondary">Back to Support</a>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3" style="background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0;">
                    <h6 class="m-0 font-weight-bold" style="color: #000066;">Ticket Details</h6>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">Please fill in all fields.</div>
                    <?php endif; ?>

                    <form action="/support/store" method="POST">
                        <div class="mb-3">
                            <label for="subject" class="form-label" style="color: #333;">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required placeholder="Brief summary of the issue" style="color: #333; background-color: #fff; border: 1px solid #ccc;">
                        </div>
                        
                        <div class="mb-3">
                            <label for="priority" class="form-label" style="color: #333;">Priority</label>
                            <select class="form-select form-control" id="priority" name="priority" style="color: #333; background-color: #fff; border: 1px solid #ccc;">
                                <option value="low">Low - General Question</option>
                                <option value="medium" selected>Medium - Issue affecting work</option>
                                <option value="high">High - System Down/Critical</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label" style="color: #333;">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="6" required placeholder="Describe your issue in detail..." style="color: #333; background-color: #fff; border: 1px solid #ccc;"></textarea>
                        </div>
                        
                        <button type="submit" class="btn w-100" style="background-color: #000066; color: white;">Submit Ticket</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../global_footer.php'; ?>
