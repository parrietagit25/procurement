<?php
// Endpoint: GET /api/quotations
$auth_user = $auth->requireAuth();

try {
    // Construir consulta con filtros
    $query = "SELECT q.*, s.company_name as supplier_name, po.title as order_title, po.order_number,
                     u.first_name, u.last_name
              FROM quotations q 
              LEFT JOIN suppliers s ON q.supplier_id = s.id 
              LEFT JOIN purchase_orders po ON q.order_id = po.id
              LEFT JOIN users u ON q.reviewed_by = u.id
              WHERE 1=1";
    
    $params = [];
    
    if(isset($_GET['order_id']) && !empty($_GET['order_id'])) {
        $query .= " AND q.order_id = :order_id";
        $params[':order_id'] = $_GET['order_id'];
    }
    
    if(isset($_GET['supplier_id']) && !empty($_GET['supplier_id'])) {
        $query .= " AND q.supplier_id = :supplier_id";
        $params[':supplier_id'] = $_GET['supplier_id'];
    }
    
    if(isset($_GET['status']) && !empty($_GET['status'])) {
        $query .= " AND q.status = :status";
        $params[':status'] = $_GET['status'];
    }
    
    if(isset($_GET['search']) && !empty($_GET['search'])) {
        $query .= " AND (s.company_name LIKE :search OR po.title LIKE :search)";
        $params[':search'] = '%' . $_GET['search'] . '%';
    }
    
    $query .= " ORDER BY q.submitted_at DESC";
    
    $stmt = $db->prepare($query);
    foreach($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
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
