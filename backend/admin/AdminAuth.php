<?php
/**
 * AdminAuth Class
 * Handles authentication for admin users
 */
class AdminAuth {
    private $db;
    
    /**
     * Constructor
     *
     * @param PDO $db Database connection
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Login admin user
     *
     * @param string $username Username
     * @param string $password Password
     * @param bool $remember Remember login
     * @return array Success status and message
     */
    public function login($username, $password, $remember = false) {
        try {
            // Find admin user by username
            $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE username = ? AND is_active = 1");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Check if admin exists and verify password
            if ($admin && password_verify($password, $admin['password'])) {
                // Start session if not already started
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                // Set session variables
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_role'] = $admin['role'];
                $_SESSION['admin_last_login'] = date('Y-m-d H:i:s');
                
                // Update last login time
                $updateStmt = $this->db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$admin['id']]);
                
                // Set remember me cookie if requested
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expires = time() + (86400 * 30); // 30 days
                    
                    // Store token in database
                    $tokenStmt = $this->db->prepare("INSERT INTO admin_tokens (admin_id, token, expires) VALUES (?, ?, ?)");
                    $tokenStmt->execute([$admin['id'], $token, date('Y-m-d H:i:s', $expires)]);
                    
                    // Set cookie
                    setcookie('admin_remember', $token, $expires, '/', '', true, true);
                }
                
                // Log login activity
                $this->logActivity($admin['id'], 'login', 'Admin login successful');
                
