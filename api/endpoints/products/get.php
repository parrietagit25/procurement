<?php
// Endpoint: GET /api/products/{id}
$auth_user = $auth->requireAuth();

try {
    // Obtener ID del producto desde GET
    $product_id = $_GET['id'] ?? null;
    
    if(!$product_id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID del producto requerido']);
        exit;
    }
    
    // Obtener producto con información de categoría
    $query = "SELECT p.*, c.name as category_name
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.id = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();
    
    if($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Producto no encontrado']);
        exit;
    }
    
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Convertir is_active a boolean
    $product['is_active'] = (bool)$product['is_active'];
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $product
    ]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener producto: ' . $e->getMessage()]);
}
?>