<?php
// Endpoint: POST /api/orders/{id}/suppliers
$auth_user = $auth->requireAuth();

if($auth_user['type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo administradores pueden asignar proveedores']);
    exit;
}

// Obtener el ID de la orden desde los parámetros GET
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if(!$order_id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de orden requerido']);
    exit;
}

if(!isset($input['supplier_ids']) || !is_array($input['supplier_ids'])) {
    http_response_code(400);
    echo json_encode(['error' => 'supplier_ids es requerido y debe ser un array']);
    exit;
}

try {
    // Verificar que la orden existe
    $order = new PurchaseOrder($db);
    $orderData = $order->getById($order_id);
    if(!$orderData) {
        http_response_code(404);
        echo json_encode(['error' => 'Orden no encontrada']);
        exit;
    }
    
    $db->beginTransaction();
    
    // Asignar proveedores
    foreach($input['supplier_ids'] as $supplier_id) {
        // Verificar que el proveedor existe y está aprobado
        $query = "SELECT id, status FROM suppliers WHERE id = :supplier_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':supplier_id', $supplier_id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($supplier['status'] === 'approved') {
                // Verificar si ya está asignado
                $query = "SELECT id FROM order_suppliers WHERE order_id = :order_id AND supplier_id = :supplier_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':order_id', $order_id);
                $stmt->bindParam(':supplier_id', $supplier_id);
                $stmt->execute();
                
                if($stmt->rowCount() == 0) {
                    // Asignar proveedor
                    $query = "INSERT INTO order_suppliers (order_id, supplier_id, status) 
                              VALUES (:order_id, :supplier_id, 'invited')";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':order_id', $order_id);
                    $stmt->bindParam(':supplier_id', $supplier_id);
                    $stmt->execute();
                }
            }
        }
    }
    
    // Actualizar estado de la orden si es necesario
    if($orderData['status'] === 'borrador') {
        $order->updateStatus('enviado');
    }
    
    $db->commit();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Proveedores asignados exitosamente'
    ]);
    
} catch(Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Error al asignar proveedores: ' . $e->getMessage()]);
}
?>
