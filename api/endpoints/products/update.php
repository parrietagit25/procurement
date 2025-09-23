<?php
// Endpoint: PUT /api/products/{id}
$auth_user = $auth->requireAuth();

if($auth_user['type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo administradores pueden editar productos']);
    exit;
}

// Obtener datos del cuerpo de la petición
$input = json_decode(file_get_contents('php://input'), true);

try {
    // Verificar que el producto existe
    $query = "SELECT id FROM products WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();
    
    if($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Producto no encontrado']);
        exit;
    }
    
    // Validar datos requeridos
    $required_fields = ['name', 'unit'];
    foreach($required_fields as $field) {
        if(empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "El campo $field es requerido"]);
            exit;
        }
    }
    
    // Actualizar producto
    $query = "UPDATE products 
              SET name = :name, description = :description, category_id = :category_id, 
                  unit = :unit, estimated_price = :estimated_price, 
                  is_active = :is_active, updated_at = NOW() 
              WHERE id = :id";
    
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
    $stmt->bindParam(':id', $product_id);
    
    if($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Producto actualizado exitosamente'
        ]);
    } else {
        throw new Exception('Error al actualizar el producto');
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar producto: ' . $e->getMessage()]);
}
?>
