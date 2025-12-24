<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Course | Academy</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand">
                <ion-icon name="school"></ion-icon>
                Casjoe<span>Academy</span>
            </div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="/" class="nav-link"><ion-icon name="apps-outline"></ion-icon>Back to
                        Apps</a></li>
                <li class="nav-item"><a href="/academy" class="nav-link active"><ion-icon
                            name="book-outline"></ion-icon>My Courses</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <h1>Create New Course</h1>
            </div>

            <div class="card" style="max-width: 600px; margin: 0 auto;">
                <form method="POST" action="/academy/store">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px;">Course Title</label>
                        <input type="text" name="title" placeholder="e.g. Advanced Financial Management" required
                            style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px;">Price ($)</label>
                        <input type="number" name="price" min="0" step="0.01" placeholder="0.00 for Free" required
                            style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px;">Description</label>
                        <textarea name="description" rows="5" placeholder="Course overview and objectives..." required
                            style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; font-family: inherit;"></textarea>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn" style="flex: 1; padding: 12px;">Create Course</button>
                        <a href="/academy" class="btn"
                            style="background: transparent; border: 1px solid #ccc; color: var(--text-color);">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>