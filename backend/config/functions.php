<?php
// Sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Generate random string
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

// Format date
function formatDate($date, $format = 'd M Y H:i') {
    return date($format, strtotime($date));
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Redirect to URL
function redirect($url) {
    header("Location: $url");
    exit;
}

// Get current URL
function getCurrentUrl() {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
           "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Get file extension
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

// Check if file type is allowed
function isAllowedFileType($filename) {
    $extension = getFileExtension($filename);
    return in_array($extension, ALLOWED_FILE_TYPES);
}

// Format file size
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

// Database connection
require_once __DIR__ . '/database.php';

/**
 * Get all services from database
 * @return array Array of services
 */
function getServices() {
    global $db;
    try {
        $query = "SELECT * FROM services ORDER BY id ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting services: " . $e->getMessage());
        return [];
    }
}

/**
 * Get all gallery items from database
 * @return array Array of gallery items
 */
function getGalleryItems() {
    global $db;
    try {
        $query = "SELECT * FROM gallery ORDER BY id DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting gallery items: " . $e->getMessage());
        return [];
    }
}

/**
 * Get about page content
 * @return array About page data
 */
function getAboutContent() {
    global $db;
    try {
        $query = "SELECT * FROM about_page LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting about content: " . $e->getMessage());
        return [];
    }
}

/**
 * Get contact information
 * @return array Contact information
 */
function getContactInfo() {
    global $db;
    try {
        $query = "SELECT * FROM contact_info LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting contact info: " . $e->getMessage());
        return [];
    }
}

// Database helper functions
function getRow($sql, $params = []) {
    global $db;
    
    try {
        $stmt = $db->prepare($sql);
        if (!empty($params)) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

function getAll($sql, $params = []) {
    global $db;
    
    try {
        $stmt = $db->prepare($sql);
        if (!empty($params)) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

function executeQuery($sql, $params = []) {
    global $db;
    
    try {
        $stmt = $db->prepare($sql);
        if (!empty($params)) {
            return $stmt->execute($params);
        }
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

// Session helper functions
function getUserData() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email']
        ];
    }
    return null;
}

// Flash message helper functions
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// Security helper functions
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// File helper functions
function uploadFile($file, $destination, $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf']) {
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if ($fileError === 0) {
        if (in_array($fileExt, $allowedTypes)) {
            if ($fileSize < 5000000) { // 5MB max
                $newFileName = uniqid('', true) . '.' . $fileExt;
                $uploadPath = $destination . $newFileName;
                
                if (move_uploaded_file($fileTmpName, $uploadPath)) {
                    return $newFileName;
                }
            }
        }
    }
    
    return false;
}

// String helper functions
function slugify($text) {
    // Replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trim
    $text = trim($text, '-');
    // Remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
    // Lowercase
    $text = strtolower($text);
    
    return $text;
}
?> 