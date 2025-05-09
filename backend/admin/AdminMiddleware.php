<?php
/**
 * Admin Middleware Class
 * Menyediakan fungsi middleware untuk halaman admin
 */
class AdminMiddleware {
    /**
     * Check if user is authenticated as admin
     * Redirect to login page if not
     */
    public static function requireAuth() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if admin is logged in
        if (!isset($_SESSION['admin_user']) || empty($_SESSION['admin_user'])) {
            // Save requested URL for redirect after login
            $_SESSION['admin_redirect_url'] = $_SERVER['REQUEST_URI'];
            
            // Redirect to admin login page
            header("Location: /frontend/admin/login.php");
            exit;
        }
        
        // Verify admin role
        if ($_SESSION['admin_user']['role'] !== 'admin') {
            // Not an admin, show access denied
            header("HTTP/1.1 403 Forbidden");
            include __DIR__ . '/../../frontend/admin/pages/access-denied.php';
            exit;
        }
        
        // Update last activity timestamp
        $_SESSION['admin_last_activity'] = time();
        
        return true;
    }
    
    /**
     * Check if session has timed out
     * Auto-logout after specified idle time
     */
    public static function checkSessionTimeout() {
        // Session timeout in seconds (30 minutes)
        $sessionTimeout = 1800;
        
        if (isset($_SESSION['admin_last_activity'])) {
            $elapsed = time() - $_SESSION['admin_last_activity'];
            
            if ($elapsed > $sessionTimeout) {
                // Session has expired, destroy session
                session_unset();
                session_destroy();
                
                // Redirect to login page with timeout message
                header("Location: /frontend/admin/login.php?timeout=1");
                exit;
            }
        }
    }
    
    /**
     * Sanitize input data
     *
     * @param mixed $input Input data to sanitize
     * @return mixed Sanitized data
     */
    public static function sanitizeInput($input) {
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = self::sanitizeInput($value);
            }
            return $input;
        }
        
        // Remove whitespace from beginning and end of string
        $input = trim($input);
        
        // Remove backslashes
        $input = stripslashes($input);
        
        // Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        return $input;
    }
    
    /**
     * Validate CSRF token
     *
     * @return boolean True if token is valid, false otherwise
     */
    public static function validateCsrfToken() {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }
    
    /**
     * Generate CSRF token
     *
     * @return string CSRF token
     */
    public static function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Log admin action
     *
     * @param PDO $db Database connection
     * @param string $action Action description
     * @param string $details Additional details (optional)
     * @return boolean True if successful, false otherwise
     */
    public static function logAction($db, $action, $details = null) {
        try {
            if (!isset($_SESSION['admin_user'])) {
                return false;
            }
            
            $adminId = $_SESSION['admin_user']['id'];
            
            $stmt = $db->prepare("INSERT INTO admin_logs (admin_id, action, details, ip_address) 
                                VALUES (:admin_id, :action, :details, :ip_address)");
            
            $stmt->bindParam(':admin_id', $adminId, PDO::PARAM_INT);
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':details', $details);
            $stmt->bindParam(':ip_address', $_SERVER['REMOTE_ADDR']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error logging admin action: " . $e->getMessage());
            return false;
        }
    }
}
?>