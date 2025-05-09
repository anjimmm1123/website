<?php
// Start session
session_start();

// Include configuration
require_once 'backend/config/config.php';

// Include database connection
require_once 'backend/config/database.php';

// Include utility functions
require_once 'backend/functions.php';

// Define default settings (fallback if settings table doesn't exist yet)
$settings = [
    'site_name' => 'STMIK Enterprise',
    'site_description' => 'Lembaga Pendidikan Tinggi Teknologi Informasi Terkemuka',
    'site_logo' => 'frontend/assets/images/logo.png',
    'site_email' => 'info@stmikenterprise.ac.id',
    'site_phone' => '+62 123 456 7890',
    'site_address' => 'Jl. Pendidikan No. 123, Jakarta Selatan, Indonesia',
    'site_facebook' => 'https://facebook.com/stmikenterprise',
    'site_instagram' => 'https://instagram.com/stmikenterprise',
    'site_twitter' => 'https://twitter.com/stmikenterprise',
    'site_youtube' => 'https://youtube.com/stmikenterprise',
];

// Try to get site settings from database if table exists
try {
    $stmt = $db->query("SELECT key, value FROM settings");
    $dbSettings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (isset($row['key']) && isset($row['value'])) {
            $dbSettings[$row['key']] = $row['value'];
        }
    }
    
    if (!empty($dbSettings)) {
        $settings = array_merge($settings, $dbSettings);
    }
} catch (PDOException $e) {
    // Silently ignore if settings table doesn't exist yet
    error_log('Error retrieving settings: ' . $e->getMessage());
}

// Get page from URL, default to home
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Define allowed pages and their titles
$allowedPages = [
    'home' => 'Beranda',
    'about' => 'Tentang Kami',
    'programs' => 'Program Studi',
    'program-detail' => 'Detail Program Studi',
    'gallery' => 'Galeri',
    'contact' => 'Kontak',
    'application' => 'Pendaftaran',
    'news' => 'Berita',
    'news-detail' => 'Detail Berita',
    'login' => 'Masuk',
    'register' => 'Daftar',
    'dashboard' => 'Dashboard',
    'profile' => 'Profil',
    'logout' => 'Keluar',
    '404' => 'Halaman Tidak Ditemukan',
];

// Check if page exists, else show 404
if (!array_key_exists($page, $allowedPages)) {
    $page = '404';
}

// Set page title
$pageTitle = $allowedPages[$page] . ' - ' . $settings['site_name'];

// Special handling for logout
if ($page === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit();
}

// Include header
include 'frontend/includes/header.php';

// Include appropriate page
$pagePath = 'frontend/pages/' . $page . '.php';
if (file_exists($pagePath)) {
    include $pagePath;
} else {
    include 'frontend/pages/404.php';
}

// Include footer
include 'frontend/includes/footer.php';
?>