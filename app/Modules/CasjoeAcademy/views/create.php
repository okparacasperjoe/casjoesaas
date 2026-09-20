<!DOCTYPE html>
<html lang="en">

<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
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
    <?php include __DIR__ . '/partials/sidebar_acad_css.php'; ?>

            <div class="acad-brand">
                <ion-icon name="school"></ion-icon>
                Casjoe<span>Academy</span>
            </div>
            <ul class="acad-menu">
                <li class="acad-item"><a href="/" class="acad-link"><ion-icon name="apps-outline"></ion-icon>Back to
                        Apps</a></li>
                <li class="acad-item"><a href="/academy" class="acad-link active"><ion-icon
                            name="book-outline"></ion-icon>My Courses</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <h1>Create New Course</h1>
            </div>

            <div class="card" style="max-width: 600px; margin: 0 auto;">
                <div style="background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%); padding: 15px; border-radius: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; color: #1a365d;">
                    <div>
                        <strong style="font-size: 1.05rem;">✨ AI Course Assistant</strong>
                        <p style="margin: 3px 0 0; font-size: 0.85rem; opacity: 0.9;">Type a topic and let AI structure your title, description & pricing.</p>
                    </div>
                    <button type="button" onclick="generateAiCourse()" class="btn" style="background: #1a365d; color: #fff; font-weight: bold; border: none; cursor: pointer; padding: 8px 16px; border-radius: 6px;">
                        ✨ Generate
                    </button>
                </div>

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

    <script>
    function generateAiCourse() {
        const topic = prompt("What topic would you like to build a course on? (e.g. 'Financial Accounting 101', 'Web Design Bootcamp')");
        if (!topic) return;

        const title = topic.replace(/\b\w/g, l => l.toUpperCase());
        const desc = `Comprehensive masterclass on ${title}.\n\nWhat students will learn:\n• Foundational theories & best practices\n• Practical real-world execution & case studies\n• Step-by-step frameworks for mastery`;

        document.querySelector('input[name="title"]').value = title;
        document.querySelector('input[name="price"]').value = "49.99";
        document.querySelector('textarea[name="description"]').value = desc;
    }
    </script>
</body>

</html>
