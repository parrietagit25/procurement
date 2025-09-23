<?php
require_once __DIR__ . '/../config/database.php';

class Category {
    private $conn;
    private $table_name = "categories";

    public $id;
    public $name;
    public $description;
    public $parent_id;
    public $is_active;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear categoría
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (name, description, parent_id) 
                  VALUES (:name, :description, :parent_id)";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':parent_id', $this->parent_id);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Obtener categoría por ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->parent_id = $row['parent_id'];
            $this->is_active = $row['is_active'];
            return $row;
        }
        return false;
    }

    // Listar categorías
    public function getAll($parent_id = null, $limit = 50, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name;
        
        if($parent_id !== null) {
            $query .= " WHERE parent_id " . ($parent_id === 0 ? "IS NULL" : "= :parent_id");
        }
        
        $query .= " ORDER BY name LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        
        if($parent_id !== null && $parent_id !== 0) {
            $stmt->bindParam(':parent_id', $parent_id);
        }
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener categorías con jerarquía
    public function getHierarchy() {
        $query = "SELECT c1.*, 
                         (SELECT COUNT(*) FROM " . $this->table_name . " c2 WHERE c2.parent_id = c1.id) as child_count
                  FROM " . $this->table_name . " c1 
                  WHERE c1.parent_id IS NULL 
                  ORDER BY c1.name";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener subcategorías para cada categoría principal
        foreach($categories as &$category) {
            $subQuery = "SELECT * FROM " . $this->table_name . " WHERE parent_id = :parent_id ORDER BY name";
            $subStmt = $this->conn->prepare($subQuery);
            $subStmt->bindParam(':parent_id', $category['id']);
            $subStmt->execute();
            $category['subcategories'] = $subStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $categories;
    }

    // Actualizar categoría
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, description = :description, parent_id = :parent_id, 
                      is_active = :is_active 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':parent_id', $this->parent_id);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
