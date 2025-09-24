<?php
// Endpoint: GET /api/orders/{id}
$auth_user = $auth->requireAuth();

if($auth_user['type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo administradores pueden ver órdenes']);
    exit;
}

// Obtener el ID de la orden desde los parámetros GET
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if(!$order_id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de orden requerido']);
    exit;
}

try {
    $order = new PurchaseOrder($db);
    $orderData = $order->getById($order_id);
    
    if(!$orderData) {
        http_response_code(404);
        echo json_encode(['error' => 'Orden no encontrada']);
        exit;
    }
    
    // Obtener items de la orden
    $query = "SELECT * FROM purchase_order_items WHERE order_id = :order_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener proveedores asignados
    $query = "SELECT s.*, os.status as assignment_status, os.invited_at, os.responded_at 
              FROM order_suppliers os 
              JOIN suppliers s ON os.supplier_id = s.id 
              WHERE os.order_id = :order_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $orderData['items'] = $items;
    $orderData['suppliers'] = $suppliers;
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $orderData
    ]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener orden: ' . $e->getMessage()]);
}
?>