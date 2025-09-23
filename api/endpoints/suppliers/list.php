<?php
// Endpoint: GET /api/suppliers
$auth_user = $auth->requireAuth();

$supplier = new Supplier($db);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$offset = ($page - 1) * $limit;

$suppliers = $supplier->getAll($limit, $offset);

http_response_code(200);
echo json_encode([
    'success' => true,
    'data' => $suppliers,
    'pagination' => [
        'page' => $page,
        'limit' => $limit,
        'total' => count($suppliers)
    ]
]);
?>