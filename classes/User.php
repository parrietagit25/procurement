<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password_hash;
    public $first_name;
    public $last_name;
    public $role;
    public $department;
    public $is_active;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Autenticar usuario
    public function authenticate($username, $password) {
        $query = "SELECT id, username, email, password_hash, first_name, last_name, role, department, is_active 
                  FROM " . $this->table_name . " 
                  WHERE (username = :username OR email = :email) AND is_active = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $username);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(password_verify($password, $row['password_hash'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->first_name = $row['first_name'];
                $this->last_name = $row['last_name'];
                $this->role = $row['role'];
                $this->department = $row['department'];
                $this->is_active = $row['is_active'];
                
                return true;
            }
        }
        return false;
    }

    // Obtener usuario por ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->role = $row['role'];
            $this->department = $row['department'];
            $this->is_active = $row['is_active'];
            return true;
        }
        return false;
    }
}
?>