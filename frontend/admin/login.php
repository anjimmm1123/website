<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set page title
$pageTitle = 'Login Admin - STMIK Enterprise';

// Include database configuration
require_once __DIR__ . '/../../backend/config/database.php';

// Include necessary classes
require_once __DIR__ . '/../../backend/admin/AdminAuth.php';

// Check if already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Initialize error message
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? (bool)$_POST['remember'] : false;
    
    // Check for empty fields
    if (empty($username) || empty($password)) {
        $error = 'Silakan masukkan username dan password.';
    } else {
        // Create instance of AdminAuth
        $adminAuth = new AdminAuth($db);
        
        // Attempt login
        $loginResult = $adminAuth->login($username, $password, $remember);
        
        if ($loginResult['success']) {
            // Redirect to dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            $error = $loginResult['message'];
        }
    }
}

// If there is a flash message, retrieve it
$flashMessage = null;
if (isset($_SESSION['flash_message'])) {
    $flashMessage = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Admin CSS -->
    <link rel="stylesheet" href="assets/css/admin.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
        }
        
        .login-card {
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .login-header {
            background-color: #4e73df;
            color: #fff;
            padding: 30px 20px;
            text-align: center;
        }
        
        .login-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .form-control {
            height: 50px;
            font-size: 16px;
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }
        
        .form-control:focus {
            box-shadow: none;
            border-color: #4e73df;
        }
        
        .btn-login {
            height: 50px;
            font-size: 16px;
            background-color: #4e73df;
            border-color: #4e73df;
            font-weight: 600;
        }
        
        .btn-login:hover, .btn-login:focus {
            background-color: #2e59d9;
            border-color: #2653d4;
        }
        
        .login-footer {
            text-align: center;
            font-size: 14px;
            margin-top: 20px;
            color: #6c757d;
        }
        
        .login-footer a {
            color: #4e73df;
            text-decoration: none;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <?php if (isset($flashMessage)): ?>
        <div class="alert alert-<?php echo $flashMessage['type']; ?> alert-dismissible fade show mb-4" role="alert">
            <?php echo $flashMessage['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <div class="login-card card">
            <div class="login-header">
                <h1><i class="fas fa-shield-alt me-2"></i>Admin Panel</h1>
                <p class="mb-0">STMIK Enterprise</p>
            </div>
            
            <div class="login-body">
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <form action="" method="POST" novalidate>
                    <div class="mb-4">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="login-footer">
            <p>&copy; <?php echo date('Y'); ?> STMIK Enterprise. All rights reserved.</p>
            <p><a href="../index.php"><i class="fas fa-home me-1"></i> Kembali ke halaman utama</a></p>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const togglePasswordButtons = document.querySelectorAll('.toggle-password');
        
        togglePasswordButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    });
    </script>
</body>
</html>