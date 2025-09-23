<?php
// Endpoint: POST /api/suppliers/{id}/approve
$auth_user = $auth->requireAuth();

if($auth_user['type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo administradores pueden aprobar proveedores']);
    exit;
}

try {
    $supplier = new Supplier($db);
    
    if(!$supplier->getById($supplier_id)) {
        http_response_code(404);
        echo json_encode(['error' => 'Proveedor no encontrado']);
        exit;
    }
    
    if($supplier->status !== 'pending') {
        http_response_code(400);
        echo json_encode(['error' => 'El proveedor ya ha sido procesado']);
        exit;
    }
    
    if($supplier->approve()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Proveedor aprobado exitosamente'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al aprobar proveedor']);
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
}
?>