<?php
/**
 * SMART DEV - CSRF Protection
 */

/**
 * Generate (or retrieve existing) CSRF token.
 */
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output a hidden CSRF input field.
 */
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

/**
 * Verify CSRF token from a POST request.
 * Terminates with 403 if invalid.
 */
function verifyCsrf(): void {
    $submitted = $_POST['csrf_token'] ?? '';
    if (!hash_equals(csrfToken(), $submitted)) {
        http_response_code(403);
        die('<h1>403 Forbidden</h1><p>Invalid security token. Please go back and try again.</p>');
    }
}
