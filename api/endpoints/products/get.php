<?php
// Endpoint: GET /api/products/{id}
$auth_user = $auth->requireAuth();

try {
    // Obtener producto con información de categoría y proveedor
    $query = "SELECT p.*, c.name as category_name, s.company_name as supplier_name
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              LEFT JOIN suppliers s ON p.supplier_id = s.id 
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