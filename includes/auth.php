<?php
/**
 * SMART DEV - Authentication Helpers
 */

/**
 * Start session with secure settings.
 */
function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', '1');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_samesite', 'Strict');
        if (isHttps()) {
            ini_set('session.cookie_secure', '1');
        }
        session_start();
    }
}

/**
 * Check if admin is logged in. Redirects to login if not.
 */
function requireAdmin(): void {
    if (empty($_SESSION['admin_id'])) {
        redirect(BASE_URL . '/admin/login.php');
    }
}

/**
 * Return currently authenticated admin ID.
 */
function adminId(): ?int {
    return isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : null;
}

/**
 * Log in an administrator after verifying credentials.
 * Returns true on success, false on failure.
 */
function attemptAdminLogin(string $username, string $password): bool {
    $db = getDB();
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    // Rate limiting: max 5 failed attempts in 15 minutes
    $stmt = $db->prepare(
        'SELECT COUNT(*) FROM login_attempts
         WHERE ip_address = ? AND success = 0 AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)'
    );
    $stmt->execute([$ip]);
    $failures = (int)$stmt->fetchColumn();

    if ($failures >= 5) {
        return false; // Locked out
    }

    // Fetch admin record
    $stmt = $db->prepare('SELECT * FROM administrators WHERE username = ? AND is_active = 1 LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    $success = false;
    if ($admin && password_verify($password, $admin['password_hash'])) {
        // Regenerate session to prevent fixation
        session_regenerate_id(true);
        $_SESSION['admin_id']       = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_name']     = $admin['full_name'];
        $_SESSION['logged_in_at']   = time();

        // Update last login
        $db->prepare('UPDATE administrators SET last_login = NOW() WHERE id = ?')
           ->execute([$admin['id']]);

        $success = true;
    }

    // Record attempt
    $db->prepare(
        'INSERT INTO login_attempts (ip_address, username_tried, success) VALUES (?, ?, ?)'
    )->execute([$ip, $username, $success ? 1 : 0]);

    return $success;
}

/**
 * Check if the current IP is locked out.
 */
function isLockedOut(): bool {
    $db = getDB();
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt = $db->prepare(
        'SELECT COUNT(*) FROM login_attempts
         WHERE ip_address = ? AND success = 0 AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)'
    );
    $stmt->execute([$ip]);
    return (int)$stmt->fetchColumn() >= 5;
}

/**
 * Destroy admin session (logout).
 */
function adminLogout(): void {
    session_unset();
    session_destroy();
    session_start();
    session_regenerate_id(true);
}
