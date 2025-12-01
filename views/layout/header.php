<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/session.php';
require_password_change();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'NEXERA'); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
    <?php if (!empty($extraStyles)): ?>
        <?php foreach ($extraStyles as $style): ?>
            <link rel="stylesheet" href="<?= e($style); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (!empty($extraScriptsHead)): ?>
        <?php foreach ($extraScriptsHead as $script): ?>
            <script src="<?= e($script); ?>" defer></script>
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <nav class="navbar" aria-label="Main Navigation">
        <div class="navbar-brand">NEXERA</div>
        <div>
            <a class="neon-button" href="logout.php" aria-label="Logout">
                <span>Logout</span>
            </a>
        </div>
    </nav>
