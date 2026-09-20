<?php include __DIR__ . '/sidebar_academy_css.php'; ?>

<aside class="sidebar">
    <div class="acad-brand">
        <ion-icon name="school-outline" style="color: #FFA600; font-size: 1.8rem;"></ion-icon>
        <span>Casjoe Business School</span>
    </div>

    <ul class="acad-menu">
        <li class="acad-item">
            <a href="/academy/overview" class="acad-link <?= $active == 'academy_overview' ? 'acad-active' : '' ?>">
                <ion-icon name="grid-outline"></ion-icon> Academy Overview
            </a>
        </li>
        <li class="acad-item">
            <a href="/academy/business" class="acad-link <?= $active == 'academy_business' ? 'acad-active' : '' ?>">
                <ion-icon name="business-outline"></ion-icon> Administration
            </a>
        </li>
        <li class="acad-item">
            <a href="/academy/catalog" class="acad-link <?= $active == 'academy_market' ? 'acad-active' : '' ?>">
                <ion-icon name="cart-outline"></ion-icon> Course Catalog
            </a>
        </li>
        <li class="acad-item">
            <a href="/academy/learn" class="acad-link <?= $active == 'academy_learn' ? 'acad-active' : '' ?>">
                <ion-icon name="book-outline"></ion-icon> My Learning
            </a>
        </li>
        <li class="acad-item">
            <a href="/academy/library" class="acad-link <?= $active == 'academy_library' ? 'acad-active' : '' ?>">
                <ion-icon name="library-outline"></ion-icon> CEO Book Reader
            </a>
        </li>

        <li class="acad-section-label">Instructor</li>

        <li class="acad-item">
            <a href="/academy/instructor" class="acad-link <?= $active == 'academy_instructor' ? 'acad-active' : '' ?>">
                <ion-icon name="construct-outline"></ion-icon> Course Builder
            </a>
        </li>

        <li class="acad-item" style="margin-top: auto;">
            <a href="/dashboard" class="acad-link">
                <ion-icon name="arrow-back-outline"></ion-icon> Back to Apps
            </a>
        </li>
    </ul>
</aside>
