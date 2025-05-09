<?php
class ContentManager {
    private $pdo;
    private $table;
    private $language;

    public function __construct($pdo, $table, $language = 'en') {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->language = $language;
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} (title, content, meta_description, slug, language, created_at) 
                 VALUES (:title, :content, :meta_description, :slug, :language, NOW())";
        
        $stmt = $this->pdo->prepare($query);
        
        return $stmt->execute([
            ':title' => $data['title'],
            ':content' => $data['content'],
            ':meta_description' => $data['meta_description'],
            ':slug' => $this->createSlug($data['title']),
            ':language' => $this->language
        ]);
    }

    public function read($id = null, $conditions = []) {
        $query = "SELECT * FROM {$this->table} WHERE language = :language";
        $params = [':language' => $this->language];

        if ($id) {
            $query .= " AND id = :id";
            $params[':id'] = $id;
        }

        foreach ($conditions as $key => $value) {
            $query .= " AND {$key} = :{$key}";
            $params[":{$key}"] = $value;
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);

        if ($id) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                 SET title = :title, 
                     content = :content, 
                     meta_description = :meta_description, 
                     updated_at = NOW() 
                 WHERE id = :id AND language = :language";
        
        $stmt = $this->pdo->prepare($query);
        
        return $stmt->execute([
            ':id' => $id,
            ':title' => $data['title'],
            ':content' => $data['content'],
            ':meta_description' => $data['meta_description'],
            ':language' => $this->language
        ]);
    }

    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id AND language = :language";
        $stmt = $this->pdo->prepare($query);
        
        return $stmt->execute([
            ':id' => $id,
            ':language' => $this->language
        ]);
    }

    private function createSlug($title) {
        // Convert to lowercase
        $slug = strtolower($title);
        
        // Replace non-alphanumeric characters with hyphens
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        
        // Remove multiple consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Remove leading and trailing hyphens
        $slug = trim($slug, '-');
        
        return $slug;
    }

    public function search($keyword) {
        $query = "SELECT * FROM {$this->table} 
                 WHERE language = :language 
                 AND (title LIKE :keyword 
                 OR content LIKE :keyword 
                 OR meta_description LIKE :keyword)";
        
        $stmt = $this->pdo->prepare($query);
        $keyword = "%{$keyword}%";
        
        $stmt->execute([
            ':language' => $this->language,
            ':keyword' => $keyword
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBySlug($slug) {
        return $this->read(null, ['slug' => $slug]);
    }
}
?> 