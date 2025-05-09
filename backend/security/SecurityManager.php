<?php
class SecurityManager {
    private $pdo;
    private $config;

    public function __construct($pdo, $config) {
        $this->pdo = $pdo;
        $this->config = $config;
    }

    public function setSecurityHeaders() {
        // Set security headers
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' \'unsafe-eval\'; style-src \'self\' \'unsafe-inline\';');
    }

    public function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public function validateCSRF() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception('CSRF token validation failed');
            }
        }
    }

    public function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function validateFileUpload($file) {
        $errors = [];

        // Check file size
        if ($file['size'] > MAX_FILE_SIZE) {
            $errors[] = 'File size exceeds limit';
        }

        // Check file type
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_FILE_TYPES)) {
            $errors[] = 'File type not allowed';
        }

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload failed';
        }

        return $errors;
    }

    public function logSecurityEvent($event, $user_id = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO security_logs (event, user_id, ip_address, created_at)
            VALUES (:event, :user_id, :ip_address, NOW())
        ");

        $stmt->execute([
            ':event' => $event,
            ':user_id' => $user_id,
            ':ip_address' => $_SERVER['REMOTE_ADDR']
        ]);
    }

    public function checkRateLimit($action, $limit = 60, $period = 3600) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = "rate_limit:{$action}:{$ip}";
        
        // Implementation would depend on your caching system
        // This is a simplified version
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 1,
                'reset_time' => time() + $period
            ];
            return true;
        }

        if (time() > $_SESSION[$key]['reset_time']) {
            $_SESSION[$key] = [
                'count' => 1,
                'reset_time' => time() + $period
            ];
            return true;
        }

        if ($_SESSION[$key]['count'] >= $limit) {
            return false;
        }

        $_SESSION[$key]['count']++;
        return true;
    }
}
?> 