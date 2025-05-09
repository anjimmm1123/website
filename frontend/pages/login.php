<?php
// Page title sudah ditetapkan di index.php
require_once __DIR__ . '/../../backend/auth/Auth.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ?page=dashboard');
    exit;
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        $auth = new Auth();
        $result = $auth->login($username, $password);
        
        if ($result['success']) {
            // Redirect based on role
            $redirect = ($_SESSION['user_role'] === 'admin') ? 'dashboard' : 'profile';
            header("Location: ?page=$redirect");
            exit;
        } else {
            $error = $result['message'];
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}
?>

<!-- CSS -->
<link rel="stylesheet" href="frontend/assets/css/common.css">
<link rel="stylesheet" href="frontend/assets/css/auth.css">

<!-- Main Content -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Masuk ke Akun</h2>
                    
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
                            <label for="username" class="form-label">Username atau Email</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                            <label class="form-check-label" for="remember_me">Ingat saya</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Masuk</button>
                        </div>

                        <div class="text-center mt-3">
                            <p class="mb-0">Belum punya akun? <a href="?page=register">Daftar di sini</a></p>
                            <p class="mt-2 mb-0"><a href="?page=forgot-password">Lupa password?</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>