<?php
/**
 * Session Management
 * Handles session initialization and security
 */

// Session security settings - MUST be set BEFORE session_start()
if (session_status() == PHP_SESSION_NONE) {
    // Configure session settings before starting
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    ini_set('session.cookie_lifetime', 0); // Session cookie expires when browser closes
    ini_set('session.gc_maxlifetime', 3600); // Session expires after 1 hour of inactivity

    // Start the session
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Check if user is client
 */
function isClient() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'client';
}

/**
 * Require login - redirect to login page if not logged in
 */
function requireLogin($redirect_to = 'login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirect_to");
        exit();
    }
}

/**
 * Require admin access
 */
function requireAdmin($redirect_to = '../index.php') {
    if (!isAdmin()) {
        header("Location: $redirect_to");
        exit();
    }
}

/**
 * Login user
 */
function loginUser($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['role'] = $user['role'];
}

/**
 * Logout user
 */
function logoutUser() {
    session_unset();
    session_destroy();
}

/**
 * Get current user info
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'full_name' => $_SESSION['full_name'],
        'role' => $_SESSION['role']
    ];
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
