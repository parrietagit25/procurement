<?php
// Endpoint: PUT /api/products/{id}/toggle-status
$auth_user = $auth->requireAuth();

if($auth_user['type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo administradores pueden cambiar el estado de productos']);
    exit;
}

// Obtener datos del cuerpo de la petición
$input = json_decode(file_get_contents('php://input'), true);

try {
    // Obtener ID del producto desde GET
    $product_id = $_GET['id'] ?? null;
    
    if(!$product_id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID del producto requerido']);
        exit;
    }
    
    // Verificar que el producto existe
    $query = "SELECT id, name, is_active FROM products WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();
    
    if($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Producto no encontrado']);
        exit;
    }
    
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Determinar el nuevo estado
    $new_status = null;
    if(isset($input['is_active'])) {
        // Si se especifica explícitamente el estado
        $new_status = $input['is_active'] ? 1 : 0;
    } else {
        // Si no se especifica, alternar el estado actual
        $new_status = $product['is_active'] ? 0 : 1;
    }
    
    // Actualizar el estado del producto
    $query = "UPDATE products 
              SET is_active = :is_active, updated_at = NOW() 
              WHERE id = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':is_active', $new_status, PDO::PARAM_INT);
    $stmt->bindParam(':id', $product_id);
    
    if($stmt->execute()) {
        $status_text = $new_status ? 'activado' : 'desactivado';
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => "Producto {$status_text} exitosamente",
            'product_name' => $product['name'],
            'new_status' => (bool)$new_status,
            'status_text' => $new_status ? 'Activo' : 'Inactivo'
        ]);
    } else {
        throw new Exception('Error al cambiar el estado del producto');
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al cambiar estado del producto: ' . $e->getMessage()]);
}
?>
