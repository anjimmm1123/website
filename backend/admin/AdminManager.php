<?php
/**
 * AdminManager Class
 * Handles admin operations for managing website content
 */
class AdminManager {
    private $db;
    private $currentAdmin;
    
    /**
     * Constructor
     *
     * @param PDO $db Database connection
     * @param array $currentAdmin Current admin user data
     */
    public function __construct($db, $currentAdmin = null) {
        $this->db = $db;
        $this->currentAdmin = $currentAdmin;
    }
    
    /**
     * Get all admin users
     *
     * @param bool $includeInactive Whether to include inactive users
     * @return array Admin users
     */
    public function getAllAdmins($includeInactive = false) {
        try {
            $sql = "SELECT * FROM admin_users";
            
            if (!$includeInactive) {
                $sql .= " WHERE is_active = 1";
            }
            
            $sql .= " ORDER BY id DESC";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get all admins error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get admin user by ID
     *
     * @param int $id Admin user ID
     * @return array|null Admin user data or null if not found
     */
    public function getAdminById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get admin by ID error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create admin user
     *
     * @param array $adminData Admin user data
     * @return int|false New admin ID or false on failure
     */
    public function createAdmin($adminData) {
        try {
            // Hash password
            $adminData['password'] = password_hash($adminData['password'], PASSWORD_DEFAULT);
            
            // Insert admin user
            $stmt = $this->db->prepare("
                INSERT INTO admin_users (name, username, password, email, role, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $adminData['name'],
                $adminData['username'],
                $adminData['password'],
                $adminData['email'] ?? null,
                $adminData['role'] ?? 'editor',
                $adminData['is_active'] ?? 1
            ]);
            
            $adminId = $this->db->lastInsertId();
            
            // Log activity
            $this->logActivity('admin_created', 'Created new admin user: ' . $adminData['username']);
            
            return $adminId;
        } catch (PDOException $e) {
            error_log('Create admin error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update admin user
     *
     * @param int $id Admin user ID
     * @param array $adminData Admin user data
     * @return bool True if update successful
     */
    public function updateAdmin($id, $adminData) {
        try {
            // Check if password is being updated
            if (!empty($adminData['password'])) {
                $adminData['password'] = password_hash($adminData['password'], PASSWORD_DEFAULT);
                
                $stmt = $this->db->prepare("
                    UPDATE admin_users 
                    SET name = ?, username = ?, password = ?, email = ?, role = ?, is_active = ? 
                    WHERE id = ?
                ");
                
                $result = $stmt->execute([
                    $adminData['name'],
                    $adminData['username'],
                    $adminData['password'],
                    $adminData['email'] ?? null,
                    $adminData['role'],
                    $adminData['is_active'] ?? 1,
                    $id
                ]);
            } else {
                // Update without changing password
                $stmt = $this->db->prepare("
                    UPDATE admin_users 
                    SET name = ?, username = ?, email = ?, role = ?, is_active = ? 
                    WHERE id = ?
                ");
                
                $result = $stmt->execute([
                    $adminData['name'],
                    $adminData['username'],
                    $adminData['email'] ?? null,
                    $adminData['role'],
                    $adminData['is_active'] ?? 1,
                    $id
                ]);
            }
            
            // Log activity
            if ($result) {
                $this->logActivity('admin_updated', 'Updated admin user ID: ' . $id);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log('Update admin error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete admin user
     *
     * @param int $id Admin user ID
     * @return bool True if delete successful
     */
    public function deleteAdmin($id) {
        try {
            // Don't allow deleting self
            if ($this->currentAdmin && $this->currentAdmin['id'] == $id) {
                return false;
            }
            
            // Get admin details for logging
            $admin = $this->getAdminById($id);
            
            $stmt = $this->db->prepare("DELETE FROM admin_users WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            // Log activity
            if ($result && $admin) {
                $this->logActivity('admin_deleted', 'Deleted admin user: ' . $admin['username']);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log('Delete admin error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get admin activities
     *
     * @param int $limit Number of activities to retrieve
     * @param int $offset Offset for pagination
     * @param int $adminId Filter by admin ID (optional)
     * @return array Admin activities
     */
    public function getAdminActivities($limit = 50, $offset = 0, $adminId = null) {
        try {
            $params = [];
            
            $sql = "
                SELECT a.*, u.username, u.name 
                FROM admin_activities a
                LEFT JOIN admin_users u ON a.admin_id = u.id
            ";
            
            if ($adminId) {
                $sql .= " WHERE a.admin_id = ?";
                $params[] = $adminId;
            }
            
            $sql .= " ORDER BY a.created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get admin activities error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get site settings
     *
     * @return array Site settings
     */
    public function getSiteSettings() {
        try {
            $stmt = $this->db->query("SELECT * FROM site_settings");
            $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $result = [];
            foreach ($settings as $setting) {
                $result[$setting['setting_key']] = $setting['setting_value'];
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log('Get site settings error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update site setting
     *
     * @param string $key Setting key
     * @param string $value Setting value
     * @return bool True if update successful
     */
    public function updateSiteSetting($key, $value) {
        try {
            // Check if setting exists
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM site_settings WHERE setting_key = ?");
            $stmt->execute([$key]);
            $exists = (bool)$stmt->fetchColumn();
            
            if ($exists) {
                // Update existing setting
                $stmt = $this->db->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
                $result = $stmt->execute([$value, $key]);
            } else {
                // Insert new setting
                $stmt = $this->db->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)");
                $result = $stmt->execute([$key, $value]);
            }
            
            // Log activity
            if ($result) {
                $this->logActivity('settings_updated', 'Updated site setting: ' . $key);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log('Update site setting error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all pages
     *
     * @return array Pages
     */
    public function getAllPages() {
        try {
            $stmt = $this->db->query("SELECT * FROM pages ORDER BY title ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get all pages error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get page by ID
     *
     * @param int $id Page ID
     * @return array|null Page data or null if not found
     */
    public function getPageById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM pages WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get page by ID error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get page by slug
     *
     * @param string $slug Page slug
     * @return array|null Page data or null if not found
     */
    public function getPageBySlug($slug) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM pages WHERE slug = ?");
            $stmt->execute([$slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get page by slug error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create page
     *
     * @param array $pageData Page data
     * @return int|false New page ID or false on failure
     */
    public function createPage($pageData) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO pages (title, slug, content, meta_description, is_published, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ");
            
            $stmt->execute([
                $pageData['title'],
                $pageData['slug'],
                $pageData['content'],
                $pageData['meta_description'] ?? null,
                $pageData['is_published'] ?? 1
            ]);
            
            $pageId = $this->db->lastInsertId();
            
            // Log activity
            $this->logActivity('page_created', 'Created new page: ' . $pageData['title']);
            
            return $pageId;
        } catch (PDOException $e) {
            error_log('Create page error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update page
     *
     * @param int $id Page ID
     * @param array $pageData Page data
     * @return bool True if update successful
     */
    public function updatePage($id, $pageData) {
        try {
            $stmt = $this->db->prepare("
                UPDATE pages 
                SET title = ?, slug = ?, content = ?, meta_description = ?, is_published = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            
            $result = $stmt->execute([
                $pageData['title'],
                $pageData['slug'],
                $pageData['content'],
                $pageData['meta_description'] ?? null,
                $pageData['is_published'] ?? 1,
                $id
            ]);
            
            // Log activity
            if ($result) {
                $this->logActivity('page_updated', 'Updated page: ' . $pageData['title']);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log('Update page error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete page
     *
     * @param int $id Page ID
     * @return bool True if delete successful
     */
    public function deletePage($id) {
        try {
            // Get page details for logging
            $page = $this->getPageById($id);
            
            $stmt = $this->db->prepare("DELETE FROM pages WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            // Log activity
            if ($result && $page) {
                $this->logActivity('page_deleted', 'Deleted page: ' . $page['title']);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log('Delete page error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all programs
     *
     * @param bool $activeOnly Whether to get only active programs
     * @return array Programs
     */
    public function getAllPrograms($activeOnly = false) {
        try {
            $sql = "SELECT * FROM programs";
            
            if ($activeOnly) {
                $sql .= " WHERE is_active = 1";
            }
            
            $sql .= " ORDER BY name ASC";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get all programs error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get program by ID
     *
     * @param int $id Program ID
     * @return array|null Program data or null if not found
     */
    public function getProgramById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM programs WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get program by ID error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create program
     *
     * @param array $programData Program data
     * @return int|false New program ID or false on failure
     */
    public function createProgram($programData) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO programs (code, name, description, curriculum, image_url, is_active, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            
            $stmt->execute([
                $programData['code'],
                $programData['name'],
                $programData['description'],
                $programData['curriculum'] ?? null,
                $programData['image_url'] ?? null,
                $programData['is_active'] ?? 1
            ]);
            
            $programId = $this->db->lastInsertId();
            
            // Log activity
            $this->logActivity('program_created', 'Created new program: ' . $programData['name']);
            
            return $programId;
        } catch (PDOException $e) {
            error_log('Create program error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update program
     *
     * @param int $id Program ID
     * @param array $programData Program data
     * @return bool True if update successful
     */
    public function updateProgram($id, $programData) {
        try {
            $stmt = $this->db->prepare("
                UPDATE programs 
                SET code = ?, name = ?, description = ?, curriculum = ?, image_url = ?, is_active = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            
            $result = $stmt->execute([
                $programData['code'],
                $programData['name'],
                $programData['description'],
                $programData['curriculum'] ?? null,
                $programData['image_url'] ?? null,
                $programData['is_active'] ?? 1,
                $id
            ]);
            
            // Log activity
            if ($result) {
                $this->logActivity('program_updated', 'Updated program: ' . $programData['name']);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log('Update program error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete program
     *
     * @param int $id Program ID
     * @return bool True if delete successful
     */
    public function deleteProgram($id) {
        try {
            // Get program details for logging
            $program = $this->getProgramById($id);
            
            $stmt = $this->db->prepare("DELETE FROM programs WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            // Log activity
            if ($result && $program) {
                $this->logActivity('program_deleted', 'Deleted program: ' . $program['name']);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log('Delete program error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all gallery items
     *
     * @param bool $activeOnly Whether to get only active items
     * @return array Gallery items
     */
    public function getAllGalleryItems($activeOnly = false) {
        try {
            $sql = "SELECT * FROM gallery";
            
            if ($activeOnly) {
                $sql .= " WHERE is_active = 1";
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get all gallery items error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get gallery item by ID
     *
     * @param int $id Gallery item ID
     * @return array|null Gallery item data or null if not found
     */
    public function getGalleryItemById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM gallery WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get gallery item by ID error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create gallery item
     *
     * @param array $galleryData Gallery item data
     * @return int|false New gallery item ID or false on failure
     */
    public function createGalleryItem($galleryData) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO gallery (title, description, image_url, category, is_active, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ");
            
            $stmt->execute([
                $galleryData['title'],
                $galleryData['description'] ?? null,
                $galleryData['image_url'],
                $galleryData['category'] ?? 'general',
                $galleryData['is_active'] ?? 1
            ]);
            
            $galleryId = $this->db->lastInsertId();
            
            // Log activity
            $this->logActivity('gallery_created', 'Added new gallery item: ' . $galleryData['title']);
            
            return $galleryId;
        } catch (PDOException $e) {
            error_log('Create gallery item error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update gallery item
     *
     * @param int $id Gallery item ID
     * @param array $galleryData Gallery item data
     * @return bool True if update successful
     */
    public function updateGalleryItem($id, $galleryData) {
        try {
            $stmt = $this->db->prepare("
                UPDATE gallery 
                SET title = ?, description = ?, image_url = ?, category = ?, is_active = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            
            $result = $stmt->execute([
                $galleryData['title'],
                $galleryData['description'] ?? null,
                $galleryData['image_url'],
                $galleryData['category'] ?? 'general',
                $galleryData['is_active'] ?? 1,
                $id
            ]);
            
            // Log activity
            if ($result) {
                $this->logActivity('gallery_updated', 'Updated gallery item: ' . $galleryData['title']);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log('Update gallery item error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete gallery item
     *
     * @param int $id Gallery item ID
     * @return bool True if delete successful
     */
    public function deleteGalleryItem($id) {
        try {
            // Get gallery item details for logging
            $galleryItem = $this->getGalleryItemById($id);
            
            $stmt = $this->db->prepare("DELETE FROM gallery WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            // Log activity
            if ($result && $galleryItem) {
                $this->logActivity('gallery_deleted', 'Deleted gallery item: ' . $galleryItem['title']);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log('Delete gallery item error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all student applications
     *
     * @param string $status Filter by status (optional)
     * @return array Student applications
     */
    public function getAllApplications($status = '') {
        try {
            $params = [];
            $sql = "
                SELECT a.*, p.name as program_name 
                FROM student_applications a
                LEFT JOIN programs p ON a.program_id = p.id
            ";
            
            if (!empty($status)) {
                $sql .= " WHERE a.application_status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY a.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get all applications error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get student application by ID
     *
     * @param int $id Application ID
     * @return array|null Application data or null if not found
     */
    public function getApplicationById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, p.name as program_name 
                FROM student_applications a
                LEFT JOIN programs p ON a.program_id = p.id
                WHERE a.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get application by ID error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update application status
     *
     * @param int $id Application ID
     * @param string $status New status
     * @param string $notes Admin notes
     * @return bool True if update successful
     */
    public function updateApplicationStatus($id, $status, $notes = '') {
        try {
            $stmt = $this->db->prepare("
                UPDATE student_applications 
                SET application_status = ?, admin_notes = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            
            $result = $stmt->execute([$status, $notes, $id]);
            
            // Log activity
            if ($result) {
                $application = $this->getApplicationById($id);
                if ($application) {
                    $this->logActivity(
                        'application_status_updated', 
                        "Updated application status for {$application['full_name']} to {$status}"
                    );
                }
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log('Update application status error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log admin activity
     *
     * @param string $action Action performed
     * @param string $description Description of activity
     * @param array $data Additional data (optional)
     * @return bool True if logging successful
     */
    private function logActivity($action, $description, $data = null) {
        // Check if current admin is set
        if (!$this->currentAdmin) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO admin_activities (admin_id, action, description, data, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $adminId = $this->currentAdmin['id'];
            $jsonData = $data ? json_encode($data) : null;
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            return $stmt->execute([$adminId, $action, $description, $jsonData, $ipAddress, $userAgent]);
        } catch (PDOException $e) {
            error_log('Log admin activity error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get dashboard statistics
     *
     * @return array Dashboard statistics
     */
    public function getDashboardStats() {
        try {
            $stats = [];
            
            // Total programs
            $stmt = $this->db->query("SELECT COUNT(*) FROM programs");
            $stats['programs_count'] = $stmt->fetchColumn();
            
            // Total gallery items
            $stmt = $this->db->query("SELECT COUNT(*) FROM gallery");
            $stats['gallery_count'] = $stmt->fetchColumn();
            
            // Total pages
            $stmt = $this->db->query("SELECT COUNT(*) FROM pages");
            $stats['pages_count'] = $stmt->fetchColumn();
            
            // Total applications
            $stmt = $this->db->query("SELECT COUNT(*) FROM student_applications");
            $stats['applications_count'] = $stmt->fetchColumn();
            
            // Applications by status
            $stmt = $this->db->query("
                SELECT application_status, COUNT(*) as count 
                FROM student_applications 
                GROUP BY application_status
            ");
            $stats['applications_by_status'] = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stats['applications_by_status'][$row['application_status']] = $row['count'];
            }
            
            // Recent applications
            $stmt = $this->db->query("
                SELECT a.*, p.name as program_name 
                FROM student_applications a
                LEFT JOIN programs p ON a.program_id = p.id
                ORDER BY a.created_at DESC 
                LIMIT 5
            ");
            $stats['recent_applications'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Recent admin activities
            $stmt = $this->db->query("
                SELECT a.*, u.username, u.name 
                FROM admin_activities a
                LEFT JOIN admin_users u ON a.admin_id = u.id
                ORDER BY a.created_at DESC 
                LIMIT 10
            ");
            $stats['recent_activities'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $stats;
        } catch (PDOException $e) {
            error_log('Get dashboard stats error: ' . $e->getMessage());
            return [];
        }
    }
}
?>