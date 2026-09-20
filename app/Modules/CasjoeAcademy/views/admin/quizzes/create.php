<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Create Quiz | Casjoe Business School</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .quiz-builder {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            max-width: 800px;
            margin: 0 auto;
        }
        .question-block {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            position: relative;
        }
        .remove-q-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #bdc3c7;
            cursor: pointer;
            font-size: 1.2rem;
        }
        .remove-q-btn:hover { color: #e74c3c; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
    <?php include __DIR__ . '/../../partials/sidebar_acad_css.php'; ?>

        <div class="acad-brand"><a href="/dashboard" style="text-decoration:none; display:flex; align-items:center;"><img src="/assets/casjoe_logo.webp" alt="Casjoe Apps" style="height: 40px;"></a></div>
        <ul class="acad-menu">
            <li class="acad-item"><a href="/academy/instructor/edit/<?= $courseId ?>" class="acad-link"><ion-icon name="arrow-back-outline"></ion-icon> Back to Course</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h1>Create New Quiz</h1>
        </div>

        <div class="quiz-builder">
            <form action="/academy/quiz/store/<?= $courseId ?>" method="POST">
                
                <div class="form-group">
                    <label>Quiz Title</label>
                    <input type="text" name="title" required class="form-control" placeholder="e.g. Module 1 Assessment">
                </div>

                <div class="form-group">
                    <label>Passing Score (%)</label>
                    <input type="number" name="passing_score" value="70" min="1" max="100" class="form-control" style="width: 100px;">
                </div>

                <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

                <div id="questions-container">
                    <!-- Questions will be added here -->
                </div>

                <button type="button" onclick="addQuestion()" class="btn" style="background: transparent; border: 1px dashed #3498db; color: #3498db; width: 100%; margin-bottom: 20px;">+ Add Question</button>

                <div style="text-align: right;">
                    <a href="/academy/instructor/edit/<?= $courseId ?>" class="btn" style="background: #ccc; color: #333; margin-right: 10px;">Cancel</a>
                    <button type="submit" class="btn">Save Quiz</button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
    let qCount = 0;

    function addQuestion() {
        const container = document.getElementById('questions-container');
        const index = qCount++;
        
        const html = `
            <div class="question-block" id="q-${index}">
                <div class="remove-q-btn" onclick="removeQuestion(${index})"><ion-icon name="trash-outline"></ion-icon></div>
                
                <div class="form-group">
                    <label>Question ${index + 1}</label>
                    <input type="text" name="questions[${index}][text]" required class="form-control" placeholder="Enter question text...">
                </div>

                <div class="form-group" style="margin-left: 20px;">
                    <label style="font-size: 0.9rem;">Options</label>
                    
                    <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 5px;">
                        <input type="radio" name="questions[${index}][correct_index]" value="0" checked>
                        <input type="text" name="questions[${index}][options][]" required class="form-control" placeholder="Option 1 (Correct?)">
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 5px;">
                        <input type="radio" name="questions[${index}][correct_index]" value="1">
                        <input type="text" name="questions[${index}][options][]" required class="form-control" placeholder="Option 2">
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 5px;">
                        <input type="radio" name="questions[${index}][correct_index]" value="2">
                        <input type="text" name="questions[${index}][options][]" required class="form-control" placeholder="Option 3">
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 5px;">
                        <input type="radio" name="questions[${index}][correct_index]" value="3">
                        <input type="text" name="questions[${index}][options][]" required class="form-control" placeholder="Option 4">
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', html);
    }

    function removeQuestion(index) {
        document.getElementById(`q-${index}`).remove();
    }

    // Add first question by default
    addQuestion();
</script>
</body>
</html>
