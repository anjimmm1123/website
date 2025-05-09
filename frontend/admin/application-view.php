<?php
// Set page title
$pageTitle = 'Detail Pendaftaran - Admin Panel';

// Include admin configuration
require_once __DIR__ . '/config.php';

// Check if user is authenticated
AdminMiddleware::requireAuth();

// Check if application ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    setFlashMessage('ID pendaftaran tidak valid.', 'danger');
    header('Location: applications.php');
    exit;
}

$applicationId = (int)$_GET['id'];

// Create admin manager instance
$adminManager = new AdminManager($db, $currentAdminUser);

// Get application data
$application = $adminManager->getApplicationById($applicationId);

if (!$application) {
    setFlashMessage('Pendaftaran tidak ditemukan.', 'danger');
    header('Location: applications.php');
    exit;
}

// Get program data
$program = null;
if (!empty($application['program_id'])) {
    $program = $adminManager->getProgramById($application['program_id']);
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    // Validate CSRF token
    if (!AdminMiddleware::validateCsrfToken()) {
        setFlashMessage('Invalid form submission, please try again.', 'danger');
    } else {
        $status = AdminMiddleware::sanitizeInput($_POST['status']);
        $notes = AdminMiddleware::sanitizeInput($_POST['notes']);
        
        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            // Update application status
            if ($adminManager->updateApplicationStatus($applicationId, $status, $notes)) {
                setFlashMessage('Status pendaftaran berhasil diperbarui.', 'success');
                
                // Refresh the page to get updated data
                header('Location: application-view.php?id=' . $applicationId);
                exit;
            } else {
                setFlashMessage('Gagal memperbarui status pendaftaran.', 'danger');
            }
        } else {
            setFlashMessage('Status tidak valid.', 'danger');
        }
    }
}

// Include header
include __DIR__ . '/includes/header.php';

// Function to format application status
function formatApplicationStatus($status) {
    switch ($status) {
        case 'pending':
            return '<span class="badge bg-warning">Menunggu</span>';
        case 'approved':
            return '<span class="badge bg-success">Diterima</span>';
        case 'rejected':
            return '<span class="badge bg-danger">Ditolak</span>';
        default:
            return '<span class="badge bg-secondary">' . ucfirst($status) . '</span>';
    }
}
?>

