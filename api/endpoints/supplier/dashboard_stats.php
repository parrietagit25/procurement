<?php
// Endpoint: GET /api/supplier/dashboard_stats
$auth_user = $auth->requireAuth();

if($auth_user['type'] !== 'supplier') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo proveedores pueden acceder a este endpoint']);
    exit;
}

try {
    $supplier_id = $auth_user['supplier']->id;
    
    // Total de órdenes asignadas
    $query = "SELECT COUNT(*) as total_orders 
              FROM order_suppliers os 
              WHERE os.supplier_id = :supplier_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':supplier_id', $supplier_id);
    $stmt->execute();
    $total_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];
    
    // Órdenes pendientes de cotización
    $query = "SELECT COUNT(*) as pending_quotations 
              FROM order_suppliers os 
              INNER JOIN purchase_orders po ON os.order_id = po.id 
              WHERE os.supplier_id = :supplier_id AND os.status = 'invited' AND po.status = 'enviado'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':supplier_id', $supplier_id);
    $stmt->execute();
    $pending_quotations = $stmt->fetch(PDO::FETCH_ASSOC)['pending_quotations'];
    
    // Órdenes cotizadas
    $query = "SELECT COUNT(*) as quoted_orders 
              FROM order_suppliers os 
              WHERE os.supplier_id = :supplier_id AND os.status = 'responded'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':supplier_id', $supplier_id);
    $stmt->execute();
    $quoted_orders = $stmt->fetch(PDO::FETCH_ASSOC)['quoted_orders'];
    
    // Órdenes aprobadas
    $query = "SELECT COUNT(*) as approved_orders 
              FROM order_suppliers os 
              WHERE os.supplier_id = :supplier_id AND os.status = 'selected'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':supplier_id', $supplier_id);
    $stmt->execute();
    $approved_orders = $stmt->fetch(PDO::FETCH_ASSOC)['approved_orders'];
    
    $stats = [
        'orders' => [
            'total_orders' => (int)$total_orders,
            'quoted_orders' => (int)$quoted_orders,
            'approved_orders' => (int)$approved_orders
        ],
        'pending_quotations' => (int)$pending_quotations
    ];
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener estadísticas: ' . $e->getMessage()]);
}
?>
