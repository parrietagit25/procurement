<?php
// Endpoint: GET /api/products
$auth_user = $auth->requireAuth();

try {
    // Construir consulta con filtros
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE 1=1";
    
    $params = [];
    
    if(isset($_GET['category_id']) && !empty($_GET['category_id'])) {
        $query .= " AND p.category_id = :category_id";
        $params[':category_id'] = $_GET['category_id'];
    }
    
    if(isset($_GET['search']) && !empty($_GET['search'])) {
        $query .= " AND (p.name LIKE :search OR p.description LIKE :search)";
        $params[':search'] = '%' . $_GET['search'] . '%';
    }
    
    if(isset($_GET['is_active']) && $_GET['is_active'] !== '') {
        $query .= " AND p.is_active = :is_active";
        $params[':is_active'] = $_GET['is_active'] ? 1 : 0;
    }
    
    $query .= " ORDER BY p.name ASC";
    
    $stmt = $db->prepare($query);
    foreach($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $products
    ]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener productos: ' . $e->getMessage()]);
}
?>
