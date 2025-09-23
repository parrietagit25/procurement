<?php
require_once __DIR__ . '/../config/database.php';

class PurchaseOrder {
    private $conn;
    private $table_name = "purchase_orders";

    public $id;
    public $order_number;
    public $title;
    public $description;
    public $requested_by;
    public $department;
    public $priority;
    public $status;
    public $total_amount;
    public $currency;
    public $required_date;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear orden de compra
    public function create() {
        // Generar número de orden
        $this->order_number = $this->generateOrderNumber();
        
        $query = "INSERT INTO " . $this->table_name . " 
                  (order_number, title, description, requested_by, department, priority, status, total_amount, currency, required_date) 
                  VALUES (:order_number, :title, :description, :requested_by, :department, :priority, :status, :total_amount, :currency, :required_date)";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->department = htmlspecialchars(strip_tags($this->department));

        $stmt->bindParam(':order_number', $this->order_number);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':requested_by', $this->requested_by);
        $stmt->bindParam(':department', $this->department);
        $stmt->bindParam(':priority', $this->priority);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':total_amount', $this->total_amount);
        $stmt->bindParam(':currency', $this->currency);
        $stmt->bindParam(':required_date', $this->required_date);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Obtener orden por ID
    public function getById($id) {
        $query = "SELECT po.*, u.first_name, u.last_name 
                  FROM " . $this->table_name . " po 
                  LEFT JOIN users u ON po.requested_by = u.id 
                  WHERE po.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->order_number = $row['order_number'];
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->requested_by = $row['requested_by'];
            $this->department = $row['department'];
            $this->priority = $row['priority'];
            $this->status = $row['status'];
            $this->total_amount = $row['total_amount'];
            $this->currency = $row['currency'];
            $this->required_date = $row['required_date'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return $row;
        }
        return false;
    }

    // Listar órdenes
    public function getAll($filters = [], $limit = 50, $offset = 0) {
        $query = "SELECT po.*, u.first_name, u.last_name 
                  FROM " . $this->table_name . " po 
                  LEFT JOIN users u ON po.requested_by = u.id 
                  WHERE 1=1";
        
        $params = [];
        
        if(isset($filters['status'])) {
            $query .= " AND po.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if(isset($filters['priority'])) {
            $query .= " AND po.priority = :priority";
            $params[':priority'] = $filters['priority'];
        }
        
        if(isset($filters['search'])) {
            $query .= " AND (po.title LIKE :search OR po.order_number LIKE :search)";
            $params[':search'] = "%{$filters['search']}%";
        }
        
        $query .= " ORDER BY po.created_at DESC LIMIT :limit OFFSET :offset";
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

    // Actualizar estado de la orden
    public function updateStatus($status) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status, updated_at = NOW() 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            $this->status = $status;
            return true;
        }
        return false;
    }

    // Generar número de orden
    private function generateOrderNumber() {
        $prefix = 'PO';
        $year = date('Y');
        $month = date('m');
        
        // Obtener el último número de orden del mes
        $query = "SELECT order_number FROM " . $this->table_name . " 
                  WHERE order_number LIKE :pattern 
                  ORDER BY order_number DESC LIMIT 1";
        
        $pattern = $prefix . $year . $month . '%';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pattern', $pattern);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $lastOrder = $stmt->fetch(PDO::FETCH_ASSOC);
            $lastNumber = intval(substr($lastOrder['order_number'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
?>