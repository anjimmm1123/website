<?php
$page_title = 'Lupa Password - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../backend/auth.php';

// Check if user is already logged in
if ($auth->isLoggedIn()) {
    header('Location: /?page=dashboard');
    exit;
}

$error = '';
$success = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    try {
        if (empty($email)) {
            throw new Exception("Email harus diisi.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format email tidak valid.");
        }

        // TODO: Implement password reset functionality
        $success = "Jika email terdaftar, kami akan mengirimkan link reset password ke email Anda.";
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
                    <h2 class="text-center mb-4">Lupa Password</h2>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <p class="text-muted text-center mb-4">
                        Masukkan email Anda untuk menerima link reset password
                    </p>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Kirim Link Reset</button>
                        </div>
                    </form>

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