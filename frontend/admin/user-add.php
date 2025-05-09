<?php
// Set page title
$pageTitle = 'Tambah Pengguna Admin - Admin Panel';

// Include admin configuration
require_once __DIR__ . '/config.php';

// Check if user is authenticated and has admin role
AdminMiddleware::requireAuth();
AdminMiddleware::requireRole(['admin']);

// Create admin manager instance
$adminManager = new AdminManager($db, $currentAdminUser);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!AdminMiddleware::validateCsrfToken()) {
        setFlashMessage('Invalid form submission, please try again.', 'danger');
    } else {
        // Validate form data
        $name = AdminMiddleware::sanitizeInput($_POST['name'] ?? '');
        $username = AdminMiddleware::sanitizeInput($_POST['username'] ?? '');
        $email = AdminMiddleware::sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $role = AdminMiddleware::sanitizeInput($_POST['role'] ?? 'editor');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        $errors = [];
        
        // Check for required fields
        if (empty($name)) {
            $errors[] = 'Nama lengkap harus diisi.';
        }
        
        if (empty($username)) {
            $errors[] = 'Username harus diisi.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
            $errors[] = 'Username harus terdiri dari 4-20 karakter dan hanya boleh berisi huruf, angka, dan underscore.';
        }
        
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }
        
        if (empty($password)) {
            $errors[] = 'Password harus diisi.';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password harus terdiri dari minimal 6 karakter.';
        }
        
        if ($password !== $confirm_password) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }
        
        if (!in_array($role, ['admin', 'editor'])) {
            $errors[] = 'Role tidak valid.';
        }
        
        // If no errors, create user
        if (empty($errors)) {
            $userData = [
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'role' => $role,
                'is_active' => $is_active
            ];
            
            // Attempt to create user
            $result = $adminAuth->register($userData);
            
            if ($result['success']) {
                setFlashMessage('Pengguna admin berhasil ditambahkan.', 'success');
                header('Location: users.php');
                exit;
            } else {
                setFlashMessage($result['message'], 'danger');
            }
        } else {
            // Set error messages
            foreach ($errors as $error) {
                setFlashMessage($error, 'danger');
            }
        }
    }
}

// Include header
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Tambah Pengguna Admin</h1>
    <div>
        <a href="users.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="" method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                        <div class="invalid-feedback">Silakan masukkan nama lengkap.</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                            <div class="invalid-feedback">Silakan masukkan username.</div>
                        </div>
                        <div class="form-text">Username harus terdiri dari 4-20 karakter dan hanya boleh berisi huruf, angka, dan underscore.</div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            <div class="invalid-feedback">Silakan masukkan email yang valid.</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="editor" <?php echo (isset($_POST['role']) && $_POST['role'] === 'editor') ? 'selected' : ''; ?>>Editor</option>
                            <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        </select>
                        <div class="invalid-feedback">Silakan pilih role.</div>
                        <div class="form-text">Admin: Akses penuh ke semua fitur. Editor: Hanya dapat mengelola konten.</div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                <i class="fas fa-eye"></i>
                            </button>
                            <div class="invalid-feedback">Silakan masukkan password.</div>
                        </div>
                        <div class="form-text">Password harus terdiri dari minimal 6 karakter.</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password">
                                <i class="fas fa-eye"></i>
                            </button>
                            <div class="invalid-feedback">Silakan konfirmasi password.</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                <label class="form-check-label" for="is_active">
                    Aktif
                </label>
                <div class="form-text">Jika tidak dicentang, pengguna tidak dapat login.</div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Simpan
                </button>
                <a href="users.php" class="btn btn-secondary ms-2">
                    <i class="fas fa-times me-2"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

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
    
    // Password confirmation validation
    const form = document.querySelector('form');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    form.addEventListener('submit', function(event) {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Passwords do not match');
        } else {
            confirmPassword.setCustomValidity('');
        }
    });
    
    confirmPassword.addEventListener('input', function() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Passwords do not match');
        } else {
            confirmPassword.setCustomValidity('');
        }
    });
});
</script>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>