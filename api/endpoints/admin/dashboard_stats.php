<?php
// Endpoint: GET /api/admin/dashboard_stats
$auth_user = $auth->requireAuth();

if($auth_user['type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo administradores pueden acceder a este endpoint']);
    exit;
}

try {
    // Estadísticas de órdenes
    $query = "SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'borrador' THEN 1 ELSE 0 END) as draft_orders,
                SUM(CASE WHEN status = 'enviado' THEN 1 ELSE 0 END) as sent_orders,
                SUM(CASE WHEN status = 'cotizado' THEN 1 ELSE 0 END) as quoted_orders,
                SUM(CASE WHEN status = 'aprobado' THEN 1 ELSE 0 END) as approved_orders,
                SUM(CASE WHEN status = 'en_ejecucion' THEN 1 ELSE 0 END) as in_progress_orders,
                SUM(CASE WHEN status = 'recibido' THEN 1 ELSE 0 END) as received_orders,
                SUM(CASE WHEN status = 'cancelado' THEN 1 ELSE 0 END) as cancelled_orders,
                SUM(total_amount) as total_amount
              FROM purchase_orders";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $order_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Estadísticas de proveedores
    $query = "SELECT 
                COUNT(*) as total_suppliers,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_suppliers,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_suppliers,
                SUM(CASE WHEN status = 'suspended' THEN 1 ELSE 0 END) as suspended_suppliers,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_suppliers
              FROM suppliers";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $supplier_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Estadísticas de productos
    $query = "SELECT 
                COUNT(*) as total_products,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_products,
                SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive_products
              FROM products";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $product_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Estadísticas de cotizaciones
    $query = "SELECT 
                COUNT(*) as total_quotations,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_quotations,
                SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted_quotations,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_quotations
              FROM quotations";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $quotation_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Órdenes por mes (últimos 6 meses)
    $query = "SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month, 
                COUNT(*) as count, 
                SUM(total_amount) as total 
              FROM purchase_orders 
              WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) 
              GROUP BY month 
              ORDER BY month";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $orders_by_month = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stats = [
        'orders' => [
            'total_orders' => (int)$order_stats['total_orders'],
            'draft_orders' => (int)$order_stats['draft_orders'],
            'sent_orders' => (int)$order_stats['sent_orders'],
            'quoted_orders' => (int)$order_stats['quoted_orders'],
            'approved_orders' => (int)$order_stats['approved_orders'],
            'in_progress_orders' => (int)$order_stats['in_progress_orders'],
            'received_orders' => (int)$order_stats['received_orders'],
            'cancelled_orders' => (int)$order_stats['cancelled_orders'],
            'total_amount' => (float)$order_stats['total_amount']
        ],
        'suppliers' => [
            'total_suppliers' => (int)$supplier_stats['total_suppliers'],
            'pending' => (int)$supplier_stats['pending_suppliers'],
            'approved' => (int)$supplier_stats['approved_suppliers'],
            'suspended' => (int)$supplier_stats['suspended_suppliers'],
            'rejected' => (int)$supplier_stats['rejected_suppliers']
        ],
        'products' => [
            'total_products' => (int)$product_stats['total_products'],
            'active_products' => (int)$product_stats['active_products'],
            'inactive_products' => (int)$product_stats['inactive_products']
        ],
        'quotations' => [
            'total_quotations' => (int)$quotation_stats['total_quotations'],
            'pending_quotations' => (int)$quotation_stats['pending_quotations'],
            'accepted_quotations' => (int)$quotation_stats['accepted_quotations'],
            'rejected_quotations' => (int)$quotation_stats['rejected_quotations']
        ],
        'orders_by_month' => $orders_by_month
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
