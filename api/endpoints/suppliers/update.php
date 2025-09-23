<?php
// Endpoint: PUT /api/suppliers/{id}
$auth_user = $auth->requireAuth();

// Obtener datos del cuerpo de la petición
$input = json_decode(file_get_contents('php://input'), true);

if($auth_user['type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo administradores pueden editar proveedores']);
    exit;
}

try {
    $supplier = new Supplier($db);
    
    if(!$supplier->getById($supplier_id)) {
        http_response_code(404);
        echo json_encode(['error' => 'Proveedor no encontrado']);
        exit;
    }
    
    // Validar datos requeridos
    $required_fields = ['company_name', 'contact_name', 'email'];
    foreach($required_fields as $field) {
        if(empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "El campo $field es requerido"]);
            exit;
        }
    }
    
    // Verificar si el email ya existe en otro proveedor
    $query = "SELECT id FROM suppliers WHERE email = :email AND id != :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $input['email']);
    $stmt->bindParam(':id', $supplier_id);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Ya existe otro proveedor con este email']);
        exit;
    }
    
    // Actualizar proveedor
    $query = "UPDATE suppliers 
              SET company_name = :company_name, contact_name = :contact_name, email = :email, 
                  phone = :phone, address = :address, city = :city, state = :state, 
                  country = :country, postal_code = :postal_code, tax_id = :tax_id, 
                  bank_account = :bank_account, bank_name = :bank_name, updated_at = NOW() 
              WHERE id = :id";
    
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
    $stmt->bindParam(':id', $supplier_id);
    
    if($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Proveedor actualizado exitosamente'
        ]);
    } else {
        throw new Exception('Error al actualizar el proveedor');
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar proveedor: ' . $e->getMessage()]);
}
?>
