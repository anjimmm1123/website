<?php
// Set page title
$pageTitle = 'Program Studi - Admin Panel';

// Include admin configuration
require_once __DIR__ . '/config.php';

// Check if user is authenticated
AdminMiddleware::requireAuth();

// Create admin manager instance
$adminManager = new AdminManager($db, $currentAdminUser);

// Handle delete program
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $programId = (int)$_GET['delete'];
    
    // Delete program
    if ($adminManager->deleteProgram($programId)) {
        setFlashMessage('Program studi berhasil dihapus.', 'success');
    } else {
        setFlashMessage('Gagal menghapus program studi.', 'danger');
    }
    
    // Redirect to avoid resubmission
    header('Location: programs.php');
    exit;
}

// Get all programs
$programs = $adminManager->getAllPrograms();

// Include header
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Program Studi</h1>
    <div>
        <a href="program-add.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Tambah Program Studi
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($programs)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> Belum ada program studi. Klik tombol "Tambah Program Studi" untuk menambahkan.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="15%">Kode</th>
                        <th width="25%">Nama Program</th>
                        <th width="35%">Deskripsi</th>
                        <th width="10%">Status</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($programs as $program): ?>
                    <tr>
                        <td><?php echo $program['id']; ?></td>
                        <td><?php echo htmlspecialchars($program['code']); ?></td>
                        <td><?php echo htmlspecialchars($program['name']); ?></td>
                        <td>
                            <?php 
                            $description = htmlspecialchars($program['description']);
                            echo (strlen($description) > 100) ? substr($description, 0, 100) . '...' : $description;
                            ?>
                        </td>
                        <td>
                            <?php if ($program['is_active']): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="program-edit.php?id=<?php echo $program['id']; ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="programs.php?delete=<?php echo $program['id']; ?>" class="btn btn-sm btn-danger btn-delete" data-bs-toggle="tooltip" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
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

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>