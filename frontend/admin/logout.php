<?php
// Include admin configuration
require_once __DIR__ . '/config.php';

// Logout user
$adminAuth->logout();

// Redirect to login page
setFlashMessage('Anda berhasil logout.', 'success');
header('Location: login.php');
exit;
?>