<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Supplier.php';
require_once __DIR__ . '/JWT.php';

class Auth {
    private $conn;
    private $user;
    private $supplier;

    public function __construct($db) {
        $this->conn = $db;
        $this->user = new User($db);
        $this->supplier = new Supplier($db);
    }

    // Autenticar usuario interno
    public function loginUser($username, $password) {
        if($this->user->authenticate($username, $password)) {
            $token = JWT::encode([
                'user_id' => $this->user->id,
                'username' => $this->user->username,
                'email' => $this->user->email,
                'role' => $this->user->role,
                'user_type' => 'admin'
            ]);
            
            return [
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $this->user->id,
                    'username' => $this->user->username,
                    'email' => $this->user->email,
                    'first_name' => $this->user->first_name,
                    'last_name' => $this->user->last_name,
                    'role' => $this->user->role,
                    'department' => $this->user->department
                ]
            ];
        }
        
        return ['success' => false, 'message' => 'Credenciales inválidas'];
    }

    // Autenticar proveedor
    public function loginSupplier($email, $password) {
        if($this->supplier->getByEmail($email)) {
            // Verificar si el proveedor está aprobado
            if($this->supplier->status !== 'approved') {
                return ['success' => false, 'message' => 'Su cuenta aún no ha sido aprobada'];
            }
            
            // Para proveedores, usamos el email como contraseña temporalmente
            if($password === 'supplier123') { // Contraseña temporal
                $token = JWT::encode([
                    'supplier_id' => $this->supplier->id,
                    'email' => $this->supplier->email,
                    'company_name' => $this->supplier->company_name,
                    'user_type' => 'supplier'
                ]);
                
                return [
                    'success' => true,
                    'token' => $token,
                    'supplier' => [
                        'id' => $this->supplier->id,
                        'company_name' => $this->supplier->company_name,
                        'contact_name' => $this->supplier->contact_name,
                        'email' => $this->supplier->email,
                        'phone' => $this->supplier->phone
                    ]
                ];
            }
        }
        
        return ['success' => false, 'message' => 'Credenciales inválidas'];
    }

    // Cerrar sesión
    public function logout() {
        return ['success' => true, 'message' => 'Sesión cerrada correctamente'];
    }

    // Requerir autenticación
    public function requireAuth() {
        $token = null;
        
        // Método principal: usar getallheaders() que funciona mejor
        $headers = getallheaders();
        if($headers && isset($headers['Authorization'])) {
            $auth = $headers['Authorization'];
            if(preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
                $token = $matches[1];
            }
        }
        
        // Método alternativo: HTTP_AUTHORIZATION
        if(!$token && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['HTTP_AUTHORIZATION'];
            if(preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
                $token = $matches[1];
            }
        }
        
        // Buscar token en query parameter
        if(!$token && isset($_GET['token'])) {
            $token = $_GET['token'];
        }
        
        // Debug: mostrar información de headers
        error_log("Auth Debug - Token encontrado: " . ($token ? 'Sí' : 'No'));
        if($token) {
            error_log("Auth Debug - Token: " . substr($token, 0, 50) . '...');
        }
        
        if(!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'Token de autenticación requerido']);
            exit;
        }
        
        try {
            $payload = JWT::decode($token);
            
            // Determinar tipo de usuario
            if(isset($payload['user_type'])) {
                if($payload['user_type'] === 'admin') {
                    // Cargar datos del usuario
                    $this->user->getById($payload['user_id']);
                    return [
                        'type' => 'admin',
                        'user' => $this->user
                    ];
                } else {
                    // Cargar datos del proveedor
                    $this->supplier->getById($payload['user_id']);
                    return [
                        'type' => 'supplier',
                        'supplier' => $this->supplier
                    ];
                }
            }
            
            http_response_code(401);
            echo json_encode(['error' => 'Token inválido']);
            exit;
            
        } catch(Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Token inválido: ' . $e->getMessage()]);
            exit;
        }
    }
}
?>