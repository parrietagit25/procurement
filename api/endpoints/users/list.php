<?php
// Endpoint: GET /api/users
$auth_user = $auth->requireAdmin();

$user = new User($db);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$offset = ($page - 1) * $limit;

$users = $user->getAll($limit, $offset);

http_response_code(200);
echo json_encode([
    'success' => true,
    'data' => $users,
    'pagination' => [
        'page' => $page,
        'limit' => $limit,
        'total' => count($users)
    ]
]);
?>
