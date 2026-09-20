<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes">
    <title>Reading: <?= htmlspecialchars($book['title']) ?></title>
    <style>
        body { margin: 0; padding: 0; background: #2b2b2b; color: #eee; font-family: sans-serif; overflow: hidden; }
        #reader-container {
            display: flex; flex-direction: column; height: 100vh;
        }
        #toolbar {
            height: 60px; background: #1a1a1a; display: flex; align-items: center; justify-content: space-between; padding: 0 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.5); z-index: 10; flex-shrink: 0;
        }
        #canvas-wrapper {
            flex: 1; overflow: auto; display: flex; justify-content: center; align-items: flex-start; padding: 20px;
            background: #525659;
            -webkit-overflow-scrolling: touch;
        }
        #the-canvas {
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }
        .btn-tool {
            background: #444; color: white; border: 1px solid #555; padding: 8px 14px; cursor: pointer; border-radius: 4px; font-size: 14px;
            min-width: 40px; margin: 0 3px; user-select: none;
        }
        .btn-tool:hover { background: #666; }
        .btn-tool:active { background: #777; }
        .page-info { font-size: 12px; color: #aaa; margin: 0 8px; white-space: nowrap; }
        #zoom-level { font-size: 11px; color: #aaa; margin-left: 5px; }
        
        @media(max-width: 500px) {
            #toolbar { padding: 0 5px; gap: 3px; }
            .btn-tool { padding: 8px 10px; font-size: 13px; }
            .page-info { display: none; }
            #zoom-level { display: none; }
        }
    </style>
    <!-- PDF.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
    </script>
</head>
<body>

<div id="reader-container">
    <div id="toolbar">
        <div style="display: flex; align-items: center;">
            <button class="btn-tool" onclick="window.history.back()" title="Exit">&times; Exit</button>
        </div>
        
        <div style="display: flex; align-items: center;">
            <button class="btn-tool" id="prev">&larr; Prev</button>
            <span class="page-info">
                Page <span id="page_num"><?= $startPage ?></span> / <span id="page_count">--</span>
            </span>
            <button class="btn-tool" id="next">Next &rarr;</button>
        </div>

        <div style="display: flex; align-items: center;">
            <button class="btn-tool" id="zoom_out" title="Zoom Out">&minus;</button>
            <button class="btn-tool" id="zoom_in" title="Zoom In">&plus;</button>
            <span id="zoom-level">100%</span>
        </div>
    </div>

    <div id="canvas-wrapper">
        <canvas id="the-canvas"></canvas>
    </div>
</div>

<script>
(function() {
    var url = '/academy/library/pdf/<?= $book['id'] ?>';
    var pdfDoc = null;
    var pageNum = <?= $startPage ?>;
    var scale = 1.0;
    var baseScale = 1.0; // The initial auto-fit scale
    var manualZoom = false;
    var isRendering = false;
    var renderQueued = false;

    var canvas = document.getElementById('the-canvas');
    var ctx = canvas.getContext('2d');
    var zoomLabel = document.getElementById('zoom-level');

    function updateZoomLabel() {
        var pct = Math.round(scale * 100 / baseScale);
        zoomLabel.textContent = pct + '%';
    }

    function doRender() {
        if (!pdfDoc) return;
        if (isRendering) {
            renderQueued = true;
            return;
        }

        isRendering = true;
        renderQueued = false;

        pdfDoc.getPage(pageNum).then(function(page) {
            var unscaled = page.getViewport({scale: 1});
            var containerWidth = document.getElementById('canvas-wrapper').clientWidth - 40;

            if (!manualZoom) {
                scale = containerWidth / unscaled.width;
                if (scale > 1.8) scale = 1.8;
                baseScale = scale;
            }

            var viewport = page.getViewport({scale: scale});
            canvas.width = viewport.width;
            canvas.height = viewport.height;

            var renderTask = page.render({
                canvasContext: ctx,
                viewport: viewport
            });

            renderTask.promise.then(function() {
                isRendering = false;
                updateZoomLabel();
                if (renderQueued) {
                    doRender();
                }
            }).catch(function() {
                isRendering = false;
                if (renderQueued) {
                    doRender();
                }
            });
        }).catch(function() {
            isRendering = false;
        });

        document.getElementById('page_num').textContent = pageNum;
        saveProgress(pageNum);
    }

    // Navigation
    function prevPage() {
        if (pageNum <= 1) return;
        pageNum--;
        doRender();
    }

    function nextPage() {
        if (!pdfDoc || pageNum >= pdfDoc.numPages) return;
        pageNum++;
        doRender();
    }

    // Zoom
    function doZoomIn() {
        manualZoom = true;
        scale = scale + 0.3;
        doRender();
    }

    function doZoomOut() {
        if (scale <= 0.4) return;
        manualZoom = true;
        scale = scale - 0.3;
        if (scale < 0.3) scale = 0.3;
        doRender();
    }

    // Button listeners
    document.getElementById('prev').addEventListener('click', prevPage);
    document.getElementById('next').addEventListener('click', nextPage);
    document.getElementById('zoom_in').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        doZoomIn();
    });
    document.getElementById('zoom_out').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        doZoomOut();
    });

    // Keyboard
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') prevPage();
        if (e.key === 'ArrowRight') nextPage();
        if (e.key === '+' || e.key === '=') doZoomIn();
        if (e.key === '-') doZoomOut();
    });

    // Touch swipe
    var touchStartX = 0;
    var wrapper = document.getElementById('canvas-wrapper');
    wrapper.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, {passive: true});
    wrapper.addEventListener('touchend', function(e) {
        var diff = e.changedTouches[0].screenX - touchStartX;
        if (diff < -50) nextPage();
        if (diff > 50) prevPage();
    }, {passive: true});

    // Load PDF
    pdfjsLib.getDocument(url).promise.then(function(pdf) {
        pdfDoc = pdf;
        document.getElementById('page_count').textContent = pdf.numPages;
        doRender();

        // Resize only if not manually zoomed
        var resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (!manualZoom) doRender();
            }, 300);
        });
    }).catch(function(err) {
        document.getElementById('canvas-wrapper').innerHTML = '<p style="color:#ff6b6b; text-align:center; padding:40px;">Error loading PDF. Please try again.</p>';
    });

    function saveProgress(page) {
        fetch('/academy/library/progress', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ book_id: <?= $book['id'] ?>, page: page })
        }).catch(function() {});
    }
})();
</script>

</body>
</html>
