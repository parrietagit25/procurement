<?php
require_once __DIR__ . '/../config/database.php';

class Supplier {
    private $conn;
    private $table_name = "suppliers";

    public $id;
    public $company_name;
    public $contact_name;
    public $email;
    public $phone;
    public $address;
    public $city;
    public $state;
    public $country;
    public $postal_code;
    public $tax_id;
    public $bank_account;
    public $bank_name;
    public $status;
    public $approved_by;
    public $approved_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener proveedor por email
    public function getByEmail($email) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->company_name = $row['company_name'];
            $this->contact_name = $row['contact_name'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->address = $row['address'];
            $this->city = $row['city'];
            $this->state = $row['state'];
            $this->country = $row['country'];
            $this->postal_code = $row['postal_code'];
            $this->tax_id = $row['tax_id'];
            $this->bank_account = $row['bank_account'];
            $this->bank_name = $row['bank_name'];
            $this->status = $row['status'];
            $this->approved_by = $row['approved_by'];
            $this->approved_at = $row['approved_at'];
            return true;
        }
        return false;
    }

    // Obtener proveedor por ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->company_name = $row['company_name'];
            $this->contact_name = $row['contact_name'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->address = $row['address'];
            $this->city = $row['city'];
            $this->state = $row['state'];
            $this->country = $row['country'];
            $this->postal_code = $row['postal_code'];
            $this->tax_id = $row['tax_id'];
            $this->bank_account = $row['bank_account'];
            $this->bank_name = $row['bank_name'];
            $this->status = $row['status'];
            $this->approved_by = $row['approved_by'];
            $this->approved_at = $row['approved_at'];
            return true;
        }
        return false;
    }

    // Listar proveedores
    public function getAll($limit = 50, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Aprobar proveedor
    public function approve() {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = 'approved', approved_at = NOW() 
                  WHERE id = :id AND status = 'pending'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            $this->status = 'approved';
            return true;
        }
        return false;
    }
}
?>