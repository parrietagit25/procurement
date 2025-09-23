<?php
// Endpoint: POST /api/suppliers
$auth_user = $auth->requireAuth();

// Obtener datos del cuerpo de la petición
$input = json_decode(file_get_contents('php://input'), true);

if($auth_user['type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo administradores pueden crear proveedores']);
    exit;
}

try {
    $supplier = new Supplier($db);
    
    // Validar datos requeridos
    $required_fields = ['company_name', 'contact_name', 'email'];
    foreach($required_fields as $field) {
        if(empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "El campo $field es requerido"]);
            exit;
        }
    }
    
    // Verificar si el email ya existe
    $query = "SELECT id FROM suppliers WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $input['email']);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Ya existe un proveedor con este email']);
        exit;
    }
    
    // Crear proveedor
    $query = "INSERT INTO suppliers 
              (company_name, contact_name, email, phone, address, city, state, country, postal_code, tax_id, bank_account, bank_name, status) 
              VALUES (:company_name, :contact_name, :email, :phone, :address, :city, :state, :country, :postal_code, :tax_id, :bank_account, :bank_name, 'pending')";
    
    $stmt = $db->prepare($query);
    
    // Preparar valores para bindParam
    $phone = $input['phone'] ?? null;
    $address = $input['address'] ?? null;
    $city = $input['city'] ?? null;
    $state = $input['state'] ?? null;
    $country = $input['country'] ?? 'México';
    $postal_code = $input['postal_code'] ?? null;
    $tax_id = $input['tax_id'] ?? null;
    $bank_account = $input['bank_account'] ?? null;
    $bank_name = $input['bank_name'] ?? null;
    
    $stmt->bindParam(':company_name', $input['company_name']);
    $stmt->bindParam(':contact_name', $input['contact_name']);
    $stmt->bindParam(':email', $input['email']);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':state', $state);
    $stmt->bindParam(':country', $country);
    $stmt->bindParam(':postal_code', $postal_code);
    $stmt->bindParam(':tax_id', $tax_id);
    $stmt->bindParam(':bank_account', $bank_account);
    $stmt->bindParam(':bank_name', $bank_name);
    
    if($stmt->execute()) {
        $supplier_id = $db->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Proveedor creado exitosamente',
            'data' => ['id' => $supplier_id]
        ]);
    } else {
        throw new Exception('Error al crear el proveedor');
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al crear proveedor: ' . $e->getMessage()]);
}
?>