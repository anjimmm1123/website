<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Panel - STMIK Enterprise'; ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    
    <!-- Admin CSS -->
    <link rel="stylesheet" href="assets/css/admin.css">
    
    <!-- Page specific CSS -->
    <?php if (isset($pageStylesheets) && is_array($pageStylesheets)): ?>
        <?php foreach ($pageStylesheets as $stylesheet): ?>
            <link rel="stylesheet" href="<?php echo $stylesheet; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="d-flex align-items-center text-decoration-none">
                <i class="fas fa-shield-alt fs-4 me-2"></i>
                <span class="fs-4">Admin Panel</span>
            </a>
            <button class="btn btn-sm sidebar-toggler d-md-none" id="sidebarToggler">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <div class="sidebar-user">
            <div class="d-flex align-items-center">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-info">
                    <h6 class="m-0"><?php echo $currentAdminUser['name'] ?? 'Admin User'; ?></h6>
                    <span class="user-role"><?php echo ucfirst($currentAdminUser['role'] ?? 'admin'); ?></span>
                </div>
            </div>
        </div>
        
        <ul class="sidebar-nav">
            <li class="sidebar-item">
                <a href="dashboard.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li class="sidebar-item">
                <a href="pages.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'pages.php' || basename($_SERVER['PHP_SELF']) === 'page-edit.php' || basename($_SERVER['PHP_SELF']) === 'page-add.php' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt"></i>
                    <span>Halaman</span>
                </a>
            </li>
            
            <li class="sidebar-item">
                <a href="programs.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'programs.php' || basename($_SERVER['PHP_SELF']) === 'program-edit.php' || basename($_SERVER['PHP_SELF']) === 'program-add.php' ? 'active' : ''; ?>">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Program Studi</span>
                </a>
            </li>
            
            <li class="sidebar-item">
                <a href="gallery.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'gallery.php' || basename($_SERVER['PHP_SELF']) === 'gallery-edit.php' || basename($_SERVER['PHP_SELF']) === 'gallery-add.php' ? 'active' : ''; ?>">
                    <i class="fas fa-images"></i>
                    <span>Galeri</span>
                </a>
            </li>
            
            <li class="sidebar-item">
                <a href="applications.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'applications.php' || basename($_SERVER['PHP_SELF']) === 'application-view.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user-graduate"></i>
                    <span>Pendaftaran</span>
                </a>
            </li>
            
            <?php if ($currentAdminUser && $currentAdminUser['role'] === 'admin'): ?>
            <li class="sidebar-header">Admin</li>
            
            <li class="sidebar-item">
                <a href="users.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' || basename($_SERVER['PHP_SELF']) === 'user-edit.php' || basename($_SERVER['PHP_SELF']) === 'user-add.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Pengguna</span>
                </a>
            </li>
            
            <li class="sidebar-item">
                <a href="settings.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan</span>
                </a>
            </li>
            
            <li class="sidebar-item">
                <a href="logs.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'logs.php' ? 'active' : ''; ?>">
                    <i class="fas fa-list"></i>
                    <span>Log Aktivitas</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
        
        <div class="sidebar-footer">
            <a href="logout.php" class="btn btn-danger w-100">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar navbar-expand navbar-light navbar-bg">
            <a class="sidebar-toggle d-flex" href="#">
                <i class="hamburger align-self-center"></i>
            </a>
            
            <div class="navbar-collapse collapse">
                <ul class="navbar-nav navbar-align">
                    <li class="nav-item dropdown">
                        <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-cog"></i>
                        </a>
                        
                        <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <span><?php echo $currentAdminUser['name'] ?? 'Admin User'; ?></span>
                        </a>
                        
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user me-2"></i> Profil
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="../index.php" target="_blank">
                                <i class="fas fa-home me-2"></i> Website
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        
        <div class="content">
            <div class="container-fluid">
                <?php if (isset($flashMessage)): ?>
                <div class="alert alert-<?php echo $flashMessage['type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo $flashMessage['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Content starts here -->