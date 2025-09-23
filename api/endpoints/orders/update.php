<?php
// Endpoint: PUT /api/orders/{id}
$auth_user = $auth->requireAuth();

if($auth_user['type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo administradores pueden editar órdenes']);
    exit;
}

try {
    $order = new PurchaseOrder($db);
    
    if(!$order->getById($order_id)) {
        http_response_code(404);
        echo json_encode(['error' => 'Orden no encontrada']);
        exit;
    }
    
    // Actualizar datos básicos de la orden
    $query = "UPDATE purchase_orders 
              SET title = :title, description = :description, priority = :priority, 
                  department = :department, required_date = :required_date, 
                  status = :status, updated_at = NOW() 
              WHERE id = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':title', $input['title']);
    $stmt->bindParam(':description', $input['description']);
    $stmt->bindParam(':priority', $input['priority']);
    $stmt->bindParam(':department', $input['department']);
    $stmt->bindParam(':required_date', $input['required_date']);
    $stmt->bindParam(':status', $input['status']);
    $stmt->bindParam(':id', $order_id);
    
    if(!$stmt->execute()) {
        throw new Exception('Error al actualizar la orden');
    }
    
    // Si se proporcionan items, actualizarlos
    if(isset($input['items']) && is_array($input['items'])) {
        // Eliminar items existentes
        $query = "DELETE FROM purchase_order_items WHERE order_id = :order_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        
        // Insertar nuevos items
        $totalAmount = 0;
        foreach($input['items'] as $itemData) {
            $query = "INSERT INTO purchase_order_items 
                      (order_id, product_name, description, quantity, unit, estimated_price, total_price) 
                      VALUES (:order_id, :product_name, :description, :quantity, :unit, :estimated_price, :total_price)";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':order_id', $order_id);
            $stmt->bindParam(':product_name', $itemData['product_name']);
            $stmt->bindParam(':description', $itemData['description']);
            $stmt->bindParam(':quantity', $itemData['quantity']);
            $stmt->bindParam(':unit', $itemData['unit']);
            $stmt->bindParam(':estimated_price', $itemData['estimated_price']);
            $stmt->bindParam(':total_price', $itemData['total_price']);
            $stmt->execute();
            
            $totalAmount += $itemData['total_price'];
        }
        
        // Actualizar monto total
        $query = "UPDATE purchase_orders SET total_amount = :total_amount WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':total_amount', $totalAmount);
        $stmt->bindParam(':id', $order_id);
        $stmt->execute();
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Orden actualizada exitosamente'
    ]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar orden: ' . $e->getMessage()]);
}
?>
