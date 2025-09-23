<?php
// Endpoint: POST /api/orders
$auth_user = $auth->requireAuth();

if($auth_user['type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo administradores pueden crear órdenes']);
    exit;
}

if(!isset($input['title']) || !isset($input['items']) || !is_array($input['items'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Title e items son requeridos']);
    exit;
}

try {
    $db->beginTransaction();
    
    // Crear orden
    $order = new PurchaseOrder($db);
    $order->title = $input['title'];
    $order->description = $input['description'] ?? '';
    $order->requested_by = $auth_user['user']->id;
    $order->department = $input['department'] ?? $auth_user['user']->department;
    $order->priority = $input['priority'] ?? 'medium';
    $order->status = $input['status'] ?? 'borrador';
    $order->total_amount = $input['total_amount'] ?? 0;
    $order->currency = $input['currency'] ?? 'MXN';
    $order->required_date = $input['required_date'] ?? null;
    
    if(!$order->create()) {
        throw new Exception('Error al crear la orden');
    }
    
    // Crear items de la orden
    $totalAmount = 0;
    foreach($input['items'] as $itemData) {
        $query = "INSERT INTO purchase_order_items 
                  (order_id, product_name, description, quantity, unit, estimated_price, total_price) 
                  VALUES (:order_id, :product_name, :description, :quantity, :unit, :estimated_price, :total_price)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':order_id', $order->id);
        $stmt->bindParam(':product_name', $itemData['product_name']);
        $stmt->bindParam(':description', $itemData['description']);
        $stmt->bindParam(':quantity', $itemData['quantity']);
        $stmt->bindParam(':unit', $itemData['unit']);
        $stmt->bindParam(':estimated_price', $itemData['estimated_price']);
        $stmt->bindParam(':total_price', $itemData['total_price']);
        
        if(!$stmt->execute()) {
            throw new Exception('Error al crear item de la orden');
        }
        
        $totalAmount += $itemData['total_price'];
    }
    
    // Actualizar monto total
    $query = "UPDATE purchase_orders SET total_amount = :total_amount WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':total_amount', $totalAmount);
    $stmt->bindParam(':id', $order->id);
    $stmt->execute();
    
    $db->commit();
    
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Orden de compra creada exitosamente',
        'order_id' => $order->id,
        'order_number' => $order->order_number
    ]);
    
} catch(Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Error al crear orden: ' . $e->getMessage()]);
}
?>