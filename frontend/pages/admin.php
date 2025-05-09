<?php
$page_title = 'Admin Dashboard - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: /?page=login');
    exit();
}

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
$total_users = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM registrations");
$total_registrations = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM registrations WHERE status = 'pending'");
$pending_registrations = $stmt->fetchColumn();

// Get recent registrations
$stmt = $pdo->query("
    SELECT r.*, u.name as user_name, u.email 
    FROM registrations r 
    JOIN users u ON r.user_id = u.id 
    ORDER BY r.created_at DESC 
    LIMIT 5
");
$recent_registrations = $stmt->fetchAll();
?>

<!-- CSS -->
<link rel="stylesheet" href="/frontend/assets/css/common.css">

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="display-5 mb-4">Admin Dashboard</h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-muted">Total Pengguna</h5>
                    <h2 class="display-6 mb-0"><?= number_format($total_users) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-muted">Total Pendaftaran</h5>
                    <h2 class="display-6 mb-0"><?= number_format($total_registrations) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-muted">Pendaftaran Pending</h5>
                    <h2 class="display-6 mb-0"><?= number_format($pending_registrations) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Registrations -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Pendaftaran Terbaru</h5>
                    
                    <?php if (empty($recent_registrations)): ?>
                        <p class="text-muted">Belum ada pendaftaran</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Program Studi</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_registrations as $registration): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($registration['user_name']) ?></td>
                                            <td><?= htmlspecialchars($registration['email']) ?></td>
                                            <td><?= htmlspecialchars($registration['program']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $registration['status'] === 'pending' ? 'warning' : ($registration['status'] === 'approved' ? 'success' : 'danger') ?>">
                                                    <?= ucfirst($registration['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($registration['created_at'])) ?></td>
                                            <td>
                                                <a href="/?page=registration-details&id=<?= $registration['id'] ?>" class="btn btn-sm btn-primary">Detail</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 