                return [
                    'success' => true,
                    'message' => 'Login successful'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Username atau password salah'
                ];
            }
        } catch (PDOException $e) {
            error_log('Login error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat login. Silakan coba lagi.'
            ];
        }
    }
    
    /**
     * Check if admin is logged in
     *
     * @return bool True if logged in, false otherwise
     */
    public function isLoggedIn() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if admin session exists
        if (isset($_SESSION['admin_id'])) {
            return true;
        }
        
        // Check for remember me cookie
        if (isset($_COOKIE['admin_remember'])) {
            $token = $_COOKIE['admin_remember'];
            
            try {
                // Find token in database
                $stmt = $this->db->prepare("
                    SELECT admin_tokens.admin_id, admin_users.* 
                    FROM admin_tokens 
                    JOIN admin_users ON admin_tokens.admin_id = admin_users.id 
                    WHERE admin_tokens.token = ? AND admin_tokens.expires > NOW() AND admin_users.is_active = 1
                ");
                $stmt->execute([$token]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result) {
                    // Set session variables
                    $_SESSION['admin_id'] = $result['admin_id'];
                    $_SESSION['admin_username'] = $result['username'];
                    $_SESSION['admin_name'] = $result['name'];
                    $_SESSION['admin_role'] = $result['role'];
                    $_SESSION['admin_last_login'] = date('Y-m-d H:i:s');
                    
                    // Update last login time
                    $updateStmt = $this->db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                    $updateStmt->execute([$result['admin_id']]);
                    
                    // Log activity
                    $this->logActivity($result['admin_id'], 'auto_login', 'Auto login via remember token');
                    
                    return true;
                }
                
                // Token is invalid or expired, delete cookie
                setcookie('admin_remember', '', time() - 3600, '/', '', true, true);
            } catch (PDOException $e) {
                error_log('Auto login error: ' . $e->getMessage());
            }
        }
        
        return false;
    }
    
    /**
     * Logout admin user
     *
     * @return bool True if logout successful
     */
    public function logout() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Log logout activity
        if (isset($_SESSION['admin_id'])) {
            $this->logActivity($_SESSION['admin_id'], 'logout', 'Admin logout');
            
            // Remove remember token if exists
            if (isset($_COOKIE['admin_remember'])) {
                $token = $_COOKIE['admin_remember'];
                
                try {
                    $stmt = $this->db->prepare("DELETE FROM admin_tokens WHERE token = ?");
                    $stmt->execute([$token]);
                } catch (PDOException $e) {
                    error_log('Logout error (token delete): ' . $e->getMessage());
                }
                
                // Delete cookie
                setcookie('admin_remember', '', time() - 3600, '/', '', true, true);
            }
        }
        
        // Destroy session
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        session_destroy();
        
        return true;
    }
    
    /**
     * Get current admin user data
     *
     * @return array|null Admin user data or null if not logged in
     */
    public function getCurrentAdmin() {
        // Check if logged in
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        try {
            // Get admin data
            $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get current admin error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Log admin activity
     *
     * @param int $adminId Admin ID
     * @param string $action Action performed
     * @param string $description Description of activity
     * @return bool True if logging successful
     */
    private function logActivity($adminId, $action, $description) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO admin_activities (admin_id, action, description, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            return $stmt->execute([$adminId, $action, $description, $ipAddress, $userAgent]);
        } catch (PDOException $e) {
            error_log('Log activity error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Register new admin user
     *
     * @param array $userData Admin user data
     * @return array Success status and message/user ID
     */
    public function register($userData) {
        try {
            // Check if username already exists
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM admin_users WHERE username = ?");
            $stmt->execute([$userData['username']]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                return [
                    'success' => false,
                    'message' => 'Username sudah digunakan'
                ];
            }
            
            // Check if email already exists
            if (!empty($userData['email'])) {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM admin_users WHERE email = ?");
                $stmt->execute([$userData['email']]);
                $count = $stmt->fetchColumn();
                
                if ($count > 0) {
                    return [
                        'success' => false,
                        'message' => 'Email sudah digunakan'
                    ];
                }
            }
            
            // Hash password
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            // Insert new admin user
            $stmt = $this->db->prepare("
                INSERT INTO admin_users (name, username, password, email, role, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $userData['name'],
                $userData['username'],
                $userData['password'],
                $userData['email'] ?? null,
                $userData['role'] ?? 'editor',
                $userData['is_active'] ?? 1
            ]);
            
            $adminId = $this->db->lastInsertId();
            
            // Log activity
            $this->logActivity($adminId, 'register', 'New admin user registered');
            
            return [
                'success' => true,
                'admin_id' => $adminId
            ];
        } catch (PDOException $e) {
            error_log('Register admin error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mendaftarkan admin. Silakan coba lagi.'
            ];
        }
    }
}

/**
 * AdminMiddleware Class
 * Handles middleware functions for admin authentication
 */
class AdminMiddleware {
    /**
     * Require admin authentication
     * Redirects to login page if not authenticated
     *
     * @param string $redirectTo URL to redirect to if not authenticated
     * @return void
     */
    public static function requireAuth($redirectTo = 'login.php') {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if admin is logged in
        if (!isset($_SESSION['admin_id'])) {
            // Set flash message
            $_SESSION['flash_message'] = [
                'type' => 'warning',
                'message' => 'Silakan login terlebih dahulu untuk mengakses halaman admin.'
            ];
            
            // Redirect to login page
            header('Location: ' . $redirectTo);
            exit;
        }
    }
    
    /**
     * Require specific admin role
     * Redirects to dashboard if role not allowed
     *
     * @param array $allowedRoles Allowed roles
     * @param string $redirectTo URL to redirect to if role not allowed
     * @return void
     */
    public static function requireRole($allowedRoles, $redirectTo = 'dashboard.php') {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if admin has required role
        if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], $allowedRoles)) {
            // Set flash message
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman tersebut.'
            ];
            
            // Redirect to dashboard
            header('Location: ' . $redirectTo);
            exit;
        }
    }
    
    /**
     * Generate CSRF token
     *
     * @return string CSRF token
     */
    public static function generateCsrfToken() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Generate token if not exists
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate CSRF token
     *
     * @return bool True if token is valid
     */
    public static function validateCsrfToken() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if token exists and matches
        if (
            isset($_POST['csrf_token']) && 
            isset($_SESSION['csrf_token']) && 
            hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
        ) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Sanitize input data
     *
     * @param string $input Input data
     * @return string Sanitized input
     */
    public static function sanitizeInput($input) {
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = self::sanitizeInput($value);
            }
            return $input;
        }
        
        // Remove whitespace from beginning and end
        $input = trim($input);
        
        // Remove backslashes
        $input = stripslashes($input);
        
        // Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        return $input;
    }
    
    /**
     * Log admin action
     *
     * @param PDO $db Database connection
     * @param string $action Action description
     * @param array $data Additional data (optional)
     * @return bool True if logging successful
     */
    public static function logAction($db, $action, $data = null) {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if admin is logged in
        if (!isset($_SESSION['admin_id'])) {
            return false;
        }
        
        try {
            $stmt = $db->prepare("
                INSERT INTO admin_activities (admin_id, action, description, data, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $adminId = $_SESSION['admin_id'];
            $description = 'Admin user performed action: ' . $action;
            $jsonData = $data ? json_encode($data) : null;
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            return $stmt->execute([$adminId, $action, $description, $jsonData, $ipAddress, $userAgent]);
        } catch (PDOException $e) {
            error_log('Log admin action error: ' . $e->getMessage());
            return false;
        }
    }
}

/**
 * Set flash message
 *
 * @param string $message Message
 * @param string $type Message type (success, danger, warning, info)
 * @return void
 */
function setFlashMessage($message, $type = 'info') {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Set flash message
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Format date
 *
 * @param string $dateString Date string
 * @param string $format Format (default: 'd M Y H:i')
 * @return string Formatted date
 */
function formatDate($dateString, $format = 'd M Y H:i') {
    if (empty($dateString)) {
        return '-';
    }
    
    try {
        $date = new DateTime($dateString);
        return $date->format($format);
    } catch (Exception $e) {
        return $dateString;
    }
}
?>