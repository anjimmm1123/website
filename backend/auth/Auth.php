<?php
// Include database connection
require_once __DIR__ . '/../config/database.php';
// Include configuration file
require_once __DIR__ . '/../config/config.php';

class Auth {
    private $db;
    private $table_name = "users";

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    public function login($username_or_email, $password) {
        try {
            // Check if input is email or username
            $is_email = filter_var($username_or_email, FILTER_VALIDATE_EMAIL);
            $field = $is_email ? 'email' : 'username';
            
            $query = "SELECT id, username, email, password, role, full_name, is_active 
                     FROM " . $this->table_name . " 
                     WHERE $field = :input AND is_active = 1 LIMIT 1";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":input", $username_or_email);
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if(password_verify($password, $row['password'])) {
                    // Update last login
                    $this->updateLastLogin($row['id']);
                    
                    // Set session
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['user_role'] = $row['role'];
                    $_SESSION['full_name'] = $row['full_name'];
                    $_SESSION['last_activity'] = time();
                    
                    return [
                        'success' => true,
                        'user' => [
                            'id' => $row['id'],
                            'username' => $row['username'],
                            'email' => $row['email'],
                            'role' => $row['role'],
                            'full_name' => $row['full_name']
                        ]
                    ];
                }
            }
            return ['success' => false, 'message' => 'Username/email atau password salah'];
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat login. Silakan coba lagi.'];
        }
    }

    public function register($username, $email, $password, $full_name) {
        try {
            // Start transaction
            $this->db->beginTransaction();

            // Validate input
            if (!$this->validateRegistration($username, $email, $password, $full_name)) {
                throw new Exception("Data registrasi tidak valid");
            }

            // Check if username or email exists
            if ($this->userExists($username, $email)) {
                throw new Exception("Username atau email sudah digunakan");
            }

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $query = "INSERT INTO " . $this->table_name . " 
                    (username, email, password, full_name, role, is_active, created_at) 
                    VALUES 
                    (:username, :email, :password, :full_name, 'user', 1, datetime('now'))";
            
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":full_name", $full_name);

            if($stmt->execute()) {
                $this->db->commit();
                return [
                    'success' => true, 
                    'message' => 'Registrasi berhasil',
                    'user_id' => $this->db->lastInsertId()
                ];
            }

            throw new Exception("Gagal membuat akun baru");

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Registration error: " . $e->getMessage());
            return [
                'success' => false, 
                'message' => $e->getMessage()
            ];
        }
    }

    private function validateRegistration($username, $email, $password, $full_name) {
        // Validate username
        if (empty($username) || strlen($username) < 3 || strlen($username) > 50) {
            throw new Exception("Username harus antara 3-50 karakter");
        }

        // Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format email tidak valid");
        }

        // Validate password
        if (empty($password) || strlen($password) < 8) {
            throw new Exception("Password harus minimal 8 karakter");
        }

        // Validate full name
        if (empty($full_name) || strlen($full_name) < 2 || strlen($full_name) > 100) {
            throw new Exception("Nama lengkap harus antara 2-100 karakter");
        }

        return true;
    }

    private function userExists($username, $email) {
        $query = "SELECT id FROM " . $this->table_name . " 
                 WHERE username = :username OR email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    private function updateLastLogin($user_id) {
        $query = "UPDATE " . $this->table_name . " 
                 SET updated_at = datetime('now') 
                 WHERE id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
    }

    public function validateToken($token) {
        // Implement JWT token validation here
        // This is a placeholder for token validation logic
        return true;
    }

    public function updateProfile($user_id, $data) {
        try {
            // Validate input
            if (empty($data['full_name']) || empty($data['email'])) {
                throw new Exception("Nama lengkap dan email harus diisi.");
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Format email tidak valid.");
            }

            // Check if email is already used by another user
            $query = "SELECT id FROM " . $this->table_name . " 
                     WHERE email = :email AND id != :user_id LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":email", $data['email']);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                throw new Exception("Email sudah digunakan oleh user lain.");
            }

            // Update user data
            $query = "UPDATE " . $this->table_name . " 
                     SET full_name = :full_name, 
                         email = :email,
                         updated_at = datetime('now')
                     WHERE id = :user_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":full_name", $data['full_name']);
            $stmt->bindParam(":email", $data['email']);
            $stmt->bindParam(":user_id", $user_id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Profil berhasil diperbarui.'
                ];
            }

            throw new Exception("Gagal memperbarui profil.");

        } catch (Exception $e) {
            error_log("Profile update error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function updatePassword($user_id, $current_password, $new_password) {
        try {
            // Validate new password
            if (strlen($new_password) < 8) {
                throw new Exception("Password baru harus minimal 8 karakter.");
            }

            // Get current password
            $query = "SELECT password FROM " . $this->table_name . " WHERE id = :user_id LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new Exception("User tidak ditemukan.");
            }

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify current password
            if (!password_verify($current_password, $row['password'])) {
                throw new Exception("Password saat ini tidak sesuai.");
            }

            // Hash new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password
            $query = "UPDATE " . $this->table_name . " 
                     SET password = :password,
                         updated_at = datetime('now')
                     WHERE id = :user_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":user_id", $user_id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Password berhasil diperbarui.'
                ];
            }

            throw new Exception("Gagal memperbarui password.");

        } catch (Exception $e) {
            error_log("Password update error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function logout() {
        // Unset all session variables
        $_SESSION = [];

        // If it's desired to kill the session, also delete the session cookie.
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();
        
        return [
            'success' => true,
            'message' => 'Berhasil logout.'
        ];
    }
}
?>