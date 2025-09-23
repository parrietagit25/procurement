<?php
// API unificada de login para usuarios internos y proveedores
// Solo permitir POST
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Obtener datos del POST
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if(empty($username) || empty($password)) {
    echo json_encode(['error' => 'Username y password son requeridos']);
    exit;
}

try {
    // Conectar a la base de datos
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../classes/JWT.php';
    $db = getDB();
    
    if(!$db) {
        throw new Exception('Error de conexión a la base de datos');
    }
    
    // Primero intentar como usuario interno
    $query = "SELECT id, username, email, password_hash, first_name, last_name, role, department, is_active 
              FROM users 
              WHERE (username = :username OR email = :email) AND is_active = 1";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $username);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        // Usuario interno encontrado
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(password_verify($password, $user['password_hash'])) {
            // Login exitoso como usuario interno
            $token = JWT::encode([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
                'user_type' => 'admin'
            ]);
            
            echo json_encode([
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'role' => $user['role'],
                    'department' => $user['department']
                ]
            ]);
            exit;
        }
    }
    
    // Si no es usuario interno, intentar como proveedor
    $query = "SELECT id, company_name, contact_name, email, phone, address, city, state, status, password_hash 
              FROM suppliers 
              WHERE email = :email";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $username);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verificar si el proveedor está aprobado
        if($supplier['status'] !== 'approved') {
            echo json_encode(['success' => false, 'message' => 'Su cuenta aún no ha sido aprobada']);
            exit;
        }
        
        // Verificar contraseña del proveedor
        if(password_verify($password, $supplier['password_hash'])) {
            // Login exitoso como proveedor
            $token = JWT::encode([
                'user_id' => $supplier['id'],
                'email' => $supplier['email'],
                'company_name' => $supplier['company_name'],
                'user_type' => 'supplier'
            ]);
            
            echo json_encode([
                'success' => true,
                'token' => $token,
                'supplier' => [
                    'id' => $supplier['id'],
                    'company_name' => $supplier['company_name'],
                    'contact_name' => $supplier['contact_name'],
                    'email' => $supplier['email'],
                    'phone' => $supplier['phone']
                ]
            ]);
            exit;
        }
    }
    
    // Si llegamos aquí, las credenciales son incorrectas
    echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
    
} catch(Exception $e) {
    echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
}
?>
