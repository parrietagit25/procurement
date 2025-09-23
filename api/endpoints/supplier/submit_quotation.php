<?php
// Endpoint: POST /api/supplier/submit_quotation
$auth_user = $auth->requireAuth();

if($auth_user['type'] !== 'supplier') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo proveedores pueden acceder a este endpoint']);
    exit;
}

// Obtener datos del cuerpo de la petición
$input = json_decode(file_get_contents('php://input'), true);

try {
    // Validar datos requeridos
    $required_fields = ['order_id', 'quotation_number', 'valid_until', 'items'];
    foreach($required_fields as $field) {
        if(empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "El campo $field es requerido"]);
            exit;
        }
    }
    
    $supplier_id = $auth_user['supplier']->id;
    
    // Verificar que la orden está asignada al proveedor
    $query = "SELECT po.id FROM purchase_orders po
              JOIN order_suppliers os ON po.id = os.order_id
              WHERE po.id = :order_id AND os.supplier_id = :supplier_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':order_id', $input['order_id']);
    $stmt->bindParam(':supplier_id', $supplier_id);
    $stmt->execute();
    
    if($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Orden no encontrada o no asignada a este proveedor']);
        exit;
    }
    
    // Calcular total
    $total_amount = 0;
    foreach($input['items'] as $item) {
        $total_amount += $item['quantity'] * $item['unit_price'];
    }
    
    // Crear cotización
    $query = "INSERT INTO quotations 
              (order_id, supplier_id, quotation_number, total_amount, valid_until, notes, status) 
              VALUES (:order_id, :supplier_id, :quotation_number, :total_amount, :valid_until, :notes, 'pending')";
    
    $stmt = $db->prepare($query);
    
    $notes = $input['notes'] ?? null;
    
    $stmt->bindParam(':order_id', $input['order_id']);
    $stmt->bindParam(':supplier_id', $supplier_id);
    $stmt->bindParam(':quotation_number', $input['quotation_number']);
    $stmt->bindParam(':total_amount', $total_amount);
    $stmt->bindParam(':valid_until', $input['valid_until']);
    $stmt->bindParam(':notes', $notes);
    
    if($stmt->execute()) {
        $quotation_id = $db->lastInsertId();
        
        // Crear items de la cotización
        foreach($input['items'] as $item) {
            $itemQuery = "INSERT INTO quotation_items 
                          (quotation_id, order_item_id, unit_price, total_price, notes) 
                          VALUES (:quotation_id, :order_item_id, :unit_price, :total_price, :notes)";
            
            $itemStmt = $db->prepare($itemQuery);
            $total_price = $item['quantity'] * $item['unit_price'];
            $item_notes = $item['notes'] ?? null;
            
            $itemStmt->bindParam(':quotation_id', $quotation_id);
            $itemStmt->bindParam(':order_item_id', $item['order_item_id']);
            $itemStmt->bindParam(':unit_price', $item['unit_price']);
            $itemStmt->bindParam(':total_price', $total_price);
            $itemStmt->bindParam(':notes', $item_notes);
            $itemStmt->execute();
        }
        
        // Actualizar estado de la asignación
        $updateQuery = "UPDATE order_suppliers 
                        SET status = 'responded', responded_at = NOW() 
                        WHERE order_id = :order_id AND supplier_id = :supplier_id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(':order_id', $input['order_id']);
        $updateStmt->bindParam(':supplier_id', $supplier_id);
        $updateStmt->execute();
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Cotización enviada exitosamente',
            'data' => ['id' => $quotation_id]
        ]);
    } else {
        throw new Exception('Error al crear la cotización');
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al enviar cotización: ' . $e->getMessage()]);
}
?>
