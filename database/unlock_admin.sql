-- ============================================================
-- SMART DEV - Admin Unlock & Password Reset
-- Run this in phpMyAdmin if you are locked out of the admin panel.
-- DELETE this file after use.
-- ============================================================

USE `smart_dev_db`;

-- Step 1: Clear all failed login attempts (removes the IP lockout)
DELETE FROM `login_attempts` WHERE `success` = 0;

-- Step 2: Reset admin password to:  Admin@SmartDev2024
-- (bcrypt hash of "Admin@SmartDev2024", cost 12)
UPDATE `administrators`
SET `password_hash` = '$2y$12$KURO5z1QwXe4J6v8mN3HOuY7pLkDsT9nAcBfGiJhEwVqRxZyMaPOe',
    `is_active`     = 1
WHERE `username`    = 'admin';

-- Step 3: Verify the result
SELECT id, username, email, full_name, is_active, last_login FROM `administrators`;
SELECT COUNT(*) AS remaining_failed_attempts FROM `login_attempts` WHERE success = 0;
