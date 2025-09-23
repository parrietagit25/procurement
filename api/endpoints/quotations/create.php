<?php
// Endpoint: POST /api/quotations
$auth_user = $auth->requireAuth();

// Obtener datos del cuerpo de la petición
$input = json_decode(file_get_contents('php://input'), true);

try {
    // Validar datos requeridos
    $required_fields = ['order_id', 'supplier_id', 'items'];
    foreach($required_fields as $field) {
        if(empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "El campo $field es requerido"]);
            exit;
        }
    }
    
    // Verificar que la orden existe
    $query = "SELECT id FROM purchase_orders WHERE id = :order_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':order_id', $input['order_id']);
    $stmt->execute();
    
    if($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Orden no encontrada']);
        exit;
    }
    
    // Verificar que el proveedor existe
    $query = "SELECT id FROM suppliers WHERE id = :supplier_id AND status = 'approved'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':supplier_id', $input['supplier_id']);
    $stmt->execute();
    
    if($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Proveedor no encontrado o no aprobado']);
        exit;
    }
    
    // Crear cotización
    $query = "INSERT INTO quotations 
              (order_id, supplier_id, status, notes, valid_until, total_amount) 
              VALUES (:order_id, :supplier_id, 'pending', :notes, :valid_until, :total_amount)";
    
    $stmt = $db->prepare($query);
    
    // Preparar valores para bindParam
    $notes = $input['notes'] ?? null;
    $valid_until = $input['valid_until'] ?? null;
    $total_amount = 0;
    
    // Calcular total de items
    foreach($input['items'] as $item) {
        $total_amount += $item['quantity'] * $item['unit_price'];
    }
    
    $stmt->bindParam(':order_id', $input['order_id']);
    $stmt->bindParam(':supplier_id', $input['supplier_id']);
    $stmt->bindParam(':notes', $notes);
    $stmt->bindParam(':valid_until', $valid_until);
    $stmt->bindParam(':total_amount', $total_amount);
    
    if($stmt->execute()) {
        $quotation_id = $db->lastInsertId();
        
        // Crear items de la cotización
        foreach($input['items'] as $item) {
            $itemQuery = "INSERT INTO quotation_items 
                          (quotation_id, product_name, description, quantity, unit, unit_price, total_price) 
                          VALUES (:quotation_id, :product_name, :description, :quantity, :unit, :unit_price, :total_price)";
            
            $itemStmt = $db->prepare($itemQuery);
            $itemStmt->bindParam(':quotation_id', $quotation_id);
            $itemStmt->bindParam(':product_name', $item['product_name']);
            $itemStmt->bindParam(':description', $item['description'] ?? null);
            $itemStmt->bindParam(':quantity', $item['quantity']);
            $itemStmt->bindParam(':unit', $item['unit']);
            $itemStmt->bindParam(':unit_price', $item['unit_price']);
            $itemStmt->bindParam(':total_price', $item['quantity'] * $item['unit_price']);
            $itemStmt->execute();
        }
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Cotización creada exitosamente',
            'data' => ['id' => $quotation_id]
        ]);
    } else {
        throw new Exception('Error al crear la cotización');
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al crear cotización: ' . $e->getMessage()]);
}
?>
