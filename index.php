<?php
declare(strict_types=1);

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(Nexera\Config\APP_NAME); ?> &mdash; Modern College Management</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar" aria-label="Primary Navigation">
        <div class="navbar-brand"><?= e(Nexera\Config\APP_NAME); ?></div>
        <div>
            <a class="neon-button" href="login.php">Login</a>
        </div>
    </nav>

    <header class="hero" id="home">
        <div class="hero__bg" aria-hidden="true"></div>
        <div class="hero__accent"></div>
        <div class="hero__accent hero__accent--right"></div>
        <div data-particles class="hero__particles" aria-hidden="true"></div>
        <div class="hero__content glass-card">
            <h1 class="hero__title"><?= e(Nexera\Config\APP_NAME); ?></h1>
            <p class="hero__tagline"><?= e(Nexera\Config\APP_TAGLINE); ?></p>
            <p class="hero__tagline">
                Manage academics, attendance, placements, and more — all in one platform.
            </p>
            <div>
                <a class="neon-button" href="#about">Explore Modules</a>
            </div>
        </div>
    </header>

    <section class="section" id="about">
        <div class="section__title">About NEXERA</div>
        <p class="section__subtitle">
            NEXERA streamlines campus operations with role-based experiences for Students, Parents, and Staff.
            Stay coordinated with digitised workflows, analytics, and collaborative tools built for modern colleges.
        </p>

        <div class="module-grid">
            <article class="module-card glass-card">
                <h3>Student Portal</h3>
                <ul>
                    <li>Update personal profile and preferences.</li>
                    <li>Access study materials and digital vault.</li>
                    <li>Track attendance trends and analytics.</li>
                    <li>Apply for leave/on-duty requests digitally.</li>
                    <li>Monitor internships, placements, and tasks.</li>
                </ul>
            </article>

            <article class="module-card glass-card">
                <h3>Parent Dashboard</h3>
                <ul>
                    <li>View child’s personal, academic, and contact details.</li>
                    <li>Track attendance percentage and leave approvals.</li>
                    <li>Review internal assessment feedback.</li>
                    <li>Monitor placement opportunities and status.</li>
                    <li>Access analytics and upcoming events.</li>
                </ul>
            </article>

            <article class="module-card glass-card">
                <h3>Staff Suite</h3>
                <ul>
                    <li>Mark attendance and publish internal marks.</li>
                    <li>Approve student requests promptly.</li>
                    <li>Assign, collect, and grade tasks with ease.</li>
                    <li>Upload study materials securely.</li>
                    <li>Visualise class performance with built-in charts.</li>
                </ul>
            </article>
        </div>
    </section>

    <footer class="footer">
        <p>
            &copy; <?= date('Y'); ?> NEXERA. Designed for future-ready institutions.
        </p>
    </footer>
    <script type="module" src="js/main.js"></script>
</body>
</html>


