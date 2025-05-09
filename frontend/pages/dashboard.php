<?php
$page_title = 'Dashboard - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../backend/auth/Auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /?page=login');
    exit;
}

$auth = new Auth();
?>

<!-- CSS -->
<link rel="stylesheet" href="/frontend/assets/css/common.css">
<link rel="stylesheet" href="/frontend/assets/css/dashboard.css">

<!-- Main Content -->
<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="/frontend/assets/images/default-avatar.png" alt="Profile" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                        <h5 class="mb-1"><?php echo htmlspecialchars($_SESSION['full_name']); ?></h5>
                        <p class="text-muted mb-0"><?php echo htmlspecialchars($_SESSION['role']); ?></p>
                    </div>
                    
                    <div class="list-group">
                        <a href="/?page=dashboard" class="list-group-item list-group-item-action active">
                            <i class="fas fa-home me-2"></i> Dashboard
                        </a>
                        <a href="/?page=profile" class="list-group-item list-group-item-action">
                            <i class="fas fa-user me-2"></i> Profil
                        </a>
                        <a href="/?page=settings" class="list-group-item list-group-item-action">
                            <i class="fas fa-cog me-2"></i> Pengaturan
                        </a>
                        <a href="/?page=logout" class="list-group-item list-group-item-action text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">Selamat Datang, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h4>
                    
                    <?php if (isset($_SESSION['flash_message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['flash_message']['type']; ?>">
                            <?php 
                            echo $_SESSION['flash_message']['message'];
                            unset($_SESSION['flash_message']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="row g-4">
                        <!-- Quick Stats -->
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Status Akun</h5>
                                    <p class="card-text">Aktif</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Terakhir Login</h5>
                                    <p class="card-text"><?php echo date('d M Y H:i'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Role</h5>
                                    <p class="card-text"><?php echo ucfirst(htmlspecialchars($_SESSION['role'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="mt-4">
                        <h5>Aktivitas Terakhir</h5>
                        <div class="list-group">
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Login Berhasil</h6>
                                    <small class="text-muted">Baru saja</small>
                                </div>
                                <p class="mb-1">Anda telah berhasil login ke sistem.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 