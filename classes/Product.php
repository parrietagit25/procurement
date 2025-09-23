<?php
require_once __DIR__ . '/../config/database.php';

class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $name;
    public $description;
    public $category_id;
    public $unit;
    public $estimated_price;
    public $is_active;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear producto
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (name, description, category_id, unit, estimated_price) 
                  VALUES (:name, :description, :category_id, :unit, :estimated_price)";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->unit = htmlspecialchars(strip_tags($this->unit));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':unit', $this->unit);
        $stmt->bindParam(':estimated_price', $this->estimated_price);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Obtener producto por ID
    public function getById($id) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->category_id = $row['category_id'];
            $this->unit = $row['unit'];
            $this->estimated_price = $row['estimated_price'];
            $this->is_active = $row['is_active'];
            return $row;
        }
        return false;
    }

    // Listar productos
    public function getAll($filters = [], $limit = 50, $offset = 0) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE 1=1";
        
        $params = [];
        
        if(isset($filters['category_id'])) {
            $query .= " AND p.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }
        
        if(isset($filters['search'])) {
            $query .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params[':search'] = "%{$filters['search']}%";
        }
        
        if(isset($filters['is_active'])) {
            $query .= " AND p.is_active = :is_active";
            $params[':is_active'] = $filters['is_active'];
        }
        
        $query .= " ORDER BY p.name LIMIT :limit OFFSET :offset";
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        $stmt = $this->conn->prepare($query);
        foreach($params as $key => $value) {
            if($key === ':limit' || $key === ':offset') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar producto
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, description = :description, category_id = :category_id, 
                      unit = :unit, estimated_price = :estimated_price, is_active = :is_active 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->unit = htmlspecialchars(strip_tags($this->unit));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':unit', $this->unit);
        $stmt->bindParam(':estimated_price', $this->estimated_price);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Buscar productos
    public function search($search_term, $limit = 20) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE (p.name LIKE :search OR p.description LIKE :search) 
                  AND p.is_active = 1 
                  ORDER BY p.name 
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $search_term = "%{$search_term}%";
        $stmt->bindParam(':search', $search_term);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
