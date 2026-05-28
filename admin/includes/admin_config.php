<?php
/**
 * Admin Configuration
 * Hardcoded admin credentials for system administrator
 * DO NOT expose this file to version control
 */

// Hardcoded admin credentials
define('ADMIN_USERNAME', 'admin');
define('ADMIN_EMAIL', 'admin@cinelist.local');
define('ADMIN_PASSWORD', 'SecureAdminPass123!'); // Change this to a secure password

// Admin session timeout (in seconds) - 1 hour
define('ADMIN_SESSION_TIMEOUT', 3600);

// Admin cookie prefix
define('ADMIN_COOKIE_PREFIX', 'cinelisr_admin_');
