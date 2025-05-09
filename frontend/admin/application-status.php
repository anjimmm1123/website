<?php
// Include admin configuration
require_once __DIR__ . '/config.php';

// Check if user is authenticated
AdminMiddleware::requireAuth();

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit('Method not allowed');
}

// Validate CSRF token
if (!AdminMiddleware::validateCsrfToken()) {
    setFlashMessage('Invalid form submission, please try again.', 'danger');
    header('Location: applications.php');
    exit;
}

// Check if required parameters are provided
if (!isset($_POST['application_id']) || !isset($_POST['status'])) {
    setFlashMessage('Parameter yang diperlukan tidak lengkap.', 'danger');
    header('Location: applications.php');
    exit;
}

// Get parameters
$applicationId = (int)$_POST['application_id'];
$status = AdminMiddleware::sanitizeInput($_POST['status']);
$notes = isset($_POST['notes']) ? AdminMiddleware::sanitizeInput($_POST['notes']) : '';

// Validate status
if (!in_array($status, ['pending', 'approved', 'rejected'])) {
    setFlashMessage('Status tidak valid.', 'danger');
    header('Location: applications.php');
    exit;
}

// Create admin manager instance
$adminManager = new AdminManager($db, $currentAdminUser);

// Update application status
if ($adminManager->updateApplicationStatus($applicationId, $status, $notes)) {
    // Log activity
    AdminMiddleware::logAction($db, "Updated application #$applicationId status to $status");
    
    // Set flash message
    $statusText = [
        'pending' => 'menunggu',
        'approved' => 'diterima',
        'rejected' => 'ditolak'
    ][$status];
    
    setFlashMessage("Status pendaftaran berhasil diubah menjadi $statusText.", 'success');
} else {
    setFlashMessage('Gagal memperbarui status pendaftaran.', 'danger');
}

// Redirect back
header('Location: applications.php');
exit;
?>