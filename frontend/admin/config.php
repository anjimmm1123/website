<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define constants
define('ADMIN_ROOT', __DIR__);
define('SITE_ROOT', dirname(__DIR__, 2));
define('UPLOAD_DIR', '../uploads/');
define('ADMIN_URL', dirname($_SERVER['PHP_SELF']) . '/');

// Include database configuration
require_once SITE_ROOT . '/backend/config/database.php';

// Include necessary classes
require_once SITE_ROOT . '/backend/admin/AdminAuth.php';
require_once SITE_ROOT . '/backend/admin/AdminManager.php';
require_once SITE_ROOT . '/backend/admin/FileUploader.php';

// Create AuthAdmin instance
$adminAuth = new AdminAuth($db);

// Check if admin is logged in
if (!$adminAuth->isLoggedIn()) {
    // Only check if not on login page or logout page
    $current_file = basename($_SERVER['PHP_SELF']);
    if ($current_file !== 'login.php' && $current_file !== 'logout.php') {
        header('Location: login.php');
        exit;
    }
} else {
    // Get current admin user
    $currentAdminUser = $adminAuth->getCurrentAdmin();
    
    // Check if admin is still active
    if (!$currentAdminUser || !$currentAdminUser['is_active']) {
        $adminAuth->logout();
        setFlashMessage('Your account has been deactivated. Please contact the administrator.', 'danger');
        header('Location: login.php');
        exit;
    }
}

// Generate CSRF token for forms
$csrfToken = AdminMiddleware::generateCsrfToken();

// Get page title if not set
if (!isset($pageTitle)) {
    $pageTitle = 'Admin Panel - STMIK Enterprise';
}

// If there is a flash message, retrieve it
$flashMessage = null;
if (isset($_SESSION['flash_message'])) {
    $flashMessage = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
?>