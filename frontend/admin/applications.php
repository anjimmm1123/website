<?php
// Set page title
$pageTitle = 'Pendaftaran Mahasiswa - Admin Panel';

// Include admin configuration
require_once __DIR__ . '/config.php';

// Check if user is authenticated
AdminMiddleware::requireAuth();

// Create admin manager instance
$adminManager = new AdminManager($db, $currentAdminUser);

// Get status filter
$statusFilter = isset($_GET['status']) ? AdminMiddleware::sanitizeInput($_GET['status']) : '';

// Get applications
$applications = $adminManager->getAllApplications($statusFilter);

// Get total counts for each status
try {
    $stmt = $db->query("SELECT application_status, COUNT(*) as count FROM student_applications GROUP BY application_status");
    $statusCounts = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $statusCounts[$row['application_status']] = $row['count'];
    }
    
    $totalApplications = array_sum($statusCounts);
} catch (PDOException $e) {
    $statusCounts = [];
    $totalApplications = count($applications);
}

// Include header
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Pendaftaran Mahasiswa Baru</h1>
    <div>
        <a href="export-applications.php" class="btn btn-success">
            <i class="fas fa-file-excel me-2"></i> Export Excel
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card card-lift text-center">
            <div class="card-body">
                <h5 class="card-title">Total Pendaftaran</h5>
                <h2 class="text-primary"><?php echo $totalApplications; ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card card-lift text-center">
            <div class="card-body">
                <h5 class="card-title">Menunggu</h5>
                <h2 class="text-warning"><?php echo $statusCounts['pending'] ?? 0; ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card card-lift text-center">
            <div class="card-body">
                <h5 class="card-title">Diterima</h5>
                <h2 class="text-success"><?php echo $statusCounts['approved'] ?? 0; ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card card-lift text-center">
            <div class="card-body">
                <h5 class="card-title">Ditolak</h5>
                <h2 class="text-danger"><?php echo $statusCounts['rejected'] ?? 0; ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link <?php echo empty($statusFilter) ? 'active' : ''; ?>" href="applications.php">
                    Semua
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $statusFilter === 'pending' ? 'active' : ''; ?>" href="applications.php?status=pending">
                    Menunggu
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $statusFilter === 'approved' ? 'active' : ''; ?>" href="applications.php?status=approved">
                    Diterima
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $statusFilter === 'rejected' ? 'active' : ''; ?>" href="applications.php?status=rejected">
                    Ditolak
                </a>
            </li>
        </ul>
    </div>
    
    <div class="card-body">
        <?php if (empty($applications)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> Belum ada pendaftaran mahasiswa baru yang <?php echo !empty($statusFilter) ? "berstatus " . $statusFilter : "terdaftar"; ?>.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>No. Telepon</th>
                        <th>Program Studi</th>
                        <th>Status</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                    <tr>
                        <td><?php echo $app['id']; ?></td>
                        <td><?php echo htmlspecialchars($app['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($app['email']); ?></td>
                        <td><?php echo htmlspecialchars($app['phone']); ?></td>
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
                        <td><?php echo formatDate($app['created_at']); ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="application-view.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <?php if ($app['application_status'] === 'pending'): ?>
                                <button type="button" class="btn btn-sm btn-success update-status-btn" data-bs-toggle="modal" data-bs-target="#updateStatusModal" data-id="<?php echo $app['id']; ?>" data-status="approved" data-name="<?php echo htmlspecialchars($app['full_name']); ?>" data-bs-toggle="tooltip" title="Terima">
                                    <i class="fas fa-check"></i>
                                </button>
                                
                                <button type="button" class="btn btn-sm btn-danger update-status-btn" data-bs-toggle="modal" data-bs-target="#updateStatusModal" data-id="<?php echo $app['id']; ?>" data-status="rejected" data-name="<?php echo htmlspecialchars($app['full_name']); ?>" data-bs-toggle="tooltip" title="Tolak">
                                    <i class="fas fa-times"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="application-status.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                <input type="hidden" name="application_id" id="modalApplicationId">
                <input type="hidden" name="status" id="modalStatus">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">Konfirmasi Perubahan Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <p id="modalMessage"></p>
                    
                    <div class="form-group">
                        <label for="notes" class="form-label">Catatan (opsional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        <div class="form-text">Catatan ini akan disimpan di database dan dapat ditampilkan kepada pendaftar.</div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn" id="modalSubmitBtn">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update Status Modal
    const updateStatusBtns = document.querySelectorAll('.update-status-btn');
    const modalApplicationId = document.getElementById('modalApplicationId');
    const modalStatus = document.getElementById('modalStatus');
    const modalMessage = document.getElementById('modalMessage');
    const modalSubmitBtn = document.getElementById('modalSubmitBtn');
    
    updateStatusBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const applicationId = this.dataset.id;
            const status = this.dataset.status;
            const name = this.dataset.name;
            
            modalApplicationId.value = applicationId;
            modalStatus.value = status;
            
            if (status === 'approved') {
                modalMessage.innerHTML = `Apakah Anda yakin ingin <strong>menerima</strong> pendaftaran dari <strong>${name}</strong>?`;
                modalSubmitBtn.className = 'btn btn-success';
                modalSubmitBtn.innerHTML = '<i class="fas fa-check me-2"></i> Terima';
            } else if (status === 'rejected') {
                modalMessage.innerHTML = `Apakah Anda yakin ingin <strong>menolak</strong> pendaftaran dari <strong>${name}</strong>?`;
                modalSubmitBtn.className = 'btn btn-danger';
                modalSubmitBtn.innerHTML = '<i class="fas fa-times me-2"></i> Tolak';
            }
        });
    });
});
</script>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>