<?php
// Endpoint: POST /api/auth/login
if(!isset($input['username']) || !isset($input['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Username y password son requeridos']);
    exit;
}

$username = $input['username'];
$password = $input['password'];

// Intentar autenticación como usuario interno
$result = $auth->loginUser($username, $password);

if(!$result['success']) {
    // Si falla como usuario interno, intentar como proveedor
    $result = $auth->loginSupplier($username, $password);
}

if($result['success']) {
    http_response_code(200);
    echo json_encode($result);
} else {
    http_response_code(401);
    echo json_encode($result);
}
?>
