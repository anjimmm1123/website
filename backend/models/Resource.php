<?php
require_once __DIR__ . '/../config/database.php';

class Resource {
    private $conn;
    private $table_name = "resources";

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function create($title, $description, $type, $file_path, $created_by, $categories = []) {
        try {
            $this->conn->beginTransaction();

            $query = "INSERT INTO " . $this->table_name . " (title, description, type, file_path, created_by) 
                     VALUES (:title, :description, :type, :file_path, :created_by)";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":description", $description);
            $stmt->bindParam(":type", $type);
            $stmt->bindParam(":file_path", $file_path);
            $stmt->bindParam(":created_by", $created_by);
            
            if($stmt->execute()) {
                $resource_id = $this->conn->lastInsertId();
                
                // Add categories if provided
                if(!empty($categories)) {
                    $this->addCategories($resource_id, $categories);
                }
                
                $this->conn->commit();
                return ['success' => true, 'id' => $resource_id];
            }
            
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Failed to create resource'];
            
        } catch(Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getById($id) {
        $query = "SELECT r.*, u.username as creator_name 
                 FROM " . $this->table_name . " r 
                 LEFT JOIN users u ON r.created_by = u.id 
                 WHERE r.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT r.*, u.username as creator_name 
                 FROM " . $this->table_name . " r 
                 LEFT JOIN users u ON r.created_by = u.id 
                 ORDER BY r.created_at DESC 
                 LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $title, $description, $type, $categories = []) {
        try {
            $this->conn->beginTransaction();
            
            $query = "UPDATE " . $this->table_name . " 
                     SET title = :title, description = :description, type = :type 
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":description", $description);
            $stmt->bindParam(":type", $type);
            
            if($stmt->execute()) {
                // Update categories if provided
                if(!empty($categories)) {
                    $this->updateCategories($id, $categories);
                }
                
                $this->conn->commit();
                return ['success' => true];
            }
            
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Failed to update resource'];
            
        } catch(Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete($id) {
        try {
            $this->conn->beginTransaction();
            
            // Delete resource categories first
            $query = "DELETE FROM resource_categories WHERE resource_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            // Delete the resource
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            
            if($stmt->execute()) {
                $this->conn->commit();
                return ['success' => true];
            }
            
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Failed to delete resource'];
            
        } catch(Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function addCategories($resource_id, $categories) {
        $query = "INSERT INTO resource_categories (resource_id, category_id) VALUES (:resource_id, :category_id)";
        $stmt = $this->conn->prepare($query);
        
        foreach($categories as $category_id) {
            $stmt->bindParam(":resource_id", $resource_id);
            $stmt->bindParam(":category_id", $category_id);
            $stmt->execute();
        }
    }

    private function updateCategories($resource_id, $categories) {
        // First delete existing categories
        $query = "DELETE FROM resource_categories WHERE resource_id = :resource_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":resource_id", $resource_id);
        $stmt->execute();
        
        // Then add new categories
        $this->addCategories($resource_id, $categories);
    }
}
?> 