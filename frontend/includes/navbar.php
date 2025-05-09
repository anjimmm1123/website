<?php
// Get current page from URL parameter
$current_page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Define base URL
$base_url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$base_url .= "://" . $_SERVER['HTTP_HOST'];
$base_url .= dirname($_SERVER['PHP_SELF']);
$base_url = rtrim($base_url, '/');

// Get user role if logged in
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="?page=home">
            <i class="fas fa-shield-alt me-2"></i>STMIK Enterprise
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Main Navigation -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'home' ? 'active' : ''; ?>" href="?page=home">
                        <i class="fas fa-home me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'programs' ? 'active' : ''; ?>" href="?page=programs">
                        <i class="fas fa-graduation-cap me-1"></i> Program Studi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'gallery' ? 'active' : ''; ?>" href="?page=gallery">
                        <i class="fas fa-images me-1"></i> Gallery
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'about' ? 'active' : ''; ?>" href="?page=about">
                        <i class="fas fa-info-circle me-1"></i> Tentang Kami
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'contact' ? 'active' : ''; ?>" href="?page=contact">
                        <i class="fas fa-envelope me-1"></i> Kontak
                    </a>
                </li>
            </ul>
            
            <!-- User Navigation -->
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($user_role === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="frontend/admin/dashboard.php" target="_blank">
                                <i class="fas fa-user-shield me-1"></i> Admin Panel
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if ($user_role === 'student'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>" href="?page=dashboard">
                                <i class="fas fa-user-graduate me-1"></i> Data Mahasiswa
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> Akun Saya
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>" href="?page=dashboard">
                                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $current_page === 'profile' ? 'active' : ''; ?>" href="?page=profile">
                                    <i class="fas fa-user me-2"></i> Profil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="?page=logout">
                                    <i class="fas fa-sign-out-alt me-2"></i> Keluar
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'login' ? 'active' : ''; ?>" href="?page=login">
                            <i class="fas fa-sign-in-alt me-1"></i> Masuk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'register' ? 'active' : ''; ?>" href="?page=register">
                            <i class="fas fa-user-plus me-1"></i> Daftar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'application' ? 'active' : ''; ?> btn btn-outline-light btn-sm ms-2" href="?page=application">
                            <i class="fas fa-user-graduate me-1"></i> Pendaftaran
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>