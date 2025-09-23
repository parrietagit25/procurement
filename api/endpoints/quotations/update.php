<?php
// Endpoint: PUT /api/quotations/{id}
$auth_user = $auth->requireAuth();

// Obtener datos del cuerpo de la petición
$input = json_decode(file_get_contents('php://input'), true);

try {
    // Verificar que la cotización existe
    $query = "SELECT id FROM quotations WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $quotation_id);
    $stmt->execute();
    
    if($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Cotización no encontrada']);
        exit;
    }
    
    // Actualizar cotización
    $query = "UPDATE quotations 
              SET status = :status, notes = :notes, valid_until = :valid_until, 
                  reviewed_by = :reviewed_by, reviewed_at = NOW() 
              WHERE id = :id";
    
    $stmt = $db->prepare($query);
    
    // Preparar valores para bindParam
    $status = $input['status'] ?? 'pending';
    $notes = $input['notes'] ?? null;
    $valid_until = $input['valid_until'] ?? null;
    $reviewed_by = $auth_user['type'] === 'admin' ? $auth_user['user']->id : null;
    
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':notes', $notes);
    $stmt->bindParam(':valid_until', $valid_until);
    $stmt->bindParam(':reviewed_by', $reviewed_by);
    $stmt->bindParam(':id', $quotation_id);
    
    if($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Cotización actualizada exitosamente'
        ]);
    } else {
        throw new Exception('Error al actualizar la cotización');
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar cotización: ' . $e->getMessage()]);
}
?>
