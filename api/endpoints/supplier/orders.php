<?php
// Endpoint: GET /api/supplier/orders
$auth_user = $auth->requireAuth();

if($auth_user['type'] !== 'supplier') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo proveedores pueden acceder a este endpoint']);
    exit;
}

try {
    $supplier_id = $auth_user['supplier']->id;
    
    // Obtener órdenes asignadas al proveedor
    $query = "SELECT po.*, os.status as assignment_status, os.invited_at, os.responded_at
              FROM purchase_orders po
              JOIN order_suppliers os ON po.id = os.order_id
              WHERE os.supplier_id = :supplier_id
              ORDER BY po.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':supplier_id', $supplier_id);
    $stmt->execute();
    
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener items de cada orden
    foreach($orders as &$order) {
        $itemsQuery = "SELECT poi.*, p.name as product_name, p.description as product_description
                       FROM purchase_order_items poi
                       LEFT JOIN products p ON poi.product_id = p.id
                       WHERE poi.order_id = :order_id";
        $itemsStmt = $db->prepare($itemsQuery);
        $itemsStmt->bindParam(':order_id', $order['id']);
        $itemsStmt->execute();
        $order['items'] = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $orders
    ]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener órdenes: ' . $e->getMessage()]);
}
?>
