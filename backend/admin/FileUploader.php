<?php
/**
 * FileUploader Class
 * Handles file uploads for admin panel
 */
class FileUploader {
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'zip'];
    private $maxFileSize = 5242880; // 5MB
    private $uploadDir = '../uploads/';
    private $createDirIfNotExists = true;
    
    /**
     * Constructor
     *
     * @param array $config Configuration options
     */
    public function __construct($config = []) {
        // Set configuration options
        if (isset($config['allowedExtensions'])) {
            $this->allowedExtensions = $config['allowedExtensions'];
        }
        
        if (isset($config['maxFileSize'])) {
            $this->maxFileSize = $config['maxFileSize'];
        }
        
        if (isset($config['uploadDir'])) {
            $this->uploadDir = rtrim($config['uploadDir'], '/') . '/';
        }
        
        if (isset($config['createDirIfNotExists'])) {
            $this->createDirIfNotExists = $config['createDirIfNotExists'];
        }
    }
    
    /**
     * Set allowed file extensions
     *
     * @param array $extensions Array of allowed extensions
     * @return void
     */
    public function setAllowedExtensions($extensions) {
        $this->allowedExtensions = $extensions;
    }
    
    /**
     * Set maximum file size
     *
     * @param int $size Maximum file size in bytes
     * @return void
     */
    public function setMaxFileSize($size) {
        $this->maxFileSize = $size;
    }
    
    /**
     * Set upload directory
     *
     * @param string $dir Directory path
     * @return void
     */
    public function setUploadDir($dir) {
        $this->uploadDir = rtrim($dir, '/') . '/';
    }
    
    /**
     * Upload a file
     *
     * @param array $file $_FILES array item
     * @param string $customName Custom filename (optional)
     * @return array Upload result with success status, file path and message
     */
    public function upload($file, $customName = '') {
        // Check if file is valid
        if (!isset($file['name']) || empty($file['name'])) {
            return $this->error('No file selected.');
        }
        
        // Check for errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessage = $this->getUploadErrorMessage($file['error']);
            return $this->error($errorMessage);
        }
        
        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            return $this->error('File size exceeds the maximum allowed size (' . $this->formatFileSize($this->maxFileSize) . ').');
        }
        
        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions)) {
            return $this->error('File type not allowed. Allowed types: ' . implode(', ', $this->allowedExtensions));
        }
        
        // Create upload directory if it doesn't exist
        if ($this->createDirIfNotExists && !is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                return $this->error('Failed to create upload directory.');
            }
        }
        
        // Generate filename
        if (empty($customName)) {
            $filename = $this->generateUniqueFilename($extension);
        } else {
            $filename = $this->sanitizeFilename($customName) . '.' . $extension;
        }
        
        $uploadPath = $this->uploadDir . $filename;
        
        // Convert upload path to absolute path
        $absolutePath = $_SERVER['DOCUMENT_ROOT'] . $uploadPath;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
            return $this->error('Failed to move uploaded file.');
        }
        
        // Set proper file permissions
        chmod($absolutePath, 0644);
        
        // Return success
        return [
            'success' => true,
            'file_path' => $absolutePath,
            'file_name' => $filename,
            'web_path' => $uploadPath,
            'file_size' => $file['size'],
            'file_type' => $file['type'],
            'message' => 'File uploaded successfully.'
        ];
    }
    
    /**
     * Upload multiple files
     *
     * @param array $files Array of $_FILES array items
     * @return array Array of upload results
     */
    public function uploadMultiple($files) {
        $results = [];
        
        foreach ($files as $file) {
            $results[] = $this->upload($file);
        }
        
        return $results;
    }
    
    /**
     * Delete a file
     *
     * @param string $path File path
     * @return bool True if file deleted successfully
     */
    public function delete($path) {
        if (file_exists($path)) {
            return unlink($path);
        }
        
        return false;
    }
    
    /**
     * Generate a unique filename
     *
     * @param string $extension File extension
     * @return string Unique filename
     */
    private function generateUniqueFilename($extension) {
        return uniqid() . '_' . time() . '.' . $extension;
    }
    
    /**
     * Sanitize filename
     *
     * @param string $filename Filename to sanitize
     * @return string Sanitized filename
     */
    private function sanitizeFilename($filename) {
        // Remove special characters
        $filename = preg_replace('/[^\w\-\.]/', '_', $filename);
        
        // Remove multiple underscores
        $filename = preg_replace('/_+/', '_', $filename);
        
        // Trim underscores from beginning and end
        $filename = trim($filename, '_');
        
        return $filename;
    }
    
    /**
     * Get upload error message
     *
     * @param int $errorCode PHP upload error code
     * @return string Error message
     */
    private function getUploadErrorMessage($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded.';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk.';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload.';
            default:
                return 'Unknown upload error.';
        }
    }
    
    /**
     * Format file size
     *
     * @param int $bytes File size in bytes
     * @return string Formatted file size
     */
    private function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Return error result
     *
     * @param string $message Error message
     * @return array Error result
     */
    private function error($message) {
        return [
            'success' => false,
            'message' => $message
        ];
    }
}
?>