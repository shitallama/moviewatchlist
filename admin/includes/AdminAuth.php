<?php
/**
 * AdminAuth Class
 * Handles admin authentication and session management
 */

class AdminAuth {
    
    /**
     * Authenticate admin credentials
     * @param string $username Admin username
     * @param string $password Admin password
     * @return bool True if credentials are valid, false otherwise
     */
    public static function authenticate($username, $password) {
        require_once __DIR__ . '/admin_config.php';
        
        return $username === ADMIN_USERNAME && $password === ADMIN_PASSWORD;
    }
    
    /**
     * Set admin session
     * @param string $username Admin username
     */
    public static function setSession($username) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_login_time'] = time();
    }
    
    /**
     * Check if admin is logged in and session is valid
     * @return bool True if admin is logged in with valid session, false otherwise
     */
    public static function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if admin session exists
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            return false;
        }
        
        // Check session timeout
        require_once __DIR__ . '/admin_config.php';
        if (isset($_SESSION['admin_login_time'])) {
            if (time() - $_SESSION['admin_login_time'] > ADMIN_SESSION_TIMEOUT) {
                self::logout();
                return false;
            }
            // Refresh the login time to prevent timeout during active session
            $_SESSION['admin_login_time'] = time();
            return true;
        }
        
        return false;
    }
    
    /**
     * Get admin username from session
     * @return string|null Admin username or null if not logged in
     */
    public static function getUsername() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return $_SESSION['admin_username'] ?? null;
    }
    
    /**
     * Logout admin
     */
    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_login_time']);
    }
    
    /**
     * Redirect to login if not authenticated
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ' . dirname(__DIR__) . '/admin/login.php');
            exit;
        }
    }
}
