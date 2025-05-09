<?php
// Set page title
$pageTitle = 'Dashboard - Admin Panel';

// Include admin configuration
require_once __DIR__ . '/config.php';

// Check if user is authenticated
AdminMiddleware::requireAuth();

// Create admin manager instance
$adminManager = new AdminManager($db, $currentAdminUser);

// Get dashboard statistics
$stats = $adminManager->getDashboardStats();

// Include header
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <div>
        <a href="settings.php" class="btn btn-outline-primary">
            <i class="fas fa-cog me-2"></i> Pengaturan
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title text-muted text-uppercase">Total Program Studi</h5>
                        <h2 class="display-5 fw-bold"><?php echo $stats['programs_count'] ?? 0; ?></h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
                <a href="programs.php" class="text-primary fw-bold mt-3 d-inline-block">Lihat Detail <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title text-muted text-uppercase">Total Galeri</h5>
                        <h2 class="display-5 fw-bold"><?php echo $stats['gallery_count'] ?? 0; ?></h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-images"></i>
                    </div>
                </div>
                <a href="gallery.php" class="text-info fw-bold mt-3 d-inline-block">Lihat Detail <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title text-muted text-uppercase">Total Halaman</h5>
                        <h2 class="display-5 fw-bold"><?php echo $stats['pages_count'] ?? 0; ?></h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
                <a href="pages.php" class="text-success fw-bold mt-3 d-inline-block">Lihat Detail <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title text-muted text-uppercase">Pendaftaran</h5>
                        <h2 class="display-5 fw-bold"><?php echo $stats['applications_count'] ?? 0; ?></h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                </div>
                <a href="applications.php" class="text-warning fw-bold mt-3 d-inline-block">Lihat Detail <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Applications by Status -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Status Pendaftaran</h5>
            </div>
            <div class="card-body">
                <?php if (isset($stats['applications_by_status']) && !empty($stats['applications_by_status'])): ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Menunggu</span>
                        <span class="text-muted"><?php echo $stats['applications_by_status']['pending'] ?? 0; ?></span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo ($stats['applications_count'] > 0) ? (($stats['applications_by_status']['pending'] ?? 0) / $stats['applications_count'] * 100) : 0; ?>%" aria-valuenow="<?php echo $stats['applications_by_status']['pending'] ?? 0; ?>" aria-valuemin="0" aria-valuemax="<?php echo $stats['applications_count']; ?>"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Diterima</span>
                        <span class="text-muted"><?php echo $stats['applications_by_status']['approved'] ?? 0; ?></span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo ($stats['applications_count'] > 0) ? (($stats['applications_by_status']['approved'] ?? 0) / $stats['applications_count'] * 100) : 0; ?>%" aria-valuenow="<?php echo $stats['applications_by_status']['approved'] ?? 0; ?>" aria-valuemin="0" aria-valuemax="<?php echo $stats['applications_count']; ?>"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Ditolak</span>
                        <span class="text-muted"><?php echo $stats['applications_by_status']['rejected'] ?? 0; ?></span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo ($stats['applications_count'] > 0) ? (($stats['applications_by_status']['rejected'] ?? 0) / $stats['applications_count'] * 100) : 0; ?>%" aria-valuenow="<?php echo $stats['applications_by_status']['rejected'] ?? 0; ?>" aria-valuemin="0" aria-valuemax="<?php echo $stats['applications_count']; ?>"></div>
                    </div>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p>Belum ada data pendaftaran</p>
                </div>
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <a href="applications.php" class="btn btn-sm btn-primary">Lihat Semua Pendaftaran</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Applications -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Pendaftaran Terbaru</h5>
            </div>
            <div class="card-body">
                <?php if (isset($stats['recent_applications']) && !empty($stats['recent_applications'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Program Studi</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats['recent_applications'] as $app): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($app['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($app['program_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php if ($app['application_status'] === 'pending'): ?>
                                        <span class="badge bg-warning">Menunggu</span>
                                    <?php elseif ($app['application_status'] === 'approved'): ?>
                                        <span class="badge bg-success">Diterima</span>
                                    <?php elseif ($app['application_status'] === 'rejected'): ?>
                                        <span class="badge bg-danger">Ditolak</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?php echo ucfirst($app['application_status']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo formatDate($app['created_at'], 'd M Y'); ?></td>
                                <td>
                                    <a href="application-view.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p>Belum ada data pendaftaran</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Aktivitas Terbaru</h5>
    </div>
    <div class="card-body">
        <?php if (isset($stats['recent_activities']) && !empty($stats['recent_activities'])): ?>
        <div class="activity-timeline">
            <?php foreach ($stats['recent_activities'] as $activity): ?>
            <div class="activity-item">
                <div class="activity-icon">
                    <?php
                    $icon = 'fas fa-info';
                    
                    if (strpos($activity['action'], 'login') !== false) {
                        $icon = 'fas fa-sign-in-alt';
                    } elseif (strpos($activity['action'], 'logout') !== false) {
                        $icon = 'fas fa-sign-out-alt';
                    } elseif (strpos($activity['action'], 'create') !== false) {
                        $icon = 'fas fa-plus';
                    } elseif (strpos($activity['action'], 'update') !== false) {
                        $icon = 'fas fa-edit';
                    } elseif (strpos($activity['action'], 'delete') !== false) {
                        $icon = 'fas fa-trash';
                    }
                    ?>
                    <i class="<?php echo $icon; ?>"></i>
                </div>
                <div class="activity-content">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-1"><?php echo htmlspecialchars($activity['username'] ?? 'Unknown'); ?></h6>
                        <span class="activity-time"><?php echo formatDate($activity['created_at']); ?></span>
                    </div>
                    <p class="mb-0"><?php echo htmlspecialchars($activity['description']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-history fa-3x text-muted mb-3"></i>
            <p>Belum ada aktivitas</p>
        </div>
        <?php endif; ?>
        
        <div class="text-center mt-3">
            <a href="logs.php" class="btn btn-sm btn-outline-primary">Lihat Semua Aktivitas</a>
        </div>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>