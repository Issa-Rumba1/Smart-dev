<?php
/**
 * SMART DEV - Global Helper Functions
 */

/**
 * Escape output to prevent XSS.
 */
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Redirect to a URL.
 */
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

/**
 * Flash message (store and retrieve single-use messages).
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Sanitize a string (trim + strip tags).
 */
function sanitize(string $value): string {
    return trim(strip_tags($value));
}

/**
 * Validate email address.
 */
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Log administrator activity.
 */
function logActivity(string $action, string $details = '', ?int $adminId = null): void {
    try {
        $db = getDB();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $stmt = $db->prepare(
            'INSERT INTO activity_logs (admin_id, action, details, ip_address) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$adminId, $action, $details, $ip]);
    } catch (Exception $e) {
        error_log('Activity log failed: ' . $e->getMessage());
    }
}

/**
 * Get site profile from database.
 */
function getProfile(): array {
    static $profile = null;
    if ($profile === null) {
        $db = getDB();
        $stmt = $db->query('SELECT * FROM profile LIMIT 1');
        $profile = $stmt->fetch() ?: [];
    }
    return $profile;
}

/**
 * Get a setting value.
 */
function getSetting(string $key, string $default = ''): string {
    static $settings = null;
    if ($settings === null) {
        $db = getDB();
        $stmt = $db->query('SELECT setting_key, setting_value FROM settings');
        $settings = [];
        foreach ($stmt->fetchAll() as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $settings[$key] ?? $default;
}

/**
 * Allowed image MIME types.
 */
function allowedImageTypes(): array {
    return ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
}

/**
 * Get human-readable file size.
 */
function humanFileSize(int $bytes): string {
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
    return round($bytes / 1048576, 1) . ' MB';
}

/**
 * Generate a secure random token.
 */
function generateToken(int $length = 32): string {
    return bin2hex(random_bytes($length));
}

/**
 * Return the current page name (filename without extension).
 */
function currentPage(): string {
    return pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
}

/**
 * Active nav class helper.
 */
function navActive(string $page): string {
    return currentPage() === $page ? 'active' : '';
}

/**
 * Format a date nicely.
 */
function formatDate(string $date): string {
    return date('F j, Y', strtotime($date));
}

/**
 * Truncate text.
 */
function truncate(string $text, int $length = 150): string {
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '…';
}

/**
 * Check if the visitor is on a secure HTTPS connection.
 */
function isHttps(): bool {
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
}
