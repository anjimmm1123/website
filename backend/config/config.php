<?php
// Application settings
define('APP_NAME', 'STMIK Enterprise');
define('APP_URL', 'http://localhost/bima.com');
define('APP_VERSION', '1.0.0');
define('BASE_URL', '/');
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);

// Debug mode
define('DEBUG_MODE', true);

// File upload settings
define('UPLOAD_DIR', __DIR__ . '/../../frontend/assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// Session settings
define('SESSION_LIFETIME', 3600); // 1 hour

// Only set session parameters if session hasn't started yet
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    session_set_cookie_params(SESSION_LIFETIME);
}

// Error reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Time zone
date_default_timezone_set('Asia/Jakarta');

// Security
define('HASH_COST', 12); // For password hashing
define('TOKEN_LIFETIME', 3600); // 1 hour for tokens

// Email settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', 'noreply@stmik-enterprise.ac.id');
define('SMTP_FROM_NAME', APP_NAME);

// API settings
define('API_KEY', 'your-api-key-here');
define('API_SECRET', 'your-api-secret-here');

// Cache settings
define('CACHE_ENABLED', true);
define('CACHE_DIR', __DIR__ . '/../../cache/');
define('CACHE_LIFETIME', 3600); // 1 hour

// Logging
define('LOG_DIR', __DIR__ . '/../../logs/');
define('LOG_LEVEL', 'debug'); // debug, info, warning, error

// Create required directories if they don't exist
$directories = [
    UPLOAD_DIR,
    CACHE_DIR,
    LOG_DIR
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}
?>