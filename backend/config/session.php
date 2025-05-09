<?php
// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
ini_set('session.gc_maxlifetime', 3600); // 1 hour
ini_set('session.cookie_lifetime', 3600); // 1 hour

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerate session ID periodically
if (!isset($_SESSION['last_regeneration']) || 
    time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Set default session variables if not set
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = null;
    $_SESSION['user_role'] = null;
    $_SESSION['user_name'] = null;
}

// Function to destroy session
function destroySession() {
    $_SESSION = array();
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}
?> 