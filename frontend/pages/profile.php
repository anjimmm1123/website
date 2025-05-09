<?php
$page_title = 'Profil Saya - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../backend/auth/Auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /?page=login');
    exit;
}

$auth = new Auth();
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $full_name = $_POST['full_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Update profile information
        if (!empty($full_name) || !empty($email)) {
            $result = $auth->updateProfile($_SESSION['user_id'], [
                'full_name' => $full_name,
                'email' => $email
            ]);

            if ($result['success']) {
                $_SESSION['full_name'] = $full_name;
                $_SESSION['email'] = $email;
                $success = 'Profil berhasil diperbarui.';
            } else {
                throw new Exception($result['message']);
            }
        }

        // Update password if provided
        if (!empty($current_password) && !empty($new_password)) {
            if ($new_password !== $confirm_password) {
                throw new Exception('Password baru dan konfirmasi password tidak cocok.');
            }

            $result = $auth->updatePassword($_SESSION['user_id'], $current_password, $new_password);
            if ($result['success']) {
                $success = 'Password berhasil diperbarui.';
            } else {
                throw new Exception($result['message']);
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
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
                        <a href="/?page=dashboard" class="list-group-item list-group-item-action">
                            <i class="fas fa-home me-2"></i> Dashboard
                        </a>
                        <a href="/?page=profile" class="list-group-item list-group-item-action active">
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
                    <h4 class="card-title mb-4">Profil Saya</h4>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <!-- Profile Information -->
                        <div class="mb-4">
                            <h5>Informasi Profil</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="full_name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($_SESSION['full_name']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="role" class="form-label">Role</label>
                                    <input type="text" class="form-control" id="role" value="<?php echo ucfirst(htmlspecialchars($_SESSION['role'])); ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Change Password -->
                        <div class="mb-4">
                            <h5>Ubah Password</h5>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="current_password" class="form-label">Password Saat Ini</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password">
                                </div>
                                <div class="col-md-6">
                                    <label for="new_password" class="form-label">Password Baru</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                </div>
                                <div class="col-md-6">
                                    <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 