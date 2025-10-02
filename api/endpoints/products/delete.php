<?php
// Endpoint: DELETE /api/products/{id}
$auth_user = $auth->requireAuth();

if($auth_user['type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo administradores pueden eliminar productos']);
    exit;
}

try {
    // Obtener ID del producto desde GET
    $product_id = $_GET['id'] ?? null;
    
    if(!$product_id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID del producto requerido']);
        exit;
    }
    
    // Verificar que el producto existe
    $query = "SELECT id, name FROM products WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();
    
    if($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Producto no encontrado']);
        exit;
    }
    
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verificar si el producto está siendo usado en órdenes de compra
    $query = "SELECT COUNT(*) as count FROM purchase_order_items WHERE product_id = :product_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($result['count'] > 0) {
        http_response_code(400);
        echo json_encode([
            'error' => 'No se puede eliminar el producto porque está siendo usado en órdenes de compra',
            'used_in_orders' => (int)$result['count']
        ]);
        exit;
    }
    
    // Eliminar el producto
    $query = "DELETE FROM products WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $product_id);
    
    if($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Producto eliminado exitosamente',
            'deleted_product' => $product['name']
        ]);
    } else {
        throw new Exception('Error al eliminar el producto');
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al eliminar producto: ' . $e->getMessage()]);
}
?>
