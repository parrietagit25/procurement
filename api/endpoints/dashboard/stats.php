<?php
// Endpoint: GET /api/dashboard/stats
$auth_user = $auth->requireAuth();

$order = new PurchaseOrder($db);
$supplier = new Supplier($db);

$stats = [];

if($auth_user['type'] === 'admin') {
    // Estadísticas para administradores
    $stats['orders'] = $order->getStats();
    
    // Contar proveedores por estado
    $query = "SELECT status, COUNT(*) as count FROM suppliers GROUP BY status";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $supplier_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stats['suppliers'] = [];
    foreach($supplier_stats as $stat) {
        $stats['suppliers'][$stat['status']] = (int)$stat['count'];
    }
    
    // Órdenes por mes (últimos 6 meses)
    $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count, SUM(total_amount) as total 
              FROM purchase_orders 
              WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) 
              GROUP BY month 
              ORDER BY month";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats['orders_by_month'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} else {
    // Estadísticas para proveedores
    $stats['orders'] = $order->getStats($auth_user['supplier']->id);
    
    // Órdenes pendientes de cotización
    $query = "SELECT COUNT(*) as pending_quotations 
              FROM order_suppliers os 
              INNER JOIN purchase_orders po ON os.order_id = po.id 
              WHERE os.supplier_id = :supplier_id AND os.status = 'invited' AND po.status = 'enviado'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':supplier_id', $auth_user['supplier']->id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['pending_quotations'] = (int)$result['pending_quotations'];
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'data' => $stats
]);
?>