<div class="page-header">
    <h1 class="page-title">Detail Pendaftaran</h1>
    <div>
        <a href="applications.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
        
        <?php if ($application['application_status'] === 'pending'): ?>
        <div class="btn-group ms-2">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#statusModal" data-status="approved">
                <i class="fas fa-check me-2"></i> Terima
            </button>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#statusModal" data-status="rejected">
                <i class="fas fa-times me-2"></i> Tolak
            </button>
        </div>
        <?php endif; ?>
        
        <a href="export-application.php?id=<?php echo $applicationId; ?>" class="btn btn-info ms-2">
            <i class="fas fa-file-pdf me-2"></i> Export PDF
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Personal Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Informasi Pribadi</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Nama Lengkap:</div>
                    <div class="col-md-9"><?php echo htmlspecialchars($application['full_name']); ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Email:</div>
                    <div class="col-md-9"><?php echo htmlspecialchars($application['email']); ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">No. Telepon:</div>
                    <div class="col-md-9"><?php echo htmlspecialchars($application['phone']); ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Alamat:</div>
                    <div class="col-md-9"><?php echo nl2br(htmlspecialchars($application['address'])); ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Tempat Lahir:</div>
                    <div class="col-md-9"><?php echo htmlspecialchars($application['birth_place']); ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Tanggal Lahir:</div>
                    <div class="col-md-9"><?php echo formatDate($application['birth_date'], 'd F Y'); ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Jenis Kelamin:</div>
                    <div class="col-md-9">
                        <?php echo $application['gender'] === 'M' ? 'Laki-laki' : 'Perempuan'; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Educational Background -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Pendidikan Terakhir</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Jenjang:</div>
                    <div class="col-md-9"><?php echo htmlspecialchars($application['last_education']); ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Nama Sekolah:</div>
                    <div class="col-md-9"><?php echo htmlspecialchars($application['school_name']); ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Tahun Lulus:</div>
                    <div class="col-md-9"><?php echo htmlspecialchars($application['graduation_year']); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Program Selection -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Program Studi</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Program Studi:</div>
                    <div class="col-md-9">
                        <?php if ($program): ?>
                            <span class="badge bg-primary me-2"><?php echo htmlspecialchars($program['code']); ?></span>
                            <?php echo htmlspecialchars($program['name']); ?>
                        <?php else: ?>
                            <span class="text-muted">Tidak ada</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Documents -->
        <?php if (!empty($application['documents_path'])): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Dokumen</h5>
            </div>
            <div class="card-body">
                <?php 
                $documents = json_decode($application['documents_path'], true);
                if ($documents && is_array($documents)):
                ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Jenis Dokumen</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($documents as $docType => $docPath): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $docType))); ?></td>
                                <td>
                                    <a href="<?php echo htmlspecialchars($docPath); ?>" class="btn btn-sm btn-primary" target="_blank">
                                        <i class="fas fa-file-download me-2"></i> Lihat Dokumen
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Format data dokumen tidak valid.
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Status Pendaftaran</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div style="font-size: 1.5rem;">
                        <?php echo formatApplicationStatus($application['application_status']); ?>
                    </div>
                    <div class="mt-2 text-muted">
                        <small>Terakhir diperbarui: <?php echo formatDate($application['updated_at']); ?></small>
                    </div>
                </div>
                
                <?php if (!empty($application['admin_notes'])): ?>
                <div class="mt-3">
                    <label class="form-label fw-bold">Catatan Admin:</label>
                    <div class="border rounded p-3 bg-light">
                        <?php echo nl2br(htmlspecialchars($application['admin_notes'])); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($application['application_status'] !== 'pending'): ?>
                <div class="mt-3">
                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#statusModal">
                        <i class="fas fa-edit me-2"></i> Ubah Status
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Application Timeline -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Timeline</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Pendaftaran Dibuat</h6>
                            <small><?php echo formatDate($application['created_at'], 'd M Y'); ?></small>
                        </div>
                        <p class="mb-1"><small>Pendaftaran mahasiswa baru berhasil dibuat.</small></p>
                    </div>
                    
                    <?php if ($application['application_status'] !== 'pending'): ?>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Status Diperbarui</h6>
                            <small><?php echo formatDate($application['updated_at'], 'd M Y'); ?></small>
                        </div>
                        <p class="mb-1">
                            <small>
                                Status pendaftaran diubah menjadi 
                                <?php 
                                    switch ($application['application_status']) {
                                        case 'approved':
                                            echo '<span class="text-success">Diterima</span>';
                                            break;
                                        case 'rejected':
                                            echo '<span class="text-danger">Ditolak</span>';
                                            break;
                                        default:
                                            echo ucfirst($application['application_status']);
                                    }
                                ?>.
                            </small>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                <input type="hidden" name="update_status" value="1">
                <input type="hidden" name="status" id="modalStatus" value="pending">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Ubah Status Pendaftaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="statusSelect" class="form-label">Status</label>
                        <select class="form-select" id="statusSelect" name="status">
                            <option value="pending" <?php echo $application['application_status'] === 'pending' ? 'selected' : ''; ?>>Menunggu</option>
                            <option value="approved" <?php echo $application['application_status'] === 'approved' ? 'selected' : ''; ?>>Diterima</option>
                            <option value="rejected" <?php echo $application['application_status'] === 'rejected' ? 'selected' : ''; ?>>Ditolak</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea class="form-control" id="notes" name="notes" rows="4"><?php echo htmlspecialchars($application['admin_notes'] ?? ''); ?></textarea>
                        <div class="form-text">Catatan ini akan disimpan di database dan dapat ditampilkan kepada pendaftar.</div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update modal status when opened
    const statusModal = document.getElementById('statusModal');
    const modalStatus = document.getElementById('modalStatus');
    const statusSelect = document.getElementById('statusSelect');
    
    statusModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        if (button && button.dataset.status) {
            statusSelect.value = button.dataset.status;
        }
    });
    
    // Sync hidden status input with select
    statusSelect.addEventListener('change', function() {
        modalStatus.value = this.value;
    });
});
</script>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>