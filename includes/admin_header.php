<?php
/**
 * SMART DEV - Admin Header
 * Variables expected: $pageTitle, $activeNav
 */
define('ADMIN_AREA', true); // Prevents maintenance mode from blocking admin pages
$pageTitle  = $pageTitle  ?? 'Dashboard';
$activeNav  = $activeNav  ?? 'dashboard';
$adminName  = $_SESSION['admin_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | SMART DEV Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/assets/images/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <meta name="robots" content="noindex, nofollow">
</head>
<body class="admin-body">
<div class="admin-wrapper">

<!-- ====== SIDEBAR ====== -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <div class="logo-icon"><span class="logo-s">S</span><span class="logo-d">D</span></div>
        <div class="sidebar-brand">
            <span class="brand-name">SMART DEV</span>
            <span class="admin-label">Admin Panel</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-nav-section">Main</div>
        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="<?= BASE_URL ?>/index.php" target="_blank">
            <i class="fas fa-globe"></i> View Website
        </a>

        <div class="sidebar-nav-section" style="margin-top: 1rem;">Content</div>
        <a href="<?= BASE_URL ?>/admin/messages.php" class="<?= $activeNav === 'messages' ? 'active' : '' ?>">
            <i class="fas fa-envelope"></i> Messages
        </a>
        <a href="<?= BASE_URL ?>/admin/projects.php" class="<?= $activeNav === 'projects' ? 'active' : '' ?>">
            <i class="fas fa-briefcase"></i> Projects
        </a>
        <a href="<?= BASE_URL ?>/admin/skills.php" class="<?= $activeNav === 'skills' ? 'active' : '' ?>">
            <i class="fas fa-star"></i> Skills
        </a>
        <a href="<?= BASE_URL ?>/admin/education.php" class="<?= $activeNav === 'education' ? 'active' : '' ?>">
            <i class="fas fa-graduation-cap"></i> Education
        </a>
        <a href="<?= BASE_URL ?>/admin/services.php" class="<?= $activeNav === 'services' ? 'active' : '' ?>">
            <i class="fas fa-concierge-bell"></i> Services
        </a>
        <a href="<?= BASE_URL ?>/admin/testimonials.php" class="<?= $activeNav === 'testimonials' ? 'active' : '' ?>">
            <i class="fas fa-comments"></i> Testimonials
        </a>

        <div class="sidebar-nav-section" style="margin-top: 1rem;">Settings</div>
        <a href="<?= BASE_URL ?>/admin/profile.php" class="<?= $activeNav === 'profile' ? 'active' : '' ?>">
            <i class="fas fa-user-circle"></i> Profile
        </a>
        <a href="<?= BASE_URL ?>/admin/settings.php" class="<?= $activeNav === 'settings' ? 'active' : '' ?>">
            <i class="fas fa-cog"></i> Settings
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>/admin/logout.php" data-confirm="Are you sure you want to log out?">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</aside>

<!-- ====== MAIN CONTENT ====== -->
<div class="admin-main">
    <header class="admin-topbar">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <button class="btn-icon" id="sidebarToggle" style="width: 38px; height: 38px; background: #f0f2f5;" aria-label="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <h1><?= e($pageTitle) ?></h1>
        </div>
        <div class="topbar-right">
            <a href="<?= BASE_URL ?>/index.php" target="_blank" class="btn btn-outline btn-sm">
                <i class="fas fa-external-link-alt"></i> View Site
            </a>
            <div class="admin-user-info">
                <i class="fas fa-user-circle" style="font-size: 1.3rem;"></i>
                <span><?= e($adminName) ?></span>
            </div>
        </div>
    </header>

    <div class="admin-content">
        <?php
        // Show flash message
        $flash = getFlash();
        if ($flash):
        ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'error' ? 'danger' : 'warning') ?>">
            <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
            <?= e($flash['message']) ?>
        </div>
        <?php endif; ?>
