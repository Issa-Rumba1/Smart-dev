<?php
/**
 * SMART DEV - Public Site Header
 * Variables expected: $pageTitle (string), $metaDescription (string)
 */
$profile = getProfile();
$pageTitle       = $pageTitle ?? 'SMART DEV | Issa Nyamawi Rumba';
$metaDescription = $metaDescription ?? e($profile['meta_description'] ?? 'SMART DEV – Innovation. Technology. Digital Solutions.');
$currentPage     = currentPage();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $metaDescription ?>">
    <meta name="author" content="Issa Nyamawi Rumba">
    <meta name="keywords" content="SMART DEV, IT support, web development, computer maintenance, networking, database management, graphic design, Kenya">
    <!-- Open Graph -->
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= $metaDescription ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?= BASE_URL ?>/assets/images/og-image.svg">
    <title><?= e($pageTitle) ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/assets/images/favicon.svg">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

<!-- ======================== NAVIGATION ======================== -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>/index.php" class="nav-logo" aria-label="SMART DEV Home">
            <div class="logo-icon">
                <span class="logo-s">S</span><span class="logo-d">D</span>
            </div>
            <div class="logo-text">
                <span class="brand-name">SMART DEV</span>
                <span class="brand-tagline">Innovation. Technology.</span>
            </div>
        </a>

        <!-- Desktop Nav Links -->
        <ul class="nav-links" id="navLinks">
            <li><a href="<?= BASE_URL ?>/index.php" class="<?= navActive('index') ?>">Home</a></li>
            <li><a href="<?= BASE_URL ?>/about.php" class="<?= navActive('about') ?>">About</a></li>
            <li><a href="<?= BASE_URL ?>/education.php" class="<?= navActive('education') ?>">Education</a></li>
            <li><a href="<?= BASE_URL ?>/skills.php" class="<?= navActive('skills') ?>">Skills</a></li>
            <li><a href="<?= BASE_URL ?>/services.php" class="<?= navActive('services') ?>">Services</a></li>
            <li><a href="<?= BASE_URL ?>/portfolio.php" class="<?= navActive('portfolio') ?>">Portfolio</a></li>
            <li><a href="<?= BASE_URL ?>/testimonials.php" class="<?= navActive('testimonials') ?>">Testimonials</a></li>
            <li><a href="<?= BASE_URL ?>/contact.php" class="<?= navActive('contact') ?>">Contact</a></li>
            <li><a href="<?= BASE_URL ?>/admin/login.php" class="nav-admin-btn <?= (strpos($currentPage, 'admin') !== false || $currentPage === 'login') ? 'active' : '' ?>"><i class="fas fa-lock"></i> Admin</a></li>
        </ul>

        <!-- Controls -->
        <div class="nav-controls">
            <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode" title="Toggle dark/light mode">
                <i class="fas fa-moon" id="themeIcon"></i>
            </button>
            <button class="hamburger" id="hamburger" aria-label="Open navigation menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>

<!-- Mobile Nav Overlay -->
<div class="nav-overlay" id="navOverlay"></div>

<!-- Flash message -->
<?php $flash = getFlash(); if ($flash): ?>
<div class="flash-message flash-<?= e($flash['type']) ?>" id="flashMessage" role="alert">
    <span><?= e($flash['message']) ?></span>
    <button onclick="this.parentElement.remove()" aria-label="Close">&times;</button>
</div>
<?php endif; ?>

<main>
