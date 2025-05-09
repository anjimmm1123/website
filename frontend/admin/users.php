<?php
// Set page title
$pageTitle = 'Pengguna Admin - Admin Panel';

// Include admin configuration
require_once __DIR__ . '/config.php';

// Check if user is authenticated and has admin role
AdminMiddleware::requireAuth();
AdminMiddleware::requireRole(['admin']);

// Create admin manager instance
$adminManager = new AdminManager($db, $currentAdminUser);

// Handle delete user
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $userId = (int)$_GET['delete'];
    
    // Don't allow deleting self
    if ($currentAdminUser['id'] == $userId) {
        setFlashMessage('Anda tidak dapat menghapus akun Anda sendiri.', 'danger');
    } else {
        // Delete user
        if ($adminManager->deleteAdmin($userId)) {
            setFlashMessage('Pengguna berhasil dihapus.', 'success');
        } else {
            setFlashMessage('Gagal menghapus pengguna.', 'danger');
        }
    }
    
    // Redirect to avoid resubmission
    header('Location: users.php');
    exit;
}

// Get users
$users = $adminManager->getAllAdmins(true); // Include inactive users

// Include header
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Pengguna Admin</h1>
    <div>
        <a href="user-add.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Tambah Pengguna
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($users)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> Belum ada pengguna admin yang terdaftar.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Login Terakhir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email'] ?? '-'); ?></td>
                        <td>
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="badge bg-primary">Admin</span>
                            <?php elseif ($user['role'] === 'editor'): ?>
                                <span class="badge bg-info">Editor</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?php echo ucfirst($user['role']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['is_active']): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Tidak Aktif</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $user['last_login'] ? formatDate($user['last_login']) : 'Belum pernah login'; ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="user-edit.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <?php if ($currentAdminUser['id'] != $user['id']): ?>
                                <a href="users.php?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger btn-delete" data-bs-toggle="tooltip" title="Hapus" data-message="Apakah Anda yakin ingin menghapus pengguna '<?php echo htmlspecialchars($user['name']); ?>'?">
                                    <i class="fas fa-trash"></i>
                                </a>
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

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>