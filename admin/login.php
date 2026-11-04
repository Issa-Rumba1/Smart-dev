<?php
require_once dirname(__DIR__) . '/includes/init.php';
define('ADMIN_AREA', true); // Allow access even in maintenance mode

// Already logged in
if (!empty($_SESSION['admin_id'])) {
    redirect(BASE_URL . '/admin/dashboard.php');
}

$error  = '';
$locked = isLockedOut();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$locked) {
    verifyCsrf();

    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } elseif (attemptAdminLogin($username, $password)) {
        logActivity('Admin login', 'Successful login', adminId());
        redirect(BASE_URL . '/admin/dashboard.php');
    } else {
        $locked = isLockedOut();
        $error  = $locked
            ? 'Too many failed attempts. Access is temporarily locked for 15 minutes.'
            : 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | SMART DEV</title>
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/assets/images/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <meta name="robots" content="noindex, nofollow">
</head>
<body class="admin-body">

<div class="admin-login-page">
    <div class="admin-login-card">
        <div class="login-header">
            <div style="display: flex; justify-content: center; margin-bottom: 1rem;">
                <div class="logo-icon" style="width: 64px; height: 64px; font-size: 1.4rem; border-radius: 16px;">
                    <span class="logo-s">S</span><span class="logo-d">D</span>
                </div>
            </div>
            <h2>SMART DEV</h2>
            <p>Administrator Login</p>
        </div>

        <?php if ($locked): ?>
        <div class="login-error">
            <i class="fas fa-lock"></i>
            Too many failed login attempts. Please wait 15 minutes before trying again.
        </div>
        <?php elseif ($error): ?>
        <div class="login-error">
            <i class="fas fa-exclamation-triangle"></i>
            <?= e($error) ?>
        </div>
        <?php endif; ?>

        <?php if (!$locked): ?>
        <form method="POST" action="">
            <?= csrfField() ?>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username"
                       value="<?= e($_POST['username'] ?? '') ?>"
                       placeholder="Enter username"
                       autocomplete="username" required>
            </div>
            <div class="form-group" style="margin-top: 1rem;">
                <label for="password">Password</label>
                <div style="position: relative;">
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Enter password"
                           autocomplete="current-password" required>
                    <button type="button" onclick="togglePw()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.5);cursor:pointer;">
                        <i class="fas fa-eye" id="pwIcon"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100" style="justify-content: center; margin-top: 1.5rem; font-size: 1rem; padding: 0.875rem;">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>
        <?php endif; ?>

        <div class="login-back">
            <a href="<?= BASE_URL ?>/index.php"><i class="fas fa-arrow-left"></i> Back to Website</a>
        </div>
    </div>
</div>

<script>
function togglePw() {
    const pw = document.getElementById('password');
    const ic = document.getElementById('pwIcon');
    if (pw.type === 'password') { pw.type = 'text'; ic.className = 'fas fa-eye-slash'; }
    else { pw.type = 'password'; ic.className = 'fas fa-eye'; }
}
</script>
</body>
</html>
