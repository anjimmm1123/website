<?php
// Page title sudah ditetapkan di index.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../backend/auth/Auth.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /?page=dashboard');
    exit;
}

$error = '';
$username = '';
$full_name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    try {
        if ($password !== $confirm_password) {
            throw new Exception("Password dan konfirmasi password tidak cocok.");
        }

        $auth = new Auth();
        $result = $auth->register($username, $email, $password, $full_name);

        if ($result['success']) {
            // Set success message
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Registrasi berhasil! Silakan login untuk melanjutkan.'
            ];
            
            // Redirect to login page
            header('Location: /?page=login');
            exit;
        } else {
            throw new Exception($result['message']);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!-- CSS -->
<link rel="stylesheet" href="/frontend/assets/css/common.css">
<link rel="stylesheet" href="/frontend/assets/css/auth.css">

<!-- Main Content -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Daftar Akun</h2>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['flash_message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['flash_message']['type']; ?>">
                            <?php 
                            echo $_SESSION['flash_message']['message'];
                            unset($_SESSION['flash_message']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="full_name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="<?php echo htmlspecialchars($full_name); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Daftar</button>
                        </div>

                        <div class="text-center mt-3">
                            <p class="mb-0">Sudah punya akun? <a href="/?page=login">Masuk di sini</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 