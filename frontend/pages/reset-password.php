<?php
$page_title = 'Reset Password - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../backend/auth.php';

// Check if user is already logged in
if ($auth->isLoggedIn()) {
    header('Location: /?page=dashboard');
    exit;
}

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    header('Location: /?page=forgot-password');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    try {
        if (empty($password)) {
            throw new Exception("Password harus diisi.");
        }

        if (strlen($password) < 8) {
            throw new Exception("Password minimal 8 karakter.");
        }

        if ($password !== $confirm_password) {
            throw new Exception("Password dan konfirmasi password tidak cocok.");
        }

        // TODO: Implement password reset functionality
        $success = "Password berhasil diubah. Silakan login dengan password baru Anda.";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Reset Password</h2>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
                            <div class="mt-3">
                                <a href="/?page=login" class="btn btn-primary">Login</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       minlength="8" required>
                                <div class="form-text">Password minimal 8 karakter</div>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="confirm_password" 
                                       name="confirm_password" minlength="8" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Reset Password</button>
                            </div>
                        </form>
                    <?php endif; ?>

                    <div class="text-center mt-4">
                        <p>
                            <a href="/?page=login" class="text-decoration-none">Kembali ke halaman login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 