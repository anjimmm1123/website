<?php
require_once __DIR__ . '/../../backend/auth/Auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session data
session_unset();
session_destroy();

// Clear remember me cookie if exists
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Set flash message
session_start();
$_SESSION['flash_message'] = [
    'type' => 'success',
    'message' => 'Anda telah berhasil keluar dari sistem.'
];

// Redirect to login page
header('Location: /?page=login');
exit; 