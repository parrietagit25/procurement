<?php
// Endpoint: GET /api/quotations/{id}
$auth_user = $auth->requireAuth();

try {
    // Obtener datos completos de la cotización
    $query = "SELECT q.*, s.company_name as supplier_name, s.contact_name, s.email, s.phone,
                     po.title as order_title, po.order_number, po.description as order_description,
                     u.first_name, u.last_name
              FROM quotations q 
              LEFT JOIN suppliers s ON q.supplier_id = s.id 
              LEFT JOIN purchase_orders po ON q.order_id = po.id
              LEFT JOIN users u ON q.reviewed_by = u.id
              WHERE q.id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $quotation_id);
    $stmt->execute();
    $quotationData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$quotationData) {
        http_response_code(404);
        echo json_encode(['error' => 'Cotización no encontrada']);
        exit;
    }
    
    // Obtener items de la cotización
    $itemsQuery = "SELECT qi.*, poi.product_name, poi.description, poi.quantity, poi.unit 
                   FROM quotation_items qi 
                   JOIN purchase_order_items poi ON qi.order_item_id = poi.id 
                   WHERE qi.quotation_id = :quotation_id";
    $itemsStmt = $db->prepare($itemsQuery);
    $itemsStmt->bindParam(':quotation_id', $quotation_id);
    $itemsStmt->execute();
    $quotationData['items'] = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $quotationData
    ]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener cotización: ' . $e->getMessage()]);
}
?>
