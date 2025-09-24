<?php
// Endpoint: DELETE /api/orders/{order_id}/suppliers/{supplier_id}
$auth_user = $auth->requireAuth();

if($auth_user['type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo administradores pueden acceder a este endpoint']);
    exit;
}

// Obtener order_id y supplier_id desde los parámetros GET
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;

if(!$order_id || !$supplier_id) {
    http_response_code(400);
    echo json_encode(['error' => 'IDs de orden y proveedor requeridos']);
    exit;
}

try {
    // Verificar que la orden existe
    $query = "SELECT id FROM purchase_orders WHERE id = :order_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->execute();
    
    if($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Orden no encontrada']);
        exit;
    }
    
    // Verificar que el proveedor está asignado a la orden
    $query = "SELECT id FROM order_suppliers WHERE order_id = :order_id AND supplier_id = :supplier_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->bindParam(':supplier_id', $supplier_id, PDO::PARAM_INT);
    $stmt->execute();
    
    if($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Proveedor no está asignado a esta orden']);
        exit;
    }
    
    // Eliminar la asignación del proveedor
    $query = "DELETE FROM order_suppliers WHERE order_id = :order_id AND supplier_id = :supplier_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->bindParam(':supplier_id', $supplier_id, PDO::PARAM_INT);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Proveedor removido exitosamente de la orden'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al remover proveedor']);
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al remover proveedor: ' . $e->getMessage()]);
}
?>
