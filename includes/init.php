<?php
/**
 * SMART DEV - Bootstrap / Initialization
 * Include this at the top of every page.
 */

// ── Base path ────────────────────────────────────────────────────────────────
define('ROOT_PATH', dirname(__DIR__));

/**
 * AUTO-DETECT BASE_URL
 *
 * Derives the correct URL prefix from the script's location on disk so the
 * app works whether it lives at http://localhost/ or http://localhost/smart-dev/
 * without any manual editing.
 *
 * How it works:
 *  - DOCUMENT_ROOT  → e.g. C:/xampp/htdocs
 *  - ROOT_PATH      → e.g. C:/xampp/htdocs/smart-dev
 *  - Difference     → /smart-dev   (the subdirectory prefix, if any)
 *
 * On Windows, str_replace normalises backslashes to forward slashes.
 * The result has NO trailing slash:  '' or '/smart-dev'
 */
if (!defined('BASE_URL')) {
    $docRoot  = rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
    $projRoot = rtrim(str_replace('\\', '/', ROOT_PATH), '/');
    $subDir   = str_replace($docRoot, '', $projRoot); // e.g. '/smart-dev' or ''
    define('BASE_URL', $subDir);
}

// ── Environment detection ────────────────────────────────────────────────────
// Requests from localhost are treated as development; all others as production.
$isLocalDev = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true);

// ── Error display ────────────────────────────────────────────────────────────
// Errors are shown on local dev; hidden (but logged) on production.
ini_set('display_errors', $isLocalDev ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

// ── HTTPS enforcement (production only) ─────────────────────────────────────
if (!$isLocalDev) {
    // Redirect HTTP → HTTPS
    $isHttpsRequest = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                   || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
                   || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

    if (!$isHttpsRequest && !empty($_SERVER['HTTP_HOST'])) {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301);
        exit;
    }

    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// ── Security headers ─────────────────────────────────────────────────────────
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: "
    . "default-src 'self'; "
    . "script-src 'self' 'unsafe-inline'; "
    . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; "
    . "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; "
    . "img-src 'self' data: https:; "
    . "connect-src 'self' https://nominatim.openstreetmap.org; "
    . "frame-ancestors 'self';"
);

// ── Load helpers ─────────────────────────────────────────────────────────────
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/csrf.php';
require_once ROOT_PATH . '/includes/auth.php';

// ── Session ──────────────────────────────────────────────────────────────────
startSecureSession();

// ── Maintenance mode ─────────────────────────────────────────────────────────
// If enabled in admin settings, block all public pages except the admin area.
if (!defined('ADMIN_AREA')) {
    if (getSetting('maintenance_mode') === '1') {
        http_response_code(503);
        header('Retry-After: 3600');
        die('<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>SMART DEV – Under Maintenance</title>
        <style>body{margin:0;font-family:\'Segoe UI\',sans-serif;background:#1a1a2e;color:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh;text-align:center;}
        .box{max-width:480px;padding:3rem 2rem;}.logo{font-size:2.5rem;font-weight:900;color:#F47B20;margin-bottom:1rem;}
        h1{font-size:1.6rem;margin-bottom:1rem;}p{color:rgba(255,255,255,0.65);line-height:1.7;}
        </style></head><body><div class="box"><div class="logo">SD</div>
        <h1>SMART DEV – Under Maintenance</h1>
        <p>The site is currently undergoing scheduled maintenance. We\'ll be back shortly.</p>
        </div></body></html>');
    }
}
