<?php
// Endpoint: GET /api/products/{id}
$auth_user = $auth->requireAuth();

if($auth_user['type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo administradores pueden ver productos']);
    exit;
}

try {
    // Obtener datos completos del producto desde la base de datos
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();
    $productData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$productData) {
        http_response_code(404);
        echo json_encode(['error' => 'Producto no encontrado']);
        exit;
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $productData
    ]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener producto: ' . $e->getMessage()]);
}
?>
