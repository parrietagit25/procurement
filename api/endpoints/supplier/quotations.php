<?php
// Endpoint: GET /api/supplier/quotations
$auth_user = $auth->requireAuth();

if($auth_user['type'] !== 'supplier') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo proveedores pueden acceder a este endpoint']);
    exit;
}

try {
    $supplier_id = $auth_user['supplier']->id;
    
    // Obtener cotizaciones del proveedor
    $query = "SELECT q.*, po.title as order_title, po.order_number
              FROM quotations q
              JOIN purchase_orders po ON q.order_id = po.id
              WHERE q.supplier_id = :supplier_id
              ORDER BY q.submitted_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':supplier_id', $supplier_id);
    $stmt->execute();
    
    $quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener items de cada cotización
    foreach($quotations as &$quotation) {
        $itemsQuery = "SELECT qi.*, poi.product_name, poi.description, poi.quantity, poi.unit
                       FROM quotation_items qi
                       JOIN purchase_order_items poi ON qi.order_item_id = poi.id
                       WHERE qi.quotation_id = :quotation_id";
        $itemsStmt = $db->prepare($itemsQuery);
        $itemsStmt->bindParam(':quotation_id', $quotation['id']);
        $itemsStmt->execute();
        $quotation['items'] = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $quotations
    ]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener cotizaciones: ' . $e->getMessage()]);
}
?>
