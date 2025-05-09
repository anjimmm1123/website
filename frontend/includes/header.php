<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo defined('APP_NAME') ? APP_NAME : 'STMIK Enterprise'; ?> - Kampus Teknologi Informasi Terkemuka</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>frontend/assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS - Animate On Scroll Library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    
    <!-- Swiper for carousels -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
    
    <!-- Base CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>frontend/assets/css/common.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>frontend/assets/css/animations.css">
    
    <!-- Page Specific CSS -->
    <?php
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 'home';
    if (file_exists(ROOT_PATH . '/frontend/assets/css/' . $currentPage . '.css')) {
        echo '<link rel="stylesheet" href="' . BASE_URL . 'frontend/assets/css/' . $currentPage . '.css">';
    }
    ?>
    
    <!-- Particles.js for interactive backgrounds -->
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js" defer></script>
    
    <!-- Preload essential scripts -->
    <link rel="preload" href="<?php echo BASE_URL; ?>frontend/assets/js/animations.js" as="script">
    <link rel="preload" href="<?php echo BASE_URL; ?>frontend/assets/js/main.js" as="script">
</head>
<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="spinner"></div>
    </div>
    
    <!-- Back to top button -->
    <a href="#" class="back-to-top" aria-label="Kembali ke atas">
        <i class="fas fa-arrow-up"></i>
    </a>
    
    <!-- Toast container for notifications -->
    <div class="toast-container"></div>
    
    <!-- Main Header -->
    <header class="main-header">
        <nav class="navbar">
            <div class="container-custom">
                <div class="navbar-content">
                    <a href="<?php echo BASE_URL; ?>" class="navbar-brand">
                        <img src="<?php echo BASE_URL; ?>frontend/assets/images/logo.png" alt="<?php echo defined('APP_NAME') ? APP_NAME : 'STMIK Enterprise'; ?>" class="logo">
                    </a>
                    
                    <div class="nav-menu">
                        <ul class="nav-list">
                            <li class="nav-item <?php echo ($currentPage == 'home') ? 'active' : ''; ?>">
                                <a href="<?php echo BASE_URL; ?>" class="nav-link">Beranda</a>
                            </li>
                            <li class="nav-item <?php echo ($currentPage == 'about') ? 'active' : ''; ?>">
                                <a href="<?php echo BASE_URL; ?>?page=about" class="nav-link">Tentang Kami</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle">Program <i class="fas fa-chevron-down"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo BASE_URL; ?>?page=programs&id=1" class="dropdown-item">Sistem Informasi</a></li>
                                    <li><a href="<?php echo BASE_URL; ?>?page=programs&id=2" class="dropdown-item">Teknik Informatika</a></li>
                                    <li><a href="<?php echo BASE_URL; ?>?page=programs&id=3" class="dropdown-item">Manajemen Informatika</a></li>
                                </ul>
                            </li>
                            <li class="nav-item <?php echo ($currentPage == 'admission') ? 'active' : ''; ?>">
                                <a href="<?php echo BASE_URL; ?>?page=admission" class="nav-link">Pendaftaran</a>
                            </li>
                            <li class="nav-item <?php echo ($currentPage == 'gallery') ? 'active' : ''; ?>">
                                <a href="<?php echo BASE_URL; ?>?page=gallery" class="nav-link">Galeri</a>
                            </li>
                            <li class="nav-item <?php echo ($currentPage == 'contact') ? 'active' : ''; ?>">
                                <a href="<?php echo BASE_URL; ?>?page=contact" class="nav-link">Kontak</a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="nav-actions">
                        <a href="#" class="search-toggle" aria-label="Cari">
                            <i class="fas fa-search"></i>
                        </a>
                        
                        <div class="search-box">
                            <form action="<?php echo BASE_URL; ?>?page=search" method="GET">
                                <input type="hidden" name="page" value="search">
                                <input type="text" name="q" placeholder="Cari..." required>
                                <button type="submit" aria-label="Cari">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="user-dropdown">
                                <a href="#" class="user-dropdown-toggle">
                                    <i class="fas fa-user-circle"></i>
                                </a>
                                <div class="user-dropdown-menu">
                                    <a href="<?php echo BASE_URL; ?>?page=profile" class="dropdown-item">
                                        <i class="fas fa-user"></i> Profil
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>?page=dashboard" class="dropdown-item">
                                        <i class="fas fa-tachometer-alt"></i> Dashboard
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>?page=logout" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="auth-buttons">
                                <a href="<?php echo BASE_URL; ?>?page=login" class="btn-outline-custom btn-sm">Masuk</a>
                                <a href="<?php echo BASE_URL; ?>?page=register" class="btn-primary-custom btn-sm">Daftar</a>
                            </div>
                        <?php endif; ?>
                        
                        <button class="hamburger-menu" aria-label="Menu">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Mobile Menu -->
        <div class="mobile-menu">
            <ul class="mobile-nav-list">
                <li class="mobile-nav-item <?php echo ($currentPage == 'home') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>" class="mobile-nav-link">Beranda</a>
                </li>
                <li class="mobile-nav-item <?php echo ($currentPage == 'about') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>?page=about" class="mobile-nav-link">Tentang Kami</a>
                </li>
                <li class="mobile-nav-item mobile-dropdown">
                    <a href="#" class="mobile-nav-link mobile-dropdown-toggle">
                        Program <i class="fas fa-chevron-down"></i>
                    </a>
                    <ul class="mobile-dropdown-menu">
                        <li><a href="<?php echo BASE_URL; ?>?page=programs&id=1" class="mobile-dropdown-item">Sistem Informasi</a></li>
                        <li><a href="<?php echo BASE_URL; ?>?page=programs&id=2" class="mobile-dropdown-item">Teknik Informatika</a></li>
                        <li><a href="<?php echo BASE_URL; ?>?page=programs&id=3" class="mobile-dropdown-item">Manajemen Informatika</a></li>
                    </ul>
                </li>
                <li class="mobile-nav-item <?php echo ($currentPage == 'admission') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>?page=admission" class="mobile-nav-link">Pendaftaran</a>
                </li>
                <li class="mobile-nav-item <?php echo ($currentPage == 'gallery') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>?page=gallery" class="mobile-nav-link">Galeri</a>
                </li>
                <li class="mobile-nav-item <?php echo ($currentPage == 'contact') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>?page=contact" class="mobile-nav-link">Kontak</a>
                </li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="mobile-nav-item">
                        <a href="<?php echo BASE_URL; ?>?page=profile" class="mobile-nav-link">Profil</a>
                    </li>
                    <li class="mobile-nav-item">
                        <a href="<?php echo BASE_URL; ?>?page=dashboard" class="mobile-nav-link">Dashboard</a>
                    </li>
                    <li class="mobile-nav-item">
                        <a href="<?php echo BASE_URL; ?>?page=logout" class="mobile-nav-link">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="mobile-nav-item">
                        <a href="<?php echo BASE_URL; ?>?page=login" class="mobile-nav-link">Masuk</a>
                    </li>
                    <li class="mobile-nav-item">
                        <a href="<?php echo BASE_URL; ?>?page=register" class="mobile-nav-link">Daftar</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </header>
    
    <!-- Main Content Container -->
    <main id="main-content">