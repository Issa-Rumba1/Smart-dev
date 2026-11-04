<?php
/**
 * SMART DEV - Database Configuration
 * Modify DB_HOST, DB_NAME, DB_USER, DB_PASS for your environment.
 * NEVER commit real credentials to version control.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'smart_dev_db');
define('DB_USER', 'root');       // Change for production
define('DB_PASS', '');           // Change for production
define('DB_CHARSET', 'utf8mb4');

/**
 * Get a PDO database connection (singleton).
 */
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Log error; never expose DB credentials or details to the browser
            error_log('DB Connection failed: ' . $e->getMessage());
            http_response_code(503);
            die('<h1>Service Temporarily Unavailable</h1><p>Please try again later.</p>');
        }
    }
    return $pdo;
}
