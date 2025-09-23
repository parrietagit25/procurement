<?php
// Endpoint: GET /api/categories
$auth_user = $auth->requireAuth();

$category = new Category($db);
$parent_id = isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : null;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

if(isset($_GET['hierarchy']) && $_GET['hierarchy'] === 'true') {
    $categories = $category->getHierarchy();
} else {
    $categories = $category->getAll($parent_id, $limit, $offset);
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'data' => $categories
]);
?>
