<?php
// Endpoint: POST /api/products
$auth_user = $auth->requireAuth();

if($auth_user['type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo administradores pueden crear productos']);
    exit;
}

// Obtener datos del cuerpo de la petición
$input = json_decode(file_get_contents('php://input'), true);

try {
    // Validar datos requeridos
    $required_fields = ['name', 'unit'];
    foreach($required_fields as $field) {
        if(empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "El campo $field es requerido"]);
            exit;
        }
    }
    
    // Crear producto
    $query = "INSERT INTO products 
              (name, description, category_id, unit, estimated_price, is_active) 
              VALUES (:name, :description, :category_id, :unit, :estimated_price, :is_active)";
    
    $stmt = $db->prepare($query);
    
    // Preparar valores para bindParam
    $description = $input['description'] ?? null;
    $category_id = $input['category_id'] ?? null;
    $estimated_price = $input['estimated_price'] ?? 0.00;
    $is_active = isset($input['is_active']) ? ($input['is_active'] ? 1 : 0) : 1;
    
    $stmt->bindParam(':name', $input['name']);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->bindParam(':unit', $input['unit']);
    $stmt->bindParam(':estimated_price', $estimated_price);
    $stmt->bindParam(':is_active', $is_active);
    
    if($stmt->execute()) {
        $product_id = $db->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Producto creado exitosamente',
            'data' => ['id' => $product_id]
        ]);
    } else {
        throw new Exception('Error al crear el producto');
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al crear producto: ' . $e->getMessage()]);
}
?>
