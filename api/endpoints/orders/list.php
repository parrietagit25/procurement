<?php
// Endpoint: GET /api/orders
$auth_user = $auth->requireAuth();

$order = new PurchaseOrder($db);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$offset = ($page - 1) * $limit;

$filters = [];
if(isset($_GET['status'])) $filters['status'] = $_GET['status'];
if(isset($_GET['priority'])) $filters['priority'] = $_GET['priority'];
if(isset($_GET['search'])) $filters['search'] = $_GET['search'];

$orders = $order->getAll($filters, $limit, $offset);

http_response_code(200);
echo json_encode([
    'success' => true,
    'data' => $orders,
    'pagination' => [
        'page' => $page,
        'limit' => $limit,
        'total' => count($orders)
    ]
]);
?>