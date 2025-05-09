<?php
/**
 * General utility functions for STMIK Enterprise website
 */

/**
 * Sanitize user input
 * @param string $input - Input to sanitize
 * @return string - Sanitized input
 */
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * Check if user is logged in
 * @return bool - True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user has admin role
 * @return bool - True if user is admin, false otherwise
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Generate URL for specified page
 * @param string $page - Page name
 * @param array $params - Optional URL parameters
 * @return string - Full URL
 */
function generateUrl($page, $params = []) {
    $url = "?page=" . urlencode($page);
    
    foreach ($params as $key => $value) {
        $url .= "&" . urlencode($key) . "=" . urlencode($value);
    }
    
    return $url;
}

/**
 * Redirect to specified page
 * @param string $page - Page to redirect to
 * @param array $params - Optional URL parameters
 */
function redirect($page, $params = []) {
    $url = generateUrl($page, $params);
    header("Location: $url");
    exit();
}

/**
 * Format date to Indonesian format
 * @param string $date - Date to format (MySQL format)
 * @param bool $withTime - Whether to include time
 * @return string - Formatted date
 */
function formatDate($date, $withTime = false) {
    $timestamp = strtotime($date);
    
    $months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $day = date('d', $timestamp);
    $month = $months[date('n', $timestamp) - 1];
    $year = date('Y', $timestamp);
    
    $formatted = "$day $month $year";
    
    if ($withTime) {
        $formatted .= ' ' . date('H:i', $timestamp);
    }
    
    return $formatted;
}

/**
 * Generate slug from string
 * @param string $string - String to convert to slug
 * @return string - Slug
 */
function generateSlug($string) {
    // Replace non-alphanumeric characters with dashes
    $string = preg_replace('/[^\p{L}\p{N}]+/u', '-', $string);
    // Remove leading/trailing dashes
    $string = trim($string, '-');
    // Convert to lowercase
    $string = strtolower($string);
    
    // Transliterate non-Latin characters to Latin equivalents
    $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
    
    // Remove any remaining non-alphanumeric characters
    $string = preg_replace('/[^a-z0-9-]/', '', $string);
    
    return $string;
}

/**
 * Truncate text to specified length
 * @param string $text - Text to truncate
 * @param int $length - Maximum length
 * @param string $append - String to append if truncated
 * @return string - Truncated text
 */
function truncateText($text, $length = 100, $append = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $text = substr($text, 0, $length);
    $text = substr($text, 0, strrpos($text, ' '));
    
    return $text . $append;
}

/**
 * Get website setting value
 * @param string $key - Setting key
 * @param string $default - Default value if setting not found
 * @return string - Setting value
 */
function getSetting($key, $default = '') {
    global $db;
    
    try {
        $stmt = $db->prepare("SELECT value FROM settings WHERE key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return $result['value'];
        }
    } catch (PDOException $e) {
        error_log("Error retrieving setting '$key': " . $e->getMessage());
    }
    
    return $default;
}

/**
 * Set website setting value
 * @param string $key - Setting key
 * @param string $value - Setting value
 * @return bool - True on success, false on failure
 */
function setSetting($key, $value) {
    global $db;
    
    try {
        // Check if setting exists
        $stmt = $db->prepare("SELECT COUNT(*) FROM settings WHERE key = ?");
        $stmt->execute([$key]);
        $exists = (int)$stmt->fetchColumn() > 0;
        
        if ($exists) {
            // Update existing setting
            $stmt = $db->prepare("UPDATE settings SET value = ? WHERE key = ?");
            $stmt->execute([$value, $key]);
        } else {
            // Insert new setting
            $stmt = $db->prepare("INSERT INTO settings (key, value) VALUES (?, ?)");
            $stmt->execute([$key, $value]);
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Error setting '$key': " . $e->getMessage());
        return false;
    }
}

/**
 * Logging function
 * @param string $message - Message to log
 * @param string $level - Log level (info, warning, error)
 */
function logActivity($message, $level = 'info') {
    $logFile = __DIR__ . '/../logs/activity.log';
    $logDir = dirname($logFile);
    
    // Create logs directory if it doesn't exist
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'guest';
    $userIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    $logEntry = "[$timestamp] [$level] [User: $userId] [IP: $userIp] $message" . PHP_EOL;
    
    // Append to log file
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Also log to PHP error log for critical issues
    if ($level === 'error') {
        error_log($message);
    }
}

/**
 * Upload file
 * @param array $file - $_FILES array element
 * @param string $uploadDir - Directory to upload to
 * @param array $allowedTypes - Allowed MIME types
 * @param int $maxSize - Maximum file size in bytes
 * @return array - [success: bool, message: string, filename: string]
 */
function uploadFile($file, $uploadDir = 'uploads', $allowedTypes = [], $maxSize = 5242880) {
    // Check if file was uploaded successfully
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi upload_max_filesize)',
            UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi MAX_FILE_SIZE)',
            UPLOAD_ERR_PARTIAL => 'File hanya terunggah sebagian',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diunggah',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
        ];
        
        $errorMessage = $errorMessages[$file['error']] ?? 'Unknown upload error';
        return ['success' => false, 'message' => $errorMessage, 'filename' => ''];
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File terlalu besar (maksimum ' . ($maxSize / 1024 / 1024) . 'MB)', 'filename' => ''];
    }
    
    // Check file type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $fileType = $finfo->file($file['tmp_name']);
    
    if (!empty($allowedTypes) && !in_array($fileType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan', 'filename' => ''];
    }
    
    // Create upload directory if it doesn't exist
    $targetDir = __DIR__ . '/../frontend/' . $uploadDir;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $baseName = generateSlug(pathinfo($file['name'], PATHINFO_FILENAME));
    $filename = $baseName . '-' . uniqid() . '.' . $fileExtension;
    $targetPath = $targetDir . '/' . $filename;
    
    // Move the file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => false, 'message' => 'Gagal mengunggah file', 'filename' => ''];
    }
    
    return [
        'success' => true,
        'message' => 'File berhasil diunggah',
        'filename' => $uploadDir . '/' . $filename
    ];
}
?>