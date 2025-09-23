<?php
// Endpoint: POST /api/users
$auth_user = $auth->requireAdmin();

if(!isset($input['username']) || !isset($input['email']) || !isset($input['password']) || 
   !isset($input['first_name']) || !isset($input['last_name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Todos los campos son requeridos']);
    exit;
}

$user = new User($db);
$user->username = $input['username'];
$user->email = $input['email'];
$user->password_hash = password_hash($input['password'], PASSWORD_DEFAULT);
$user->first_name = $input['first_name'];
$user->last_name = $input['last_name'];
$user->role = $input['role'] ?? 'buyer';
$user->department = $input['department'] ?? '';

if($user->create()) {
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Usuario creado exitosamente',
        'user_id' => $user->id
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al crear usuario']);
}
?>
