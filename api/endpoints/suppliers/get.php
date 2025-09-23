<?php
// Endpoint: GET /api/suppliers/{id}
$auth_user = $auth->requireAuth();

if($auth_user['type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo administradores pueden ver proveedores']);
    exit;
}

try {
    $supplier = new Supplier($db);
    
    if(!$supplier->getById($supplier_id)) {
        http_response_code(404);
        echo json_encode(['error' => 'Proveedor no encontrado']);
        exit;
    }
    
    // Obtener datos completos del proveedor desde la base de datos
    $query = "SELECT * FROM suppliers WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $supplier_id);
    $stmt->execute();
    $supplierData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $supplierData
    ]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener proveedor: ' . $e->getMessage()]);
}
?>
